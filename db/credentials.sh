#!/bin/bash

[ "$DB_HOSTNAME" = "" ] && DB_HOSTNAME="localhost"
[ "$DB_HOSTPORT" = "" ] && DB_HOSTPORT="3306"
[ "$DB_DATABASE" = "" ] && DB_DATABASE="Badminton"
[ "$DB_USERNAME" = "" ] && DB_USERNAME="Badminton"
[ "$DB_PASSWORD" = "" ] && DB_PASSWORD="Badminton"

MYSQL=$( mysql --protocol=tcp --host=$DB_HOSTNAME --port=$DB_HOSTPORT --user=$DB_USERNAME --password=$DB_PASSWORD --database=$DB_DATABASE)
