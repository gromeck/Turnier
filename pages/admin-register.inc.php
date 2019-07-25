<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to register a new player or to update an existing
**
**	GET params:
**		pid			the player id, if update
**		action		the action to apply on the pids
**					activate, deactivate, ban, unban, delete
**
**	POST params:
**		pid			player id, if update
**		lastname	lastname of the player
**		firstname	firstname of the player
**		gender		gender of the player (M or F)
**		class		class of the player (1 or 2)
*/
if (!ADMIN) { print "Not admin!"; exit(); };

if (@$_GET) {
	//print_r($_GET);
	if (@$_GET['pid']) {
		$player = db_player_lookup_by_pid($_GET['pid']);
		define('UPDATE',1);
	}
//	print_r(@$player);
}
if (!defined('UPDATE'))
	define('UPDATE',0);

$errmsg = '';
$infomsg = '';
if ($_POST) {
	//print_r($_POST);
	/*
	**	we received a register request
	*/
	$player = array(
		'Pid' => @$_POST['pid'],
		'Lastname' => @$_POST['lastname'],
		'Firstname' => @$_POST['firstname'],
		'Gender' => @$_POST['gender'],
		'Class' => @$_POST['class'],
		);
	if (empty($player['Lastname'])) {
		$errmsg .= "Nachname fehlt!<br>";
	}
	if (empty($player['Firstname'])) {
		$errmsg .= "Vorname fehlt!<br>";
	}
	if ($player['Gender'] != 'M' && $player['Gender'] != 'F') {
		$errmsg .= "Geschlecht fehlt!<br>";
	}
	if ($player['Class'] != 1 && $player['Class'] != 2) {
		$errmsg .= "Klasse fehlt!<br>";
	}

	if (empty($errmsg)) {
		/*
		**	no error, so check if update or new player
		*/
		if (UPDATE) {
			/*
			**	update mode
			*/
			list ($result,$dbmsg) = db_player_update($player['Pid'],$player['Lastname'],$player['Firstname'],$player['Gender'],$player['Class']);
			if (!$result) {
				$errmsg .= "Fehler beim Aktualisieren der Datenbank! ($dbmsg)<br>";
			}
			else {
				$infomsg .= $player['Firstname']." ".$player['Lastname']." wurde erfolgreich aktualisiert!<br>";
			}
		}
		else {
			/*
			**	new registration
			*/
			list ($result,$dbmsg) = db_player_create($player['Lastname'],$player['Firstname'],$player['Gender'],$player['Class']);
			echo "<br>result=$result error=$dbmsg";
			if (!$result) {
				$errmsg .= "Fehler beim Einfügen in die Datenbank! ($dbmsg)<br>";
			}
			else {
				$player['Pid'] = $result;
				$infomsg .= $player['Firstname']." ".$player['Lastname']." wurde erfolgreich registriert!<br>";
			}
		}
		/*
		**	read the data back
		*/
		if ($player = db_player_lookup_by_pid($player['Pid'])) {
			$errmsg .= "Konnte Spieler nicht finden!<br>";
		}
	}
}

?>
<script language="JavaScript">
function initPage()
{
	<?php if (@$player) {
		/*
		**	check the post vars and mark everything which wasn't ok
		*/
		?>
		<?php if (@$player['Lastname']) { ?>
			input_set('lastname','<?php print $player['Lastname'] ?>');
		<?php } else if (@$_POST) { ?>
			input_warn('lastname',1);
		<?php } ?>
		<?php if (@$player['Firstname']) { ?>
			input_set('firstname','<?php print $player['Firstname'] ?>');
		<?php } else if (@$_POST) { ?>
			input_warn('firstname',1);
		<?php } ?>
		<?php if (@$player['Gender'] == 'M') { ?>
			button_select('male',1);
			button_select('female',0);
			input_set('gender','M');
		<?php } else if (@$player['Gender'] == 'F') { ?>
			button_select('male',0);
			button_select('female',1);
			input_set('gender','F');
		<?php } else if (@$_POST) { ?>
			button_warn('male',1);
			button_warn('female',1);
		<?php } ?>
		<?php if (@$player['Class'] == '1') { ?>
			button_select('class1',1);
			button_select('class2',0);
			input_set('class','1');
		<?php } else if (@$player['Class'] == '2') { ?>
			button_select('class1',0);
			button_select('class2',1);
			input_set('class','2');
		<?php } else if (@$_POST) { ?>
			button_warn('class1',1);
			button_warn('class2',1);
		<?php } ?>
		<?php if (@$player['Pid']) { ?>
			button_disable('register',1);
		<?php } ?>
		<?php if (!@$player['Pid']) { ?>
			setTimeout('setFocus()',1000);
		<?php } ?>
	<?php } ?>
	setFocus();
}
function clickedMale()
{
	input_set('gender','M');
	button_select('male',1);
	button_select('female',0);
}
function clickedFemale()
{
	input_set('gender','F');
	button_select('male',0);
	button_select('female',1);
}
function clickedClass1()
{
	input_set('class','1');
	button_select('class1',1);
	button_select('class2',0);
}
function clickedClass2()
{
	input_set('class','2');
	button_select('class1',0);
	button_select('class2',1);
}

function clearAllInput()
{
	input_set('lastname','');
	input_set('firstname','');
	input_set('gender','');
	button_select('male',0);
	button_select('female',0);
	input_set('class','');
	button_select('class1',0);
	button_select('class2',0);
}

function setFocus()
{
	document.getElementById('firstname').focus();
}

function clickedRegister()
{
	document.forms['registerform'].submit();
}

function clickedUpdate()
{
	document.forms['registerform'].submit();
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
<form name=registerform method=post>
	<input name=pid id=pid type=hidden value="<?php print @$player['Pid'] ?>">
	<tr>
		<td class=bigtext>Vorname:</td>
		<td><input name=firstname id=firstname type=text tabindex=2 autocomplete=off></td>
	</tr>
	<tr>
		<td class=bigtext>Nachname:</td>
		<td><input name=lastname id=lastname type=text tabindex=3 autocomplete=off></td>
	</tr>
	<tr>
		<td class=bigtext>Geschlecht:</td>
		<td><input name=gender id=gender type=hidden>
		<?php
			html_button('male','männlich','clickedMale();',4);
			html_button('female','weiblich','clickedFemale()',5);
		?>
		</td>
	</tr>
	<tr>
		<td class=bigtext>Klasse:</td>
		<td><input name=class id=class type=hidden>
		<?php
			html_button('class1',lookup_class(1),'clickedClass1();',6);
			html_button('class2',lookup_class(2),'clickedClass2()',7);
		?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?php
			html_separator();
			if (UPDATE) {
				html_button('update','Aktualisieren','clickedUpdate();',9);
				html_button('cancel','Abbruch','window.history.back()',10);
			}
			else {
				html_button('register','Registrieren','clickedRegister();',9);
				html_button('cancel','Abbruch','window.history.back()',10);
			}
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
