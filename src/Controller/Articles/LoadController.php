<?php

declare(strict_types=1);

namespace App\Controller\Articles;

use App\Dto\Articles;
use App\Message\ArticlesMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoadController
{
    /**
     * @Route("/articles/load", name="articles_load", methods={"POST"})
     */
    public function __invoke(Articles $articles, MessageBusInterface $bus)
    {
        $bus->dispatch(new ArticlesMessage($articles));

        return new Response('', Response::HTTP_ACCEPTED);
    }
}
