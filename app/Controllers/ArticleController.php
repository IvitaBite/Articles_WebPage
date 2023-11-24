<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Collections\ArticleCollection;
use App\Models\Article;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Response\ViewResponse;
use Carbon\Carbon;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class ArticleController extends DatabaseController
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
                (int)$article['id'],
                $article['updated_at'],
            ));
        }

        return new ViewResponse('Articles/index', [
            'articles' => $articlesCollection->getAllArticles()
        ]);
    }

    public function show(string $id): Response
    {
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$articleData) {
            $_SESSION['flush']['error'][] = 'Article not found.';
            return new RedirectResponse('/articles');
        }

        $article = new Article(
            $articleData['title'],
            $articleData['description'],
            $articleData['picture'],
            $articleData['created_at'],
            (int)$articleData['id'],
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
        $title = $_POST['title'];
        $description = $_POST['description'];
        $picture = !empty($_POST['picture']) ? $_POST['picture'] : 'https://random.imagecdn.app/500/150';

        $titleValidator = v::notEmpty()
            ->length(1, 255)
            ->not(v::space())
            ->setName('Title');
        try {
            $titleValidator->assert($title);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/create');
        }

        $descriptionValidator = v::notEmpty()
            ->not(v::space())
            ->setName('Description');
        try {
            $descriptionValidator->assert($description);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/create');
        }

        $pictureValidator = v::optional(
            v::stringType()
                ->notEmpty()
                ->length(1, 255)
                ->setName('Picture URL')
        );

        try {
            $pictureValidator->assert($picture);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/create');
        }

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
                'title' => $title,
                'description' => $description,
                'picture' => $picture,
                'created_at' => Carbon::now()
            ])->executeQuery();

        $_SESSION['flush']['success'][] = 'Article created successfully!';

        return new RedirectResponse('/articles');
    }

    public function edit(string $id): Response
    {
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$articleData) {
            $_SESSION['flush']['error'][] = 'Article not found.';
            return new RedirectResponse('/articles');
        }

        $article = new Article(
            $articleData['title'],
            $articleData['description'],
            $articleData['picture'],
            $articleData['created_at'],
            (int)$articleData['id'],
            $articleData['updated_at'],
        );

        $_SESSION['flush']['success'][] = 'Article edited successfully!';

        return new ViewResponse('Articles/edit', [
            'article' => $article
        ]);
    }

    public function update(string $id): Response
    {
        $articleData = $this->database->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$articleData) {
            $_SESSION['flush']['error'][] = 'Article not found.';
            return new RedirectResponse('/articles');

        }

        $title = $_POST['title'];
        $description = $_POST['description'];
        $picture = !empty($_POST['picture']) ? $_POST['picture'] : 'https://random.imagecdn.app/500/150';

        $titleValidator = v::notEmpty()
            ->length(1, 255)
            ->not(v::space())
            ->setName('Title');
        try {
            $titleValidator->assert($title);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/' . $id);
        }

        $descriptionValidator = v::notEmpty()
            ->not(v::space())
            ->setName('Description');;
        try {
            $descriptionValidator->assert($description);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/' . $id);
        }

        $pictureValidator = v::optional(
            v::stringType()
                ->notEmpty()
                ->length(1, 255)
                ->setName('Picture URL')
        );

        try {
            $pictureValidator->assert($picture);
        } catch (NestedValidationException $exception) {
            $this->handleValidationException($exception);
            return new RedirectResponse('/articles/create');
        }

        $this->database->createQueryBuilder()
            ->update('articles')
            ->set('title', ':title')
            ->set('description', ':description')
            ->set('picture', ':picture')
            ->set('updated_at', ':updated_at')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'picture' => $picture,
                'updated_at' => Carbon::now()
            ])->executeQuery();

        $_SESSION['flush']['success'][] = 'Article updated successfully!';
        var_dump($_SESSION['flush']);
        return new RedirectResponse('/articles/' . $id);
    }

    public function delete(string $id): Response
    {
        $this->database->createQueryBuilder()
            ->delete('articles')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $_SESSION['flush']['success'][] = 'Article deleted successfully!';

        return new RedirectResponse('/articles');
    }

    private function handleValidationException(NestedValidationException $exception): void
    {
        $messages = $exception->getMessages();
        var_dump($messages);
        foreach ($messages as $validator => $message) {
            $_SESSION['flush']['error'][] = $message;
        }
    }
}