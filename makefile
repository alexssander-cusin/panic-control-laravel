pint:
	php ./vendor/bin/pint

stan:
	php ./vendor/bin/phpstan

test: pint
	composer test
