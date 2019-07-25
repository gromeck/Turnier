<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to check in or out
**
**	GET params:
**		-
**
**	POST params:
**		code		the player associated with that code will
**					toggle its active/passive state
*/

$msg = '';
$color = '';
if ($_POST) {
	//print_r($_POST);
	/*
	**	we received a checkin/out request
	*/
	if ($player = db_player_lookup_by_rfidcode($_POST['code'])) {
		/*
		**	player is known, so activate/deactivate him
		*/
		//print_r($player);
		if (!$player['Banned']) {
			db_player_activate($player['Pid'],$player['Active'] ? 0 : 1);

			/*
			**	read the data back
			*/
			if (!($player = db_player_lookup_by_pid($player['Pid'])))
				$msg .= "<br>\nSpieler nun unbekannt!";
		}
	}
	else
		$msg .= "<br>\nSpieler unbekannt!";
	if ($msg)
		$color = 'red';
}

?>
<center>
<form name=checkinform method=post>
<?php
html_separator(1);
html_print_bigger('Code eingeben oder scannen ...','biggertext');
html_separator(1);
?>
<input name=code id=code type=text autocomplete=off>
</form>
<spacer type=vertical size=100>
<span class="biggesttext" id=msg><?php print $msg; ?></span>
<?php
	if (ADMIN && @$player) {
		print $player['Firstname'].' '.@$player['Lastname'];
	}
?>
<span class="biggesttext" id=info><?php
	if (@$player) {
		html_separator();
		print $player['Nick'];
		if ($player['Banned']) {
			$color = 'yellow';
			print ",<br>du bist gesperrt!";
		}
		else if ($player['Active']) {
			$color = 'green';
			print ",<br>du bist dabei!";
		}
		else {
			$color = 'orange';
			print ",<br>du bist raus!";
		}
	}
?></span>
</center>
<script language="JavaScript">
$(document).ready(function() {
	$('#code').focus();
	setInterval(function() { $('#code').focus(); },1000);
	$('#msg').delay(4000).fadeOut(400);
	$('#info').delay(4000).fadeOut(400);
	<?php if ($color) { ?>
	$('body').css('background-color','<?php print $color ?>');
	setTimeout(function() { $('body').css('background-color','white'); },1000);
	<?php } ?>
});
</script>
<?php
