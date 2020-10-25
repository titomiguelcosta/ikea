<?php

namespace App\Tests;

trait TestsTrait
{
    public function getHeaders(): array
    {
        return [
            'content-type' => 'application/ld+json'
        ];
    }
}
