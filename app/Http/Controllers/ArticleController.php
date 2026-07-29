<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\SocialComment;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    protected SentimentAnalysisService $sentimentEngine;

    public function __construct(SentimentAnalysisService $sentimentEngine)
    {
        $this->sentimentEngine = $sentimentEngine;
    }

    public function home()
    {
        $totalArticles = Article::count();
        $totalViews = Article::sum('views_count');
        $categoriesCount = Category::count();

        $featuredArticles = Article::with('category')->where('is_featured', true)->latest()->take(4)->get();
        if ($featuredArticles->isEmpty()) {
            $featuredArticles = Article::with('category')->latest()->take(4)->get();
        }

        $latestArticles = Article::with('category')->latest()->take(6)->get();
        $categories = Category::withCount('articles')->get();

        return view('home', compact(
            'totalArticles',
            'totalViews',
            'categoriesCount',
            'featuredArticles',
            'latestArticles',
            'categories'
        ));
    }

    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])->latest();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(9)->withQueryString();
        $featuredArticles = Article::with('category')->where('is_featured', true)->latest()->take(2)->get();
        $categories = Category::withCount('articles')->get();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];

        return view('articles.index', compact('articles', 'featuredArticles', 'categories', 'districts'));
    }

    /**
     * Fase 3: Display Algorithm for Article Reading Page
     */
    public function show(Request $request, $slug)
    {
        $article = Article::with(['category', 'author'])->where('slug', $slug)->firstOrFail();
        $article->increment('views_count');

        // Auto-ingestion disabled as requested

        // Fase 3 Query: news_id = Current_News_ID AND status = 'approved' ORDER BY posted_at DESC LIMIT 10 (via paginate)
        $commentsQuery = SocialComment::where('article_id', $article->id)
            ->approved()
            ->orderByRaw('COALESCE(posted_at, created_at) DESC');

        if ($request->filled('sentiment')) {
            $commentsQuery->where('sentiment', $request->sentiment);
        }

        $comments = $commentsQuery->paginate(10)->withQueryString();

        $relatedArticles = Article::with('category')
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->latest()
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'comments', 'relatedArticles'));
    }

    /**
     * Store public visitor comment & run AI Sentiment Analysis
     */
    public function storeComment(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $request->validate([
            'author_name' => 'required|string|max:100',
            'raw_comment' => 'required|string|max:1000',
        ], [
            'author_name.required' => 'Masukkan nama atau nama akun Anda.',
            'raw_comment.required' => 'Tuliskan tanggapan / komentar Anda.',
        ]);

        $commentText = $request->raw_comment;
        $authorName = $request->author_name;

        if (!str_starts_with($authorName, '@') && !str_contains($authorName, ' ')) {
            $authorName = '@' . $authorName;
        }

        // Run AI Sentiment Analysis
        $sentResult = $this->sentimentEngine->analyzeSentiment($commentText);

        SocialComment::create([
            'article_id' => $article->id,
            'comment_id' => 'web_' . Str::random(10),
            'platform' => 'Metrologi',
            'author_name' => $authorName,
            'author_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($authorName) . '&background=0d9488&color=fff',
            'raw_comment' => $commentText,
            'sentiment' => $sentResult['sentiment'],
            'sentiment_score' => $sentResult['sentiment_score'],
            'status' => 'approved',
            'posted_at' => now(),
        ]);

        // Recalculate article comment counters
        $posCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'positif')->count();
        $negCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'negatif')->count();
        $neuCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'netral')->count();

        $article->update([
            'positive_count' => $posCount,
            'negative_count' => $negCount,
            'neutral_count' => $neuCount,
        ]);

        return redirect()->to(url()->previous() . '#comments-section')
            ->with('success', 'Komentar Anda telah terkirim dan dianalisis oleh sistem sentimen AI (Kategori: Sentimen ' . ucfirst($sentResult['sentiment']) . ')!');
    }
}
