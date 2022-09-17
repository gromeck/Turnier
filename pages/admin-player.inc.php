<?php
/*
**	This file is part of Turnier.
**	
**	(c) 2004-2018 by Christian Lorenz Christian.Lorenz@gromeck.de
**
**	-------------------------------------------------------------
**
**	Admin module to manage the players
**
**	GET params:
**		pid			a single pid or a complete array of pids
**		action		the action to apply on the pids
**					activate, deactivate, ban, unban, delete
**
**	POST params:
**		-
*/
if (!ADMIN) { print "Not admin!"; exit(); };

if (@$_GET) {
	if (@$_GET['pid']) {
		if (is_array($_GET['pid']))
			$pids = $_GET['pid'];
		else
			$pids = array( $_GET['pid'] );
	}
	else
		$pids = array();

	foreach ($pids as $pid) {
		switch (@$_GET['action']) {
			case 'activate':
				/*
				**	activate
				*/
				//html_print_info('Spieler '.$pid.' aktiviert!');
				db_player_activate($pid,1);
				break;
			case 'deactivate':
				/*
				**	activate
				*/
				//html_print_info('Spieler '.$pid.' deaktiviert!');
				db_player_activate($pid,0);
				break;
			case 'ban':
				/*
				**	ban
				*/
				//html_print_info('Spieler '.$pid.' gesperrt!');
				db_player_ban($pid,1);
				break;
			case 'unban':
				/*
				**	unban
				*/
				//html_print_info('Spieler '.$pid.' entsperrt!');
				db_player_ban($pid,0);
				break;
			case 'delete':
				/*
				**	unban
				*/
				//html_print_info('Spieler '.$pid.' gelöscht!');
				db_player_delete($pid);
				break;
		}
	}
}

/*
**	get all players from this class
*/
$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL);
//dump_array('players',$players);

/*
**	count the number of linked players
*/
$players_linked = 0;
$players_unbanned = 0;
for ($n = 0;$n < count($players);$n++) {
	if (!$players[$n]['Banned']) {
		$players_unbanned++;
		if ($players[$n]['RFIDcode'])
			$players_linked++;
	}
}

/*
**	set the page headline
*/
$_page_headline = 'Ungesperrte Spieler: '.$players_unbanned.', davon gekoppelt: '.$players_linked;

?>
<script language="JavaScript">
$(document).ready(function() {
	$("#filter").keyup(function() {
		var pattern = this.value.toUpperCase().split(" ");
		var rows = $("#admin_player_list_tbody").find("tr");
		if (this.value == "") {
			rows.show();
		}
		else {
			rows.hide();
			rows.filter(function (i, v) {
				var $t = $(this);
				for (var d = 0; d < pattern.length; ++d) {
					if ($t.text().toUpperCase().indexOf(pattern[d]) > -1) {
						return true;
					}
				}
				return false;
			})
			.show();
		}
	});
	$('#filter').change(function() { $('#filter').trigger('keyup'); });
	$('input.deletable').wrap('<span class="deleteicon" />').after($('<span/>').click(function() {
		$(this).prev('input').val('').trigger('change').focus();
	}));
	$('#filter').focus();

	$('#toggleplayer').click(function() {
		$('#admin_player_list_tbody tr:visible input:checkbox').prop('checked', $(this).is(':checked'));
	});
});
function marked2query()
{
	var query = '';
	var checkboxes = document.getElementsByName('playermark[]');
	for (var cb in checkboxes){
		if (checkboxes[cb].type == 'checkbox' && checkboxes[cb].checked)
			query += '&pid[]='+checkboxes[cb].value;
	}
	return query;
}
function clickedPlayer(pid)
{
	clickedInfo(pid);
}
function clickedAddPlayer(pid)
{
	document.location = '?page=admin-register';
}
function clickedInfo(pid)
{
	document.location = '?page=playerinfo&pid=' + pid;
}
function clickedEdit(pid)
{
	document.location = '?page=admin-register&pid=' + pid;
}
function clickedActionMarked()
{
	var obj = document.getElementById('action');
	var conf = parseInt(obj.options[obj.selectedIndex].getAttribute('data-confirm'));
	var frameurl = obj.options[obj.selectedIndex].getAttribute('data-frameurl');
	var text = obj.options[obj.selectedIndex].text;
	var action = obj.options[obj.selectedIndex].value;

	if (frameurl) {
		/*
		**	push the PDF generation into this frame
		*/
		document.location = frameurl + marked2query();
	}
	else {
		/*
		**	send a GET request
		*/
		if (!conf || confirm('Alle ausgewählten Spieler ' + text + '?'))
			document.location = '?page=<?php print $_GET['page'] ?>&action=' + action + marked2query()
	}
}
function clickedRFIDlink(pid)
{
	document.location = '?page=admin-rfidlink&action=link&pid=' + pid;	
}
function clickedRFIDunlink(pid)
{
	document.location = '?page=admin-rfidlink&action=unlink&pid=' + pid;	
}
</script>
	<div class=admin-panel>
		<div style="float:left;">
			<?php
				print('Filter:');
			?>
			<input name="filter" id="filter" class="deletable" autocomplete=off size=40>
		</div>
		<div style="float:right;">
			<?php
				print('Alle ausgewählten Spieler ...');
				?>
				<select name="action" id="action" size="1">
					<option data-confirm=0 data-frameurl="" value=""></option>
					<option data-confirm=0 data-frameurl="" value="activate">aktivieren</option>
					<option data-confirm=0 data-frameurl="" value="deactivate">deaktivieren</option>
					<option data-confirm=1 data-frameurl="" value="ban">sperren</option>
					<option data-confirm=1 data-frameurl="" value="unban">entsprerren</option>
					<option data-confirm=1 data-frameurl="" value="delete">löschen</option>
				</select>
				<?php
				html_button('action','Los!','clickedActionMarked();',-1,'button_tiny');
			?>
		</div>
		<div style="float:fill;">
			<?php
				html_button('addplayer','Neuer Spieler','clickedAddPlayer();',-1,'button_tiny');
			?>
		</div>
		<?php
		html_separator();

		?>
	</div>
	<center>
	<table class="table-player-admin" border=1 cellspacing=0 cellpadding=5 width=100%>
		<tr class="tr-head">
			<td align=center><input type=checkbox id=toggleplayer></td>
			<td class="td-head">#</td>
			<td class="td-head">Nick</td>
			<td class="td-head">Name</td>
			<td class="td-head">RFID-Code</td>
			<td class="td-head" align=center>Klasse</td>
			<td class="td-head">Bearbeitung</td>
		</tr>
		<tbody id="admin_player_list_tbody">
		<?php
		for ($n = 0;$n < count($players);$n++) {
			?>
			<tr class=<?php print ($n % 2) ? 'tr-odd' : 'tr-even' ?>>
				<td align=center><input type=checkbox autocomplete=off
					name=playermark[]
					id=player<?php print $players[$n]['Pid']; ?>
					value=<?php print $players[$n]['Pid']; ?>></td>
				<td><?php print $n + 1 ?></td>
				<td><?php print print_player($players[$n],0); ?></td>
				<td><?php print $players[$n]['Firstname'].' '.$players[$n]['Lastname']; ?></td>
				<td align=center><?php print $players[$n]['RFIDcode']; ?></td>
				<td align=center><?php print lookup_class($players[$n]['Class']); ?></td>
				<td><?php
					html_button('playerinfo','Info','clickedInfo('.$players[$n]['Pid'].');');
				    html_button('playeredit','bearbeiten','clickedEdit('.$players[$n]['Pid'].');');
					if ($players[$n]['RFIDcode'])
						html_button('rfidunlink','entkoppeln','clickedRFIDunlink('.$players[$n]['Pid'].');');
					else
						html_button('rfidlink','koppeln','clickedRFIDlink('.$players[$n]['Pid'].');');
					?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<tr class="tr-foot">
			<td class="td-foot"></td>
			<td class="td-foot"><?php print count($players) ?></td>
			<td class="td-foot"></td>
			<td class="td-foot"></td>
			<td class="td-foot"><?php print $players_linked ?></td>
			<td class="td-foot"></td>
			<td class="td-foot"></td>
		</tr>
	</table>
	</center>
