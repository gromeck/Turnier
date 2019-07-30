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
**  load the complete player list
*/
$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL);

#print_r($players);
#exit;

foreach ($players as $player)
	printf("%-30s  %-20s\n",$player['Firstname'].' '.$player['Lastname'],$player['Nick']);

?>
