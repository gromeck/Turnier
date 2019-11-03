#!/bin/bash
#
#	Database script to load initial data
#

. ./credentials.sh

DO_DELETE=0
if [ "$1" = "-d" ] || [ "$1" = "--delete" ]; then
	#
	#	deleting of database records is requested
	#
	shift
	DO_DELETE=1
fi

#
#	get the tablename from the parameters
#
INITSQL=$( basename "$1" .sql )

#
#	check the name of the init file
#
if [ "$( echo $INITSQL | cut -f1 -d- )" != "$DB_DATABASE" ]; then
	echo "$DB_DATABASE: init file $INITSQL doesn't belong to database $DB_DATABASE -- aborting!"
	exit 1
fi

#
#	check if there is a SQL script for this table
#
if [ "$INITSQL" != "" ] && [ ! -f init/$INITSQL.sql ]; then
	echo "$DB_DATABASE: no table init SQL script found for table $INITSQL."
	INITSQL=""
fi
if [ "$INITSQL" = "" ]; then
	echo "Usage: $0 [-d|--delete] <tablename>"
	echo "with:"
	echo "    -d"
	echo "    --delete  will remove all entries from the table"
	echo "              before inserting the ones from the SQL script"
	echo "    <tablename>  is the table to be reinitialized"
	echo
	echo "Available tables to be reinitialized are:"
	for INITSQL in init/*.sql; do
		echo "    $( basename $INITSQL .sql )"
	done
	exit 1
fi

if [ $DO_DELETE != 0 ]; then
	#
	#	remove all entries from the table before
	#
	echo "$DB_DATABASE: Deleting all entries from database table $TABLE ..."
	echo "DELETE FROM $TABLE;" | $MYSQL
fi

#
#	load the SQL script to fill the table
#
echo "$DB_DATABASE: Initializing database table $TABLE from SQL script init/$INITSQL.sql ..."
$MYSQL <init/$INITSQL.sql
[ $? != 0 ] && exit

echo "$DB_DATABASE: Initializing database table $TABLE done."

exit
#
