# Magnum
Web crawler with Javascript support based on Symfony 5, Panther, BrowserKit, DomCrawler, Php WebDriver, Chrome + API

Installation
- composer install
- MariaDb(MySql) database install (local database used, to update your please edit .env)

Web server run

Database create
- php bin/console doctrine:database:create

Database update with
- php bin/console doctrine:migrations:migrate

Run webserver
- php bin/console server:run 
  or install Symfony package from symfony.com
- symfony serve

For API please follow https://127.0.0.1:8000/api
Saving results to CSV -> follow link https://127.0.0.1:8000/api/products.csv

P.s. if real Chrome webserver not run please check \vendor\symfony\panther\chromedriver-bin\
1) run script update.sh
2) check filename in Windows should be chromedriver.exe If filename is chromedriver_win32.exe please rename.
