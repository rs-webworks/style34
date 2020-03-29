
init:
	# Remove all containers and the related volumes and networks.
	docker-compose down -v

	# Create copy dist of docker-compose.override (if it does not already exist)
	@test -f docker-compose.override.yml || cp docker-compose.override.yml.dist docker-compose.override.yml

	docker-compose build

	docker-compose run --rm api composer install
	docker-compose run --rm api bin/console assets:install

	docker-compose run --rm frontend composer install
	docker-compose run --rm frontend bin/console assets:install
	docker-compose run --rm frontend bash -c '/root/.yarn/bin/yarn'
	docker-compose run --rm frontend bash -c '/root/.yarn/bin/yarn dev'

	docker-compose up -d

	@sleep 3 && docker-compose exec mongo mongo --eval "rs.initiate()"
	@sleep 3 && docker-compose run --rm api bin/console app:mongo:schema

cs-fix: cs-fix-api cs-fix-frontend

cs-fix-api:
	docker-compose run --rm api /bin/bash -c './vendor/bin/php-cs-fixer fix'

cs-fix-frontend:
	docker-compose run --rm frontend /bin/bash -c './vendor/bin/php-cs-fixer fix'
