<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Module to display the main menu dialog with all available
**	pages
**
**	GET params:
**		-
**
**	POST params:
**		-
*/

html_separator();
foreach ($pages as $pageid => $page) {
	if ($page['title'] && $page['show'] && !$page['admin']) {
		html_button_href($page['title'],'?page='.$pageid,'8em',$page['accessKey']);
		if ($page['separator'])
			html_separator();
	}
}

if (ADMIN) {
	?></div><div class=adminfooter><?php
	foreach ($pages as $pageid => $page) {
		if ($page['title'] && $page['show'] && $page['admin']) {
			html_button_href($page['title'],'?page='.$pageid,-1,$page['accessKey']);
		}
	}
	html_button_href('Handbuch','doc/Turnier.pdf');
	?></div><div><?php
}
