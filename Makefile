
help:
	@echo "-- list commands --"
	@echo "cli - open bash to php-fpm container"
	@echo "up - start env working"

cli:
	@docker-compose exec sf_app /bin/bash

up:
	@docker-compose up -d