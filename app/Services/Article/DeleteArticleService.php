<?php


declare(strict_types=1);

namespace App\Services\Article;

use App\Repositories\MysqlArticleRepository;
use App\Repositories\ArticleRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DeleteArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new MysqlArticleRepository();
    }

    public function execute(string $id): void
    {
        $article = $this->articleRepository->getById($id);

        if ($article == null) { //$article->getTitle == "NEAIZTIKT vai satur {DRAFT} WE CANT DELETE IT
            // trow exception
            return;
        }

        $this->articleRepository->delete($article);
    }
}