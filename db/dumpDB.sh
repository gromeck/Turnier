#!/bin/bash

. ./credentials.sh

DBTABLES="Players Pairings Matches"
DBTABLES="$( echo "SHOW TABLES;" | mysql --host=$DBHOST --user=$DBUSER --password=$DBPASS --database=$DBNAME --skip-column-names )"

DATE=$( date +%Y%m%d-%H%M%S )

mkdir -p dump/$DATE


#
#	complete dump
#
mysqldump --host=$DBHOST --user=$DBUSER --password=$DBPASS $DBNAME \
	--opt \
	--skip-extended-insert \
	>dump/$DATE/dump-$DBNAME.sql
ln -sf $DATE/dump-$DBNAME.sql dump/dump-$DBNAME.sql

#
#	dump table wise
#
for DBTABLE in $DBTABLES; do
	mysqldump --host=$DBHOST --user=$DBUSER --password=$DBPASS $DBNAME $DBTABLE \
		--opt \
		--skip-extended-insert \
		>dump/$DATE/dump-$DBNAME-$DBTABLE.sql
	ln -sf $DATE/dump-$DBNAME-$DBTABLE.sql dump/dump-$DBNAME-$DBTABLE.sql
done
