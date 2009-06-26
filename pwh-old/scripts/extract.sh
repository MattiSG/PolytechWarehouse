#!/bin/bash

ldapsearch -x -b 'ou=People,dc=polytech,dc=unice,dc=fr' \
    -h ns2.polytech.unice.fr -LLL '(corps=$1)' uid dn corps cn uidNumber