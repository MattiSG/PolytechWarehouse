#!/bin/bash

FILE=$1
NAME=$2
GROUPS_ID=`echo $3 | tr , \ `
COURSES=`echo $4 | tr , \ `

function extractStudents() {
    echo "  <students>"
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
	    *) echo "    <student uid=\"$MYUID\" login=\"$LOGIN\">"
		echo "      <lastname>$LASTNAME</lastname>"
		echo "      <firstname>$FIRSTNAME</firstname>"
		echo "    </student>"
	esac
    done < $FILE
    echo "  </students>"
}

function extractPromotion(){
    echo "    <group name=\"Promotion\">"
    while read LINE
    do
	KIND=`echo $LINE | cut -d : -f 1`
	case "$KIND" in
	    uidNumber) MUID=`echo $LINE | cut -d : -f 2 | tr -d \ `
		echo "      <member uid=\"$MUID\" />";;
	    *) continue;;
	esac
    done < $FILE
    echo "    </group>"
}

function extractGroup() {
    echo "    <group name=\"$1\">"
    while read LINE
    do
	KIND=`echo $LINE | cut -d : -f 1`
	case "$KIND" in
	    corps) CORPS=`echo $LINE | cut -d : -f 2 | grep -i $1`;;
	    uidNumber) MUID=`echo $LINE | cut -d : -f 2 | tr -d \ `;;
	    "") if [[ $CORPS ]]
	    then
	    echo "      <member uid=\"$MUID\" />"
	    fi;;
	    *) continue;;
	esac
    done < $FILE
    echo "    </group>"
}


echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"
LABEL=`echo $1 | cut -d . -f 1`
echo "<promotion label=\"$LABEL\">"
echo "  <name>$2</name>"
extractStudents
echo "  <groups>"
extractPromotion
for g in $GROUPS_ID
do
    extractGroup $g
done;
echo "  </groups>"
echo "  <courses>"
for c in $COURSES
do
    echo "    <course descriptor=\"$c\" />"
done
echo "  </courses>"
echo "</promotion>"