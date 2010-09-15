#!/bin/bash

ldapsearch -x -b 'ou=People,dc=polytech,dc=unice,dc=fr' -h ns2 -LLL "(corps=etudiants)"  corps | grep corps | sort | uniq 
