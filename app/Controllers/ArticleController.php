<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ArticleRepository;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Response\ViewResponse;
use App\Services\Article\DeleteArticleService;
use App\Services\Article\IndexArticleService;
use App\Services\Article\ShowArticleService;
use App\Services\Article\StoreArticleService;
use App\Services\Article\UpdateArticleService;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class ArticleController
{
    private ArticleRepository $articleRepository;
    private IndexArticleService $indexArticleService;
    private ShowArticleService $showArticleService;
    private StoreArticleService $storeArticleService;
    private UpdateArticleService $updateArticleService;
    private DeleteArticleService $deleteArticleService;

    public function __construct(
        ArticleRepository    $articleRepository,
        IndexArticleService  $indexArticleService,
        ShowArticleService   $showArticleService,
        StoreArticleService  $storeArticleService,
        UpdateArticleService $updateArticleService,
        DeleteArticleService $deleteArticleService
    )
    {
        $this->articleRepository = $articleRepository;
        $this->indexArticleService = $indexArticleService;
        $this->showArticleService = $showArticleService;
        $this->storeArticleService = $storeArticleService;
        $this->updateArticleService = $updateArticleService;
        $this->deleteArticleService = $deleteArticleService;
    }

    public function index(): Response
    {
        $articles = $this->indexArticleService->execute();

        return new ViewResponse('Articles/index', [
            'articles' => $articles
        ]);
    }

    public function show(array $vars): Response
    {
        $id = $vars['id'] ?? null;

        try {
            $article = $this->showArticleService->execute($id);

            return new ViewResponse('Articles/show', [
                'article' => $article
            ]);

        } catch (\Exception $e) {
            $_SESSION['flush']['error'][] = $e->getMessage();
            return new RedirectResponse('/articles');
        }
    }

    public function create(): Response
    {
        return new ViewResponse('Articles/create', []);
    }

    public function store(): Response
    {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $picture = $_POST['picture'];

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

        $this->storeArticleService->execute($title, $description, $picture);

        $_SESSION['flush']['success'][] = 'Article created successfully!';

        return new RedirectResponse('/articles');
    }

    public function edit(array $vars): Response
    {
        $id = $vars['id'] ?? null;

        try {
            $article = $this->showArticleService->execute($id);

            $_SESSION['flush']['success'][] = 'Article edited successfully!';

            return new ViewResponse('Articles/edit', [
                'article' => $article
            ]);

        } catch (\Exception $e) {
            $_SESSION['flush']['error'][] = $e->getMessage();
            return new RedirectResponse('/articles');
        }
    }

    public function update(array $vars): Response
    {
        $id = $vars['id'] ?? null;

        $article = $this->articleRepository->getById($id);

        if (!$article) {
            $_SESSION['flush']['error'][] = 'Article not found.';
            return new RedirectResponse('/articles');
        }

        $title = $_POST['title'];
        $description = $_POST['description'];
        $picture = $_POST['picture'];

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

        $this->updateArticleService->execute($id, $title, $description, $picture);

        $_SESSION['flush']['success'][] = 'Article updated successfully!';

        return new RedirectResponse('/articles/' . $id);
    }

    public function delete(array $vars): Response
    {
        $id = $vars['id'] ?? null;

        try {
            $this->deleteArticleService->execute($id);
            $_SESSION['flush']['success'][] = 'Article deleted successfully!';

            return new RedirectResponse('/articles');

        } catch (\Exception $e) {
            $_SESSION['flush']['error'][] = $e->getMessage();
            return new RedirectResponse('/articles');
        }
    }

    private function handleValidationException(NestedValidationException $exception): void
    {
        $messages = $exception->getMessages();
        foreach ($messages as $validator => $message) {
            $_SESSION['flush']['error'][] = $message;
        }
    }
}