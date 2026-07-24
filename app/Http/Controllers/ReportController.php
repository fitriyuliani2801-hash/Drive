<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('category')->latest();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(9)->withQueryString();
        $categories = Category::withCount('reports')->get();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];

        return view('reports.index', compact('reports', 'categories', 'districts'));
    }

    public function map(Request $request)
    {
        $categories = Category::all();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];
        return view('reports.map', compact('categories', 'districts'));
    }

    public function apiGeojson(Request $request)
    {
        $query = Report::with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $reports = $query->get();

        $features = $reports->map(function ($report) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $report->longitude, (float) $report->latitude],
                ],
                'properties' => [
                    'id' => $report->id,
                    'ticket_code' => $report->ticket_code,
                    'title' => $report->title,
                    'category' => $report->category->name ?? 'Umum',
                    'category_slug' => $report->category->slug ?? 'umum',
                    'color' => $report->category->color_code ?? '#3B82F6',
                    'icon' => $report->category->icon ?? 'ri-map-pin-line',
                    'status' => $report->status,
                    'status_label' => $report->status_label,
                    'status_badge' => $report->status_badge_class,
                    'urgency' => $report->urgency,
                    'urgency_label' => $report->urgency_label,
                    'district' => $report->district,
                    'location' => $report->location_address,
                    'image' => $report->image_path ? asset('storage/' . $report->image_path) : null,
                    'created_at' => $report->created_at->diffForHumans(),
                    'url' => route('reports.show', $report->id),
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];
        return view('reports.create', compact('categories', 'districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'reporter_name' => 'required|string|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_address' => 'required|string|max:255',
            'district' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        // Generate Ticket Code
        $dateStr = now()->format('Ym');
        $count = Report::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() + 1;
        $ticketCode = 'MTR-' . $dateStr . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $report = Report::create([
            'ticket_code' => $ticketCode,
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'reporter_name' => $validated['reporter_name'],
            'reporter_phone' => $validated['reporter_phone'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_address' => $validated['location_address'],
            'district' => $validated['district'],
            'image_path' => $imagePath,
            'status' => 'pending',
            'urgency' => 'medium',
        ]);

        // Record initial log
        ReportLog::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'status_from' => null,
            'status_to' => 'pending',
            'note' => 'Laporan berhasil dibuat oleh warga dan menunggu proses verifikasi admin.',
        ]);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Laporan Anda berhasil dikirim dengan kode tiket ' . $ticketCode . '! Tim verifikasi akan segera meninjau laporan Anda.');
    }

    public function show($id)
    {
        $report = Report::with(['category', 'user', 'logs.user'])->findOrFail($id);
        return view('reports.show', compact('report'));
    }
}
