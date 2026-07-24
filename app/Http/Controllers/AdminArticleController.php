<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\SocialComment;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    protected SentimentAnalysisService $sentimentEngine;

    public function __construct(SentimentAnalysisService $sentimentEngine)
    {
        $this->sentimentEngine = $sentimentEngine;
    }

    public function dashboard()
    {
        $totalArticles = Article::count();
        $totalViews = Article::sum('views_count');
        $featuredCount = Article::where('is_featured', true)->count();
        $categoriesCount = Category::count();

        $recentArticles = Article::with('category')->latest()->take(5)->get();
        $popularArticles = Article::with('category')->orderBy('views_count', 'desc')->take(5)->get();
        $categoriesStats = Category::withCount('articles')->get();

        return view('admin.dashboard', compact(
            'totalArticles',
            'totalViews',
            'featuredCount',
            'categoriesCount',
            'recentArticles',
            'popularArticles',
            'categoriesStats'
        ));
    }

    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles = $query->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];
        return view('admin.articles.create', compact('categories', 'districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'source' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'district' => 'nullable|string',
            'verdict' => 'nullable|in:asli,hoaks',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'middle_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'end_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'comment_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'comment_images' => 'nullable|array|max:10',
            'comment_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'comment_images.max' => 'Maksimal screenshot komentar yang dapat diunggah adalah 10 foto.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        $middleImagePath = null;
        if ($request->hasFile('middle_image')) {
            $middleImagePath = $request->file('middle_image')->store('articles', 'public');
        }

        $endImagePath = null;
        if ($request->hasFile('end_image')) {
            $endImagePath = $request->file('end_image')->store('articles', 'public');
        }

        $commentImagePath = null;
        if ($request->hasFile('comment_image')) {
            $commentImagePath = $request->file('comment_image')->store('comments', 'public');
        }

        // Handle multiple screenshot images (up to 10)
        $commentImagePaths = [];
        if ($request->hasFile('comment_images')) {
            foreach ($request->file('comment_images') as $file) {
                $commentImagePaths[] = $file->store('comments', 'public');
            }
        }

        // If single comment_image was uploaded, add to list
        if ($commentImagePath && empty($commentImagePaths)) {
            $commentImagePaths[] = $commentImagePath;
        }

        $slug = Str::slug($validated['title']) . '-' . Str::random(5);
        $verdict = $validated['verdict'] ?? 'asli';

        $article = Article::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'source' => $validated['source'] ?? 'Redaksi Metrologi Kota Metro',
            'source_url' => $validated['source_url'] ?? null,
            'district' => $request->input('district'),
            'verdict' => $verdict,
            'verdict_score' => 95.0,
            'verdict_reasoning' => $verdict === 'asli' 
                ? 'Berita ini diterbitkan oleh Redaksi Pemkot Metro dan terkonfirmasi validasi fakta.' 
                : 'Peringatan disinformasi terindikasi pada berita ini.',
            'is_featured' => $request->boolean('is_featured'),
            'image_path' => $imagePath,
            'middle_image_path' => $middleImagePath,
            'end_image_path' => $endImagePath,
            'comment_image_path' => $commentImagePath,
            'comment_images' => $commentImagePaths,
            'published_at' => now(),
        ]);

        // Process multiline social media comments input by admin (if provided)
        if ($request->filled('comments_text')) {
            $lines = explode("\n", $request->comments_text);
            $posCount = 0;
            $negCount = 0;
            $neuCount = 0;

            foreach ($lines as $index => $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) continue;

                $sent = $this->sentimentEngine->analyzeSentiment($trimmed);

                SocialComment::create([
                    'article_id' => $article->id,
                    'author_name' => '@netizen_metro' . ($index + 1),
                    'raw_comment' => $trimmed,
                    'sentiment' => $sent['sentiment'],
                    'sentiment_score' => $sent['sentiment_score'],
                ]);

                if ($sent['sentiment'] === 'positif') $posCount++;
                elseif ($sent['sentiment'] === 'negatif') $negCount++;
                else $neuCount++;
            }

            $article->update([
                'positive_count' => $posCount,
                'negative_count' => $negCount,
                'neutral_count' => $neuCount,
            ]);
        }

        return redirect()->route('admin.articles.index')->with('success', 'Artikel Berita berhasil diterbitkan!');
    }

    public function edit($id)
    {
        $article = Article::with('comments')->findOrFail($id);
        $categories = Category::all();
        $districts = ['Metro Pusat', 'Metro Timur', 'Metro Barat', 'Metro Utara', 'Metro Selatan'];
        return view('admin.articles.edit', compact('article', 'categories', 'districts'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'source' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'district' => 'nullable|string',
            'verdict' => 'nullable|in:asli,hoaks',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'middle_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'end_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'comment_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'comment_images' => 'nullable|array|max:10',
            'comment_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_comment_images' => 'nullable|array',
            'delete_comment_images.*' => 'string',
            'delete_main_image' => 'nullable|boolean',
            'delete_middle_image' => 'nullable|boolean',
            'delete_end_image' => 'nullable|boolean',
        ], [
            'comment_images.max' => 'Maksimal screenshot komentar yang dapat diunggah adalah 10 foto.',
        ]);

        // Handle Main Image (Delete or Replace)
        $imagePath = $article->image_path;
        if ($request->boolean('delete_main_image') && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }
        if ($request->hasFile('image')) {
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        // Handle Middle Image (Delete or Replace)
        $middleImagePath = $article->middle_image_path;
        if ($request->boolean('delete_middle_image') && $middleImagePath) {
            Storage::disk('public')->delete($middleImagePath);
            $middleImagePath = null;
        }
        if ($request->hasFile('middle_image')) {
            if ($article->middle_image_path) {
                Storage::disk('public')->delete($article->middle_image_path);
            }
            $middleImagePath = $request->file('middle_image')->store('articles', 'public');
        }

        // Handle End Image (Delete or Replace)
        $endImagePath = $article->end_image_path;
        if ($request->boolean('delete_end_image') && $endImagePath) {
            Storage::disk('public')->delete($endImagePath);
            $endImagePath = null;
        }
        if ($request->hasFile('end_image')) {
            if ($article->end_image_path) {
                Storage::disk('public')->delete($article->end_image_path);
            }
            $endImagePath = $request->file('end_image')->store('articles', 'public');
        }

        $commentImagePath = $article->comment_image_path;
        if ($request->hasFile('comment_image')) {
            $commentImagePath = $request->file('comment_image')->store('comments', 'public');
        }

        // Handle Comment Screenshots Deletion & Addition
        $commentImagePaths = $article->comment_images ?? [];
        if ($request->has('delete_comment_images')) {
            $toDelete = $request->input('delete_comment_images', []);
            foreach ($toDelete as $deletePath) {
                Storage::disk('public')->delete($deletePath);
            }
            $commentImagePaths = array_values(array_diff($commentImagePaths, $toDelete));
        }

        if ($request->hasFile('comment_images')) {
            $newPaths = [];
            foreach ($request->file('comment_images') as $file) {
                $newPaths[] = $file->store('comments', 'public');
            }
            $commentImagePaths = array_merge($commentImagePaths, $newPaths);
            $commentImagePaths = array_slice($commentImagePaths, 0, 10); // Keep max 10
        }

        $article->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'source' => $validated['source'] ?? 'Redaksi Metrologi Kota Metro',
            'source_url' => $validated['source_url'] ?? $article->source_url,
            'district' => $request->input('district'),
            'verdict' => $validated['verdict'] ?? $article->verdict,
            'is_featured' => $request->boolean('is_featured'),
            'image_path' => $imagePath,
            'middle_image_path' => $middleImagePath,
            'end_image_path' => $endImagePath,
            'comment_image_path' => $commentImagePath,
            'comment_images' => $commentImagePaths,
        ]);

        // Process new multiline social media comments input by admin (if provided)
        if ($request->filled('comments_text')) {
            $lines = explode("\n", $request->comments_text);

            foreach ($lines as $index => $line) {
                $trimmed = trim($line);
                if (empty($trimmed)) continue;

                $sent = $this->sentimentEngine->analyzeSentiment($trimmed);

                SocialComment::create([
                    'article_id' => $article->id,
                    'author_name' => '@netizen_metro' . rand(10, 99),
                    'raw_comment' => $trimmed,
                    'sentiment' => $sent['sentiment'],
                    'sentiment_score' => $sent['sentiment_score'],
                ]);
            }

            // Recalculate article comment counters
            $posCount = SocialComment::where('article_id', $article->id)->where('sentiment', 'positif')->count();
            $negCount = SocialComment::where('article_id', $article->id)->where('sentiment', 'negatif')->count();
            $neuCount = SocialComment::where('article_id', $article->id)->where('sentiment', 'netral')->count();

            $article->update([
                'positive_count' => $posCount,
                'negative_count' => $negCount,
                'neutral_count' => $neuCount,
            ]);
        }

        return redirect()->route('admin.articles.index')->with('success', 'Artikel Berita berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
