# style34

### Instalation
1. Set all required settings to .env file.
2. Run `docker-compose up -d`
3. Run composer via `docker exec composer install`
3. Run simple command `docker exec php bin/console app:install`
4. Use localhost:8989

Optional:
- Add to your `/etc/hosts` domain. Anything pointing to 127.0.0.1 will be accessible on :8989 port.
- Run tests with `./bin/phpunit`

For assets compilation:
./node_modules/.bin/encore dev --watch