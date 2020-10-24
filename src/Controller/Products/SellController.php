<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Entity\Product;
use App\Entity\ProductArticle;
use Doctrine\ORM\EntityManagerInterface;

class SellController
{
    public function __invoke(Product $data, EntityManagerInterface $entityManager)
    {
        /** @var ProductArticle $productArticle */
        foreach ($data->getProductArticles() as $productArticle) {
            $article = $productArticle->getArticle();
            $article->setStock($article->getStock() - $productArticle->getAmount());
        }

        $entityManager->flush();

        return $data;
    }
}
