#!/bin/bash

FILE=$1
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"
echo "<teachers>"
while read LINE
do
    KIND=`echo $LINE | cut -d : -f 1`
    case "$KIND" in
	dn) continue;;
	cn) LASTNAME=`echo $LINE | cut -d \  -f 2 `
	    FIRSTNAME=`echo $LINE | cut -d \  -f 3 `;;
	uid) LOGIN=`echo $LINE | cut -d : -f 2 | tr -d \ `;;
	uidNumber) MYUID=`echo $LINE | cut -d : -f 2 | tr -d \ `;;
	corps) continue;;
	*) echo "    <teacher login=\"$LOGIN\">"
	    echo "      <lastname>$LASTNAME</lastname>"
	    echo "      <firstname>$FIRSTNAME</firstname>"
	    echo "    </teacher>"
    esac
done < $FILE
echo "</teachers>"