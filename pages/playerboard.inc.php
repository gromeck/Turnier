<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to display the player board
**
**	GET params:
**		toggle		is the player id which states should toggle
**					between active/passive
**
**	POST params:
**		-
*/

if ($_GET) {
	if (@$_GET['toggle'] && ADMIN) {
		/*
		**	toggle the active flag
		*/
		db_player_activate_toggle($_GET['toggle']);
	}
}

?>
<script language="JavaScript">
function initPage()
{
	setTimeout("reloadPage()",3000);
}
function reloadPage()
{
	document.location = '?page=<?php print @$_GET['page'] ?>';
}

function clickedPlayer(pid)
{
	<?php if (ADMIN) { ?>
		document.location = '?page=<?php print @$_GET['page'] ?>&toggle=' + pid;
	<?php } else { ?>
		document.location = '?page=playerinfo&pid=' + pid;
	<?php } ?>
}
</script>
<?php

function print_list($class,$players)
{
	if (!$nplayers = count($players))
		return;
	$cols = ceil(sqrt($nplayers * 0.6));
	$rows = ceil($nplayers / $cols);

	html_separator();
	print('Klasse: '.lookup_class($class).' ('.$nplayers.')');
	html_separator();
	?>
	<center>
	<table class="table-player-board">
		<?php
		for ($row = 0;$row < $rows;$row++) {
			?>
			<tr><?php
				for ($col = 0;$col < $cols;$col++) {
					?><td><?php if (@$players[$col * $rows + $row]['Pid']) print_player($players[$col * $rows + $row]) ?></td><?php
				}
			?></tr>
			<?php
		}
		?>
	</table>
	</center>
	<?php
}

if (ADMIN) {
	/*
	**	as admin offer a button to jump to the round
	*/
	html_button_href('weiter zur Spielrunde','?page=playround',0,'a');
}

foreach (array( DB_PLAYER_ONLYCLASS1, DB_PLAYER_ONLYCLASS2) as $class) {
	/*
	**	get all players from this class
	*/
	$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,$class);
	//dump_array('players',$players);
	print_list($class,$players);
}
