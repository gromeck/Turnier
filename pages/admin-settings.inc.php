<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to manage the global settings
**
**	GET params:
**		-
**
**	POST params:
**		<settings>		all values from the settings array
*/
if (!ADMIN) { print "Not admin!"; exit(); };

$errmsg = '';
$infomsg = '';
if ($_POST) {
	//print_r($_POST);
	/*
	**	copy stuff into the settings array
	*/
	for ($n = 0;$n < count($settings);$n++) {
		if (isset($_POST[$settings[$n]['Name']])) {
			$settings[$n]['Value'] = $_POST[$settings[$n]['Name']];
		}
	}

	/*
	**	write the settings to the database
	*/
	if (!($ret = db_settings_save($settings))) {
		$errmsg .= "Unbekannter Code!<br>";
	}
	else {
		$infomsg .= "Einstellungen gespeichert!<br>";
	}
}

?>
<script language="JavaScript">
function clickedSubmit()
{
	document.forms['settingsform'].submit();
}

function clickedCancel()
{
	document.location = 'index.php?page=index';
}

<?php if ($infomsg) { ?>
clickedCancel();
<?php } ?>
</script>
<center>
<table>
<form name=settingsform method=post>
	<?php
		$tabindex = 1;
		foreach ($settings as $setting) { ?>
	<tr>
		<td class=bigtext><?php print $setting['Title'] ?>:</td>
		<td><input type=text autocomplete=off
			name=<?php print $setting['Name'] ?>
			id=<?php print $setting['Name'] ?>
			value="<?php print $setting['Value'] ?>"
			tabindex=<?php print $tabindex++ ?>
			style="width:500px;"></td>
		<td class=bigtext><?php print $setting['Units'] ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td></td>
		<td>
		<?php
			html_separator();
			html_button('submit','Speichern','clickedSubmit();',9);
			html_button('cancel','Abbruch','clickedCancel()',10);
		?>
		</td>
	</tr>
</form>
</table>
<?php foreach (explode('<br>',$errmsg) as $line) html_print_fail($line); ?>
</center>
<?php
