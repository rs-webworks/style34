# eRyse Client 

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/01967488cc49481687af144087326bfd)](https://www.codacy.com?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=raitocz/eryse-client&amp;utm_campaign=Badge_Grade)

Community portal interface application to connect at serve content from eRyse CDS. Currently in development. The aim is to provide hub for car owners to quickly 
organize their cars, parts, sales, car history, events and other stuff.

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
`./node_modules/.bin/encore dev --watch`
