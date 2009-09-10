#!/bin/bash

LDAP_SERVER='ns2.polytech.unice.fr'
MAIL_SERVER='polytech.unice.fr'

function extract () {
    ldapsearch -x -b 'ou=People,dc=polytech,dc=unice,dc=fr' -h $LDAP_SERVER -LLL "(corps=$1)" uid dn corps cn uidNumber >> $2
}

function transform() {
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
            *) echo "$LOGIN;$LOGIN@$MAIL_SERVER;$LASTNAME;$FIRSTNAME;"
	esac
    done < $1
}

function main() {
    TMP=`mktemp -t extractTeachers.sh.XXXXXX`
    trap "rm $TMP* 2>/dev/null" EXIT
    for i in "ater" "moniteur" "profs" "these" "vacataire"; do
	extract $i $TMP
    done
    transform $TMP | sort -u > $1
}

main $@
