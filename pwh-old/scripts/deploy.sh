#!/bin/bash

TMP=`mktemp -t deploy.sh.XXXXXX`
trap "rm $TMP* 2>/dev/null" 0

rm -rf pwh
## Password available at http://code.google.com/p/pwh/source/checkout
svn export https://pwh.googlecode.com/svn/trunk/ pwh --username sebastien.mosser

tar czvf $TMP www/boxes/* www/config/*
mv www www-old
mv pwh/website www
tar zxvf $TMP
chown -R www-data:www-data www/boxes www/config

echo "If everything is OK, you should delete www-old directory"
