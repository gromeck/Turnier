#!/bin/bash

. ./credentials.sh

#
#	create the database & the database user
#
$MYSQLROOT <<EOF
CREATE DATABASE IF NOT EXISTS $DB_DATABASE CHARACTER SET utf8 COLLATE utf8_general_ci;
USE mysql;
DELETE FROM user WHERE User="$DB_USERNAME";
FLUSH PRIVILEGES;
GRANT ALL PRIVILEGES ON *.* TO "$DB_USERNAME"@"$DB_HOSTNAME" IDENTIFIED BY "$DB_PASSWORD" WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO "$DB_USERNAME"@"%" IDENTIFIED BY "$DB_PASSWORD" WITH GRANT OPTION;
EXIT
EOF

#
#	create the tables
#
$MYSQL <<EOF
CREATE TABLE IF NOT EXISTS Settings (
	Name varchar(100) NOT NULL default "",
	Value varchar(1000) NOT NULL default "",
	PRIMARY KEY (Name)
) COMMENT='Settings';

CREATE TABLE IF NOT EXISTS Users (
	Uid int(5) unsigned NOT NULL auto_increment,
	Username varchar(50) NOT NULL default "",
	Password varchar(50) NOT NULL default "",
	Admin int(1) NOT NULL default 0,
	CreationDate datetime NOT NULL default "0000-00-00 00:00:00",
	PRIMARY KEY (Uid),
	UNIQUE KEY (Username)
) COMMENT='Users';

CREATE TABLE IF NOT EXISTS Players (
	Pid int(5) unsigned NOT NULL auto_increment,
	RFIDcode varchar(15) NOT NULL default "",
	Nick varchar(50) NOT NULL default "",
	Firstname varchar(50) NOT NULL default "",
	Lastname varchar(50) NOT NULL default "",
	Banned int(1) NOT NULL default 0,
	BannedChangeDate datetime NOT NULL default "0000-00-00 00:00:00",
	Active int(1) NOT NULL default 0,
	ActiveChangeDate datetime NOT NULL default "0000-00-00 00:00:00",
	Gender varchar(1) NOT NULL default 0,
	Class int(1) NOT NULL default 0,
	Matches int(5) NOT NULL default 0,
	CreationDate datetime NOT NULL default "0000-00-00 00:00:00",
	PRIMARY KEY (Pid),
	KEY (RFIDcode),
	UNIQUE KEY (Firstname,Lastname),
	KEY (Nick)
) COMMENT='Players';

CREATE TABLE IF NOT EXISTS RFIDcodes (
	RFIDcode varchar(15) NOT NULL default "",
	CreationDate datetime NOT NULL default "0000-00-00 00:00:00",
	PRIMARY KEY (RFIDcode)
) COMMENT='RFIDcodes';

CREATE TABLE IF NOT EXISTS Pairings (
	PidA int(5) NOT NULL,
	PidB int(5) NOT NULL,
	Count int(5) default NULL,
	PRIMARY KEY (PidA,PidB)
) COMMENT="Count the pairing of players"; 

CREATE TABLE IF NOT EXISTS Matches (
	Round int(5) default NULL,
	Class int(5) default NULL,
	Court int(5) default NULL,
	PidA int(5) NOT NULL,
	PidB int(5) NOT NULL,
	PidC int(5) NOT NULL,
	PidD int(5) NOT NULL,
	PRIMARY KEY (Round,Class,Court),
	KEY (Round)
) COMMENT="Match"; 

CREATE TABLE IF NOT EXISTS Pausers (
	Round int(5) default NULL,
	Class int(5) default NULL,
	Pid int(5) NOT NULL,
	PRIMARY KEY (Round,Pid),
	KEY (Round)
) COMMENT="Pausers"; 

CREATE TABLE IF NOT EXISTS Rounds (
	Round int(5) default NULL,
	State int(5) default NULL,
	StartDate datetime NOT NULL default "0000-00-00 00:00:00",
	PlayDate datetime NOT NULL default "0000-00-00 00:00:00",
	StopDate datetime NOT NULL default "0000-00-00 00:00:00",
	PRIMARY KEY (Round)
) COMMENT="Pausers"; 

EOF

#
#	process initial sql script
#
for INITSQL in init/*.sql; do
	./reinitTable.sh $INITSQL
done

#
#	generate the nicknames
#
../cli/generate-nicks.php
