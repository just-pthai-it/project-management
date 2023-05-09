# Project management

## Requirements

PHP version: 8.1.2

Composer version: 2.5.5

Redis server version: 6.0.16

Mariadb server: 10.11.2

Docker engine version: 23.0.4

## Installation
Install dependencies
```
sudo docker run --rm --user 1000:1000 -v $(pwd):/app composer:2.5.5 composer i
```
Start containers
```
sudo docker compose up -d --build
```
