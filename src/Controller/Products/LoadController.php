<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Dto\Products;
use App\Message\ProductsMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class LoadController
{
    public function __invoke(Products $products, MessageBusInterface $bus)
    {
        $bus->dispatch(new ProductsMessage($products));

        return new Response('', Response::HTTP_ACCEPTED);
    }
}
