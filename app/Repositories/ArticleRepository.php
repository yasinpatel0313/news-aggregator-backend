<?php

namespace App\Repositories;

use App\Models\NewsArticle;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ArticleRepository implements ArticleRepositoryInterface
{
    protected $model;

    public function __construct(NewsArticle $model)
    {
        $this->model = $model;
    }

    public function getAllArticles(array $params = [])
    {
        $query = $this->model->newQuery()->with(['source']);

        // Apply search
        $query = $this->applySearch($query, $params);

        // Apply filters
        $query = $this->applyFilters($query, $params);

        // Apply sorting
        $query = $this->applySorting($query, $params);

        // Get per_page or default to 15
        $perPage = $params['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    public function findArticleById(int $id)
    {
        return $this->model->with(['source'])->find($id);
    }

    protected function applySearch(Builder $query, array $params): Builder
    {
        // Search in title, description, and author
        if (isset($params['search']) && !empty($params['search'])) {
            $searchTerm = $params['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('author', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query;
    }

    protected function applyFilters(Builder $query, array $params): Builder
    {
        // Filter by source (single or multiple)
        if (isset($params['source']) && !empty($params['source'])) {
            if (is_array($params['source'])) {
                $query->whereIn('source_id', $params['source']);
            } else {
                $query->where('source_id', $params['source']);
            }
        }

        // Filter by author
        if (isset($params['author']) && !empty($params['author'])) {
            $query->where('author', 'LIKE', "%{$params['author']}%");
        }

        // Filter by date range
        if (isset($params['date_from']) && !empty($params['date_from'])) {
            $query->whereDate('published_at', '>=', $params['date_from']);
        }

        if (isset($params['date_to']) && !empty($params['date_to'])) {
            $query->whereDate('published_at', '<=', $params['date_to']);
        }

        // Filter by specific date
        if (isset($params['date']) && !empty($params['date'])) {
            $query->whereDate('published_at', $params['date']);
        }

        // Filter by keyword (alternative to search)
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            });
        }

        return $query;
    }

    protected function applySorting(Builder $query, array $params): Builder
    {
        $sortBy = $params['sort_by'] ?? 'published_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        // Allowed sort fields
        $allowedSortFields = ['published_at', 'title', 'created_at', 'author'];
        $allowedSortOrders = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortFields) && in_array($sortOrder, $allowedSortOrders)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Default sorting
            $query->orderBy('published_at', 'desc');
        }

        return $query;
    }
}
