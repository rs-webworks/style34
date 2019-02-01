#!/usr/bin/env bash
openssl req -config eryse-client.net.conf -new -sha256 -newkey rsa:2048 \
-nodes -keyout eryse-client.net.key -x509 -days 365 \
-out eryse-client.net.crt