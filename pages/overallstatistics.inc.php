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
				<td colspan=4><?php print('Teilnehmer ('.$state.')') ?></td>
			</tr>
			<tr class="tr-head">
				<td class="td-head" align=center></td>
				<td class="td-head" align=center>männlich</td>
				<td class="td-head" align=center>weiblich</td>
				<td class="td-head" align=center>gesamt</td>
			</tr>
			<?php foreach (array(1,2) as $class) { ?>
			<tr>
				<td class="table-stats-col-title"><?php print(lookup_class($class).':'); ?></td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYMALE,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYFEMALE,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,$class,'Nick ASC',$state == 'gespielt'))) ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="table-stats-col-title">Gesamt:</td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYMALE,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ONLYFEMALE,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
				<td class="table-stats-col-number"><?php print(count(db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL,'Nick ASC',$state == 'gespielt'))) ?></td>
			</tr>
		<?php } ?>
		<tr class="tr-subhead">
			<td colspan=4>Spiele</td>
		</tr>
		<tr class="tr-head">
			<td class="td-head" align=center></td>
			<td class="td-head" align=center colspan=3>Anzahl</td>
		</tr>
		<?php
			$rounds = db_round_list(DB_ROUND_ONLYSTOPPED);
		?>
		<tr>
			<td class="table-stats-col-title">Beginn:</td>
			<td class="table-stats-col-number" colspan=3><?php print(timestamp2datetime($rounds[0]['StartDate'])) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title">Ende:</td>
			<td class="table-stats-col-number" colspan=3><?php print(timestamp2datetime($rounds[count($rounds) - 1]['StopDate'])) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title">Runden:</td>
			<td class="table-stats-col-number" colspan=3><?php print(count($rounds)) ?></td>
		</tr>
		<tr>
			<td class="table-stats-col-title">Matches:</td>
			<td class="table-stats-col-number" colspan=3><?php print(count(db_match_list())) ?></td>
		</tr>
	</table>

<?php
	html_button('back','Zurück','window.history.back();');
?>
<center>
