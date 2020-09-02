install: composer-install env-install
c-i: composer-install
c-u: composer-update
check: phplint phpcs
fix: phpcbf


phplint:
	${PWD}/vendor/bin/phplint -v

phpcs:
	${PWD}/vendor/bin/phpcs -v

phpcbf:
	${PWD}/vendor/bin/phpcbf


composer-install:
	composer install

composer-update:
	composer update

env-install:
	cp .env.example .env