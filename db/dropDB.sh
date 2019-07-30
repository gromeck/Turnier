#!/bin/bash

. ./credentials.sh

#
#	create the database & the database user
#
mysql --host=$DBHOST --user=root <<EOF
DROP DATABASE $DBNAME;
EXIT
EOF
