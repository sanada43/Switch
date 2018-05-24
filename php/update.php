<?php
	require("common.php");

	try {
		$mysqli = create_connection();
		require("prepare_info.php");

		$start_date = strftime('%Y%m%d', $stand_s);
		$start_hour = strftime('%H', $stand_s);
		$start_min = strftime('%M', $stand_s);
		$start_date_msg = "";

		$end_date = strftime('%Y%m%d', $stand_e);
		$end_hour = strftime('%H', $stand_e);
		$end_min = strftime('%M', $stand_e);
		$end_date_msg = "";

		$network_id = "";
		$network_id_msg = "";

		$room_id = "";
		$room_id_msg = "";
	
		$applicant = "";
		$applicant_msg = "";
	
		$purpose = "";
		$purpose_msg = "";

		$error_message = "";
		$cerror_message = "";

		session_start();
		if (isset($_SESSION['error_data'])) {
			$error_data = $_SESSION['error_data'];
	
			$id = $error_data['id'];

			$start_date = $error_data['start_date'];
			$start_hour = $error_data['start_hour'];
			$start_min = $error_data['start_min'];
			$start_date_msg = $error_data['start_date_msg'];

			$end_date = $error_data['end_date'];
			$end_hour = $error_data['end_hour'];
			$end_min = $error_data['end_min'];
			$end_date_msg = $error_data['end_date_msg'];

			$network_id = $error_data['network_id'];
			$network_id_msg = $error_data['network_id_msg'];

			$room_id = $error_data['room_id'];
			$room_id_msg = $error_data['room_id_msg'];

			$applicant = $error_data['applicant'];
			$applicant_msg = $error_data['applicant_msg'];

			$purpose = $error_data['purpose'];
			$purpose_msg = $error_data['purpose_msg'];
	
			$error_message = $error_data['error_message'];

			unset($_SESSION['error_data']);
		} else {
			$id = $_GET['id'];
			if (!preg_match("/^[0-9]+$/", $id)) {
				$error_message = "予約番号が無効です。";
			} else {
				$stmt = $mysqli->prepare(
					'SELECT ' .
					'substring(sdate, 1, 8) as start_date, ' .
					'substring(sdate, 9, 2) as start_hour, ' .
					'substring(sdate, 11, 2) as start_min, ' .
					'substring(edate, 1, 8) as end_date, ' .
					'substring(edate, 9, 2) as end_hour, ' .
					'substring(edate, 11, 2) as end_min, ' .
					'network_id, room_id, applicant, purpose FROM schedules ' .
					'where id=? AND static=0');
				if ($stmt) {
					$stmt->bind_param("i", $id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result(
						$start_date, $start_hour, $start_min,
						$end_date, $end_hour, $end_min, 
						$network_id, $room_id, $applicant, $purpose);
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
		}
	} finally {
		$mysqli->close();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="./jquery-3.2.1.min.js"></script>
	<script src="./confirm.js"></script>
	<script src="./list_sched.js"></script>
	<link rel="stylesheet" type="text/css" href="./style.css" />
</head>
<body>

<hr class="hr-text" data-content="予約変更" />

<?php
	if ($cerror_message) {
?>
<font color='#FF0000'><?php echo $cerror_message; ?><br />
<a href="./index.php">トップ</a>
<?php
	} else {
?>
<form method="post" action="./do_update.php">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
<?php
		require("input_form.php");
?>
<input type="button" value="戻る" onClick='window.location.href = "./index.php";' />
<input type="submit" value="変更" id="update" />
</form>

<br />
<hr class="hr-text" data-content="接続状態" />
<div id="connections" align="center"></div>
<div id="date" align="center"></div>

<br />
<hr class="hr-text" data-content="スケジュール" />
<div id="schedules"></div>

<?php
	}
?>

</body>
</html>
