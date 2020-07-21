# Magnum
Web crawler with Javascript support based on Symfony 5, Panther, BrowserKit, DomCrawler, Php WebDriver, Chrome + API

Installation
- composer install
- database install (local database used, to update your please edit .env)
==== Web server run
* Database create
- php bin/console doctrine:database:create
* Database update with
- php bin/console doctrine:migrations:migrate

* Run webserver
- php bin/console server:run 
  or install Symfony package from symfony.com
- symfony serve
