<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReportController extends Controller
{
    public function dashboard()
    {
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $verifiedReports = Report::where('status', 'verified')->count();
        $inProgressReports = Report::where('status', 'in_progress')->count();
        $resolvedReports = Report::where('status', 'resolved')->count();
        $rejectedReports = Report::where('status', 'rejected')->count();

        $recentReports = Report::with('category')->latest()->take(5)->get();
        $criticalReports = Report::with('category')->where('urgency', 'critical')->whereIn('status', ['pending', 'verified', 'in_progress'])->get();

        return view('admin.dashboard', compact(
            'totalReports',
            'pendingReports',
            'verifiedReports',
            'inProgressReports',
            'resolvedReports',
            'rejectedReports',
            'recentReports',
            'criticalReports'
        ));
    }

    public function index(Request $request)
    {
        $query = Report::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%")
                  ->orWhere('reporter_name', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.reports.index', compact('reports', 'categories'));
    }

    public function show($id)
    {
        $report = Report::with(['category', 'user', 'logs.user'])->findOrFail($id);
        $categories = Category::all();
        return view('admin.reports.show', compact('report', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,verified,in_progress,resolved,rejected',
            'urgency' => 'required|in:low,medium,high,critical',
            'category_id' => 'required|exists:categories,id',
            'admin_note' => 'nullable|string',
            'resolution_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $oldStatus = $report->status;
        $newStatus = $validated['status'];

        $resolutionImagePath = $report->resolution_image_path;
        if ($request->hasFile('resolution_image')) {
            $resolutionImagePath = $request->file('resolution_image')->store('resolutions', 'public');
        }

        $updateData = [
            'status' => $newStatus,
            'urgency' => $validated['urgency'],
            'category_id' => $validated['category_id'],
            'admin_note' => $validated['admin_note'],
            'resolution_image_path' => $resolutionImagePath,
        ];

        if ($oldStatus !== 'verified' && $newStatus === 'verified' && !$report->verified_at) {
            $updateData['verified_at'] = now();
        }

        if ($oldStatus !== 'resolved' && $newStatus === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $report->update($updateData);

        // Record log if status changed or admin note added
        if ($oldStatus !== $newStatus || $validated['admin_note']) {
            ReportLog::create([
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'status_from' => $oldStatus,
                'status_to' => $newStatus,
                'note' => $validated['admin_note'] ?? ('Status laporan diperbarui dari ' . $oldStatus . ' ke ' . $newStatus),
            ]);
        }

        return redirect()->back()->with('success', 'Laporan ' . $report->ticket_code . ' berhasil diperbarui!');
    }

    public function analytics()
    {
        // Category Distribution
        $categoriesStats = Category::withCount('reports')->get();

        // District Stats
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];
        $districtStats = [];
        foreach ($districts as $district) {
            $districtStats[$district] = Report::where('district', $district)->count();
        }

        // Status Distribution
        $statusCounts = [
            'Pending' => Report::where('status', 'pending')->count(),
            'Terverifikasi' => Report::where('status', 'verified')->count(),
            'Diproses' => Report::where('status', 'in_progress')->count(),
            'Selesai' => Report::where('status', 'resolved')->count(),
            'Ditolak' => Report::where('status', 'rejected')->count(),
        ];

        // Urgency Stats
        $urgencyCounts = [
            'Rendah' => Report::where('urgency', 'low')->count(),
            'Sedang' => Report::where('urgency', 'medium')->count(),
            'Tinggi' => Report::where('urgency', 'high')->count(),
            'Kritis' => Report::where('urgency', 'critical')->count(),
        ];

        return view('admin.analytics.index', compact(
            'categoriesStats',
            'districtStats',
            'statusCounts',
            'urgencyCounts'
        ));
    }
}
