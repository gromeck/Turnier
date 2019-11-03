#!/bin/bash

. ./credentials.sh

#
#	drop the database
#
echo "DROP DATABASE $DB_DATABASE" | $MYSQL
