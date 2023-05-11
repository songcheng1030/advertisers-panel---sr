make.PHONY:
start:
	docker-compose up -d

.PHONY:
stop:
	docker-compose stop

.PHONY:
init:
	docker-compose build
	docker-compose up -d

.PHONY:
ngnix-shell:
	docker exec -it advertisers-panel-nginx /bin/ash

.PHONY:
php-shell:
	docker exec -it advertisers-panel-php /bin/bash

.PHONY:
db-shell:
	docker exec -it advertisers-panel-db /bin/bash
