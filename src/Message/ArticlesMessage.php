<?php

namespace App\Message;

use App\Dto\Articles;

final class ArticlesMessage
{
    private $articles;

    public function __construct(Articles $articles)
    {
        $this->articles = $articles;
    }

    public function getArticles(): Articles
    {
        return $this->articles;
    }
}
