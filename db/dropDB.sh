#!/bin/bash

. ./credentials.sh

#
#	create the database & the database user
#
mysql --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=root <<EOF
DROP DATABASE $DB_DATABASE;
EXIT
EOF
