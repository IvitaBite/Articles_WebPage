<?php

declare(strict_types=1);

namespace App\Controllers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
abstract class BaseController
{
    protected Connection $database;
    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'Articles_WebPage',
            'user' => 'root',
            'password' => 'xxxx', //todo: .env
            'host' => 'localhost',
            'driver' => 'pdo_mysql', //??
        ];
        try {
            $this->database = DriverManager::getConnection($connectionParams);
        } catch (\Doctrine\DBAL\Exception $e) {
            die('Database connection error: ' . $e->getMessage());
        }
    }
}
