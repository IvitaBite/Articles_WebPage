<?php

use App\Controllers\ArticleController;

return [
    ['GET', '/articles', [ArticleController::class, 'index']],
    ['GET', '/articles/create', [ArticleController::class, 'create']],
    ['POST', '/articles', [ArticleController::class, 'store']],
    ['GET', '/articles/{id}', [ArticleController::class, 'show']],
    ['GET', '/articles/{id}/edit', [ArticleController::class, 'edit']],
    ['POST', '/articles/{id}', [ArticleController::class, 'update']],
    ['POST', '/articles/{id}/delete', [ArticleController::class, 'delete']]
];