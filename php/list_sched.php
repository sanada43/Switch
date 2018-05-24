<?php
	require("common.php");

	try {
		$mysqli = create_connection();

		$schedules = $mysqli->query(
			'SELECT s.id as sid, s.sdate as ssdate, s.edate as sedate, ' .
				'n.id as nid, n.name as nname, r.id rid, r.name as rname, ' .
				's.static as sstatic, LEFT(s.applicant, 10) as sapplicant, ' .
				'LEFT(s.purpose, 10) as spurpose, ' .
				'DATE_FORMAT(now(), "%Y%m%d%H%i") ' .
				'BETWEEN s.sdate AND s.edate as nowon ' .
			'FROM schedules s, network_master n, room_master r ' .
			'WHERE s.edate > DATE_FORMAT(now(), "%Y%m%d%H%i") AND ' .
				's.network_id = n.id AND s.room_id = r.id ' .
			'ORDER BY s.static, s.sdate, s.edate, s.room_id');
		$connections = $mysqli->query(
			'SELECT n.name as nname, r.name as rname ' .
			'FROM vlanstatus v, network_master n, room_master r ' .
			'WHERE v.vlan = n.vlan AND v.portno = r.portno ' .
			'ORDER BY n.id, r.id');
		$dates = $mysqli->query(
			'SELECT DATE_FORMAT(now(), "%Y/%m/%d %H:%i") AS a FROM dual');
	} finally {
		$mysqli->close();
	}

	// Content-TypeをJSONに指定する
	header('Content-Type: application/json');

	$data = array();

	$data['schedules'] = array();
	foreach ($schedules as $schedule) {
		array_push($data['schedules'], $schedule);
	}

	$data['connections'] = array();
	foreach ($connections as $connection) {
		array_push($data['connections'], $connection);
	}

	foreach ($dates as $date) {
		$data['date'] = $date['a'];
	}

	echo json_encode($data);
?>
