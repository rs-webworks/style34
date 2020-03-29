init:
	# Remove all containers and the related volumes and networks.
	docker-compose down -v

	# Create copy dist of docker-compose.override (if it does not already exist)
	@test -f docker-compose.override.yml || cp docker-compose.override.yml.dist docker-compose.override.yml

	docker-compose build

	docker-compose run --rm ec-php composer install
	docker-compose run --rm ec-php bin/console assets:install
	docker-compose run --rm ec-php bash -c '/root/.yarn/bin/yarn'
	docker-compose run --rm ec-php bash -c '/root/.yarn/bin/yarn dev'

	docker-compose up -d

run:
	docker-compose up -d
