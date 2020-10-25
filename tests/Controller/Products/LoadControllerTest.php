<?php

namespace App\Tests\Controller\Products;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\TestsTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class LoadControllerTest extends ApiTestCase
{
  use ReloadDatabaseTrait;
  use TestsTrait;

  public function testEmptyBody(): void
  {
    static::createClient()->request('POST', '/v1/products/load', ['headers' => $this->getHeaders()]);

    $this->assertResponseStatusCodeSame(400);
  }

  public function testStoringProducts(): void
  {
    $body = <<<JSON
{
  "products": [
    {
      "name": "Coffee Table",
      "contain_articles": [
        {
          "art_id": "1",
          "amount_of": "4"
        },
        {
          "art_id": "3",
          "amount_of": "1"
        }
      ]
    }
  ]
}
JSON;

    $options = [
      'body' => $body,
      'headers' => [
        'content-type' => 'application/ld+json'
      ]
    ];

    $client = static::createClient();

    $response = $client->request('POST', '/v1/products/load', $options);
    $this->assertResponseStatusCodeSame(202);
    $this->assertEmpty($response->getContent());

    $response = $client->request('GET', '/v1/products', ['headers' => $this->getHeaders()]);
    $this->assertCount(3, $response->toArray()['hydra:member']);
  }

  public function testInvalidProducts(): void
  {
    $body = <<<JSON
{
  "products": [
    {
      "name": "Bedside Table",
      "contain_articles": [
        {
          "art_id": "",
          "amount_of": "0"
        },
        {
          "art_id": "3",
          "amount_of": "1"
        }
      ]
    }
  ]
}
JSON;

    $options = [
      'body' => $body,
      'headers' => [
        'content-type' => 'application/ld+json'
      ]
    ];

    $client = static::createClient();

    $response = $client->request('POST', '/v1/products/load', $options);
    $this->assertResponseStatusCodeSame(400);

    $response = $client->request('GET', '/v1/products', ['headers' => $this->getHeaders()]);
    $this->assertCount(2, $response->toArray()['hydra:member']);
  }
}
