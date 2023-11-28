<?php


declare(strict_types=1);

namespace App\Services\Article;

use App\Repositories\ArticleRepository;

class DeleteArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function execute(string $id): void
    {
        $article = $this->articleRepository->getById($id);

        if (!$article) {
            throw new \Exception('Article not found.');
        }

        $forbiddenKeywords = ['DRAFT', 'DO NOT TOUCH'];

        foreach ($forbiddenKeywords as $keyword) {
            if (stripos($article->getTitle(), $keyword) !== false) {
                throw new \Exception('Article cannot be deleted!');
            }
        }

        $this->articleRepository->delete($article);
    }
}