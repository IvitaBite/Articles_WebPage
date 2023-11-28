<?php

declare(strict_types=1);

use App\Repositories\ArticleRepository;
use App\Repositories\MysqlArticleRepository;
use App\Services\Article\IndexArticleService;
use App\Services\Article\ShowArticleService;
use App\Services\Article\StoreArticleService;
use App\Services\Article\UpdateArticleService;
use App\Services\Article\DeleteArticleService;
use function DI\create;
use function DI\get;

return [
    ArticleRepository::class => create(MysqlArticleRepository::class),
    IndexArticleService::class => create()->constructor(get(ArticleRepository::class)),
    ShowArticleService::class => create()->constructor(get(ArticleRepository::class)),
    StoreArticleService::class => create()->constructor(get(ArticleRepository::class)),
    UpdateArticleService::class => create()->constructor(get(ArticleRepository::class)),
    DeleteArticleService::class => create()->constructor(get(ArticleRepository::class))
];