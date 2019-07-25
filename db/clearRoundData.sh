#!/bin/bash

DBNAME="Badminton"
DBUSER="Badminton"
DBPASS="Badminton"

#
#	delete data of the rounds
#
mysql --host=localhost --user=$DBUSER --password=$DBPASS --database=$DBNAME <<EOF
UPDATE Players SET Matches=0;
DELETE FROM Pairings;
DELETE FROM Matches;
DELETE FROM Pausers;
DELETE FROM Rounds;
EOF
