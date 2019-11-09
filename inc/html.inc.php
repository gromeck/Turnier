<?php
/*
**	html functions
**
*/

function html_print_big($text,$class = 'bigtext')
{
	if (strlen($text))
		print '<span class="'.$class.'">'.$text.'</span>';
}

function html_print_bigger($text)
{
	html_print_big($text,$class = 'biggertext');
}

function html_print_fail($text)
{
	if (strlen($text))
		print '<p><span class="bigtext message message_fail">'.$text.'</span><p>';
}

function html_print_info($text)
{
	if (strlen($text))
		print '<p><span class="bigtext message message_ok">'.$text.'</span><p>';
}

function html_separator($wide = 0)
{
	if ($wide)
		print '<p class="separator_wide"></p>';
	else
		print '<p class="separator"></p>';
}

function html_header($page,$title = '',$headline = '')
{
	global $_SESSION;
	global $_round;
	?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="SHORTCUT ICON" href="/favicon.ico" title="external:/favicon.ico">
<title><?php print TITLE ?></title>
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/button.css" rel="stylesheet" type="text/css">
<link href="css/input.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
var stopwatch_pre_seconds = <?php print floor(__DURATIONPREMATCH__ * 60) ?>;
var stopwatch_play_seconds = <?php print floor(__DURATIONMATCH__ * 60) ?>;
var stopwatch_stop_seconds = <?php print floor(__DURATIONPPOSTMATCH__ * 60) ?>;

function getKeyCode(event)
{
	event = event || window.event;
	//alert('keycode=' + event.keyCode);
	switch (event.keyCode) {
		case 40:
			if (keybind_next)
				keybind_next();
			break;
		case 38:
			if (keybind_prev)
				keybind_prev();
			break;
		case 66:
			if (keybind_exec)
				keybind_exec();
			break;
	}
	return event.keyCode;
}

-->
</script>
<script src="js/stopwatch.js" type="text/javascript"></script>
<script src="js/jquery.js" type="text/javascript"></script>
</head>
<body onkeydown="getKeyCode(event)" onload="initPage()">
<div class="header">
	<a href="?page=index" accessKey="e"><img class="logo" src="images/Logo/Logo.svg" border=0></a>
	<span class="banner" onclick="document.location='?page=index';"><?php
		if ($title) {
			html_print_big(TITLE);
			print "<br>";
			html_print_bigger($title);
		}
		else {
			html_print_bigger(TITLE);
		}
		?></span>
	<span class="round round_<?php print (@$_round['Round'] > 0) ? $_round['State'] : '' ?>" onclick="document.location='?page=playround'";>
		<?php print (@$_round['Round'] > 0) ? $_round['Round'] : '' ?>
	</span>
</div>
<div id="stopwatch" class="stopwatch">00:00</div>
<div class="page">
<div class="pagetitle">
	<span class="pagetitleleft"><iframe class=audio name=audio id=audio src="frames/audio.php" border=0 frameborder=0 marginheight=0 marginwidth=0></iframe></span>
	<span class="pagetitlecenter"><?php print $headline; ?></span>
	<span class="pagetitleright"><?php
		if (@$_SESSION['User'] && @$_SESSION['User']['Uid']) {
				print htmlentities($_SESSION['User']['Username']);
				if (ADMIN) {
					print " (Admin)";
				}
			?> <a href="index.php?page=login&type=logout" tabindex=-1>Abmelden</a><?php
		}
		else {
			?><a href="index.php?page=login" tabindex=-1>Anmelden</a><?php
		}
	?></span>
</div>
	<?php
}

function html_footer()
{
	global $_execution_time;
	?>
</div>
<?php if (defined('__SHOW_EXECUTIONTIME__') && __SHOW_EXECUTIONTIME__) { ?>
	<div class="executiontime" id="executiontime">
	Execution Time: <?php print $_execution_time ?>ms @ <?php print $_ENV['HOSTNAME'] ?>
	</div>
<?php } ?>
</body>
</html>
	<?php
}

$button_select_redered = 0;

function html_button($id,$title,$onclick = 'return false;',$tabindex = -1,$class = 'button',$accessKey = '')
{
	global $button_select_redered;

	if (!$button_select_redered) {
		$button_select_redered = 1;
?>
<script language="JavaScript" type="text/javascript">
<!--

function button_select(id,onoff)
{
	var button = document.getElementById('button_' + id);

	button_warn(id,0);
	if (button) {
		if (onoff) {
			button.style.border = '1px solid #a0a0a0';
			button.style.background = '#d0ffd0';
		}
		else {
			button.style.border = '1px solid #c0c0c0';
			button.style.background = '#d0d0d0';
		}
	}
}

function button_disable(id,onoff)
{
	var button = document.getElementById('button_' + id);

	button_warn(id,0);
	if (button) {
		if (onoff) {
			button.style.color = '#c0c0c0';
			button.style.cursor = 'auto';
			button.style.cursor = 'auto';
			button.onclick = null;
		}
		else {
			button.style.color = '#808080';
			button.style.cursor = 'pointer';
			button.onclick = null; // better restore the right event handler
		}
	}
}

function button_warn(id,onoff)
{
	var button = document.getElementById('button_' + id);

	if (button) {
		if (onoff) {
			button.style.border = '1px solid #f00000';
		}
		else {
			button.style.border = '1px solid #c0c0c0';
		}
	}
}

function button_keypress(ev,id)
{
	var button = document.getElementById('button_' + id);

	if (ev.keyCode != 9) {
		button.onclick();
	}
}

function input_warn(id,onoff)
{
	var input = document.getElementById(id);

	if (input) {
		if (onoff) {
			input.style.border = '1px solid #f00000';
		}
		else {
			input.style.border = '1px solid #c0c0c0';
		}
	}
}

function input_set(id,value)
{
	var input = document.getElementById(id);

	if (input) {
		input.value = value;
		input_warn(id,0);
	}
}

-->
</script>
<?php
	}
	?>
	<span class="<?php print $class ?>" id='button_<?php print $id ?>'
		onclick="<?php print $onclick ?>"
		onkeypress="button_keypress(event,'<?php print $id ?>')"
		tabindex="<?php print $tabindex ?>"
		<?php print ($accessKey) ? ' accessKey="'.$accessKey.'"' : ''; ?>
		><?php print $title ?></span>
	<?php
}

function html_button_href($title,$url,$width = 0,$accessKey = 0)
{
	if (is_numeric($width))
		$width .= 'px';
	?>
	<a href="<?php print $url ?>"
		class=button
		<?php print ($width != 0) ? 'style="width:'.$width.';"' : '' ?>
		<?php print ($accessKey) ? ' accessKey="'.$accessKey.'"' : ''; ?>
		><?php print $title ?></a>
	<?php
}
