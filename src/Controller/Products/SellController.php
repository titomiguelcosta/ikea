<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Entity\Product;
use App\Entity\ProductArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class SellController
{
    /**
     * @Route("/products/{id}/sell", name="products_sell", methods={"POST"})
     */
    public function __invoke(Product $product, EntityManagerInterface $entityManager)
    {
        /** @var ProductArticle $productArticle */
        foreach ($product->getProductArticles() as $productArticle) {
            $article = $productArticle->getArticle();
            $article->setStock($article->getStock() - $productArticle->getAmount());
        }

        $entityManager->flush();

        return $product;
    }
}
