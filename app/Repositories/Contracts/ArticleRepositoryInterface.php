<?php

namespace App\Repositories\Contracts;

interface ArticleRepositoryInterface
{
    public function getAllArticles(array $params = []);
    public function findArticleById(int $id);
}
