#!/usr/bin/php
<?php
/*
**	generate all nicknames
*/
define('CLI',1);
include_once "../inc/settings.inc.php";
include_once "../inc/common.inc.php";
include_once "../inc/database.inc.php";

/*
**	(re-)generate the nicknames in the database
*/
list ($result,$dbmsg) = db_player_computenicks();
if (!$result) {
	print "Fehler beim Generieren der Nicknames! ($dbmsg)\n";
	exit(1);
}

/*
**	print a message
*/
print "Nicknames generiert!\n";
?>
