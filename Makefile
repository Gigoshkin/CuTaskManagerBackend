up:
	docker compose up -d

build_test:
	docker compose -f compose-test.yaml build

down:
	docker compose down

rebuild:
	docker compose down --remove-orphans
	docker compose up -d --build

hard_rebuild:
	docker compose down -v --remove-orphans
	docker compose rm -vsf
	docker compose up -d --build

restart:
	docker compose down --remove-orphans
	docker compose up -d

db:
	docker compose exec php ./bin/console doctrine:database:drop --force
	docker compose exec php ./bin/console doctrine:database:create
	docker compose exec php ./bin/console doctrine:migrations:migrate -n

ps:
	docker compose ps

prod_build:
	docker compose -f compose-prod.yaml build

prod:
	docker compose -f compose-prod.yaml up -d

clear:
	docker compose down -v --remove-orphans
	docker compose rm -vsf

prune:
	docker kill $(docker ps -q)
	docker rmi $(docker images -a --filter=dangling=true -q)
	docker rmi $(docker images -a -q)
	docker system prune -a -f
	docker volume prune -f