# ptdls


## Documentation

### Postman

Import file `ptdls.postman_collection.json` to postman

## Storage

For storage used Redis.
Redis has data structures such as List, Set, Sorted Set, Hash etc.

### Redis settings

If you run outside container, please specify ENV `REDIS_URL`

```
REDIS_URL=redis://redis:6379
```

For example in `.env` file

## Container

### Dockerfile

Using 3 stage:
 - Install dependencies
 - Linter 
 - Create data container
 
### Docker Compose

Use 4 container
 - Data container
 - php-fpm
 - nginx
 - redis
 
 
How to run

```shell script
docker-compose up --force-recreate --build
```

How to stop

```shell script
docker-compose down -v
```