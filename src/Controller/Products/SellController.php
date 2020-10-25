<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Entity\Product;
use App\Manager\ProductManager;

class SellController
{
    public function __invoke(Product $data, ProductManager $productManager)
    {
        return $productManager->sell($data);
    }
}
