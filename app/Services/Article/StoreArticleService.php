<?php


declare(strict_types=1);

namespace App\Services\Article;

use App\Repositories\MysqlArticleRepository;
use App\Repositories\ArticleRepository;
use App\Models\Article;

class StoreArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new MysqlArticleRepository();
    }
    //DTO - data transfer object => CreateArticleRequest -? $_POST,/ $_GET => ..execute(CreateArticleRequest $request)
    public function execute(string $title, string $description, string $picture): void
    {
        $article = new Article(
            $title,
            $description,
            $picture
        );

        $this->articleRepository->save($article);
    }
}