# Publisher-symfony-2023

### Create project config
```bash
% cp .env .env.local
% cp ./docker/.env.dist ./docker/.env
```

### Execute a migration
```bash
% php bin/console doctrine:migrations:migrate
```

### Load data fixtures to database
```bash
% php bin/console d:f:l --purge-with-truncate
```

### Run Symfony server
```bash
% symfony serve
```

### Run Docker
```bash
% docker-compose -f ./docker/docker-compose.yaml up -d
```

### Run tests
```bash
% php bin/console doctrine:migrations:migrate --no-interaction --env=test
% ./vendor/bin/phpunit tests
```
