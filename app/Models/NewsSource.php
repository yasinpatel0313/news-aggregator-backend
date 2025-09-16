<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSource extends Model
{
  protected $table = 'news_sources';

  protected $fillable = [
       'name',
       'slug',
       'is_active'
   ];

   protected $casts = [
    'is_active' => 'boolean',
   ];

   public function articles()
   {
      return $this->hasMany(NewsArtical::class);
   }
}
