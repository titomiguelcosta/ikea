<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Product;
use App\Entity\ProductArticle;
use App\Exception\OutOfStockException;
use Doctrine\ORM\EntityManagerInterface;

class ProductManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function sell(Product $product): Product
    {
        if ($product->getStock() <= 0) {
            throw new OutOfStockException(sprintf('Product %s is out of stock.', $product->getName()));
        }

        /** @var ProductArticle $productArticle */
        foreach ($product->getProductArticles() as $productArticle) {
            $article = $productArticle->getArticle();
            $article->setStock($article->getStock() - $productArticle->getAmount());
        }

        $this->entityManager->flush();

        return $product;
    }
}
