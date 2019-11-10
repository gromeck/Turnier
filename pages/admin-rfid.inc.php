<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to manage the RFID codes in the system
**
**	GET params:
**		-
**
**	POST params:
**		code		RFID code to check
*/
if (!ADMIN) { print "Not admin!"; exit(); };

$msg = '';
$color = '';
if ($_POST) {
	//print_r($_POST);
	/*
	**	we received a checkin/out request
	*/
	if ($Rfid = db_rfidcode_lookup_by_rfidcode($_POST['code'])) {
		$msg = 'RFID-Code '.$_POST['code'].' ist registriert.';
		$color = 'green';
	}
	else {
		$msg = 'RFID-Code '.$_POST['code'].' ist unbekannt.';
		$color = 'red';
	}
	if ($player = db_player_lookup_by_rfidcode($_POST['code'])) {
		if (!$player['Active'])
			$color = 'orange';
	}
}

?>
<script language="JavaScript">
function initPage()
{
	setFocus();
}

function setFocus()
{
	document.getElementById('code').focus();
}

function flash_page(color)
{
	document.body.style.backgroundColor = color;
	if (color != 'white')
		setTimeout("flash_page('white')",1000);
}

function clear_msg()
{
	document.getElementById('msg').innerHTML = "";
}

function clear_info()
{
	document.getElementById('info').innerHTML = "";
}

</script>
<center>
<form name=checkinform method=post>
<?php
html_separator(1);
html_print_bigger('Code eingeben oder scannen ...');
html_separator(1);
?>
<input name=code id=code type=text autocomplete=off>
</form>
<?php
html_separator(1);
if (@$player) {
	?>
	<table class=table-rfid-admin>
		<tr>
			<td>RFID-Code:</td>
			<td><?php print($player['RFIDcode']); ?></td>
		</tr>
		<tr>
			<td>Nick:</td>
			<td><?php print_player($player); ?></td>
		</tr>
		<tr>
			<td>Name:</td>
			<td><?php print $player['Firstname'].' '.$player['Lastname']; ?></td>
		</tr>
		<tr>
			<td>Spiele:</td>
			<td><?php print $player['Matches']; ?></td>
		</tr>
	</table>
	<?php
}
else
	html_print_bigger($msg);
?>
</center>
<script>
flash_page('<?php print $color ?>');
setTimeout("clear_msg()",4000);
setTimeout("clear_info()",4000);
</script>
<?php
