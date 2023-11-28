<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Models\Article;
use App\Repositories\MysqlArticleRepository;
use App\Repositories\ArticleRepository;

class ShowArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new MysqlArticleRepository();
    }

    public function execute(string $id): Article
    {
        $article = $this->articleRepository->getById($id);

        if (!$article) {
            throw new \Exception('Article not found.');
        }

        return $article;
    }
}