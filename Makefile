all:: clean composer test
test:: phpunit

clean:
	rm -rf ./vendor
	rm -f ./composer.phar

composer: composer.json
	curl -sS https://getcomposer.org/installer | php
	./composer.phar install

phpunit: phpunit.xml
	./vendor/bin/phpunit

vm:
	cd ./cookbook/ && kitchen converge

shell:
	cd ./cookbook/ && kitchen login

