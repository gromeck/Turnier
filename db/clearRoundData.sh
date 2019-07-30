#!/bin/bash

. ./credentials.sh

#
#	delete data of the rounds
#
$MYSQL <<EOF
UPDATE Players SET Matches=0;
DELETE FROM Pairings;
DELETE FROM Matches;
DELETE FROM Pausers;
DELETE FROM Rounds;
EOF
