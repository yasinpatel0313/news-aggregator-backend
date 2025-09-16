<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $table = 'news_articles';

    protected $fillable = [
        'title',
        'description',
        'content',
        'url',
        'image_url',
        'published_at',
        'author',
        'news_source_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source()
    {
      return $this->belongsTo(NewsSource::class, 'news_source_id', 'id');
    }
}
