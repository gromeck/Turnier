<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to test the audio conectivity
**
**	GET params:
**		-
**
**	POST params:
**		-
*/
if (!ADMIN) { print "Not admin!"; exit(); };

$infomsg = '';
$errmsg = '';

$bw = 300;

html_print_big('Audio-Player: '.__MPC_HOST__);
print '<br>';
html_button('audioplayer','Web-Interface des Audio-Players öffnen',"audio_openplayer('".__MPC_HOST__."')",$bw);
print '<p>';
html_print_big('Playliste: '.__MPC_PLAYLIST__);
print '<br>';
html_print_big('Laustärke für Effekt: '.__MPC_VOLUME_EFFECT__.'%');
print '<br>';
html_print_big('Laustärke für Musik: '.__MPC_VOLUME_MUSIC__.'%');
html_separator();

html_print_big('Test');
print '<br>';
html_button('audiotest_start','Start-Signal',"audio_play('start')",$bw);
print '<br>';
html_button('audiotest_stop','Stop-Signal',"audio_play('stop')",$bw);
print '<br>';
html_button('audiotest_playlist','Playlist (neu) laden',"audio_playlist('".__MPC_PLAYLIST__."')",$bw);
print '<br>';
html_button('audiotest_playlist','Playlist leeren',"audio_playlist('')",$bw);

?>
