<?php

namespace App\Tests;

trait TestsTrait
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        static::ensureKernelShutdown();
        $this->client = static::createClient();
        self::bootKernel();
    }

    public function getHeaders(): array
    {
        return [
            'content-type' => 'application/ld+json'
        ];
    }
}
