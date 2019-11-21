#!/bin/bash
php bin/console doctrine:migrations:migrate --em=eryseClient --configuration=./bin/migrations_client.yaml
php bin/console doctrine:migrations:migrate --em=eryseServer --configuration=./bin/migrations_server.yaml