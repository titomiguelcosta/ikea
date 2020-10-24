<?php

namespace App\MessageHandler;

use App\Dto\Component;
use App\Dto\Product as ProductDto;
use App\Entity\Article;
use App\Entity\Product as ProductEntity;
use App\Entity\ProductArticle;
use App\Message\ProductsMessage;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProductsMessageHandler implements MessageHandlerInterface
{
    private $productRepository;
    private $articleRepository;
    private $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->productRepository = $productRepository;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ProductsMessage $message)
    {
        /** @var ProductDto $producDto */
        foreach ($message->getProducts()->getProducts() as $productDto) {
            $product = $this->productRepository->findOneByName($productDto->getName());

            if (null === $product) {
                $product = new ProductEntity();
                $this->entityManager->persist($product);
            }

            $product->setName($productDto->getName());
            $product->setPrice($productDto->getPrice());

            $product->clearProductArticles();

            /** @var Component $component */
            foreach ($productDto->getComponents() as $component) {
                $article = $this->articleRepository->findOneByCode($component->getCode());

                if ($article instanceof Article) {
                    $productArticle = new ProductArticle();
                    $productArticle->setAmount($component->getAmount());
                    $productArticle->setProduct($product);
                    $productArticle->setArticle($article);

                    $this->entityManager->persist($productArticle);
                }
            }
        }

        $this->entityManager->flush();
    }
}
