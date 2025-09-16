<?php

namespace App\Http\Controllers\CronJobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\GuardianService;
use App\Models\NewsArticle;
use App\Models\NewsSource;

class GuardianController extends Controller
{
  private $guardianService;

  public function __construct(GuardianService $guardianService)
  {
      $this->guardianService = $guardianService;
  }

  public function fetchArticles()
  {
      // Get or create NewsAPI source
      $source = NewsSource::firstOrCreate(
          ['slug' => 'the-guardian'],
          [
              'name' => 'The Guardian',
              'is_active' => true
          ]
      );

      // get last date of article
      $lastPublishedAt = NewsArticle::where('news_source_id',$source->id)->max('published_at');

      $fromDate = null;
      if ($lastPublishedAt) {
           $fromDate = \Carbon\Carbon::parse($lastPublishedAt)->toIso8601String();
      }

      // fetch data
      $articles = $this->guardianService->fetchArticles($fromDate);
      $saved = 0;

      // remove article which are already inserted
      $urls = array_column($articles, 'url');
      $existingUrls = NewsArticle::whereIn('url', $urls)->pluck('url')->toArray();

      $newArticles = array_filter($articles, function ($a) use ($existingUrls) {
          return !in_array($a['url'], $existingUrls);
      });

      foreach ($newArticles as $articleData) {

          NewsArticle::create([
              'title' => $articleData['title'],
              'description' => $articleData['description'],
              'content' => $articleData['content'],
              'url' => $articleData['url'],
              'image_url' => $articleData['image_url'],
              'published_at' => $articleData['published_at'],
              'author' => $articleData['author'],
              'news_source_id' => $source->id
          ]);

          $saved++;
      }

      return response()->json([
          'message' => "Fetched and saved {$saved} articles from Guardian",
          'total_fetched' => count($articles),
          'saved' => $saved
      ]);
  }
}
