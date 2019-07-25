<?php
/*
**	a quick hack for the
**
**		1. Goddelauer Badminton Nachturnier
**		2. Goddelauer Badminton Nachturnier
**		3. Goddelauer Badminton Nachturnier
**		4. Goddelauer Badminton Nachturnier
**
**
**	Christian Lorenz
*/

/*
**	database access
*/
define('DB_HOSTNAME','localhost');
define('DB_DATABASE','Badminton');
define('DB_USERNAME','Badminton');
define('DB_PASSWORD','Badminton');
include_once 'util.inc.php';
include_once 'database.inc.php';

/*
**	set the defaults
*/
$settings = array(
	array( 'Name' => 'TITLE',              'Title' => 'Titel',                            'Value' => '15. Goddelauer Badminton Nachtturnier', 'Units' => '' ),
	array( 'Name' => 'YEAR',               'Title' => 'Jahr',                             'Value' => 2014, 'Units' => '' ),
	array( 'Name' => 'LOGO',               'Title' => 'Logo',                             'Value' => '',   'Units' => '' ),
	array( 'Name' => 'COURTS_MAX',         'Title' => 'Max. Plätze gesamt',               'Value' => 9,    'Units' => '' ),
	array( 'Name' => 'COURTS_CLASS1_MIN',  'Title' => 'Min. Plätze Klasse '.lookup_class(1),    'Value' => 2,    'Units' => '' ),
	array( 'Name' => 'COURTS_CLASS2_MIN',  'Title' => 'Min. Plätze Klasse '.lookup_class(2),    'Value' => 7,    'Units' => '' ),
	array( 'Name' => 'DURATIONPREMATCH',   'Title' => 'Einspielzeit',                     'Value' => 1,    'Units' => 'Minuten' ),
	array( 'Name' => 'DURATIONMATCH',      'Title' => 'Spielzeit',                        'Value' => 10,   'Units' => 'Minuten' ),
	array( 'Name' => 'DURATIONPPOSTMATCH', 'Title' => 'Nachspielzeit',                    'Value' => 0.5,  'Units' => 'Minuten' ),
	array( 'Name' => 'MPC_HOST',           'Title' => 'MusicPlayer Host oder IP',         'Value' => '192.168.2.15',  'Units' => '' ),
	array( 'Name' => 'MPC_PLAYLIST',       'Title' => 'MusicPlayer Playlist',             'Value' => 'Mixed',  'Units' => '' ),
	array( 'Name' => 'MPC_VOLUME_EFFECT',  'Title' => 'MusicPlayer Pegel für Effekte',    'Value' => '50',  'Units' => '%' ),
	array( 'Name' => 'MPC_VOLUME_MUSIC',   'Title' => 'MusicPlayer Pegel für Musik',      'Value' => '30',  'Units' => '%' ),
);

/*
**	load the settings from the database
*/
$settings = db_settings_load($settings);

/*
**	define all settings as defines
*/
foreach ($settings as $setting) {
	define('__'.$setting['Name'].'__',$setting['Value']);
}

/*
**	are the classes used?
*/
define('CLASS1USED',(__COURTS_CLASS1_MIN__) ? true : false);
define('CLASS2USED',(__COURTS_CLASS2_MIN__) ? true : false);
define('CLASSESUSED',(CLASS1USED && CLASS2USED) ? true : false);

//print_r($settings);


