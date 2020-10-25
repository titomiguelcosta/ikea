<?php

declare(strict_types=1);

namespace App\Exception;

use ApiPlatform\Core\Exception\InvalidResourceException;

class OutOfStockException extends InvalidResourceException
{
}
