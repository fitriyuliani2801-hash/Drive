<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'social_analysis_id',
        'article_id',
        'platform',
        'author_name',
        'author_avatar',
        'raw_comment',
        'sentiment',
        'sentiment_score',
        'status',
        'posted_at',
    ];

    protected $casts = [
        'sentiment_score' => 'float',
        'posted_at' => 'datetime',
    ];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function socialAnalysis()
    {
        return $this->belongsTo(SocialAnalysis::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
