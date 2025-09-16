<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticlesController extends Controller
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Display the articles frontend page
     */
    public function index()
    {
        return view('articles.index');
    }

    /**
     * Get sources for frontend dropdown
     */
    public function getSources()
    {
        try {
            $sources = \App\Models\NewsSource::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $sources
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sources'
            ], 500);
        }
    }
}
