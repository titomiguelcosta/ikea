<?php

namespace App\MessageHandler;

use App\Dto\Article as ArticleDto;
use App\Entity\Article as ArticleEntity;
use App\Message\ArticlesMessage;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ArticlesMessageHandler implements MessageHandlerInterface
{
    private $articleRepository;
    private $entityManager;

    public function __construct(
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ArticlesMessage $message)
    {
        /** @var ArticleDto $articleDto */
        foreach ($message->getArticles()->getArticles() as $articleDto) {
            $article = $this->articleRepository->findOneByCode($articleDto->getCode());

            if (null === $article) {
                $article = new ArticleEntity();
                $this->entityManager->persist($article);
            }

            $article->setName($articleDto->getName());
            $article->setCode($articleDto->getCode());
            $article->setStock($articleDto->getStock());
        }

        $this->entityManager->flush();
    }
}
