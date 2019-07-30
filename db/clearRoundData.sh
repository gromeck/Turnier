#!/bin/bash

. ./credentials.sh

#
#	delete data of the rounds
#
mysql --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=$DB_USERNAME --password=$DB_PASSWORD --database=$DB_DATABASE <<EOF
UPDATE Players SET Matches=0;
DELETE FROM Pairings;
DELETE FROM Matches;
DELETE FROM Pausers;
DELETE FROM Rounds;
EOF
