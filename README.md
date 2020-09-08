# ptdls


## Container

### Dockerfile

Using 3 stage:
 - Install dependencies
 - Linter 
 - Create data container
 
### Docker Compose

Use 3 container
 - Data container
 - php-fpm
 - nginx
How to run
```shell script
docker-compose up --force-recreate --build
```

How to stop
```shell script
docker-compose down -v
```