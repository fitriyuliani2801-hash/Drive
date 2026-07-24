<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'platform',
        'author_name',
        'post_title',
        'post_content',
        'media_image',
        'verdict',
        'verdict_score',
        'verdict_reasoning',
        'category_id',
        'positive_count',
        'negative_count',
        'neutral_count',
    ];

    protected $casts = [
        'verdict_score' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(SocialComment::class);
    }
}
