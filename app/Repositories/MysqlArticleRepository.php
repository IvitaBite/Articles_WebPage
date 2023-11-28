<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Collections\ArticleCollection;
use App\Models\Article;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class MysqlArticleRepository implements ArticleRepository
{
    protected Connection $database;

    public function __construct()
    {
        $connectionParams = [
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_SECRET'],
            'host' => $_ENV['DB_HOST'],
            'driver' => $_ENV['DB_DRIVER'],
        ];
        $this->database = DriverManager::getConnection($connectionParams);
    }

    public function getAllArticles(): ArticleCollection
    {
        $articles = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->orderBy('id', 'desc')
            ->fetchAllAssociative();

        $articlesCollection = new ArticleCollection();

        foreach ($articles as $article) {
            $articlesCollection->add(
                $this->buildArticleModel($article)
            );
        }

        return $articlesCollection;
    }

    public function getById(string $id): ?Article
    {
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (empty($articleData)) {
            throw new \Exception('Article not found.');
        }

        return $this->buildArticleModel($articleData);
    }

    public function save(Article $article): void
    {
        if ($article->getId()) {
            $this->update($article);
            return;
        }
        $this->insert($article);
    }

    public function delete(Article $article): void
    {
        $this->database->createQueryBuilder()
            ->delete('articles')
            ->where('id = :id')
            ->setParameter('id', $article->getId())
            ->executeQuery(); //try catch???
    }

    private function insert(Article $article): void
    {
        $this->database->createQueryBuilder()
            ->insert('articles')
            ->values(
                [
                    'title' => ':title',
                    'description' => ':description',
                    'picture' => ':picture',
                    'created_at' => ':created_at'
                ]
            )->setParameters([
                'title' => $article->getTitle(),
                'description' => $article->getDescription(),
                'picture' => $article->getPicture(),
                'created_at' => $article->getCreatedAt()
            ])->executeQuery();
    }

    private function update(Article $article): void
    {
        $this->database->createQueryBuilder()
            ->update('articles')
            ->set('title', ':title')
            ->set('description', ':description')
            ->set('picture', ':picture')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameters([
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'description' => $article->getDescription(),
                'picture' => $article->getPicture(),
                'updated_at' => $article->getUpdatedAt()
            ])->executeQuery();
    }

    private function buildArticleModel(array $articleData): Article
    {
        return new Article(
            $articleData['title'],
            $articleData['description'],
            $articleData['picture'],
            $articleData['created_at'],
            (int)$articleData['id'],
            $articleData['updated_at'],
        );
    }
}