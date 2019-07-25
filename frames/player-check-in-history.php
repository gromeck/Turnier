<?php
/*
**	list players
*/
include_once "../inc/settings.inc.php";
include_once "../inc/common.inc.php";
include_once "../inc/database.inc.php";
include_once "../inc/html.inc.php";
include_once "../inc/util.inc.php";

?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link href="../css/common.css" rel="stylesheet" type="text/css">
<link href="../css/button.css" rel="stylesheet" type="text/css">
<link href="../css/input.css" rel="stylesheet" type="text/css">
</head>
<body onload="initPage()">
<script>
function initPage()
{
	setTimeout("document.location.reload()",2000);
}

</script>
<center>
<div class=fadeoutbox>
<?php

$players = db_player_list(DB_PLAYER_ALL,DB_PLAYER_ALL,DB_PLAYER_ALL,"ActiveChangeDate DESC LIMIT 20");
//print_r($players);
foreach ($players as $player) {
	print_player($player);
	print ' '.timestamp2relative($player['ActiveChangeDate']).'<br>';
}

?>
<div class=fadeout></div>
</div>
</center>
</body>
</html>
