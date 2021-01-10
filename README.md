# MassSpecBlocks backend
## Development
### Clone project and install dependencies
```git clone https://github.com/privrja/thesis.git```

```cd thesis/```

```composer install --dev```

### Database
```php bin/console doctrine:database:create```

```php bin/console doctrine:mig:mig```

```php bin/console doctrine:fixtures:load```

### Run tests
Before every test you need to delete, create, migrate and purge DB like in previuos step, now test can't be run more times in a row.

To run test use
```composer test```
or

```php ./vendor/symfony/phpunit-bridge/bin/simple-phpunit```

### Start local dev server 
```symfony server:start```

### CORS
To test on local machine you need to use chrome without CORS policy check

```”C:\Program Files (x86)\Google\Chrome\Application\chrome.exe” — disable-web-security — disable-gpu — user-data-dir=~/chromeTemp```

## How to deploy on server
### Checkout project
```git clone https://github.com/privrja/thesis.git```

```cd thesis/```

```git checkout deploy```

### Setup ENV variables and export them
```composer dump-env prod```

### Install dependecies
 ```composer install --no-dev --optimize-autoloader```

### Clear cache
```php bin/console cache:clear```

### Zip it and transfer to server