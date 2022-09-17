<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to manage the rounds
**
**	GET params:
**		pid			player id to show info
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

function print_playerinfo($player)
{
	$n = 0;
	?>
	<center>
	<table class="table-playerinfo" cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<td class="td-head" colspan=2><center><?php html_print_big('Spielerinfo'); ?></center></td>
		</tr>
		<tr class=<?php print ($n++ % 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Pid:'); ?></td>
			<td><?php html_print_big($player['Pid']); ?></td>
		</tr>
		<tr class=<?php print ($n++ % 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Nick:'); ?></td>
			<td><?php print_player($player); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Name:'); ?></td>
			<td><?php html_print_big($player['Firstname'].' '.$player['Lastname']); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Letzte Aktivierung:'); ?></td>
			<td><?php html_print_big(timestamp2datetime($player['ActiveChangeDate'])); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Spielklasse:'); ?></td>
			<td><?php html_print_big(lookup_class($player['Class'])); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Aktiv:'); ?></td>
			<td><?php html_print_big(lookup_active($player['Active'])); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Banned:'); ?></td>
			<td><?php html_print_big(lookup_banned($player['Banned'])); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Spiele:'); ?></td>
			<td><?php html_print_big($player['Matches']); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('RFIDcode:'); ?></td>
			<td><?php html_print_big($player['RFIDcode']); ?></td>
		</tr>
		<tr class=<?php print ($n ++% 2) ? 'tr-odd' : 'tr-even' ?>>
			<td><?php html_print_big('Registrierungsdatum:'); ?></td>
			<td><?php html_print_big(timestamp2datetime($player['CreationDate'])); ?></td>
		</tr>
	</table>
	</center>
	<?php
}

/*
**	print a list of all matches of this player
*/
function print_matches($pid,$matches)
{
	?>
	<center>
	<table class="table-round" cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<td class="td-head" align=center><?php html_print_big('Runde','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Platz','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Klasse','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Zeit','bigtextwhite'); ?></td>
			<td class="td-head" colspan=5><center><?php print html_print_big('Paarung','bigtextwhite'); ?></center></td>
		</tr>
		<?php $n = 0; foreach ($matches as $match) { ?>
		<tr class="tr-<?php print ($n++ % 2) ? 'odd' : 'even' ?>">
			<td class="table-round-col-round"><?php html_print_big($match['Round']); ?></td>
			<td class="table-round-col-court"><?php html_print_big($match['Court'] + 1); ?></td>
			<td class="table-round-col-class"><?php html_print_big(lookup_class($match['Class'])); ?></td>
			<td class="table-round-col-time"><?php html_print_big(timestamp2datetime($match['PlayDate'])); ?></td>
			<td class="table-round-col-left"><?php print_pair(array(db_player_lookup_by_pid($match['PidA']),db_player_lookup_by_pid($match['PidB'])),1,$pid); ?></td>
			<td class="table-round-col-vs"><span class=bigtext>:</span></td>
			<td class="table-round-col-left"><?php print_pair(array(db_player_lookup_by_pid($match['PidC']),db_player_lookup_by_pid($match['PidD'])),1,$pid); ?></td>
		</tr><?php
		}
		?>
	</table>
	</center>
	<?php
}

/*
**	print a list of all pairings of this player
*/
function print_pairings($pid,$pairings)
{
	?>
	<center>
	<table class="table-round" cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<td class="td-head" align=center><?php html_print_big('Spiel&nbsp;mit&nbsp;Partner','bigtextwhite'); ?></td>
			<td class="td-head" align=center><?php html_print_big('Anzahl','bigtextwhite'); ?></td>
		</tr>
		<?php $n = 0; foreach ($pairings as $pairing) { ?>
		<tr class="tr-<?php print ($n++ % 2) ? 'odd' : 'even' ?>">
			<td class="table-round-col-partner"><?php print_player(db_player_lookup_by_pid(($pairing['PidA'] != $pid) ? $pairing['PidA'] : $pairing['PidB']),1); ?></td>
			<td class="table-round-col-count"><?php html_print_big($pairing['Count']); ?></td>
		</tr><?php
		}
		?>
	</table>
	</center>
	<?php
}

	/*
	**	get info about the player
	*/
	if (!($player = db_player_lookup_by_pid(@$_GET['pid'])))
		html_print_fail('Unbekannter Spieler!');
	//dump_array('player',$player);
	print_playerinfo($player);
	html_separator();

	/*
	**	retrieve all matches
	*/
	$matches = db_player_matches($player['Pid']);
	//dump_array('matches',$matches);
	if ($matches) {
		print_matches($player['Pid'],$matches);
		html_separator();
	}
	else
		html_print_fail('Keine Spiele für diesen Spieler gefunden.');

	/*
	**	retrieve all pairings
	*/
	$pairings = db_player_pairings($player['Pid']);
	//dump_array('pairings',$pairings);
	if ($pairings) {
		print_pairings($player['Pid'],$pairings);
		html_separator();
	}
	else
		html_print_fail('Keine Paarungen mit diesen Spieler gefunden.');

	html_button('back','Zurück','window.history.back();');
