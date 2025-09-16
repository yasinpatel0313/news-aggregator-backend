<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Get all articles with search, filter, and sort capabilities
     */
    public function index(ArticleRequest $request): JsonResponse
    {
        try {
            $params = $request->getFilters();
            $articles = $this->articleRepository->getAllArticles($params);

            $collection = new ArticleCollection($articles);
            $collection->additional(['filters' => array_filter($params)]);

            return response()->json($collection, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve articles',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get single article by ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $article = $this->articleRepository->findArticleById($id);

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Article retrieved successfully',
                'data' => new ArticleResource($article)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve article',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
