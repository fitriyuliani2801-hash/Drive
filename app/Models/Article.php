<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'video_transcript',
        'video_description',
        'image_path',
        'middle_image_path',
        'end_image_path',
        'comment_image_path',
        'comment_images',
        'source',
        'source_url',
        'platform',
        'verdict',
        'verdict_score',
        'verdict_reasoning',
        'positive_count',
        'negative_count',
        'neutral_count',
        'latitude',
        'longitude',
        'district',
        'location_address',
        'views_count',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'verdict_score' => 'float',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'comment_images' => 'array',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SocialComment::class);
    }
}
