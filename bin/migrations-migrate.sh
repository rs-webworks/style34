#!/bin/bash
php bin/console doctrine:migrations:migrate --em=eryseClient --configuration=./bin/dm_eryseClient.yaml
php bin/console doctrine:migrations:migrate --em=eryseServer --configuration=./bin/dm_eryseServer.yaml