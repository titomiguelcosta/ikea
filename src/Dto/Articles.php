<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Articles implements DtoInterface
{
    /**
     * @var Article[]
     */
    private $articles = [];

    /**
     * @return Article[]
     * @Assert\Valid()
     * @Assert\All({
     *  @Assert\Type(Article::class)
     * })
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * @param $articles Article[]
     */
    public function setArticles(array $articles): self
    {
        $this->articles = $articles;

        return $this;
    }
}
