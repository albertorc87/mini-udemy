up:
	docker compose up -d --build

down:
	docker compose down -v

bash:
	docker compose exec php bash

setup:
	docker compose up -d --build

composer:
	docker compose exec php composer $(cmd)

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate -n

migration:
	docker compose exec php php bin/console make:migration

console:
	docker compose exec php php bin/console $(cmd)

workers:
	docker compose exec php php bin/console messenger:consume async -vv

logs:
	docker compose logs -f