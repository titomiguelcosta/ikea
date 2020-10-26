# IKEA: Technical test

If you would like to test the API, there are 7 endpoints, and available on https://ikea.titomiguelcosta.com/v1/docs

If you would like to test locally, all you need to do is clone the repository and make sure you have docker 19.03.13+ and docker-compose 1.23.2+ and at the root of the project run

```
$ docker-compose up
```

And then access the API on http://localhost:8080/v1/docs

Two commands are provided to load the articles and products, as an alternative to the endpoints.


To run them, first connect to the php-fpm docker instance.

```
$ docker exec -it ikea-php-fpm /bin/bash
```

And then execute

```
$ php bin/console articles:load /path/to/file

$ php bin/console products:load /path/to/file
```

The original files provided in the assignment are available in the doc/ directory.

## Code

* API powered by [Symfony](https://symfony.com/) and [API Platform](https://api-platform.com/)
* Functional tests using [PHPUnit](https://phpunit.de/)
* [Doctrine](https://www.doctrine-project.org/) for the ORM
* [Deployer](https://deployer.org/) to manage releases

## Tests

Only functional tests are provided and they can be run by executing in the php-fpm docker container the following command

```
$ php vendor/bin/phpunit
```

It is one of the steps in the deployment process, so if they are not green, release will fail. 

## Architecture

* Loading products and articles is handled in an asynchronous way by using the [messenger bus](https://symfony.com/doc/current/components/messenger.html) component. In production, messages are stored in the [AWS SQS](https://aws.amazon.com/sqs/) queue and only one worker is available. In development and test environments, they are processed synchronously.

## Unknowns

* Products have a price, but they are not provided in the sample json, so hardcoding a value.
* No business rules for what to do when invalid articles/products are detecting while loading.

## Assumptions

* Only one currency, so not storing in the database any value.
* No need to support translations.
* Public API, no need for authentication.
* Product names are unique.
* Files will not have millions of thousands of records, cos if they do, loading everything in memory won't be the best approach.

## Considerations

* Peaks during evenings
  * We should have a load balancer in front on the API, so we could easily scale horizontally. For loading articles and products, since they are handled asynchronously, all we need to do is add extra workers.
* What will happen if same product is sold at the same time and there is only one available?
  * With the current implementation, first to be processed, gets it. 
* What if someone tries to buy a product out of stock during the load of articles? 
  * We should use a lock mechanism, Symfony provides a [component](https://symfony.com/doc/current/components/lock.html) exactly for it.
* How to handle multiple versions of the API? 
  * They will be deployed independently, and we use the path in the url to distinguish them, e.g., /v1, making sure there are no backwards incompatibilities in the data layer.
* Multi regional
  * Hosting provider should provide solution for it. For instance, on AWS, we can deploy our code to regions all over the world and database can be configured to support [Multi-AZ](https://aws.amazon.com/rds/features/multi-az/).
* Observability
  * Integrated [Sentry](https://sentry.io/) so that any errors are logged and we could setup alerts. A more generic solution would be to use [DataDog](https://www.datadoghq.com/) or [New Relic](https://newrelic.com/). But same approach, let an external service handle it instead of overloading API.

## Known issues

* When booting docker for the first time, php-fpm will be notified that database is ready, when in reality causes to fail migrations and fixtures. To fix this, at the end of loading, stop docker-composer (Ctrl+c) an execute 

```
$ docker-compose up --build
```

or as an alternative, just access the php-fpm container and manually load data

```
$ docker exec -it ikea-php-fpm /bin/bash
$ make bootstrap
```