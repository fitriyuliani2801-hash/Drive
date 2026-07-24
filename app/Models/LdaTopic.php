<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LdaTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_number',
        'category_id',
        'label',
        'keywords',
        'coherence_score',
    ];

    protected $casts = [
        'keywords' => 'array',
        'coherence_score' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(CrawledComment::class);
    }
}
