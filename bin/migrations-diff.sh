#!/bin/bash
php bin/console doctrine:migrations:diff --em=eryseClient --configuration=./bin/migrations_client.yaml
php bin/console doctrine:migrations:diff --em=eryseServer --configuration=./bin/migrations_server.yaml