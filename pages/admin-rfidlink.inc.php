<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to link players with their RFID chips
**
**	GET params:
**		pid			the player id
**		action		link or unlink
**
**	POST params:
**		pid			player id
**		action		link or unlink
**		rfidcode	code to link
*/
if (!ADMIN) { print "Not admin!"; exit(); };

if (@$_GET) {
	//print_r($_GET);
	if (@$_GET['pid']) {
		$player = db_player_lookup_by_pid($_GET['pid']);
	}
//	print_r(@$player);
}
$errmsg = '';
$infomsg = '';
if ($_POST) {
	//print_r($_POST);
	if (@$_POST['pid']) {
		/*
		**	check if the RFID is valid
		*/
		if (db_rfidcode_lookup_by_rfidcode(@$_POST['rfidcode'])) {
			/*
			**	check if the player exists
			*/
			if ($player = db_player_lookup_by_pid($_POST['pid'])) {
				/*
				**	link or unlink
				*/
				if (@$_POST['action'] == 'link') {
					/*
					**	check if this RFID is already linked
					*/
					if ($player2 = db_player_lookup_by_rfidcode(@$_POST['rfidcode'])) {
						$errmsg .= "RFID-Code ist bereits mit ".$player2['Firstname']." ".$player2['Lastname']." gekoppelt!<br>";
					}
					else {
						/*
						**	link this code to the player
						*/
						list ($result,$dbmsg) = db_player_link_rfidcode($_POST['pid'],$_POST['rfidcode']);
						if (!$result) {
							$errmsg .= "Fehler beim Koppeln! ($dbmsg)<br>";
						}
						else
							$infomsg .= "RFID-Code erfolgreich mit ".$player['Firstname']." ".$player['Lastname']." gekoppelt!";
					}
				}
				else if (@$_POST['action'] == 'unlink') {
					/*
					**	unlink
					*/
					list ($result,$dbmsg) = db_player_unlink_rfidcode($_POST['rfidcode']);
					if (!$result) {
						$errmsg .= "Fehler beim Entkoppeln! ($dbmsg)<br>";
					}
					else
						$infomsg .= "RFID-Code erfolgreich mit ".$player['Firstname']." ".$player['Lastname']." entkoppelt!";
				}
				else
					$errmsg .= "Unbekannte Aktion!<br>";
			}
		}
		else
			$errmsg .= "Unbekannter RFID-Code!<br>";
	}
	else
		$errmsg .= "Spieler-Index nicht gesetzt!<br>";
}

?>
<script language="JavaScript">
$(document).ready(function() {
	$('#rfidcode').focus();
});
function clickedRFIDlink(pid)
{
	document.forms['rfidlink'].submit();
}
function clickedRFIDunlink(pid)
{
	document.forms['rfidlink'].submit();
}
function clickedCancel()
{
	document.location = 'index.php?page=admin-player';
}

</script>
<center>

<?php if ($infomsg) {
	/*
	**	we are done
	*/
	html_print_info($infomsg);
	html_separator();
	html_button('done','Ok, super!','clickedCancel();',1);
?>
<script>
setTimeout("clickedCancel()",20000);
</script>
<?php } else {
	/*
	**	show the dialog
	*/
?>
<table>
<form name=rfidlink method=post>
	<input name=pid id=pid type=hidden value="<?php print @$player['Pid'] ?>">
	<input name=action id=action type=hidden value="<?php print @$_GET['action'] ?>">
	<tr>
		<td class=bigtext>Name:</td>
		<td class=bigtext><?php print $player['Firstname'].' '.$player['Lastname']; ?></td>
	</tr>
	<tr>
		<td class=bigtext>Nick:</td>
		<td class=bigtext><?php print print_player($player); ?></td>
	</tr>
	<tr>
		<td class=bigtext>RFID-Code:</td>
		<td><input name=rfidcode id=rfidcode type=text tabindex=3 autocomplete=off
			<?php print ($_GET['action'] == 'unlink') ? 'readonly value="'.$player['RFIDcode'].'"' : ''; ?>></td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?php
			html_separator();
			if ($_GET['action'] == 'unlink')
				html_button('rfidunlink','entkoppeln','clickedRFIDunlink();');
			else
				html_button('rfidlink','koppeln','clickedRFIDlink();');
			html_button('cancel','Abbruch','clickedCancel();');
		?>
		</td>
	</tr>
</form>
</table>
<?php
}
/*
**	display errors
*/
foreach (explode('<br>',$errmsg) as $line) html_print_fail($line);
?>
</center>
