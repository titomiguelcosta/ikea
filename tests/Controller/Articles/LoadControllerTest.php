<?php

namespace App\Tests\Controller\Articles;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\TestsTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class LoadControllerTest extends ApiTestCase
{
  use ReloadDatabaseTrait;
  use TestsTrait;

  public function testEmptyBody(): void
  {
    static::createClient()->request('POST', '/v1/articles/load', ['headers' => $this->getHeaders()]);

    $this->assertResponseStatusCodeSame(400);
  }

  public function testStoringNewArticle(): void
  {
    $body = <<<JSON
{
  "inventory": [
    {
      "art_id": "6",
      "name": "glass",
      "stock": "44"
    }
  ]
}
JSON;

    $options = [
      'body' => $body,
      'headers' => $this->getHeaders()
    ];

    $client = static::createClient();

    $response = $client->request('POST', '/v1/articles/load', $options);
    $this->assertResponseStatusCodeSame(202);
    $this->assertEmpty($response->getContent());

    $response = $client->request('GET', '/v1/articles', ['headers' => $this->getHeaders()]);
    $this->assertCount(5, $response->toArray()['hydra:member']);
  }

  public function testInvalidArticle(): void
  {
    $body = <<<JSON
{
  "inventory": [
    {
      "art_id": "",
      "name": "",
      "stock": "-23"
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

    $response = $client->request('POST', '/v1/articles/load', $options);
    $this->assertResponseStatusCodeSame(400);

    $response = $client->request('GET', '/v1/articles', ['headers' => $this->getHeaders()]);
    $this->assertCount(4, $response->toArray()['hydra:member']);
  }
}
