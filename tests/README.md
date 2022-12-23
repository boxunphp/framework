## 启动容器

## 启动MySQL容器
```bash
$ docker run --name mysql-5.7-3306 -p 3306:3306 -v $PWD/data/3306:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=123456 -d imarno/mysql:v1
```

## 启动Redis容器
```bash
$ docker run -p 6379:6379 --name redis-5.0-6379 -v $PWD/data/6379:/data -d imarno/redis
```

## 启动Memcache容器
```bash
$ docker run -p 11211:11211 --name memcached-1.6-11211 -d imarno/memcached memcached -m 100m
```