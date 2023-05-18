pint:
	php ./vendor/bin/pint

test: pint
	composer test
