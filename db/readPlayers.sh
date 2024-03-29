#!/bin/bash
#
#	read the players from a file
#
DBNAME="Badminton"
DBUSER="Badminton"
DBPASS="Badminton"

. ./credentials.sh

PLAYERFILE="$1"

if [ ! -f "$PLAYERFILE" ]; then
	echo "Can't read from file $PLAYERFILE -- aborting!"
	exit 1
fi

#
#	drop all players
#
$MYSQL <<EOF
	TRUNCATE TABLE Players;
EOF


#
#	create the tables
#
ROW=0
cat $PLAYERFILE | while read PLAYER; do
	[ "$PLAYER" = "" ] && continue
	[ "$PLAYER" = "CSV" ] && continue
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

	$MYSQL <<EOF
		INSERT INTO Players (Firstname,Lastname,Gender,Class,Active) VALUES ("$FIRSTNAME","$LASTNAME","$GENDER","$CLASS",$ACTIVE)
EOF
	cat <<EOF
		INSERT INTO Players (Firstname,Lastname,Gender,Class,Active) VALUES ("$FIRSTNAME","$LASTNAME","$GENDER","$CLASS",$ACTIVE)
EOF
done

echo -n "Players inserted: "
$MYSQL <<EOF
	SELECT COUNT(*) FROM Players;
EOF

#
#	generate the nicknames
#
../cli/generate-nicks.php
