docker-run:
	docker-compose up

php-fix:
	php vendor/bin/php-cs-fixer fix src/

deploy:
	php vendor/bin/dep deploy prod
