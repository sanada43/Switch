<?php
	// 開始の基準日付
	$stand_s = time();
	// 終了の基準日付
	$stand_e = $stand_s + 3600;

	// ネットワークSELECT
	$networks = $mysqli->query('SELECT * FROM network_master');

	// 会議室SELECT
	$rooms = $mysqli->query('SELECT * FROM room_master');

	// 日付SELECT
	for ($i = -1; $i <= 40; $i++) {
		$start_date_nam = strftime('20%y/%m/%d', $stand_s + 86400 * $i);
		$start_date_val = strftime('20%y%m%d', $stand_s + 86400 * $i);
		$end_date_nam = strftime('20%y/%m/%d', $stand_e + 86400 * $i);
		$end_date_val = strftime('20%y%m%d', $stand_e + 86400 * $i);
		$start_dates[$start_date_val] = $start_date_nam;
		$end_dates[$end_date_val] = $end_date_nam;
	}

	// 時間SELECT
	for ($h = 0; $h < 24; $h++) {
		$hour_nam = sprintf("%02d", $h);
		$hour_val = $hour_nam;
		$hours[$hour_val] = $hour_nam;
	}

	// 分SELECT
	for ($m = 0; $m < 60; $m++) {
		$min_nam = sprintf("%02d", $m);
		$min_val = $min_nam;
		$mins[$min_val] = $min_nam;
	}

	// 年SELECT
	$years = array();
	$year = strftime('20%y', $stand_s);
	for ($i = 0; $i <= 20; $i++) {
		$year_nam = $year - $i;
		$year_val = $year - $i;
		$years[$year_val] = $year_nam;
	}

	// 月SELECT
	for ($m = 1; $m <= 12; $m++) {
		$month_nam = $m;
		$month_val = $m;
		$months[$month_val] = $month_nam;
	}

	// 日SELECT
	for ($d = 1; $d <= 31; $d++) {
		$day_nam = $d;
		$day_val = $d;
		$days[$day_val] = $day_nam;
	}
?>
