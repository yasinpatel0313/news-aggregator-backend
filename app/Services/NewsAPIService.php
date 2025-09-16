<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class NewsAPIService
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
      $this->apiKey = Config::get("news.providers.newsapi.api_key");
      $this->baseUrl = Config::get("news.providers.newsapi.base_url");
    }

    public function fetchArticles($from = null)
    {
        $allArticles = [];
        $page = 1;
        $maxPages = 2; // NewsAPI free tier allows limited requests per day

        do {
            $query = [
                'apiKey'   => $this->apiKey,
                'language' => 'en',
                'sortBy'   => 'publishedAt',
                'pageSize' => 100, // Maximum allowed by NewsAPI
                'page'     => $page,
                'q'        => 'technology OR business OR health',
            ];

            if ($from) {
                $query['from'] = $from;
            }

            try {
                $response = $this->client->get($this->baseUrl . '/everything', [
                    'query' => $query
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                if (!isset($data['articles'])) {
                    Log::warning("NewsAPI: No articles found in response for page {$page}");
                    break;
                }

                $articles = $this->transformArticles($data['articles']);
                $allArticles = array_merge($allArticles, $articles);

                $totalResults = $data['totalResults'] ?? 0;
                $currentArticleCount = ($page - 1) * 100 + count($articles);

                Log::info("NewsAPI: Fetched page {$page}, got " . count($articles) . " articles, total available: {$totalResults}");

                // Break conditions:
                // 1. No more articles on this page
                // 2. Reached maximum pages
                // 3. Fetched all available articles
                // 4. NewsAPI free tier limit (typically 1000 requests per day, so be conservative)
                if (count($articles) === 0 ||
                    $page >= $maxPages ||
                    $currentArticleCount >= $totalResults ||
                    count($articles) < 100) { // If we got less than 100, probably the last page
                    break;
                }

                $page++;

                // Small delay to respect rate limits
                // NewsAPI free tier has limits, so be conservative
                sleep(1);

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorBody = $response->getBody()->getContents();

                if ($statusCode === 426) {
                    Log::error("NewsAPI: Rate limit exceeded or upgrade required");
                } elseif ($statusCode === 400) {
                    Log::error("NewsAPI: Bad request - " . $errorBody);
                } else {
                    Log::error("NewsAPI fetch error on page {$page}: HTTP {$statusCode} - " . $errorBody);
                }
                break;

            } catch (\Exception $e) {
                Log::error("NewsAPI fetch error on page {$page}: " . $e->getMessage());
                break;
            }

        } while (true);

        Log::info("NewsAPI: Total articles fetched: " . count($allArticles));
        return $allArticles;
    }

    private function transformArticles($articles)
    {
        $transformed = [];
        foreach ($articles as $article) {
            if (empty($article['url']) || empty($article['title'])) {
                continue;
            }

            $transformed[] = [
                'title' => $article['title'],
                'description' => $article['description'],
                'content' => $article['content'],
                'url' => $article['url'],
                'image_url' => $article['urlToImage'],
                'published_at' => $article['publishedAt'],
                'author' => $article['author'] ?? 'Unknown',
                'source_name' => $article['source']['name'] ?? 'NewsAPI'
            ];
        }
        return $transformed;
    }
}
