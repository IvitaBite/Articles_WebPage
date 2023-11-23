<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Collections\ArticleCollection;
use App\Models\Article;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Response\ViewResponse;
use Carbon\Carbon;

class ArticleController extends BaseController
{
    public function index(): Response
    {
        $articles = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->fetchAllAssociative();
        $articlesCollection = new ArticleCollection();
        foreach ($articles as $article) {
            $articlesCollection->add(new Article(
                $article['title'],
                $article['description'],
                $article['picture'],
                $article['created_at'],
                (int) $article['id'],
                $article['updated_at'],
            ));
        }
        return new ViewResponse('Articles/index', [
            'articles' => $articlesCollection->getAllArticles()
        ]);
    }
    public function show(string $id): Response
    {
        //if doesnot exit, redirect to /
        //if exist => display template
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();
        $article = new Article(
            $articleData['title'],
            $articleData['description'],
            $articleData['picture'],
            $articleData['created_at'],
            (int) $articleData['id'],
            $articleData['updated_at'],
        );
        return new ViewResponse('Articles/show', [
            'article' => $article
        ]);
    }
    public function create(): Response
    {
        return new ViewResponse('Articles/create', []);
    }
    public function store(): Response
    {
        //$_POST validate gan $_POST['title'] gan $_POST['description'] validejam
        //
        //lai nav arī empty space un pēc tam saglabā $this->articleRepository->save($article); un darbs ar datu bāzi

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
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'picture' => 'https://via.assets.so/img.jpg?w=400&h=150',
                'created_at' => Carbon::now()
            ])->executeQuery();
        return new RedirectResponse('/articles');
    }
    public function edit(string $id): Response // todo iespejams string
    {
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();
        $article = new Article(
            $articleData['title'],
            $articleData['description'],
            $articleData['picture'],
            $articleData['created_at'],
            (int) $articleData['id'],
            $articleData['updated_at'],
        );
        return new ViewResponse('Articles/edit', [
            'article' => $article
        ]);
    }
    public function update(string $id): Response
    {
        $this->database->createQueryBuilder()
            ->update('articles')
            ->set('title', ':title')
            ->set('description', ':description')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'title' => $_POST['title'],
                'description' => $_POST['description']
            ])
            ->executeQuery();
        return new RedirectResponse('/articles/' . $id);
    }
    public function delete(string $id): Response
    {
        $this->database->createQueryBuilder()
            ->delete('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
        return new RedirectResponse('/articles');
    }
}