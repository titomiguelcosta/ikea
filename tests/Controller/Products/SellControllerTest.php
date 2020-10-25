<?php

namespace App\Tests\Controller\Products;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\TestsTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class SellControllerTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use TestsTrait;

    public function testNonFoundProduct(): void
    {
        static::createClient()->request('POST', '/v1/products/99/sell', ['headers' => $this->getHeaders()]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSellProduct(): void
    {
        $client = static::createClient();

        // check first product stock
        $response = $client->request('GET', '/v1/products', ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $id = $data['hydra:member'][0]['id'];
        $stock = $data['hydra:member'][0]['stock'];
        $this->assertSame(2, $stock);

        // sell it
        $response = $client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['stock' => 1]);

        // sell it again
        $response = $client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['stock' => 0]);

        // out of stock
        $response = $client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['hydra:description' => 'Product Dining Chair is out of stock.']);
    }
}
