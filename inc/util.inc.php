<?php
/*
**	utility functions
**
**	Christian Lorenz
*/

/*
**  add a parameter to an URL
*/
function addparamURL($url,$param,$value)
{
	$value = urlencode($value);
	$elements = parse_url($url);
	if (is_array($elements)) {
		/*
		**  split up the query string and
		**  add/replace the desired parameter
		*/
		$query = explode("&",$elements["query"]);
		for ($n = 0;$n < count($query);$n++) {
			if (!strncmp($query[$n],"$param=",strlen("$param="))) {
				/*
				**  replace 'em
				*/
				$query[$n] = "$param=$value";
				break;
			}
		}
		if ($n >= count($query)) {
			/*
			**  did't found the parameter
			**  so add it as new
			*/
			$query[$n] = "$param=$value";
		}
		$elements["query"] = implode("&",$query);

		/*
		**  setup the URL again
		*/
		$url = "";
		if (!empty($elements["scheme"]))
			$url .= $elements["scheme"]."://";
		$userpass = "";
		if (!empty($elements["user"]))
			$userpass .= $elements["user"];
		if (!empty($elements["pass"]))
			$userpass .= ":".$elements["pass"];
		if (!empty($userpass))
			$userpass .= "@";
		if (!empty($elements["host"]))
			$url .= $userpass.$elements["host"];
		if (!empty($elements["port"]))
			$url .= ":".$elements["port"];
		if (!empty($elements["path"]))
			$url .= $elements["path"];
		if (!empty($elements["query"]))
			$url .= "?".$elements["query"];
		if (!empty($elements["fragment"]))
			$url .= "#".$elements["fragment"];
	}
	return $url;
}

function dump_array($title,$array)
{
	print "<table border=1 cellspacing=0 cellpadding=3 noshade style=\"border:1px solid black;\">\n";
	print "  <tr bgcolor=#000000><td colspan=2><font color=#ffffff><b>".$title."</b></font></td></tr>\n";
	reset($array);
	while (list($key,$value) = each($array)) {
		print "  <tr><td align=left valign=top><b><code>$key</code></b></td><td>";
		if (is_array($value))
			dump_array("SubArray <code>".$key."</code>",$value);
		else
			print "<code>".htmlspecialchars($value)."</code>&nbsp";
		print "</td></tr>\n";
	}
	print "</table>\n";
}

/*
**	shift an array in dependence of n.
**
**	a value of 0 will return the array in the same
**	order
*/
function shift_array($a,$round)
{
	if (count($a)) {
		$round %= count($a);
		for ($n = 0;$n < $round;$n++) {
			$item = array_pop($a);
			array_unshift($a,$item);
		}
	}
	return $a;
}

function lookup_class($players_class)
{
	switch ($players_class) {
		case 1:	return "Junioren";
		case 2:	return "Senioren";
	}
	return "???";
}

function lookup_round_state($round_state)
{
	switch ($round_state) {
		case DB_ROUND_ONLYSTARTED:	return "Gestartet";
		case DB_ROUND_ONLYPLAYING:	return "Spielend";
		case DB_ROUND_ONLYSTOPPED:	return "Gestoppt";
	}
	return "???";
}

/*
**	permute an array in dependence of n.
**
**	a value of 0 will return the array in the same
**	order
*/
function permute_array($a,$round)
{
	srand($round);
	shuffle($a);
	return $a;
}

/*
**	compute the length of the common part from two strings
*/
function commonstrlen($a,$b)
{
	for ($len = 0;$len < min(strlen($a),strlen($b));$len++)
		if (substr($a,0,$len) != substr($b,0,$len))
			return $len - 1;
	return -1;
}

/*
**	print a date & time (database) time string in a more readable form
*/
function timestamp2datetime($timestamp)
{
	if ($t = strtotime($timestamp))
		return strftime('%d.%m.%Y  %H:%M',$t);
	return '';
}

/*
**	print a date & time (database) time string in a more readable form
*/
function timestamp2relative($timestamp)
{
	if ($t = strtotime($timestamp)) {
		$delta = time() - $t;
		if ($delta < 0) {
			/*
			**	timestamp in the future
			*/
			$prefix = "in";
			$delta = -$delta;
		}
		else {
			/*
			**	timestamp in the future
			*/
			$prefix = "vor";
		}

		if ($delta < 60)
			return "$prefix $delta Sekunde".(($delta != 1) ? 'n' : '');
		$delta = round($delta / 60);
		if ($delta < 60)
			return "$prefix $delta Minute".(($delta != 1) ? 'n' : '');
		$delta = round($delta / 60);
		if ($delta < 24)
			return "$prefix $delta Stunde".(($delta != 1) ? 'n' : '');
		$delta = round($delta / 24);
		if ($delta < 31)
			return "$prefix $delta Tage".(($delta != 1) ? 'n' : '');
		$delta = round($delta / 31);
		return "$prefix $delta Monate".(($delta != 1) ? 'n' : '');
	}
	return '';
}
