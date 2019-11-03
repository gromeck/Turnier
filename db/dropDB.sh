#!/bin/bash

. ./credentials.sh

#
#	create the database & the database user
#
mysql --protocol=tcp --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=root <<EOF
DROP DATABASE $DB_DATABASE;
EXIT
EOF
