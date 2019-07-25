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
**		clear		if 1, clear round #nround
**					if all, clear all rounds
**		nround		round to clear
**
**	POST params:
**		-
*/
if (!ADMIN) { print "Not admin!"; exit(); };

if (@$_GET) {
	if (@$_GET['clear'] == 1 && @$_GET['nround']) {
		/*
		**	clear this round
		*/
		//print "clearing round ".$_GET['nround'];
		html_print_info('Runde '.$_GET['nround'].' gelöscht!');
		html_separator();
		db_round_clear($_GET['nround']);
	}
	if (@$_GET['clear'] == 'all') {
		html_print_info('Alle Rundendaten gelöscht!');
		html_separator();
		db_round_clear_all();
	}
	if (@$_GET['rebuild']) {
		html_print_info('Alle Match und Pairing Counter neu berechnet!');
		html_separator();
		db_round_rebuild_all();
	}
}
?>
<script language="JavaScript">
function clickedClear(nround)
{
	if (confirm('Runde #' + nround + ' löschen?')) 
		document.location = '?page=admin-round&clear=1&nround=' + nround;
}
function clickedClearAll()
{
	if (confirm('Alle Rundendaten wirklich löschen?')) 
		document.location = '?page=admin-round&clear=all';
}
function clickedRebuild()
{
	document.location = '?page=admin-round&rebuild=all';
}
</script>
<?php

function print_list($rounds)
{
	$nrounds = count($rounds);

	?>
	<center>
	<table class="table-round-admin" border=1 cellspacing=0 cellpadding=5 width=100%>
		<tr class="tr-head">
			<td class="td-head">Runde</td>
			<td class="td-head">Status</td>
			<td class="td-head">Gestartet</td>
			<td class="td-head">Gespielt</td>
			<td class="td-head">Gestoppt</td>
			<td class="td-head">Bearbeitung</td>
		</tr>
		<?php $n = 0; foreach ($rounds as $round) {
			?>
			<tr class=<?php print ($n++ % 2) ? 'tr-odd' : 'tr-even' ?>>
				<td class=table-round-col-round><?php print$round['Round']; ?></td>
				<td class=table-round-col-round><?php print lookup_round_state($round['State']); ?></td>
				<td class=table-round-col-time><?php print timestamp2datetime($round['StartDate']); ?></td>
				<td class=table-round-col-time><?php print timestamp2datetime($round['PlayDate']); ?></td>
				<td class=table-round-col-time><?php print timestamp2datetime($round['StopDate']); ?></td>
				<td align=left><?php if ($round['State'] == DB_ROUND_ONLYSTARTED)
						html_button('roundclear','Löschen','clickedClear('.$round['Round'].');'); ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	</center>
	<?php
}

	/*
	**	offer option to clear all round data
	*/
	html_button('clearall','Alle Runden löschen','clickedClearAll();');
	html_button('rebuild','Match &amp; Pairing Counter neu berechnen','clickedRebuild();');
	html_separator();

	/*
	**	get all players from this class
	*/
	$rounds = db_round_list(DB_ROUND_ALL);
	//dump_array('rounds',$rounds);

	print_list($rounds);
