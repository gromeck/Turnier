#!/bin/bash

. ./credentials.sh

DBTABLES="Players Pairings Matches"
DBTABLES="$( echo "SHOW TABLES;" | mysql --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=$DB_USERNAME --password=$DB_PASSWORD --database=$DB_DATABASE --skip-column-names )"

DATE=$( date +%Y%m%d-%H%M%S )

mkdir -p dump/$DATE


#
#	complete dump
#
mysqldump --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=$DB_USERNAME --password=$DB_PASSWORD $DB_DATABASE \
	--opt \
	--skip-extended-insert \
	>dump/$DATE/dump-$DB_DATABASE.sql
ln -sf $DATE/dump-$DB_DATABASE.sql dump/dump-$DB_DATABASE.sql

#
#	dump table wise
#
for DBTABLE in $DBTABLES; do
	mysqldump --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=$DB_USERNAME --password=$DB_PASSWORD $DB_DATABASE $DBTABLE \
		--opt \
		--skip-extended-insert \
		>dump/$DATE/dump-$DB_DATABASE-$DBTABLE.sql
	ln -sf $DATE/dump-$DB_DATABASE-$DBTABLE.sql dump/dump-$DB_DATABASE-$DBTABLE.sql
done
