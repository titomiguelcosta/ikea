<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Products implements DtoInterface
{
    /**
     * @var Product[]
     */
    private $products = [];

    /**
     * @return Product[]
     * @Assert\Valid()
     * @Assert\All({
     *  @Assert\Type(Product::class)
     * })
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param Product[]
     */
    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }
}
