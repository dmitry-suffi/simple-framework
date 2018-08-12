Запуск тестов
```sh
clear && php70 phpunit.phar --colors=always --bootstrap=tests/autoload.php tests/

clear && php vendor/bin/phpunit --colors=always --bootstrap=../tests/autoload.php  ../tests/
```

2 вариант (для windows с покрытием кода)
```sh
cls && php vendor/bin/phpunit -c ..\tests\phpunit.xml ../tests
```

3 вариант (для linux с покрытием кода)
```sh
 clear && php vendor/bin/phpunit -c tests/phpunit.xml tests
 clear && php vendor/bin/phpunit -c ../tests/phpunit.xml ../tests
```

