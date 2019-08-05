#!/bin/bash
php bin/console doctrine:migrations:diff --em=eryseClient --configuration=./bin/dm_eryseClient.yaml
php bin/console doctrine:migrations:diff --em=eryseServer --configuration=./bin/dm_eryseServer.yaml