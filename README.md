# Publisher-symfony-2023

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
% ./vendor/bin/phpunit tests
```
