# MassSpecBlocks backend

This repo is backend part of application MassSpecBlocks. The Main repo with documentation is [here](https://github.com/privrja/MassSpecBlocks).

## Development

### Clone project and install dependencies

```git clone https://github.com/privrja/thesis.git```

```cd thesis/```

```composer install --dev```

### Database

To drop DB and create new use:

```php bin/console doctrine:database:drop --force```

```php bin/console doctrine:database:create```

```php bin/console doctrine:mig:mig```

```php bin/console doctrine:fixtures:load```

### Run tests
Before every test you need to delete, create, migrate and purge DB like in previuos step, now test can't be run more times in a row.

To run test use ```composer test``` or ```php ./vendor/symfony/phpunit-bridge/bin/simple-phpunit```

### Start local dev server 

```symfony server:start```

Documentation of API is [localhost:8000/api/doc](https://localhost:8000/api/doc)
Rest API is on [localhost:8000/rest](https://localhost:8000/rest)

### CORS

To test on local machine i recomend you to use chrome without CORS policy check. Used only for development it can be security risk for other else actions!

```”C:\Program Files (x86)\Google\Chrome\Application\chrome.exe” — disable-web-security — disable-gpu — user-data-dir=~/chromeTemp```
