<?php

namespace App\Tests\Controller\Products;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\TestsTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class SellControllerTest extends ApiTestCase
{
    use ReloadDatabaseTrait;
    use TestsTrait;

    public function testNonFoundProduct(): void
    {
        $this->client->request('POST', '/v1/products/99/sell', ['headers' => $this->getHeaders()]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSellProduct(): void
    {
        // check first product stock
        $response = $this->client->request('GET', '/v1/products', ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $id = $data['hydra:member'][0]['id'];
        $stock = $data['hydra:member'][0]['stock'];
        $this->assertSame(2, $stock);

        // sell it
        $response = $this->client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['stock' => 1]);

        // sell it again
        $response = $this->client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['stock' => 0]);

        // out of stock
        $response = $this->client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['hydra:description' => 'Product Dining Chair is out of stock.']);
    }

    public function testImpactOnArticles(): void
    {
        // check first product stock
        $response = $this->client->request('GET', '/v1/products', ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $id = $data['hydra:member'][0]['id'];
        $productStock = $data['hydra:member'][0]['stock'];
        $articleStock = $data['hydra:member'][0]['productArticles'][0]['article']['stock'];
        $amount = $data['hydra:member'][0]['productArticles'][0]['amount'];
        $this->assertSame(2, $productStock);
        $this->assertSame(12, $articleStock);
        $this->assertSame(4, $amount);

        // sell it
        $response = $this->client->request('POST', sprintf('/v1/products/%d/sell', $id), ['headers' => $this->getHeaders()]);
        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertJsonContains(['stock' => 1]);
        $this->assertSame(1, $data['stock']);
        $this->assertSame(8, $data['productArticles'][0]['article']['stock']);
        $this->assertSame(4, $amount);
    }
}
