cs:
	vendor/squizlabs/php_codesniffer/bin/phpcs .

phpmd:
	vendor/phpmd/phpmd/src/bin/phpmd . text phpmd.xml --exclude vendor/

phpunit:
	vendor/phpunit/phpunit/phpunit --configuration phpunit.xml.dist tests/

.PHONY: cs phpmd phpunit