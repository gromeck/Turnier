<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to display overall statistics
**
**	GET params:
**		-
**
**	POST params:
**		-
*/

	html_separator(1);
?>

<center>
	<table class="table-stats" cellspacing=0 cellpadding=0>
		<?php foreach (array( 'gemeldet', 'gespielt') as $state) { ?>
			<tr class="tr-subhead">
				<td colspan=4><?php html_print_big('Teilnehmer ('.$state.')') ?></td>
			</tr>
			<tr class="tr-head">
				<td class="td-head" align=center></td>
				<td class="td-head" align=center><?php html_print_big('männlich') ?></td>
				<td class="td-head" align=center><?php html_print_big('weiblich') ?></td>
				<td class="td-head" align=center><?php html_print_big('gesamt') ?></td>
			</tr>
			<?php foreach (array(1,2) as $class) { ?>
			<tr>
				<td class="table-stats-col-title"><?php html_print_big(lookup_class($class).':'); ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYMALE,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYFEMALE,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="table-stats-col-title"><?php html_print_big('Gesamt:'); ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYMALE,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYFEMALE,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php html_print_big(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
			</tr>
		<?php } ?>
		<tr class="tr-subhead">
			<td colspan=4><?php html_print_big('Spiele') ?></td>
		</tr>
		<tr class="tr-head">
			<td class="td-head" align=center></td>
			<td class="td-head" align=center colspan=3><?php html_print_big('Anzahl') ?></td>
		</tr>
		<?php
			$rounds = db_round_list(DB_ROUND_ONLYSTOPPED);
		?>
		<tr>
			<td class="table-stats-col-title"><?php html_print_big('Beginn:'); ?></td>
			<td class="table-stats-col-number" colspan=3><?php html_print_big(timestamp2datetime($rounds[0]['StartDate'])) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title"><?php html_print_big('Ende:'); ?></td>
			<td class="table-stats-col-number" colspan=3><?php html_print_big(timestamp2datetime($rounds[count($rounds) - 1]['StopDate'])) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title"><?php html_print_big('Runden:'); ?></td>
			<td class="table-stats-col-number" colspan=3><?php html_print_big(count($rounds)) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title"><?php html_print_big('Matches:'); ?></td>
			<td class="table-stats-col-number" colspan=3><?php html_print_big(count(db_match_list())) ?></td>
		</tr>
	</table>

<?php
	html_button('back','Zurück','window.history.back();');
?>
<center>
