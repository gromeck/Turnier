<?php
/*
**	function to compute badminton rounds in a smart way
*/

/*
**	compare function to sort the player arrays
*/
$_pid = 0;
$_pairings = array();

function compare_player_pairings($playerA,$playerB)
{
	global $_pid,$_pairings;

	return @$_pairings[$_pid][$playerA['Pid']] - @$_pairings[$_pid][$playerB['Pid']];
}

/*
**	compute a complete round and store it in the
**	database
*/
function round_compute($nround)
{
	global $_pid,$_pairings;

	/*
	**	compute the number of active players
	*/
	$round = array();
	for ($class = 1;$class <= 2;$class++) {
		$round[$class]['players'] = db_player_list(DB_PLAYER_ONLYACTIVE,DB_PLAYER_ALL,$class,'Matches ASC');
		$round[$class]['nplayers'] = count($round[$class]['players']);
	}

	/*
	**	compute the maximum number of courts to use
	*/
	//print __COURTS_CLASS1_MIN__." ".__COURTS_CLASS2_MIN__." ".__COURTS_MAX__;
	if (__COURTS_CLASS1_MIN__ + __COURTS_CLASS2_MIN__ > __COURTS_MAX__) {
		html_print_fail('__COURTS_CLASS1_MIN__ + __COURTS_CLASS2_MIN__ &gt; __COURTS_MAX__');
		exit(1);
	}

	/*
	**	compute the number of courts which could be filled
	*/
	for ($class = 1;$class <= 2;$class++) {
		$round[$class]['ncourts'] = floor($round[$class]['nplayers'] / PLAYERS_PER_COURT);
	}

	/*
	**	compute the number of courts to fit into the court limits
	**
	**	TODO: use also the $class variable and get rid of the hardcoded classes
	*/
	while ($round[1]['ncourts'] + $round[2]['ncourts'] > __COURTS_MAX__) {
		/*
		**	too many players
		*/
		if ($round[1]['ncourts'] + $round[2]['ncourts'] > __COURTS_MAX__ && $round[1]['ncourts'] > __COURTS_CLASS1_MIN__) {
			$round[1]['ncourts']--;
		}
		if ($round[1]['ncourts'] + $round[2]['ncourts'] > __COURTS_MAX__ && $round[2]['ncourts'] > __COURTS_CLASS2_MIN__) {
			$round[2]['ncourts']--;
		}
	}

	/*
	**	strip down the arrays to the number of players on the courts
	*/
	for ($class = 1;$class <= 2;$class++) {
		$round[$class]['nplayers'] = $round[$class]['ncourts'] * PLAYERS_PER_COURT;
		$round[$class]['pausers'] = array_slice($round[$class]['players'],$round[$class]['nplayers']);
		$round[$class]['players'] = array_slice($round[$class]['players'],0,$round[$class]['nplayers']);
		$round[$class]['npausers'] = count($round[$class]['pausers']);
	}

	if (0)
	for ($class = 1;$class <= 2;$class++) {
		?><code><pre><?php
		print "round[$class][ncourts]=".$round[$class]['ncourts']."<br>\n";
		print "round[$class][nplayers]=".$round[$class]['nplayers']."<br>\n";
		print "round[$class][npausers]=".$round[$class]['npausers']."<br>\n";
		print "round[$class][players]="; print_r($round[$class]['players']); print "<br>\n";
		print "round[$class][pausers]="; print_r($round[$class]['pausers']); print "<br>\n";
		?></pre></code><?php
	}

	/*
	**	compute the pairings for each class
	*/
	$_pairings = db_pairing_load();

	/*
	**  pair the players so that no pair is double
	**  in the history of all pairings
	*/ 
	for ($class = 1;$class <= 2;$class++) {
		$round[$class]['players'] = shift_array($round[$class]['players'],$nround);
		for ($pair = 0;$pair < $round[$class]['nplayers'] / 2;$pair++) {
			$p1 = array_shift($round[$class]['players']);
			/*
			**  sort the players by the ascending
			**  number of matches together with p1
			*/
			$_pid = $p1['Pid'];
			usort($round[$class]['players'],'compare_player_pairings');

			$p2 = array_shift($round[$class]['players']);
			$round[$class][$pair / 2][$pair % 2] = array($p1,$p2);
		}
	}

	/*
	**	write the round to the database and make all necessary updates
	*/
	db_round_store($nround,$round);
	//TODO
	return db_round_restore($nround);

	return $round;
}

/*
**	simulate the given number of rounds
*/
function round_simulate($nrounds)
{
	for ($n = 0;$n < $nrounds;$n++) {
		/*
		**	start a new round
		*/
		$nround = db_round_start();
		//print "Round $nround: started ...\n";
		db_round_store($nround,round_compute($nround));

		/*
		**	play the round
		*/
		$nround = db_round_play($nround);
		//print "Round $nround: now playing ...\n";
		$round = db_round_restore($nround);

		/*
		**	stop the round
		*/
		$nround = db_round_stop($nround);
		//print "Round $nround: stopped.\n";
	}
}
