<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to play a round
**
**	GET params:
**		round		start: start a new round
**					play: play the current round
**					stop: stop the current round
**					clear: clear the current round
**		nround		round to play, stop or to clear
**
**	POST params:
**		-
*/
if (!ADMIN) { print "Not admin!"; exit(); };
/*
**	Christian Lorenz
*/
if ($_GET) {
	//print_r($_GET);
	if (ADMIN && @$_GET['round'] == 'start') {
		/*
		**	start a new round, by computing the
		**	matches, and store it to the database
		*/
		$nround = db_round_start();
		//print "round started is $nround";
		db_round_store($nround,round_compute($nround));
		$redirect = '?page='.@$_GET['page'];
	}
	if (ADMIN && @$_GET['round'] == 'clear') {
		/*
		**	start a new round, by computing the
		**	matches, and store it to the database
		*/
		db_round_clear(@$_GET['nround']);
		//print "round cleared is $nround";
		$redirect = '?page='.@$_GET['page'];
	}
	if (ADMIN && @$_GET['round'] == 'play' && @$_GET['nround'] > 0) {
		/*
		**	play the given round, by stetting rounds
		**	state and storing all pairings in the
		**	database
		*/
		$nround = db_round_play(@$_GET['nround']);
		$round = db_round_restore($nround);
		//dump_array('round',$round);
		//exit();
		//print "round playing is $nround";
		$redirect = '?page='.@$_GET['page'];
	}
	if (ADMIN && @$_GET['round'] == 'stop' && @$_GET['nround'] > 0) {
		/*
		**	stop the given round
		*/
		$nround = db_round_stop(@$_GET['nround']);
		//print "round stopped is $nround";
		$redirect = '?page='.@$_GET['page'];
	}
}

?>
<script language="JavaScript">
function clickedPlayer(pid)
{
}

<?php if (@$redirect) { ?>
document.location = '<?php print $redirect ?>';
<?php } ?>
</script>
<center>
<?php
if (@$redirect)
	exit();

/*
**	print the given round
*/
function print_round($nround,$round)
{
	/*
	**	print the courts
	*/
	$abscourt = 1;
	//dump_array('round',$round);
	for ($class = 1;$class <= 2;$class++) {
		//html_separator();

		if ($round[$class]['ncourts'] > 0) {
			/*
			**	we have at least players for one court
			*/
			?>
			<table class="table-round" cellspacing=0 cellpadding=0>
				<tr class="tr-head">
					<td class="td-head" align=center><?php html_print_big('Platz','bigtextwhite'); ?></td>
					<td class="td-head" colspan=3><center><?php
						print print('Klasse: '.lookup_class($class));
					?></center></td>
				</tr>
			<?php for ($court = 0;$court < $round[$class]['ncourts'];$court++,$abscourt++) { ?>
				<tr class="tr-<?php print ($court % 2) ? 'odd' : 'even' ?>">
					<td class="table-round-col-court"><?php print($abscourt); ?></td>
					<td class="table-round-col-left"><?php print_pair($round[$class][$court][0],1); ?></td>
					<td class="table-round-col-vs">:</td>
					<td class="table-round-col-right"><?php print_pair($round[$class][$court][1],1); ?></td>
				</tr><?php
			}
			?>
			</table>
			<?php
		}
	}

	/*
	**	print the pausers
	*/
	$comma = 0;
	html_separator();
	?>
	<table class="table-pause" cellspacing=0 cellpadding=0>
		<tr class="">
			<td class="" align=center><?php
	print('Pause: ');
	for ($class = 1;$class <= 2;$class++) {
		for ($pauser = 0;$pauser < $round[$class]['npausers'];$pauser++) {
			if (!$comma)
				$comma = 1;
			else
				print(', ');
			print_player($round[$class]['pausers'][$pauser]);
		}
	}
	?>
			</td>
		</tr>
	</table>
	<?php
}

/*
**	findout the current round
*/
if ($rounds = db_round_list(DB_ROUND_ONLYPLAYING)) {
	/*
	**	there is an started & playing round, so display
	**	the stopwatch ...
	*/
	//dump_array("rounds",$rounds);
	$nround = $rounds[0]['Round'];
	$round = db_round_restore($nround);
	$playdate = strtotime($rounds[0]['PlayDate']);
	$timeleft = $playdate + round(__DURATIONPREMATCH__ * 60) + round(__DURATIONMATCH__ * 60) - time();
	if ($timeleft > 0) {
		/*
		**	still in time
		*/
		?>
		<script>
		stopwatch_init(<?php print $playdate ?>);
		</script>
		<?php
	}
	else if (ADMIN) {
			/*
			**	time is over, so automatically stop the round
			*/
		?>
		<script>
		document.location = '?page=playround&round=stop&nround=<?php print $nround ?>';
		</script>
		<?php
	}
	//dump_array("rounds",$rounds);
	if (ADMIN) {
		if ($timeleft < 0)
			html_print_big('Zeit ist abgelaufen!');
		html_button_href('Spielrunde '.$nround.' abbrechen/stoppen','?page=playround&round=stop&nround='.$nround,0,HTML_BUTTON_BLUE);
		html_separator();
	}
	print_round($nround,$round);
}
else if ($rounds = db_round_list(DB_ROUND_ONLYSTARTED)) {
	/*
	**	there is a started round, so display it, and offer the user to play it
	*/
	$nround = $rounds[0]['Round'];
	$round = db_round_restore($nround);
	//dump_array("rounds",$rounds);
	if (ADMIN) {
		html_button_href('Spielrunde '.$nround.' jetzt spielen','?page=playround&round=play&nround='.$nround,0,HTML_BUTTON_RED);
		html_button_href('Spielrunde '.$nround.' löschen','?page=playround&round=clear&nround='.$nround,0,HTML_BUTTON_BLUE);
	}
	html_separator(1);
	print_round($nround,$round);
}
else {
	/*
	**	we have only closed rounds, so offer the user to compute a new one
	*/
	html_separator(1);
	html_print_big('Keine laufende Spielrunde vorhanden.');
	if (ADMIN) {
		html_separator();
		html_button_href('Neue Spielrunde starten','?page=playround&round=start',0,HTML_BUTTON_RED);
	}
	html_separator(1);
	html_print_big('Letzte Check-Ins/-Outs:');
	html_separator();
	?>
	<iframe class=check-in-history name=lastcheckins id=lastcheckins
		src="frames/player-check-in-history.php"
		width=600 height=400
		marginwidth=0 marginheight=0 border=0 frameborder=0 framespacing=0 scrolling=no noresize></iframe>
	<?php
}

?>
</center>
