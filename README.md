<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Football simulator
- PHP: 8
- Composer: 2.1.5
- MySQL: 8.0.20

### Requirements:
- Docker Engine release 18.06.0+
- Docker-compose version 1.25+
- Make


### Quickstart guide:

1. Init env
   ``
   make up 
   ``

2. Open php-fpm container
   ``
   make cli
   ``
3. Enter command for migrate
   ``
   php artisan migrate
   ``
4. Enter command in container
   ``
   php artisan league:import
   ``


