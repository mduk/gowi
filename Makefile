all:: clean composer test
test:: phpunit

clean:
	rm -rf ./vendor

composer: composer.json
	./composer.phar install

phpunit: phpunit.xml
	./vendor/bin/phpunit
