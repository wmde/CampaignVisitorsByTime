cs:
	vendor/squizlabs/php_codesniffer/bin/phpcs .

phpmd:
	vendor/phpmd/phpmd/src/bin/phpmd . text phpmd.xml --exclude vendor/

.PHONY: cs phpmd
