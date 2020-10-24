<?php

namespace App\Message;

use App\Dto\Products;

final class ProductsMessage
{
    private $products;

    public function __construct(Products $products)
    {
        $this->products = $products;
    }

    public function getProducts(): Products
    {
        return $this->products;
    }
}
