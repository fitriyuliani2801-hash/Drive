<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CrawledComment;
use App\Models\CronLog;
use App\Models\LdaTopic;
use App\Services\LdaTopicEngineService;
use App\Services\TextPreprocessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AnalysisController extends Controller
{
    protected TextPreprocessingService $preprocessor;
    protected LdaTopicEngineService $ldaEngine;

    public function __construct(TextPreprocessingService $preprocessor, LdaTopicEngineService $ldaEngine)
    {
        $this->preprocessor = $preprocessor;
        $this->ldaEngine = $ldaEngine;
    }

    /**
     * Dashboard Analisis LDA, Status Task Scheduler (Cron Job), & Visualisasi Kata Kunci Dominan
     */
    public function index(Request $request)
    {
        // Public portal displays LDA topics that have been reviewed & published by Admin Redaksi
        $topics = LdaTopic::published()->with('category')->withCount('comments')->get();
        $isPublishedState = true;

        if ($topics->isEmpty()) {
            // Fallback for initial state before Admin clicks Publish
            $topics = LdaTopic::with('category')->withCount('comments')->get();
            $isPublishedState = false;
        }

        $categories = Category::withCount('comments')->get();

        $query = CrawledComment::with(['category', 'ldaTopic']);

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $comments = $query->latest()->paginate(10)->withQueryString();
        $totalComments = CrawledComment::count();
        $platforms = CrawledComment::select('platform')->distinct()->pluck('platform');

        // Scheduler Server Status Logs
        $lastCronLog = CronLog::where('status', 'success')->latest()->first();
        $cronLogs = CronLog::latest()->take(5)->get();

        // Extract Top Keywords across LDA topics for Word Cloud / Word Frequency Chart
        $allKeywords = [];
        foreach ($topics as $topic) {
            $keywords = $topic->keywords ?? [];
            foreach ($keywords as $kw) {
                $word = $kw['word'] ?? '';
                $weight = $kw['weight'] ?? 0.5;
                if ($word) {
                    $allKeywords[$word] = ($allKeywords[$word] ?? 0) + ($weight * 100);
                }
            }
        }
        arsort($allKeywords);

        return view('analysis.index', compact(
            'topics',
            'categories',
            'comments',
            'totalComments',
            'platforms',
            'allKeywords',
            'lastCronLog',
            'cronLogs'
        ));
    }

    /**
     * Halaman Transparansi & Inspeksi 4 Tahapan Pre-processing Teks
     */
    public function preprocessing(Request $request)
    {
        $query = CrawledComment::with(['category', 'ldaTopic']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('raw_text', 'like', "%{$search}%");
        }

        $comments = $query->latest()->paginate(15)->withQueryString();

        return view('analysis.preprocessing', compact('comments'));
    }

    /**
     * Halaman Inspeksi Langkah 3: Representasi Dokumen (DTM & TF-IDF Vectorization)
     */
    public function vectorization(Request $request)
    {
        $comments = CrawledComment::with(['category', 'ldaTopic'])->latest()->take(20)->get();
        $matrixData = $this->ldaEngine->buildDocumentTermMatrix($comments);

        return view('analysis.vectorization', [
            'comments' => $comments,
            'vocabulary' => $matrixData['vocabulary'],
            'dtmMatrix' => $matrixData['dtm_matrix'],
            'tfidfMatrix' => $matrixData['tfidf_matrix'],
            'totalDocs' => $matrixData['total_documents'],
        ]);
    }

    /**
     * Halaman Tabel Data Mentah Komentar Scraper
     */
    public function comments(Request $request)
    {
        $query = CrawledComment::with(['category', 'ldaTopic']);

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('raw_text', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('source_account', 'like', "%{$search}%");
        }

        $comments = $query->latest()->paginate(15)->withQueryString();
        $platforms = CrawledComment::select('platform')->distinct()->pluck('platform');

        return view('analysis.comments', compact('comments', 'platforms'));
    }

    /**
     * Trigger Scraper & Re-run LDA Topic Modeling Engine via Artisan Command
     */
    public function runAnalysis()
    {
        Artisan::call('lda:auto-run');
        return redirect()->route('analysis.index')->with('success', 'Sistem Task Scheduler & Pipeline LDA Topic Modeling otomatis berhasil dijalankan!');
    }
}
