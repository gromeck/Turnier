<?php
/*
**	a quick hack for the
**
**		1. Goddelauer Badminton Nachturnier
**		2. Goddelauer Badminton Nachturnier
**		3. Goddelauer Badminton Nachturnier
**		4. Goddelauer Badminton Nachturnier
**
**
**	Christian Lorenz
*/
define('PLAYERS_PER_COURT',4);
define('COLOR_STOPWATCH_PRE_BACKGROUND','#ff7c0a');
define('COLOR_STOPWATCH_PRE_FOREGROUND','#000000');
define('COLOR_STOPWATCH_PLAY_BACKGROUND','#000000');
define('COLOR_STOPWATCH_PLAY_FOREGROUND','#3dc514');
define('COLOR_STOPWATCH_STOP_BACKGROUND','#f31920');
define('COLOR_STOPWATCH_STOP_FOREGROUND','#000000');
define('COLWIDTH',550);
define('FONTSIZE',4);
define('FONTSIZEPX',40);
define('SHOWSTRENGTH',0);
define('MAX_PLAYERS',100);
define('TITLE',__TITLE__);
define('IMAGE_BACKGROUND','images/Nachtturnier-bg.png');

define('ROUND_MODE_RANDOM',0);
define('ROUND_MODE_MIXED',1);
define('ROUND_MODE_DOUBLE',2);
define('ROUND_MODE_NORMAL',3);

/*
**	start a session if not in CLI mode
*/
if (!defined('CLI') || (defined('CLI') && !CLI)) {
	session_start();
	define('ADMIN',(@$_SESSION['User'] && @$_SESSION['User']['Uid'] && $_SESSION['User']['Admin']) ? 1 : 0);
}
if (!defined('ADMIN'))
	define('ADMIN',0);

if (!isset($round))
	$round = -1;

function print_player($player,$no_grey_out = 0,$pid_highlight = 0)
{
	global $_SERVER;

	print '<a href="Javascript:clickedPlayer('.$player['Pid'].')"><span class="player_'.(($player['Gender'] == 'M') ? 'male' : 'female').' player_'.(($player['Active'] || $no_grey_out) ? 'active' : 'passive').(($player['Pid'] == $pid_highlight) ? ' player_highlight' : '').(($player['Banned']) ? ' player_banned' : '').'">'.str_replace(' ','&nbsp;',$player['Nick']).'</span></a>';
}

function print_pair($pair,$no_grey_out = 0,$pid_highlight = 0)
{
	print_player($pair[0],$no_grey_out,$pid_highlight);
	html_print_big(' + ');
	print_player($pair[1],$no_grey_out,$pid_highlight);
}

function print_match($match,$sep)
{
	print_pair($match[0]);
	print $sep;
	print_pair($match[1]);
}

/*
**	return the courts
*/
function get_court($adults = 1)
{
	global $court_adults,$court_kids;

	return ($adults) ? $court_adults : $court_kids;
}

/*
**	lookup player
*/
function lookup_player($pid)
{
	$result = db_query("SELECT Name FROM Players WHERE Pid=$pid");
	if ($row = mysql_fetch_assoc($result)) {
		$name = $row[0];
	}
	else
		$name = "unknown";
	mysql_free_result($result);
	return $name;
}

/*
**	return the players
*/
function read_players($adults = 1,$active = 1)
{
	$mplayer = array();
	$result = db_query("SELECT Pid,Name,Active,Matches FROM Players WHERE Class=$adults".(($active) ? " AND Active=$active" : "")." AND Male=1");
	while ($row = mysql_fetch_assoc($result)) {
		$mplayer[] = array(
			'pid' => $row['Pid'],
			'name' => $row['Name'],
			'sex' => 'm',
			'strength' => 0,
			'active' => $row['Active'],
			'matches' => $row['Matches'],
			'adult' => $adults);
	}
	mysql_free_result($result);
	//dump_array("mplayer",$mplayer);

	$fplayer = array();
	$result = db_query("SELECT Pid,Name,Active,Matches FROM Players WHERE Class=$adults".(($active) ? " AND Active=$active" : "")." AND Male=0");
	while ($row = mysql_fetch_assoc($result)) {
		$fplayer[] = array(
			'pid' => $row['Pid'],
			'name' => $row['Name'],
			'sex' => 'f',
			'strength' => 0,
			'active' => $row['Active'],
			'matches' => $row['Matches'],
			'adult' => $adults);
	}
	mysql_free_result($result);
	//dump_array("fplayer",$fplayer);

	return array($mplayer,$fplayer);
}

/*
**	read the pairings done so far
*/
function dump_pairings()
{
	global $_pairings;

	print "<pre><code>";
	for ($row = 0;$row < MAX_PLAYERS;$row++) {
		for ($col = 0;$col < MAX_PLAYERS;$col++) {
			print $_pairings[$row][$col];
		}
		print "\n";
	}
	print "</code></pre>";
}
