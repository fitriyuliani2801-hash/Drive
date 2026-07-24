<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SocialAnalysis;
use App\Models\SocialComment;
use App\Services\HoaxDetectionService;
use App\Services\SentimentAnalysisService;
use App\Services\SocialUrlParserService;
use Illuminate\Http\Request;

class SocialAnalysisController extends Controller
{
    protected SocialUrlParserService $urlParser;
    protected HoaxDetectionService $hoaxDetector;
    protected SentimentAnalysisService $sentimentEngine;

    public function __construct(
        SocialUrlParserService $urlParser,
        HoaxDetectionService $hoaxDetector,
        SentimentAnalysisService $sentimentEngine
    ) {
        $this->urlParser = $urlParser;
        $this->hoaxDetector = $hoaxDetector;
        $this->sentimentEngine = $sentimentEngine;
    }

    /**
     * Index: Catalog of Analyzed Social Media Links
     */
    public function index(Request $request)
    {
        $query = SocialAnalysis::with('category')->latest();

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('verdict')) {
            $query->where('verdict', $request->verdict);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('post_title', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
        }

        $analyses = $query->paginate(9)->withQueryString();
        $totalAnalyzed = SocialAnalysis::count();
        $totalFacts = SocialAnalysis::where('verdict', 'asli')->count();
        $totalHoaxes = SocialAnalysis::where('verdict', 'hoaks')->count();

        return view('social.index', compact('analyses', 'totalAnalyzed', 'totalFacts', 'totalHoaxes'));
    }

    /**
     * Store: Analyze a New Social Media Link (Instagram / Facebook / TikTok)
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ], [
            'url.required' => 'Masukkan link URL postingan Instagram, Facebook, atau TikTok.',
            'url.url' => 'Format link URL tidak valid. Pastikan diawali http:// atau https://',
        ]);

        $url = $request->url;

        // Check if URL was already analyzed
        $existing = SocialAnalysis::where('url', $url)->first();
        if ($existing) {
            return redirect()->route('social.show', $existing->id)->with('info', 'Link ini telah dianalisis sebelumnya.');
        }

        // Parse Social Media URL content & comments
        $parsed = $this->urlParser->parseUrl($url);

        // Detect Hoax / Fact Verdict
        $verdictData = $this->hoaxDetector->detectHoax($parsed['post_title'], $parsed['post_content'], $url);

        $posCount = 0;
        $negCount = 0;
        $neuCount = 0;

        $categories = Category::all()->keyBy('slug');
        $catId = $categories->get('ekonomi')->id ?? null;

        $analysis = SocialAnalysis::create([
            'url' => $url,
            'platform' => $parsed['platform'],
            'author_name' => $parsed['author_name'],
            'post_title' => $parsed['post_title'],
            'post_content' => $parsed['post_content'],
            'verdict' => $verdictData['verdict'],
            'verdict_score' => $verdictData['verdict_score'],
            'verdict_reasoning' => $verdictData['verdict_reasoning'],
            'category_id' => $catId,
        ]);

        // Process comments & sentiment
        foreach ($parsed['raw_comments'] as $c) {
            $sent = $this->sentimentEngine->analyzeSentiment($c['comment']);

            SocialComment::create([
                'social_analysis_id' => $analysis->id,
                'author_name' => $c['author'],
                'raw_comment' => $c['comment'],
                'sentiment' => $sent['sentiment'],
                'sentiment_score' => $sent['sentiment_score'],
            ]);

            if ($sent['sentiment'] === 'positif') $posCount++;
            elseif ($sent['sentiment'] === 'negatif') $negCount++;
            else $neuCount++;
        }

        $analysis->update([
            'positive_count' => $posCount,
            'negative_count' => $negCount,
            'neutral_count' => $neuCount,
        ]);

        return redirect()->route('social.show', $analysis->id)->with('success', 'Analisis keaslian berita & sentimen komentar berhasil diproses!');
    }

    /**
     * Show: Detailed Analysis Result Page (Verdict, Fact-Check Reasoning, & Comments Sentiment Filter)
     */
    public function show(Request $request, $id)
    {
        $analysis = SocialAnalysis::with('category')->findOrFail($id);

        $query = SocialComment::where('social_analysis_id', $analysis->id);

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        $comments = $query->paginate(15)->withQueryString();

        return view('social.show', compact('analysis', 'comments'));
    }
}
