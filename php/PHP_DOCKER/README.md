# Docker

```
docker build -f Dockerfile -t php_8-5-3_apache .
```

```
docker run -d -p 8083:80 -v D:\projects\MusaFormazione\BackendWebDevelopment_0125\php\PHP_DOCKER:/var/www/html php_8-5-3_apache
```