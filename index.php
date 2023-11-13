<?php
/*
**	a quick hack for the
**
**		1. Goddelauer Badminton Nachturnier
**
**
**	Christian Lorenz
*/

/*
**	store the execution time
*/
$_execution_time = microtime(true);

/*
**	do the includes
*/
include_once 'inc/settings.inc.php';
include_once 'inc/common.inc.php';
include_once 'inc/html.inc.php';
include_once 'inc/util.inc.php';
include_once 'inc/round.inc.php';
include_once 'inc/database.inc.php';


/*
**	the available pages
*/
$pages = array(
	'index' =>		   		array( 'title' => '',							'show' => 1, 'admin' => 0, 'separator' => 0, 'accessKey' => '' ),
	'login' =>		   		array( 'title' => 'Login',						'show' => 0, 'admin' => 0, 'separator' => 0, 'accessKey' => '' ),
	'checkin' =>	   		array( 'title' => 'Check-In',					'show' => 1, 'admin' => 0, 'separator' => 1, 'accessKey' => '' ),
	'overallstatistics' =>	array( 'title' => 'Gesamt-Statistik',			'show' => 1, 'admin' => 0, 'separator' => 0, 'accessKey' => '' ),
	'playerstatistics' =>	array( 'title' => 'Spieler-Statistik',			'show' => 1, 'admin' => 0, 'separator' => 0, 'accessKey' => '' ),
	'roundstatistics' =>	array( 'title' => 'Runden-Statistik',			'show' => 1, 'admin' => 0, 'separator' => 1, 'accessKey' => '' ),
	'playerboard' =>   		array( 'title' => 'Spielerliste',				'show' => 1, 'admin' => 0, 'separator' => 1, 'accessKey' => HTML_BUTTON_GREEN ),
	'playround' =>	   		array( 'title' => 'Spielrunde',					'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => HTML_BUTTON_RED ),
	'admin-intro' =>		array( 'title' => 'Intro',						'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => HTML_BUTTON_YELLOW ),
	'admin-settings' =>	   	array( 'title' => 'Einstellungen',				'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-player' =>   	array( 'title' => 'Spieler-Administration',		'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-register' =>	   	array( 'title' => 'Spieler-Registrierung',		'show' => 0, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-round' =>   		array( 'title' => 'Runden-Administration',		'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-audiotest' =>	array( 'title' => 'Audio-Test',					'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-rfid' =>			array( 'title' => 'RFID-Mgmt',					'show' => 1, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'admin-rfidlink' =>		array( 'title' => 'RFID-Kopplung',				'show' => 0, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
	'playerinfo' =>			array( 'title' => 'Spielerinfo',				'show' => 0, 'admin' => 1, 'separator' => 0, 'accessKey' => '' ),
);

if (!($page = @$pages[$pageid = @$_GET['page']]))
	$page = $pages[$pageid = 'index'];
$_page_title = $page['title'];
//print_r($page);

/*
**	get the current round number from the database
*/
$_round = db_round_last();
//print_r($_round);

/*
**	call the page (which might set the page headline)
*/
ob_start();
include 'pages/'.$pageid.'.inc.php';
$content = ob_get_clean();

html_header($pageid,$_page_title,$_page_headline);
print $content;
$_execution_time = round((microtime(true) - $_execution_time) * 1000,3);
html_footer();
