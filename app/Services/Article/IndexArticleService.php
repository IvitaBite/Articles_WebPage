<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Collections\ArticleCollection;
use App\Repositories\MysqlArticleRepository;
use App\Repositories\ArticleRepository;

class IndexArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new MysqlArticleRepository();
    }

    public function execute(): ArticleCollection
    {
        return $this->articleRepository->getAllArticles();
    }
}