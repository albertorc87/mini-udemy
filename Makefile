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

schema-create:
	docker compose exec php php bin/console doctrine:schema:create --no-interaction

schema-drop:
	docker compose exec php php bin/console doctrine:schema:drop --force --no-interaction

load-fixtures:
	docker compose exec php php bin/console app:load-fixtures

reset-db-with-fixtures:
	docker compose exec php php bin/console doctrine:schema:drop --force --no-interaction
	docker compose exec php php bin/console doctrine:schema:create --no-interaction
	docker compose exec php php bin/console app:load-fixtures

cache-clear:
	docker compose exec php php bin/console cache:clear

cache-warmup:
	docker compose exec php php bin/console cache:warmup

cache-reset:
	docker compose exec php php bin/console cache:clear
	docker compose exec php php -r "if (function_exists('apcu_clear_cache')) { apcu_clear_cache(); echo 'APCu cleared\n'; }"
	docker compose exec php php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache reset\n'; }"
	docker compose exec php composer dump-autoload --no-interaction

opcache-reset:
	docker compose exec php php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache reset successfully\n'; } else { echo 'OPcache not available\n'; }"

apcu-clear:
	docker compose exec php php -r "if (function_exists('apcu_clear_cache')) { apcu_clear_cache(); echo 'APCu cleared successfully\n'; } else { echo 'APCu not available\n'; }"

console:
	docker compose exec php php bin/console $(cmd)

workers:
	docker compose exec php php bin/console messenger:consume async -vv

workers-background:
	docker compose exec -d php php bin/console messenger:consume async -vv

worker-stop:
	docker compose exec php php bin/console messenger:stop-workers

messenger-stats:
	docker compose exec php php bin/console messenger:stats

messenger-failed:
	docker compose exec php php bin/console messenger:failed:show

messenger-retry:
	docker compose exec php php bin/console messenger:failed:retry

messenger-debug:
	docker compose exec php php bin/console debug:messenger

logs:
	docker compose logs -f