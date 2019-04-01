Admin platform
==============

Project skeleton

Installation
------------

```bash
composer install
yarn install
yarn build
bin/console doctrine:database:create -n 
bin/console doctrine:schema:create -n 
bin/console sylius:fixtures:load -n 
```

Running behat tests
-------------------

```bash
chromedriver --url-base=wd/hub --port=4444
APP_ENV=test bin/console server:start 127.0.0.1:8000

bin/behat
```


###### Without chrome
```bash
bin/behat -p no_js
```

###### All tests in chrome
```bash
bin/behat -p chrome
```
