#!/usr/bin/env bash
sudo apt install libnss3-tools
certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n eryse-client.net.crt -i eryse-client.net.crt
certutil -d sql:$HOME/.pki/nssdb -L