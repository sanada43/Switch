<?php
	require("common.php");

	$start_year = "";
	$start_month = "";
	$start_day = "";
	$start_hour = "";
	$start_min = "";
	$end_year = "";
	$end_month = "";
	$end_day = "";
	$end_hour = "";
	$end_min = "";
	$network_id = "";
	$network_name = "";
	$room_id = "";
	$room_name = "";
	$applicant = "";
	$purpose = "";
	$error_message = "";

	$id = $_GET['id'];
	if (!preg_match("/^[0-9]+$/", $id)) {
		$error_message = "予約番号が無効です。";
	} else {

		try {
			$mysqli = create_connection();
			require("prepare_info.php");

			$id = $_GET['id'];
			if (!preg_match("/^[0-9]+$/", $id)) {
				$error_message = "予約番号が無効です。";
			} else {
				$stmt = $mysqli->prepare(
					'SELECT ' .
						'substring(s.sdate, 1, 4) as start_year, ' .
						'substring(s.sdate, 5, 2) as start_month, ' .
						'substring(s.sdate, 7, 2) as start_day, ' .
						'substring(s.sdate, 9, 2) as start_hour, ' .
						'substring(s.sdate, 11, 2) as start_min, ' .
						'substring(s.edate, 1, 4) as end_year, ' .
						'substring(s.edate, 5, 2) as end_month, ' .
						'substring(s.edate, 7, 2) as end_day, ' .
						'substring(s.edate, 9, 2) as end_hour, ' .
						'substring(s.edate, 11, 2) as end_min, ' .
						'n.id as nid, n.name as nname, r.id as rid, ' .
						'r.name as rname, s.applicant as sapplicant, ' .
						's.purpose as spurpose ' .
					'FROM schedules s, network_master n, room_master r ' .
					'WHERE s.id=? AND s.static=0 AND ' .
						's.network_id = n.id AND s.room_id = r.id'
				);
				if ($stmt) {
					$stmt->bind_param("i", $id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($start_year, $start_month, $start_day,
						$start_hour, $start_min, $end_year, $end_month,
						$end_day, $end_hour, $end_min, $network_id,
						$network_name, $room_id, $room_name, $applicant,
						$purpose);
					if ($stmt->fetch()) {
						;
					} else {
						$cerror_message = "該当する予約はありませんでした。";
					}
					$stmt->close();
				} else {
					$cerror_message = "検索に失敗しました。id=$id";
				}
			}
		} finally {
			$mysqli->close();
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="./jquery-3.2.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="./style.css" />
</head>
<body>

<hr class="hr-text" data-content="詳細" />

<?php
	if ($error_message) {
?>
<font color='#FF0000'><?php echo $error_message; ?><br />
<a href="./index.php">トップ</a>
<?php
	} else {
?>

<table id="input_form">

<tr>
<th class="t_top">開始日時</th>
<td class="t_top">
<?php echo htmlspecialchars($start_year) ?>年<?php echo htmlspecialchars($start_month) ?>月<?php echo htmlspecialchars($start_day) ?>日<?php echo htmlspecialchars($start_hour) ?>時<?php echo htmlspecialchars($start_min) ?>分
</td>
</tr>

<tr>
<th>終了日時</th>
<td>
<?php echo htmlspecialchars($end_year) ?>年<?php echo htmlspecialchars($end_month) ?>月<?php echo htmlspecialchars($end_day) ?>日<?php echo htmlspecialchars($end_hour) ?>時<?php echo htmlspecialchars($end_min) ?>分
</td>
</tr>

<tr>
<th>ネットワーク</th>
<td>
<?php echo htmlspecialchars($network_name) ?>
</td>
</tr>

<tr>
<th>会議室・応接室</th>
<td>
<?php echo htmlspecialchars($room_name) ?>
</td>
</tr>

<tr>
<th>申請者</th>
<td>
<?php echo htmlspecialchars($applicant) ?>
</td>
</tr>

<tr>
<th>利用目的</th>
<td>
<?php echo str_replace("\r\n", "<br />", htmlspecialchars($purpose)) ?>
</td>
</tr>

</table>
<a href="javascript:history.back();">戻る</a>
<a href="./index.php">トップ</a>

<?php
	}
?>

</body>
</html>
