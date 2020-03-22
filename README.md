# thesis


## How to deploy on server

### Checkout project

```git clone https://github.com/privrja/thesis.git```

```cd thesis/```

```git checkout deploy```

### Setup ENV variables and export them

```composer dum-env prod```

### Install dependecies

 ```composer install --no-dev --optimize-autoloader```

### Clear cache

```php bin/console cache:clear```

### Zip it and transfer to server
