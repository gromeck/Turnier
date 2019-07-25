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

PLAYERS="
Luke01,Skywalker,M,1
Luke02,Skywalker,M,1
Luke03,Skywalker,M,1
Luke04,Skywalker,M,1
Luke05,Skywalker,M,1
Luke06,Skywalker,M,1
Luke07,Skywalker,M,1
Luke08,Skywalker,M,1
Luke09,Skywalker,M,1
Luke10,Skywalker,M,1
Luke11,Skywalker,M,1
Leia01,Organa,F,1
Leia02,Organa,F,1
Leia03,Organa,F,1
Leia04,Organa,F,1
Leia05,Organa,F,1
Leia06,Organa,F,1
Leia07,Organa,F,1
Leia08,Organa,F,1
Leia09,Organa,F,1
Leia10,Organa,F,1
Leia11,Organa,F,1
"

# read in the players from file
# Lastname,Firstname,Sex,Class
PLAYERS="$( cat players.csv )"

#
#	create the tables
#
ROW=0
for PLAYER in $PLAYERS; do
	ROW=$(( $ROW + 1 ))
	FIRSTNAME="$( echo $PLAYER | cut -f2 -d, )"
	LASTNAME="$( echo $PLAYER | cut -f1 -d, )"
	GENDER="$( echo $PLAYER | cut -f3 -d, )"
	CLASS="$( echo $PLAYER | cut -f4 -d, )"
	if [ $CLASS = 2 ]; then
		ACTIVE=1
	else
		ACTIVE=0
	fi

	echo "inserting [$ROW]: $FIRSTNAME,$LASTNAME,$CLASS,$ACTIVE"

	mysql --host=localhost --user=$DBUSER --password=$DBPASS --database=$DBNAME <<EOF
		INSERT INTO Players (Firstname,Lastname,Gender,Class,Active) VALUES ("$FIRSTNAME","$LASTNAME","$GENDER","$CLASS",$ACTIVE)
EOF
done

#
#	generate the nicknames
#
../cli/generate-nicks.php
