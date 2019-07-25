<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to handle the login as admin
**
**	GET params:
**		type		logout, for logout
**
**	POST params:
**		username	username for login
**		password	password for login
*/

$errmsg = '';
if ($_GET) {
	if (@$_GET['type'] == 'logout') {
		unset($_SESSION['User']);
		?>
		<script>
		document.location = 'index.php';
		</script>
		<?php
		return;
	}
}
if ($_POST) {
	//print_r($_POST);
	/*
	**	we received a POST request
	*/
	if ($user = db_user_authenticate($_POST['username'],$_POST['password'])) {
		/*
		**	username was valid, so push the user into the session context
		*/
		$_SESSION['User'] = $user;
		?>
		<script>
		document.location = 'index.php';
		</script>
		<?php
		return;
	}
	else {
		$errmsg .= "Authentisierungfehler!<br>";
		unset($_SESSION['User']);
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
	document.getElementById('username').focus();
}

function clickedLogin()
{
	document.forms['loginform'].submit();
}

function clickedCancel()
{
	document.location = 'index.php';
}
</script>

<center>
<table>
<form name=loginform method=post>
	<tr>
		<td class=bigtext>Benutzername:</td>
		<td><input name=username id=username type=text tabindex=1 autocomplete=off></td>
	</tr>
	<tr>
		<td class=bigtext>Kennwort:</td>
		<td><input name=password id=password type=password tabindex=2 autocomplete=off></td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?php
			html_separator();
			html_button('login','Anmelden','clickedLogin();',3);
			html_button('cancel','Abbruch','clickedCancel()',4);
		?>
		</td>
	</tr>
</form>
</table>
<?php foreach (explode('<br>',$errmsg) as $line) html_print_fail($line); ?>
</center>

<?php
