<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawledComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'platform',
        'source_account',
        'author_name',
        'raw_text',
        'cleaned_text',
        'tokens',
        'stemmed_tokens',
        'category_id',
        'lda_topic_id',
        'scraped_at',
    ];

    protected $casts = [
        'tokens' => 'array',
        'stemmed_tokens' => 'array',
        'scraped_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ldaTopic()
    {
        return $this->belongsTo(LdaTopic::class);
    }
}
