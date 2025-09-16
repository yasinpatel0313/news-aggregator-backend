<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class NytService
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
      $this->apiKey = Config::get("news.providers.nytimes.api_key");
      $this->baseUrl = Config::get("news.providers.nytimes.base_url");
    }

    public function fetchArticles($from = null)
    {
        $allArticles = [];
        $page = 0; // NYT starts from page 0
        $maxPages = 2; // Limit to prevent hitting rate limits

        do {
            $query = [
                'api-key' => $this->apiKey,
                'sort' => 'newest',
                'page' => $page,
                'q' => 'technology OR business OR health'
            ];

            if ($from) {
                $query['begin_date'] = $from;
            }

            try {
                $response = $this->client->get($this->baseUrl, [
                    'query' => $query
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!isset($data['response']['docs'])) {
                    break;
                }

                $articles = $this->transformArticles($data['response']['docs']);
                $allArticles = array_merge($allArticles, $articles);

                // Check pagination info
                $meta = $data['response']['meta'] ?? [];
                $hits = $meta['hits'] ?? 0;
                $offset = $meta['offset'] ?? 0;

                Log::info("NYT API: Fetched page {$page}, got " . count($articles) . " articles, total hits: {$hits}");

                // Break if no more articles or reached limits
                if (count($articles) === 0 || $page >= $maxPages || ($offset + 10) >= $hits) {
                    break;
                }

                $page++;

                // Respect rate limits (NYT allows 5 requests per minute)
                sleep(12); // 12 seconds between requests = 5 per minute

            } catch (\Exception $e) {
                Log::error("NYT fetch error on page {$page}: " . $e->getMessage());
                break;
            }

        } while (true);

        Log::info("NYT API: Total articles fetched: " . count($allArticles));
        return $allArticles;
    }

    private function transformArticles($articles)
    {
        $transformed = [];
        foreach ($articles as $article) {
            if (empty($article['web_url']) || empty($article['headline']['main'])) {
                continue;
            }

            $imageUrl = '';
            if (isset($article['multimedia'][0])) {
                $imageUrl = 'https://static01.nyt.com/' . $article['multimedia'][0]['url'];
            }

            $transformed[] = [
                'title' => $article['headline']['main'],
                'description' => $article['abstract'],
                'content' => ($article['lead_paragraph'])??'',
                'url' => $article['web_url'],
                'image_url' => $imageUrl,
                'published_at' => $article['pub_date'],
                'author' => $article['byline']['original'] ?? 'New York Times',
                'source_name' => 'New York Times'
            ];
        }
        return $transformed;
    }
}
