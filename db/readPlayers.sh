#!/bin/bash

DBNAME="Badminton"
DBUSER="Badminton"
DBPASS="Badminton"

PLAYERS="
Luke,Skywalker,M,2
Han,Solo,M,2
Leia,Organa,F,2
Jar-Jar,Binks,M,2
Darth,Vader,M,2
Obi-Wan,Kenobi,M,2
Qui-Gon,Jinn,M,2
Lando,Calrissian,M,2
Count,Dooku,M,2
Beru,Lars,F,2
"


# read in the players from file
# Lastname,Firstname,Sex,Class
#PLAYERS="$( cat players.csv )"
PLAYERS="$( cat players-utf8.csv )"

#
#	create the tables
#
ROW=0
for PLAYER in $PLAYERS; do
	ROW=$(( $ROW + 1 ))
	FIRSTNAME="$( echo $PLAYER | cut -f1 -d, )"
	LASTNAME="$( echo $PLAYER | cut -f2 -d, )"
	GENDER="$( echo $PLAYER | cut -f3 -d, )"
	CLASS="$( echo $PLAYER | cut -f4 -d, )"
	if [ $CLASS = 2 ]; then
		ACTIVE=1
	else
		ACTIVE=0
	fi

	#echo "inserting [$ROW]: $FIRSTNAME,$LASTNAME,$CLASS,$ACTIVE"

	mysql --host=localhost --user=$DBUSER --password=$DBPASS --database=$DBNAME <<EOF
		INSERT INTO Players (Firstname,Lastname,Gender,Class,Active) VALUES ("$FIRSTNAME","$LASTNAME","$GENDER","$CLASS",$ACTIVE)
EOF
	cat <<EOF
		INSERT INTO Players (Firstname,Lastname,Gender,Class,Active) VALUES ("$FIRSTNAME","$LASTNAME","$GENDER","$CLASS",$ACTIVE)
EOF
done

#
#	generate the nicknames
#
../cli/generate-nicks.php
