<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Collections\ArticleCollection;
use App\Repositories\ArticleRepository;

class IndexArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function execute(): ArticleCollection
    {
        return $this->articleRepository->getAllArticles();
    }
}