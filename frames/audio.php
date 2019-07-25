<?php
/*
**	trigger audio effect
*/
include_once "../inc/settings.inc.php";

if (@isset($_GET['effect'])) {
	$cmdline = '../scripts/mpc-play-effect.sh '.__MPC_HOST__.' '.__MPC_VOLUME_EFFECT__.' '.__MPC_VOLUME_MUSIC__.' '.$_GET['effect'];
	print "Audio: $cmdline";
	passthru($cmdline);
}
if (@isset($_GET['playlist'])) {
	$cmdline = '../scripts/mpc-start-playlist.sh '.__MPC_HOST__.' '.__MPC_VOLUME_MUSIC__.' '.$_GET['playlist'];
	print "Audio: $cmdline";
	passthru($cmdline);
}

?>
