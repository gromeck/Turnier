<?php
/*
**	a quick hack for the
**
**		1. Goddelauer Badminton Nachturnier
**
**
**	Christian Lorenz
*/

define('DB_PLAYER_ALL',0);
define('DB_PLAYER_ONLYACTIVE',1);
define('DB_PLAYER_ONLYPASSIVE',2);
define('DB_PLAYER_ONLYMALE',1);
define('DB_PLAYER_ONLYFEMALE',2);
define('DB_PLAYER_ONLYCLASS1',1);
define('DB_PLAYER_ONLYCLASS2',2);

define('DB_ROUND_ALL',0);
define('DB_ROUND_ONLYSTARTED',1);
define('DB_ROUND_ONLYPLAYING',2);
define('DB_ROUND_ONLYSTOPPED',3);

/*
**	=========================================================
**
**	B A S I C   S T U F F
**
**	=========================================================
*/

global $mysqli;

/*
**	do a database query
*/
function db_query($query)
{
	global $mysqli;

    if (!($mysqli = new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE))) {
		//print('db_query: mysqli() failed');
		return false;
	}

//	print "query: $query\n";
	if (!($result = $mysqli->query($query))) {
		print('<br>db_query: mysqli->query('.$query.') failed: '.$mysqli->error);
		return false;
	}
	return $result;
}

/*
**	=========================================================
**
**	S E T T I N G S
**
**	=========================================================
*/

/*
**	load settings
*/
function db_settings_load($settings)
{
	for ($n = 0;$n < count($settings);$n++) {
		if ($result = db_query("SELECT Value FROM Settings WHERE Name='".$settings[$n]['Name']."';")) {
			if (($row = $result->fetch_assoc())) {
				$settings[$n]['Value'] = $row['Value'];
			}
			$result->free();
		}
	}
	return $settings;
}

/*
**	save settings
*/
function db_settings_save($settings)
{
	foreach ($settings as $setting) {
		if (!db_query("INSERT INTO Settings (Name,Value) VALUES ('".$setting['Name']."','".$setting['Value']."')".
			" ON DUPLICATE KEY UPDATE Value='".$setting['Value']."';"))
			return false;
	}
	return true;
}

/*
**	=========================================================
**
**	U S E R
**
**	=========================================================
*/

/*
**	authenticate a user with username and password
*/
function db_user_authenticate($username,$password)
{
	$result = db_query("SELECT * FROM Users WHERE Username='".$username."' AND Password=ENCRYPT('".$password."',Password);");

	if (!$result)
		return false;

	$row = $result->fetch_assoc();
	$result->free();

	return $row;
}


/*
**	=========================================================
**
**	R F I D C O D E
**
**	=========================================================
*/

/*
**	lookup a RFIDcode by its code
*/
function db_rfidcode_lookup_by_rfidcode($rfidcode)
{
	$result = db_query("SELECT * FROM RFIDcodes WHERE RFIDcode='".$rfidcode."';");

	if (!$result)
		return false;

	$row = $result->fetch_assoc();
	$result->free();

	return $row;
}

/*
**	=========================================================
**
**	P L A Y E R
**
**	=========================================================
*/

/*
**	lookup a player by its rfidcode
*/
function db_player_lookup_by_rfidcode($rfidcode)
{
	$result = db_query("SELECT * FROM Players WHERE RFIDcode='".$rfidcode."';");

	if (!$result)
		return false;

	$row = $result->fetch_assoc();
	$result->free();

	return $row;
}

/*
**	lookup a player by its pid
*/
function db_player_lookup_by_pid($pid)
{
	$result = db_query("SELECT * FROM Players WHERE Pid='".$pid."';");

	if (!$result)
		return false;

	$row = $result->fetch_assoc();
	$result->free();

	return $row;
}

/*
**	create a new player
*/
function db_player_create($lastname,$firstname,$gender,$class)
{
	global $mysqli;

	$lastname = trim($lastname);
	$firstname = trim($firstname);
	$gender = (substr(strtoupper($gender),0,1) == 'M') ? 'M' : 'F';
	$class = (substr($class,0,1) == '1') ? '1' : '2';
	if (!db_query("INSERT INTO Players (Lastname,Firstname,Gender,Class,CreationDate) ".
		"VALUES('".$lastname."','".$firstname."','".$gender."','".$class."','".date('Y-m-d H:i:s')."');")) {
		return array(false,$mysqli->error);
	}
	$pid = $mysqli->insert_id;
	list ($rc,$dbmsg) = db_player_computenicks();

	return array($pid,false);
}

/*
**	update a player
*/
function db_player_update($pid,$lastname,$firstname,$gender,$class)
{
	global $mysqli;

	$lastname = trim($lastname);
	$firstname = trim($firstname);
	$gender = (substr(strtoupper($gender),0,1) == 'M') ? 'M' : 'F';
	$class = (substr($class,0,1) == '1') ? '1' : '2';
	if (!db_query("UPDATE Players SET Lastname='".$lastname."',Firstname='".$firstname."',Gender='".$gender."',Class='".$class."' WHERE Pid='".$pid."';"))
		return array(false,$mysqli->error);
	return db_player_computenicks();
}

/*
**	link a players wrist (RFIDcode)
*/
function db_player_link_rfidcode($pid,$rfidcode)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET RFIDcode='".$rfidcode."' WHERE Pid='".$pid."';"))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	unlink a wrist (RFIDcode) by removing all links
*/
function db_player_unlink_rfidcode($rfidcode)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET RFIDcode='' WHERE RFIDcode='".$rfidcode."';"))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	update the match counter for the given player
*/
function db_player_update_matches($player)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET Matches=Matches+1 WHERE Pid=".$player['Pid']))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	delete a player
*/
function db_player_delete($pid)
{
	global $mysqli;

	if (!db_query("DELETE FROM Players WHERE Pid=".$pid))
		return array(false,$mysqli->error);
	return db_player_computenicks();
}

/*
**	ban a player
*/
function db_player_ban($pid,$ban)
{
	global $mysqli;

	if ($ban)
		db_player_activate($pid,0);
	if (!db_query("UPDATE Players SET Banned=".(($ban) ? 1 : 0).",BannedChangeDate='".date('Y-m-d H:i:s')."' WHERE Pid=$pid"))
		return array(false,$mysqli->error);

	return array(true,true);
}

/*
**	activate/deactivate a player
*/
function db_player_activate($pid,$active)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET Active=".(($active) ? 1 : 0).",ActiveChangeDate='".date('Y-m-d H:i:s')."' WHERE Pid=$pid AND Banned=0"))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	toggle the activation of a player
*/
function db_player_activate_toggle($pid)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET Active=NOT Active,ActiveChangeDate='".date('Y-m-d H:i:s')."' WHERE Pid=$pid AND Banned=0"))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	activate/deactivate all players
*/
function db_player_activate_all($active)
{
	global $mysqli;

	if (!db_query("UPDATE Players SET Active=".(($active) ? 1 : 0).",ActiveChangeDate='".date('Y-m-d H:i:s')."' WHERE Banned=0"))
		return array(false,$mysqli->error);
	return array(true,true);
}

/*
**	lookup the complete list of players using filters
*/
function db_player_list($which_active,$which_gender,$which_class,$order = "Nick ASC",$only_matches = 0)
{
	global $mysqli;

	$where = '';

	switch ($which_active) {
		case DB_PLAYER_ONLYACTIVE:
			$where .= " AND Active=1 AND Banned=0";
			break;
		case DB_PLAYER_ONLYPASSIVE:
			$where .= " AND Active=0 AND Banned=0";
			break;
		default:
			break;
	}
	switch ($which_gender) {
		case DB_PLAYER_ONLYMALE:
			$where .= " AND Gender='M' AND Banned=0";
			break;
		case DB_PLAYER_ONLYFEMALE:
			$where .= " AND Gender='F' AND Banned=0";
			break;
		default:
			break;
	}
	switch ($which_class) {
		case DB_PLAYER_ONLYCLASS1:
			$where .= " AND Class=1 AND Banned=0";
			break;
		case DB_PLAYER_ONLYCLASS2:
			$where .= " AND Class=2 AND Banned=0";
			break;
		default:
			break;
	}
	if ($only_matches) {
		$where .= " AND Matches>0";
	}

	if (!($result = db_query("SELECT * FROM Players".(empty($where) ? '' : ' WHERE 1 ').$where." ORDER BY $order;")))
		return false;

	$players = array();
	while ($row = $result->fetch_assoc()) {
		$players[] = $row;
	}

	$result->free();
	return $players;
}

/*
**	(re)compute the players nicknames out of firstname and lastname
*/
function db_player_computenicks()
{
	global $mysqli;

	/*
	**	load the complete player list
	*/
	$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL);

	/*
	**	1. pass: use the firstname as the nick
	*/
	for ($n = 0;$n < count($players);$n++) {
		$players[$n]['newNick'] = $players[$n]['Firstname'];
	}

	/*
	**	2. pass: solve duplicate nicks
	*/
	foreach ($players as $idxA => $playerA) {
		foreach ($players as $idxB => $playerB) {
			if ($playerA['Pid'] != $playerB['Pid']) {
				/*
				**	check if we got a duplicate
				*/
				if ($playerA['newNick'] == $playerB['newNick']) {
				//|| !strncasecmp($playerA['newNick'],$playerB['newNick'],min(strlen($playerA['newNick']),strlen($playerB['newNick'])))) {
					/*
					**	ok, these two players have the same nick so far
					**
					**	find the length of the common part of the lastnames
					*/
					$common = commonstrlen($playerA['Lastname'],$playerB['Lastname']);
					if (strlen($players[$idxA]['newNick']) < strlen($playerA['Firstname'].' '.substr($playerA['Lastname'],0,$common + 1)))
						$players[$idxA]['newNick'] = $playerA['Firstname'].' '.substr($playerA['Lastname'],0,$common + 1);
					if (strlen($players[$idxB]['newNick']) < strlen($playerB['Firstname'].' '.substr($playerB['Lastname'],0,$common + 1)))
						$players[$idxB]['newNick'] = $playerB['Firstname'].' '.substr($playerB['Lastname'],0,$common + 1);
				}
			}
		}
	}

	/*
	**	3. pass: write back the nicks into database where we have a new nickname
	*/
	foreach ($players as $player) {
		if ($player['newNick'] != $player['Firstname']) {
			if ($player['newNick'] != $player['Firstname'].' '.$player['Lastname'])
				$player['newNick'] .= '.';
		}
		if ($player['Nick'] != $player['newNick']) {
			if (!db_query("UPDATE Players SET Nick='".$player['newNick']."' WHERE Pid=".$player['Pid']))
				return array(false,$mysqli->error);
		}
	}
	return array(true,true);
}

/*
**	retrieve a list of all completed rounds of the given player
*/
function db_player_matches($pid)
{
	if (!($result = db_query("SELECT * FROM Matches,Rounds ".
			"WHERE Matches.Round=Rounds.Round AND State=".DB_ROUND_ONLYSTOPPED." AND ".
			"(PidA=".$pid." OR PidB=".$pid." OR PidC=".$pid." OR PidD=".$pid.") ORDER BY Rounds.Round ASC;")))
		return false;

	$matches = array();
	while ($row = $result->fetch_assoc()) {
		$matches[] = $row;
	}

	$result->free();
	return $matches;
}

/*
**	retrieve a list of all pairings of the given player
*/
function db_player_pairings($pid)
{
	if (!($result = db_query("SELECT * FROM Pairings WHERE PidA=".$pid." OR PidB=".$pid." ORDER BY Count DESC;")))
		return false;

	$pairings = array();
	while ($row = $result->fetch_assoc()) {
		$pairings[] = $row;
	}

	$result->free();
	return $pairings;
}

/*
**	=========================================================
**
**	P A I R I N G
**
**	=========================================================
*/

/*
**	load the complete pairing history table
*/
function db_pairing_load()
{
	$pairings = array();
	for ($row = 0;$row < MAX_PLAYERS;$row++)
		for ($col = 0;$col < MAX_PLAYERS;$col++)
			$pairings[$row][$col] = 0;

	if (($result = db_query("SELECT PidA,PidB,Count FROM Pairings;"))) {
		while ($row = $result->fetch_assoc()) {
			$pairings[$row['PidA']][$row['PidB']] = $row['Count'];
			$pairings[$row['PidB']][$row['PidA']] = $row['Count'];
		}
		return $pairings;
	}
	return false;
}

/*
**	update the pairing counter for the given pairing
**
**	NOTE: the smaller Pid is always first
*/
function db_pairing_update($pair)
{
	db_player_update_matches($pair[0]);
	db_player_update_matches($pair[1]);

	db_query("INSERT INTO Pairings (PidA,PidB,Count) ".
		" VALUES(".
			min($pair[0]['Pid'],$pair[1]['Pid']).",".
			max($pair[0]['Pid'],$pair[1]['Pid']).",1)".
		" ON DUPLICATE KEY UPDATE Count=Count+1");
}

/*
**	load the complete pairing history table
*/
function db_pairing_list()
{
	if (!($result = db_query("SELECT * FROM Pairings ORDER BY Count DESC;")))
		return false;

	$pairings = array();
	while ($row = $result->fetch_assoc()) {
		$pairings[] = $row;
	}

	$result->free();
	return $pairings;
}

/*
**	=========================================================
**
**	R O U N D S
**
**	=========================================================
*/

/*
**	retrieve the complete list of rounds
*/
function db_round_list($which)
{
	$where = '';

	switch ($which) {
		case DB_ROUND_ONLYSTARTED:
		case DB_ROUND_ONLYPLAYING:
		case DB_ROUND_ONLYSTOPPED:
			$where .= " AND State=".$which;
			break;
		default:
			break;
	}
	
	if (!($result = db_query("SELECT * FROM Rounds ".(empty($where) ? '' : ' WHERE 1 ').$where.";")))
		return false;

	$rounds = array();
	while ($row = $result->fetch_assoc()) {
		$rounds[] = $row;
	}

	$result->free();
	return $rounds;
}

/*
**	get the last stopped round number
*/
function db_round_last_stopped()
{
	if ($result = db_query("SELECT Round FROM Rounds WHERE State=".DB_ROUND_ONLYSTOPPED." ORDER BY Round DESC LIMIT 1;")) {
		/*
		**	there is an open round, use it!
		*/
		if ($row = $result->fetch_assoc())
			return $row['Round'];
	}
	return 0;
}

/*
**	return the highest Round number together with the state
*/
function db_round_last()
{
	if ($result = db_query("SELECT Round,State FROM Rounds ORDER BY Round DESC LIMIT 1;")) {
		if ($row = $result->fetch_assoc()) {
			switch ($row['State']) {
				case DB_ROUND_ONLYSTARTED:
					$state = 'started';
					break;
				case DB_ROUND_ONLYPLAYING:
					$state = 'playing';
					break;
				case DB_ROUND_ONLYSTOPPED:
					$state = 'stopped';
					break;
				default:
					$state = 'unknown';
			}
			return array( 'Round' => $row['Round'], 'State' => $state );
		}
	}
	return false;
}

/*
**	start a new round in the database with the status open
*/
function db_round_start()
{
	$nround = 1;

	if ($result = db_query("SELECT Round FROM Rounds WHERE State=".DB_ROUND_ONLYPLAYING.";")) {
		/*
		**	there is an already playing round, so will fail for now!
		*/
		if ($row = $result->fetch_assoc())
			return false;
	}
	if ($result = db_query("SELECT Round FROM Rounds WHERE State=".DB_ROUND_ONLYSTARTED.";")) {
		/*
		**	there is a started round, use it!
		*/
		if ($row = $result->fetch_assoc())
			return $row['Round'];
	}
	if ($result = db_query("SELECT Round FROM Rounds WHERE State=".DB_ROUND_ONLYSTOPPED." ORDER BY Round DESC LIMIT 1;")) {
		/*
		**	so, read the highest round number
		*/
		if ($row = $result->fetch_assoc())
			$nround = $row['Round'] + 1;
	}

	/*
	**	now, start a new round
	*/
	if (!($result = db_query("INSERT INTO Rounds (Round,State,StartDate) VALUES('".$nround."','".DB_ROUND_ONLYSTARTED."','".date('Y-m-d H:i:s')."')")))
		return false;
	return $nround;
}

/*
**	play the current round
**
**	NOTE: this also updateds the pairing and players match counter
*/
function db_round_play($nround)
{
	if (!db_query("UPDATE Rounds SET State=".DB_ROUND_ONLYPLAYING.", PlayDate='".date('Y-m-d H:i:s')."' WHERE Round=".$nround." AND State=".DB_ROUND_ONLYSTARTED.";")) {
		return false;
	}

	/*
	**	load the round and update the pairing table and the players match counter
	*/
	if ($round = db_round_restore($nround)) {
		for ($class = 1;$class <= 2;$class++) {
			for ($court = 0;$court < $round[$class]['ncourts'];$court++) {
				db_pairing_update($round[$class][$court][0]);
				db_pairing_update($round[$class][$court][1]);
			}
		}
	}

	return $nround;
}

/*
**	start a new round
*/
function db_round_stop($nround)
{
	if (!db_query("UPDATE Rounds SET State=".DB_ROUND_ONLYSTOPPED.", StopDate='".date('Y-m-d H:i:s')."' WHERE Round=".$nround." AND State=".DB_ROUND_ONLYPLAYING.";")) {
		return false;
	}
	return $nround;
}

/*
**	remove the current round as long it is in the state STARTED
**
**	NOTE: the Pairing Counters and the Match Counters habe to be corrected
*/
function db_round_clear($nround)
{
	if (!($rounds = db_round_list(DB_ROUND_ONLYSTARTED)))
		return false;
	if ($rounds[0]['Round'] != $nround)
		return false;

	/*
	**	clear the data for this round, might be recomputed
	*/
	if (db_query("DELETE FROM Rounds WHERE Round=$nround AND State=".DB_ROUND_ONLYSTARTED.";")) {
		db_query("DELETE FROM Matches WHERE Round=$nround;");
		db_query("DELETE FROM Pausers WHERE Round=$nround;");
	}

	/*
	**	as this round was already increasing some counters,
	**	we will have to recompute these
	*/
	return db_round_rebuild_all();
}

/*
**	rebuild all counters (pairings and matches from the round data)
*/
function db_round_rebuild_all()
{
	/*
	**	clear the counters
	*/
	db_query('UPDATE Players SET Matches=0;');
	db_query('DELETE FROM Pairings;');

	/*
	**	get all rounds
	*/
	if (!($rounds = db_round_list(DB_ROUND_ONLYSTOPPED)))
		return false;

	/*
	**	load the round and update the pairing table and the players match counter
	*/
	foreach ($rounds as $round) {
		if ($round = db_round_restore($round['Round'])) {
			for ($class = 1;$class <= 2;$class++) {
				for ($court = 0;$court < $round[$class]['ncourts'];$court++) {
					db_pairing_update($round[$class][$court][0]);
					db_pairing_update($round[$class][$court][1]);
				}
			}
		}
	}
	return true;
}

/*
**	remove all round data
*/
function db_round_clear_all()
{
	db_query('UPDATE Players SET Matches=0;');
	db_query('DELETE FROM Pairings;');
	db_query('DELETE FROM Matches;');
	db_query('DELETE FROM Pausers;');
	db_query('DELETE FROM Rounds;');

	return true;
}

/*
**	write a complete round to the database
*/
function db_round_store($nround,$round)
{
	/*
	**	clear the data for this round, might be recomputed
	*/
	db_query("DELETE FROM Matches WHERE Round=$nround;");
	db_query("DELETE FROM Pausers WHERE Round=$nround;");

	for ($class = 1;$class <= 2;$class++) {
		for ($court = 0;$court < $round[$class]['ncourts'];$court++)
				db_query("INSERT INTO Matches (Round,Class,Court,PidA,PidB,PidC,PidD) ".
					" VALUES(".$nround.",".$class.",".$court.",".
						$round[$class][$court][0][0]['Pid'].",".
						$round[$class][$court][0][1]['Pid'].",".
						$round[$class][$court][1][0]['Pid'].",".
						$round[$class][$court][1][1]['Pid'].");");
	}
	for ($class = 1;$class <= 2;$class++) {
		for ($pauser = 0;$pauser < $round[$class]['npausers'];$pauser++) {
			db_query("INSERT INTO Pausers (Round,Class,Pid) Values(".$nround.",".$class.",".$round[$class]['pausers'][$pauser]['Pid'].");");
		}
	}
}

/*
**	read a complete round from the database
*/
function db_round_restore($nround)
{
	$round = array();
	for ($class = 1;$class <= 2;$class++) {
		$round[$class]['nplayers'] = 0;
		$round[$class]['ncourts'] = 0;
		$round[$class]['npausers'] = 0;
	}

	/*
	**	read the round data
	*/
	if ($result = db_query("SELECT * FROM Matches WHERE Round=$nround;")) {
		while (($row = $result->fetch_assoc())) {
			$round[$row['Class']][$row['Court']][0][0] = db_player_lookup_by_pid($row['PidA']);
			$round[$row['Class']][$row['Court']][0][1] = db_player_lookup_by_pid($row['PidB']);
			$round[$row['Class']][$row['Court']][1][0] = db_player_lookup_by_pid($row['PidC']);
			$round[$row['Class']][$row['Court']][1][1] = db_player_lookup_by_pid($row['PidD']);
			$round[$row['Class']]['nplayers'] += 4;
			$round[$row['Class']]['ncourts'] += 1;
		}
		$result->free();
	}

	/*
	**	read the pausers
	*/
	if ($result = db_query("SELECT * FROM Pausers WHERE Round=$nround;")) {
		while (($row = $result->fetch_assoc())) {
			$round[$row['Class']]['pausers'][] = db_player_lookup_by_pid($row['Pid']);
			$round[$row['Class']]['npausers'] += 1;
		}
		$result->free();
	}

	return $round;
}

/*
**	=========================================================
**
**	M A T C H E S
**
**	=========================================================
*/

/*
**	retrieve a list of all completed rounds of the given player
*/
function db_match_list()
{
	if (!($result = db_query("SELECT * FROM Matches,Rounds ".
			"WHERE Matches.Round=Rounds.Round AND State=".DB_ROUND_ONLYSTOPPED." ORDER BY Rounds.Round ASC;")))
		return false;

	$matches = array();
	while ($row = $result->fetch_assoc()) {
		$matches[] = $row;
	}

	$result->free();
	return $matches;
}

?>
