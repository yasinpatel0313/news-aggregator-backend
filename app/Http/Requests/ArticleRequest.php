<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Search parameters
            'search' => 'nullable|string|min:2|max:255',
            'keyword' => 'nullable|string|min:2|max:255',

            // Filter parameters
            'source' => 'nullable|array',
            'source.*' => 'integer|exists:sources,id',
            'author' => 'nullable|string|max:255',
            'date' => 'nullable|date|date_format:Y-m-d',
            'date_from' => 'nullable|date|date_format:Y-m-d',
            'date_to' => 'nullable|date|date_format:Y-m-d|after_or_equal:date_from',

            // Sort parameters
            'sort_by' => 'nullable|in:published_at,title,created_at,author',
            'sort_order' => 'nullable|in:asc,desc',

            // Pagination
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'search.min' => 'Search query must be at least 2 characters long.',
            'keyword.min' => 'Keyword must be at least 2 characters long.',
            'source.*.exists' => 'One or more selected sources do not exist.',
            'date.date_format' => 'Date must be in Y-m-d format.',
            'date_from.date_format' => 'Date from must be in Y-m-d format.',
            'date_to.date_format' => 'Date to must be in Y-m-d format.',
            'date_to.after_or_equal' => 'Date to must be after or equal to date from.',
            'sort_by.in' => 'Sort by must be one of: published_at, title, created_at, author.',
            'sort_order.in' => 'Sort order must be either asc or desc.',
            'per_page.max' => 'Per page cannot exceed 100 items.',
            'per_page.min' => 'Per page must be at least 1.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    public function getFilters(): array
    {
        return $this->only([
            'search', 'keyword', 'source', 'author',
            'date', 'date_from', 'date_to',
            'sort_by', 'sort_order', 'per_page', 'page'
        ]);
    }
}
