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
    $this->client->request('POST', '/v1/articles/load', ['headers' => $this->getHeaders()]);

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

    $response = $this->client->request('POST', '/v1/articles/load', $options);
    $this->assertResponseStatusCodeSame(202);
    $this->assertEmpty($response->getContent());

    $response = $this->client->request('GET', '/v1/articles', ['headers' => $this->getHeaders()]);
    // loading will append articles, and 4 of them already loaded as part of the fixtures
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

    $response = $this->client->request('POST', '/v1/articles/load', $options);
    $this->assertResponseStatusCodeSame(400);
    $this->assertJsonContains(['hydra:description' => 'articles[0].stock: This value should be greater than or equal to 1.
articles[0].code: This value is too short. It should have 1 character or more.
articles[0].name: This value is too short. It should have 1 character or more.']);

    $response = $this->client->request('GET', '/v1/articles', ['headers' => $this->getHeaders()]);
    // no article was added
    $this->assertCount(4, $response->toArray()['hydra:member']);
  }

  public function testInvalidJson(): void
  {
    $body = <<<JSON
{
  "inventory": [
    {
      "art_id": ,
      "name": ""
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

    $response = $this->client->request('POST', '/v1/articles/load', $options);
    $this->assertResponseStatusCodeSame(400);
    $this->assertJsonContains(['hydra:description' => 'Syntax error']);

    $response = $this->client->request('GET', '/v1/articles', ['headers' => $this->getHeaders()]);
    // no article was added
    $this->assertCount(4, $response->toArray()['hydra:member']);
  }
}
