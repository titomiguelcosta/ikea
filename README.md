# IKEA: Technical test

If you would like to test the API, there are 4 endpoints, and available on https://ikea.titomiguelcosta.com/v1/docs

If you would like to test locally, all you need to do is clone the repository and make sure you have docker 19.03.13+ and docker-compose 1.23.2+ and at the root of the project run

```
$ docker-compose up
```

And then access the API on http://localhost:8080/v1/docs

## Code

* API powered by [Symfony](https://symfony.com/) and [API Platform](https://api-platform.com/)
* Functional tests using [PHPUnit](https://phpunit.de/)
* [Doctrine](https://www.doctrine-project.org/) as the ORM
* [Deployer](https://deployer.org/) to manage releases

## Architecture

* Processing products and articles in an asynchronous way by using the messenger bus pattern.

## Unknowns

* Products have a price, but they are not provided in the sample json, so hardcoding a value.

## Assumptions

* Only one currency, so not storing in the database any value.
* No need to support translations.
* Public API, no need for authentication.
* Product names are unique.
* Files will not have millions of thousands of records, cos if they do, loading everything in memory won't be the best approach.

## Considerations

### Concurrency

* What will happen if same product is sold at the same time and there is only one available?
* What if someone tries to buy a product out of stock during the load of articles?

## Decision

* Multiple versions of the API will be deployed independently, and we use the path in the url to distinguish them, e.g., /v1