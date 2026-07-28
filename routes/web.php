<?php

use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public News Portal & Comments Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [ArticleController::class, 'home'])->name('home');
Route::get('/berita', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/berita/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/berita/{slug}/comment', [ArticleController::class, 'storeComment'])->name('articles.comment');

/*
|--------------------------------------------------------------------------
| LDA Topic Modeling Public & Admin Analysis Routes
|--------------------------------------------------------------------------
*/
Route::get('/analisis', [AnalysisController::class, 'index'])->name('analysis.index');
Route::get('/analisis/preprocessing', [AnalysisController::class, 'preprocessing'])->name('analysis.preprocessing');
Route::get('/analisis/vektorisasi', [AnalysisController::class, 'vectorization'])->name('analysis.vectorization');
Route::get('/analisis/komentar', [AnalysisController::class, 'comments'])->name('analysis.comments');
Route::post('/analisis/run', [AnalysisController::class, 'runAnalysis'])->name('analysis.run');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Editorial Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminArticleController::class, 'dashboard'])->name('dashboard');

    // Admin Articles Management
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::post('/articles/import-link', [AdminArticleController::class, 'importLink'])->name('articles.import-link');
    Route::get('/articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::post('/articles/{id}/update', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::post('/articles/{id}/delete', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
});
