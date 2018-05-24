<?php
	$db_host = "localhost";
	$db_user = "switch_techno";
	$db_passwd = "x2S8VUDz";
	$db = "switch_techno";

	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	function create_connection() {
		global $db_host, $db_user, $db_passwd, $db;
		$mysqli = new mysqli($db_host, $db_user, $db_passwd, $db);
		$mysqli->set_charset("utf8");
		return $mysqli;
	}
?>
