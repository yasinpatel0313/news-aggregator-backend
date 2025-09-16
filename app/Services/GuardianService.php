<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class GuardianService
{
    private $client;
    private $apiKey;
    private $baseUrl;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
      $this->client = new Client(['timeout' => 30]);
      $this->apiKey = Config::get("news.providers.guardian.api_key");
      $this->baseUrl = Config::get("news.providers.guardian.base_url");
    }

    public function fetchArticles($from = null)
    {
        $allArticles = [];
        $page = 1;
        $maxPages = 2; // for demo only, we can increate pages

        do {
            $query = [
                'api-key' => $this->apiKey,
                'show-fields' => 'all',
                'page-size' => 50,
                'order-by' => 'newest',
                'page' => $page
            ];

            if ($from) {
                $query['from-date'] = $from;
            }

            try {
                $response = $this->client->get($this->baseUrl . '/search', [
                    'query' => $query
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!isset($data['response']['results'])) {
                    break;
                }

                $articles = $this->transformArticles($data['response']['results']);
                $allArticles = array_merge($allArticles, $articles);

                // Check if we have more pages
                $currentPage = $data['response']['currentPage'] ?? $page;
                $totalPages = $data['response']['pages'] ?? 1;

                Log::info("Guardian API: Fetched page {$currentPage} of {$totalPages}, got " . count($articles) . " articles");

                // Break if no more pages or reached max pages
                if ($currentPage >= $totalPages || $page >= $maxPages || count($articles) === 0) {
                    break;
                }

                $page++;

                // Small delay to respect rate limits
                sleep(1);

            } catch (\Exception $e) {
                Log::error("Guardian fetch error on page {$page}: " . $e->getMessage());
                break;
            }

        } while (true);

        Log::info("Guardian API: Total articles fetched: " . count($allArticles));
        return $allArticles;
    }

    private function transformArticles($articles)
    {
       $transformed = [];
       foreach ($articles as $article) {
           if (empty($article['webUrl']) || empty($article['webTitle'])) {
               continue;
           }

           $fields = $article['fields'] ?? [];

           $transformed[] = [
               'title' => $article['webTitle'],
               'description' => $fields['trailText'] ?? '',
               'content' => $fields['bodyText'] ?? '',
               'url' => $article['webUrl'],
               'image_url' => $fields['thumbnail'] ?? '',
               'published_at' => $article['webPublicationDate'],
               'author' => $fields['byline'] ?? 'The Guardian',
               'source_name' => 'The Guardian'
           ];
       }
       return $transformed;
    }
}
