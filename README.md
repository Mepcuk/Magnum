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

Run fixtures (fake data)
- php bin/console doctrine:fixtures:load

Run webserver
- php bin/console server:run 
  or install Symfony package from symfony.com
- symfony serve

For API please follow https://127.0.0.1:8000/api
username - info@magnum.com
password - 123
//TODO Web based authentication not completed, need add form register and login.

Please use postman for checking with token below
Token - a0a5e1842d3fb3d333c84ead96ff9569854e8b56eeb81db235403c3d2e70b15d0a6344eda94bdce9086f54c52a6e678624397be5356f8a6f2cf0495a

Saving results to CSV -> follow link https://127.0.0.1:8000/api/products.csv
Example of saving data located here -> \public\results\products-1.csv

P.s. if real Chrome webserver not run please check \vendor\symfony\panther\chromedriver-bin\
1) run script update.sh
2) check filename in Windows should be chromedriver.exe If filename is chromedriver_win32.exe please rename.
