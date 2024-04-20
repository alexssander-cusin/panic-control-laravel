pint:
	php ./vendor/bin/pint

stan:
	php ./vendor/bin/phpstan --memory-limit=-1

test: pint
	composer test

all: pint stan test
