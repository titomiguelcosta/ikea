docker-run:
	docker-compose up

bootstrap:
	php bin/console doctrine:migrations:migrate -nq
	php bin/console hautelook:fixtures:load -nq --no-bundles

php-fix:
	php vendor/bin/php-cs-fixer fix src/

deploy:
	php vendor/bin/dep deploy prod
