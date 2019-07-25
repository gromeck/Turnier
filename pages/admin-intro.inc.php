<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to show the intro presentation
**
**	GET params:
**		subpage		controls which page of the presentation is displayed
**
**	POST params:
**		-
*/
if (!ADMIN) { print "Not admin!"; exit(); };

if (!($subpage = @$_GET['subpage']))
	$subpage = 0;

$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL);

/*
**	the intro pages
*/
$intro = array(
		array(
			'title' => 'Willkommen',
			'content' => '<p><center>
					Willkommen zum
					<p>
					<b>'.__TITLE__.'</b>
					</center>
					<p>'
		),
		array(
			'title' => 'Essen & Trinken',
			'content' => '<ul>'.
					'<li>Super Buffet &mdash; danke an Alle für Ihren Beitrag dazu!
					<li>Für die Getränke hängt eine Strichliste am Kühlschrank.
					<li>Bevor ihr nach Hause geht, zahlt bitte eure Getränke zusammen mit dem Startgeld bei Trixi, Zoltan oder Bernd.
					<li>Gebt bitte auch eure Armbänder zurück.
				</ul>'
		),
		array(
			'title' => 'Organisatorisches',
			'content' => '<ul>
					<li>Aus Rechtlichen Gründen sind wir eine geschlossene Gesellschaft (d.h. Türen schließen).
					<li>Vermeidet bitte Lärm beim Verlassen der Halle oder wenn ihr draussen raucht.
					<li>Wir räumen morgen früh um 10:00 auf &mdash; dazu sind alle herzlich eingeladen.
				</ul>'
		),
		array(
			'title' => 'noch mehr Organisatorisches',
			'content' => '<ul>
					<li>Große Pause machen wir ca. ab 21:45-22:00 (nach 6 Runden).
					<li>Spieler, die in der 6. Runde Pause haben, dürfen das Buffet eröffnen.
				</ul>'
		),
		array(
			'title' => 'Spielfelder',
			'content' =>
					'<ul>'.
				((__COURTS_CLASS1_MIN__)
					?
						'<li>Kids zwischen 12 und 16 Jahren spielen eigene Runde
						<li>mind. '.__COURTS_CLASS1_MIN__.' Felder für Kids
						<li>mind. '.__COURTS_CLASS2_MIN__.' Felder für Erwachsene'
					:
						'<li>'.count($players).' Spieler haben sich registriert.
						<li>Wir spielen auf '.__COURTS_CLASS2_MIN__.' Feldern.').
					'<li>Bitte <b>Vorsicht beim Durchlaufen der Halle</b>!
				</ul>'
		),
		array(
			'title' => 'Auslosung',
			'content' => '<ul>
					<li>In jeder Runde werden die Paarungen ausgelost.
					<li>Es wird angezeigt, wer mit wem gegen wen spielt und wer pausiert.
					<li>Bei jeder Paarung ist die Platznummer angegeben.
					<li>Ein- und Auschecken am Terminal mit Armband ist jederzeit möglich.
					<li>Denkt immer daran, dass wir ein Jux-Turnier spielen: spielt fair!
				</ul>'
		),
		array(
			'title' => 'Spieldauer',
			'content' => '<ul>
					<li>Vor jedem Match sind '.__DURATIONPREMATCH__.' Minute Einspielzeit vorgesehen.
					<li>Das Match dauert '.__DURATIONMATCH__.' Minuten ohne Punktelimit.
					<li>Jeder Punkt zählt, wer punktet bekommt das Aufschlagsrecht.
					<li>Aufschlag diagonal von links bzw. rechts je nach eigenem Punktestand.
					<li>Gewinner erhalten Bändchen für den Schläger.
				</ul>'
		),
		array(
			'title' => 'Foto',
			'content' => '<center><img style="width:80vw;" src="/images/Foto/Foto-'.(__YEAR__ - 1).'.jpg">
					</center>'
		),
		array(
			'title' => 'Los geht\'s!',
			'content' => '<p><center>
					... nun viel Spa&szlig; und faire Spieler bei unserem
					<br><b>'.__TITLE__.'</b>
					</center>
					<p>'
		),
	);

?>
<script language="JavaScript" type="text/javascript">

var keybind_allow = 1;

function keybind_prev()
{
	if (keybind_allow)
		document.location = '<?php print "?page=$pageid&subpage=".($subpage - 1) ?>';
}

function keybind_next()
{
	if (keybind_allow)
		document.location = '<?php print "?page=$pageid&subpage=".($subpage + 1) ?>';
}

function keybind_exec()
{
}

</script>
<center>
<div id=page>
	<?php
		html_separator();
		if ($subpage > 0) {
			html_button(1,'&lt;&lt;&lt;','keybind_prev()',-1,'button','b');
		}

		print '<span class="intro-title">'.$intro[$subpage]['title'].'</span>';

		if ($subpage < count($intro) - 1) {
			html_button(1,'&gt;&gt;&gt;','keybind_next()',-1,'button','d');
		}
		else
			html_button_href('weiter zur Spielerliste','?page=playerboard',0,'a');

		html_separator();
		print '<div class=intro>'.$intro[$subpage]['content'].'</div>';
	?>
</div>
</center>
