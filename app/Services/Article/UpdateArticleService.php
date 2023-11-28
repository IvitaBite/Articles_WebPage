<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Repositories\ArticleRepository;

class UpdateArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function execute(
        string $id,
        string $title,
        string $description,
        string $picture
    ): void
    {
        $article = $this->articleRepository->getById($id);

        if (!$article) {
            throw new \Exception('Article not found.');
        }

        $article->update([
            'title' => $title,
            'description' => $description,
            'picture' => $picture
        ]);

        $this->articleRepository->save($article);
    }
}