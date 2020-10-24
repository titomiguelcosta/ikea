<?php

namespace App\Repository;

use App\Entity\ProductArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductArticle[]    findAll()
 * @method ProductArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductArticle::class);
    }
}
