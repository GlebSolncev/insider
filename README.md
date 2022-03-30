<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Football simulator
- PHP: 8
- Composer: 2.1.5
- MySQL: 8.0.20

### Requirements:
- Docker Engine release 18.06.0+
- Docker-compose version 1.25+
- GNU Make 3.81


### Quickstart guide:

0. Build containers: `` make build ``
1. Start project: `` make up `` 
2. Open php-fpm container: `` make cli ``
   0. Enter install composer: `` composer install ``
   1. Replace .env.example: `` mv .env.example .enc``
   2. Key generate: `` php artisan key:generate ``
   3. Migrate tables: `` php artisan migrate ``

3. Import data (clubs, players, player skills) from FIFA.

   `` php artisan league:import ``


