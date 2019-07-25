<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to display round statistics
**
**	GET params:
**		-
**
**	POST params:
**		-
*/
?>
<script language="JavaScript">
function clickedPlayer(pid)
{
	document.location = '?page=playerinfo&pid=' + pid;
}
</script>
<?php

/*
**	print a list of all matches of this player
*/
function print_matches($matches)
{
	?>
	<center>
	<table class="table-round" cellspacing=0 cellpadding=0>
		<?php $n = 0; $prevmatch = false; foreach ($matches as $match) {
		if (!$prevmatch || $prevmatch['Round'] != $match['Round']) {
			$abscourt = 0;
		?>
		<tr class="tr-subhead">
			<td class="table-round-col-round" colspan=5><?php html_print_big('Runde '.$match['Round'].' &middot; Zeit: '.timestamp2datetime($match['PlayDate']),'bigtextwhite'); ?></td>
		</tr>
		<tr class="tr-head">
			<td class="td-head" align=center><?php html_print_big('Platz','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Klasse','bigtextwhite'); ?></td>
			<td class="td-head" colspan=3><center><?php print html_print_big('Paarung','bigtextwhite'); ?></center></td>
		</tr>
		<?php
		}
		?>
		<tr class="tr-<?php print ($n++ % 2) ? 'odd' : 'even' ?>">
			<td class="table-round-col-court"><?php html_print_big(++$abscourt); ?></td>
			<td class="table-round-col-class"><?php html_print_big(lookup_class($match['Class'])); ?></td>
			<td class="table-round-col-left"><?php print_pair(array(db_player_lookup_by_pid($match['PidA']),db_player_lookup_by_pid($match['PidB'])),1); ?></td>
			<td class="table-round-col-vs"><span class=bigtext>:</span></td>
			<td class="table-round-col-left"><?php print_pair(array(db_player_lookup_by_pid($match['PidC']),db_player_lookup_by_pid($match['PidD'])),1); ?></td>
		</tr><?php
			$prevmatch = $match;
		}
		?>
	</table>
	</center>
	<?php
}

/*
**	print a list of all pairings of this player
*/
function print_pairings($pairings)
{
	?>
	<center>
	<table class="table-round" cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<td class="td-head" align=center><?php html_print_big('Paarung','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Anzahl','bigtextwhite'); ?></td>
		</tr>
		<?php $n = 0; foreach ($pairings as $pairing) { ?>
		<tr class="tr-<?php print ($n++ % 2) ? 'odd' : 'even' ?>">
			<td class="table-round-col-left"><?php print_pair(array(db_player_lookup_by_pid($pairing['PidA']),db_player_lookup_by_pid($pairing['PidB'])),1); ?></td>
			<td class="table-round-col-count"><?php html_print_big($pairing['Count']); ?></td>
		</tr><?php
		}
		?>
	</table>
	</center>
	<?php
}

	html_separator(1);

	/*
	**	retrieve all matches
	*/
	$matches = db_match_list();
	//dump_array('matches',$matches);
	if ($matches) {
		print_matches($matches);
		html_separator();
	}
	else
		html_print_fail('Keine Runden gefunden.');

	/*
	**	retrieve all pairings
	*/
	$pairings = db_pairing_list();
	//dump_array('pairings',$pairings);
	if ($matches) {
		print_pairings($pairings);
		html_separator();
	}
	else
		html_print_fail('Keine Paarungen gefunden.');

	html_button('back','Zurück','window.history.back();');
