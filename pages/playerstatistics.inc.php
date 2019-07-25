<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to show player statistics
**
**	GET params:
**		-
**
**	POST params:
**		class		show classes: 1, 2, <all others>
**		sort		sort by: name, matches
*/
if (!ADMIN) { print "Not admin!"; exit(); };
/*
**	a quick hack for the
**
**		1. Goddelauer Badminton Nachturnier
**
**
**	Christian Lorenz
*/
?>
<script language="JavaScript">
function initPage()
{
	button_select('classall',<?php print (@$_POST['class'] != 1 && @$_POST['class'] != 2) ? 1 : 0 ?>);
	button_select('class1',<?php print (@$_POST['class'] == 1) ? 1 : 0 ?>);
	button_select('class2',<?php print (@$_POST['class'] == 2) ? 1 : 0 ?>);
	input_set('class','<?php print @$_POST['class']; ?>');
		
	button_select('sortmatches',<?php print (@$_POST['sort'] != 'name') ? 1 : 0 ?>);
	button_select('sortname',<?php print (@$_POST['sort'] == 'name') ? 1 : 0 ?>);
	input_set('sort','<?php print @$_POST['sort']; ?>');

	setTimeout("resetPage()",60 * 1000);
}
function clickedPlayer(pid)
{
	document.location = '?page=playerinfo&pid=' + pid;
}
function reloadPage()
{
    document.forms['statisticform'].submit();
}
function resetPage()
{
	input_set('class','0');
	input_set('sort','matches');
	reloadPage();
}
function clickedClassAll()
{
	input_set('class','0');
	button_select('classall',1);
	button_select('class1',0);
	button_select('class2',0);
	reloadPage();
}
function clickedClass1()
{
	input_set('class','1');
	button_select('classall',0);
	button_select('class1',1);
	button_select('class2',0);
	reloadPage();
}
function clickedClass2()
{
	input_set('class','2');
	button_select('classall',0);
	button_select('class1',0);
	button_select('class2',1);
	reloadPage();
}
function clickedSortMatches()
{
	input_set('sort','matches');
	button_select('sortname',0);
	button_select('sortmatches',1);
	reloadPage();
}
function clickedSortName()
{
	input_set('sort','name');
	button_select('sortname',1);
	button_select('sortmatches',0);
	reloadPage();
}

</script>
<?php

function print_list($players)
{
	$nplayers = count($players);

	?>
	<center>
	<table class="table-player-statistics" border=1 cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<td class="td-head">#</td>
			<td class="td-head">Name</td>
			<?php if (CLASSESUSED) { ?><td class="td-head" align=center>Klasse</td><?php } ?>
			<td class="td-head" align=center>Anzahl Spiele</td>
		</tr>
		<?php
		for ($n = 0;$n < count($players);$n++) {
			?>
			<tr class=<?php print ($n % 2) ? 'tr-odd' : 'tr-even' ?>>
				<td><?php print $n + 1; ?></td>
				<td><?php print print_player($players[$n],1); ?></td>
				<?php if (CLASSESUSED) { ?><td align=center><?php print $players[$n]['Class']; ?></td><?php } ?>
				<td align=center><?php print $players[$n]['Matches']; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	</center>
	<?php
}

function print_cols_list($cols,$players)
{
	$nplayers = count($players);
	$nplayerspercol = ceil($nplayers / $cols);

	?>
	<center>
	<table class="table-player-statistics" border=1 cellspacing=0 cellpadding=0>
		<tr class="tr-head">
			<?php for ($col = 0;$col < $cols;$col++) { ?>
				<?php if ($col) { ?> <td class="tr-col-separator"></td><?php } ?>
				<td class="td-head">#</td>
				<td class="td-head">Name</td>
				<?php if (CLASSESUSED) { ?><td class="td-head" align=center>Klasse</td><?php } ?>
				<td class="td-head" align=center>Anzahl Spiele</td>
			<?php } ?>
		</tr>
		<?php
		for ($n = 0;$n < $nplayerspercol;$n++) {
			?>
			<tr class=<?php print ($n % 2) ? 'tr-odd' : 'tr-even' ?>>
				<?php for ($col = 0;$col < $cols;$col++) {
					$idx = $col * $nplayerspercol + $n;
					$player = $players[$idx];
					if ($col) {
						?>
						<td class="tr-col-separator"></td>
						<?php
					}
					if ($player) {
						?>
						<td><?php print $idx + 1; ?></td>
						<td><?php print print_player($player,1); ?></td>
						<?php if (CLASSESUSED) { ?><td align=center><?php print lookup_class($players[$n]['Class']); ?></td><?php } ?>
						<td align=center><?php print $player['Matches']; ?></td>
						<?php
					}
					else {
						?>
						<td></td>
						<td></td>
						<?php if (CLASSESUSED) { ?><td></td><?php } ?>
						<td></td>
						<?php
					}
				} ?>
			</tr>
			<?php
		}
		?>
	</table>
	</center>
	<?php
}

?>
<form name=statisticform method=post>
	<input name=class id=class type=hidden>
	<input name=sort id=sort type=hidden>
</form>
<?php
	if (CLASSESUSED) {
		html_print_big('Klasse:');
		html_button('classall','Alle','clickedClassAll();');
		html_button('class1',lookup_class(1),'clickedClass1();');
		html_button('class2',lookup_class(2),'clickedClass2();');
	}

	html_print_big('Sortierung:');
	html_button('sortmatches','nach Anzahl Spiele','clickedSortMatches();');
	html_button('sortname','nach Name','clickedSortName();');

	html_separator();

	/*
	**	get all players from this class
	*/
	switch (@$_POST['class']) {
		case 1:
			$class = DB_PLAYER_ONLYCLASS1;
			break;
		case 2:
			$class = DB_PLAYER_ONLYCLASS2;
			break;
		default:
			$class = DB_PLAYER_ALL;
	}
	switch (@$_POST['sort']) {
		case 'name':
			$sortby = 'Nick ASC';
			break;
		default:
			$sortby = 'Matches DESC, NICK ASC';
	}
	$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,$class,$sortby);
	//dump_array('players',$players);

	if ($class == DB_PLAYER_ALL) {
		/*
		**	split the list in three lists
		*/
		print_cols_list(4,$players);
	}
	else
		print_list($players);
