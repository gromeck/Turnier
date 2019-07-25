#!/usr/bin/php
<?php
/*
**	simulate rounds
*/
define('CLI',1);
include_once "../inc/settings.inc.php";
include_once "../inc/common.inc.php";
include_once "../inc/util.inc.php";
include_once "../inc/database.inc.php";
include_once "../inc/round.inc.php";

$nrounds = $argv[1];
print "Simulating $nrounds rounds ...\n";

for ($n = 0;$n < $nrounds;$n++) {
	/*
	**	start a new round
	*/
	$nround = db_round_start();
	print "Round $nround: started ...\n";
	db_round_store($nround,round_compute($nround));

	/*
	**	play the round
	*/
	$nround = db_round_play($nround);
	print "Round $nround: now playing ...\n";
	$round = db_round_restore($nround);

	/*
	**	stop the round
	*/
	$nround = db_round_stop($nround);
	print "Round $nround: stopped.\n";
}
?>
