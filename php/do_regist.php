<?php
	require("common.php");

	$is_input_error = 0;

	$start_date = $_POST['start_date'];
	$start_hour = $_POST['start_hour'];
	$start_min = $_POST['start_min'];
	$end_date = $_POST['end_date'];
	$end_hour = $_POST['end_hour'];
	$end_min = $_POST['end_min'];
	$network_id = $_POST['network_id'];
	$room_id = $_POST['room_id'];
	$applicant = $_POST['applicant'];
	$purpose = $_POST['purpose'];

	$error_data['start_date'] = $start_date;
	$error_data['start_hour'] = $start_hour;
	$error_data['start_min'] = $start_min;
	$error_data['end_date'] = $end_date;
	$error_data['end_hour'] = $end_hour;
	$error_data['end_min'] = $end_min;
	$error_data['network_id'] = $network_id;
	$error_data['room_id'] = $room_id;
	$error_data['applicant'] = $applicant;
	$error_data['purpose'] = $purpose;

	if (!preg_match("/^[0-9]{8}$/", $start_date) ||
		!preg_match("/^[0-9]{2}$/", $start_hour) ||
		!preg_match("/^[0-9]{2}$/", $start_min)) {
		$start_date_msg = "開始日時が無効です。";
		$is_input_error = 1;
	} else {
		$start_date_msg = "";
	}

	if (!preg_match("/^[0-9]{8}$/", $end_date) ||
		!preg_match("/^[0-9]{2}$/", $end_hour) ||
		!preg_match("/^[0-9]{2}$/", $end_min)) {
		$end_date_msg = "終了日時が無効です。";
		$is_input_error = 1;
	} else {
		$end_date_msg = "";
	}

	if (strlen($network_id) == 0) {
		$network_id_msg = "ネットワークを選択してください。";
		$is_input_error = 1;
	} else if (!preg_match("/^[0-9]{1,3}$/", $network_id)) {
		$network_id_msg = "ネットワークが無効です。";
		$is_input_error = 1;
	} else {
		$network_id_msg = "";
	}

	if (strlen($room_id) == 0) {
		$room_id_msg = "会議室・応接室を選択してください。";
		$is_input_error = 1;
	} else if (!preg_match("/^[0-9]{1,3}$/", $room_id)) {
		$room_id_msg = "会議室・応接室が無効です。";
		$is_input_error = 1;
	} else {
		$room_id_msg = "";
	}

	if (strlen($applicant) == 0) {
		$applicant_msg = "申請者を入力してください。";
		$is_input_error = 1;
	} else if (strlen($applicant) > 128) {
		$applicant_msg = "申請者が長すぎます。";
		$is_input_error = 1;
	} else {
		$applicant_msg = "";
	}

	if (strlen($purpose) == 0) {
		$purpose_msg = "利用目的を入力してください。";
		$is_input_error = 1;
	} else if (strlen($purpose) > 128) {
		$purpose_msg = "利用目的が長すぎます。";
		$is_input_error = 1;
	} else {
		$purpose_msg = "";
	}

	if ($is_input_error == 1) {
		$error_data['error_message'] = "入力に誤りがあります。";
		$error_data['start_date_msg'] = $start_date_msg;
		$error_data['end_date_msg'] = $end_date_msg;
		$error_data['network_id_msg'] = $network_id_msg;
		$error_data['room_id_msg'] = $room_id_msg;
		$error_data['applicant_msg'] = $applicant_msg;
		$error_data['purpose_msg'] = $purpose_msg;

		session_start();
		$_SESSION['error_data'] = $error_data;

		header('Location: ./index.php');
		exit;
	}

	$sdate = $start_date . $start_hour . $start_min;
	$edate = $end_date . $end_hour . $end_min;
	if ($sdate >= $edate) {
		$error_data['error_message'] = "入力に誤りがあります。";
		$error_data['end_date_msg'] = "開始日時 >= 終了日時です。";

		session_start();
		$_SESSION['error_data'] = $error_data;

		header('Location: ./index.php');
		exit;
	}

	try {
		$mysqli = create_connection();

		// 重複チェック
		$stmt = $mysqli->prepare('SELECT id FROM schedules WHERE room_id=? ' .
			'AND sdate < ? AND edate > ? AND static=0');
		if ($stmt) {
			$stmt->bind_param("iss", $room_id, $edate, $sdate);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->fetch()) {
				$error_data['error_message'] = "重複する予約があります。";
				session_start();
				$_SESSION['error_data'] = $error_data;
				$stmt->close();
				header('Location: ./index.php');
				exit;
			}
			$stmt->close();
		} else {
			$error_data['error_message'] = "登録に失敗しました。1";
			session_start();
			$_SESSION['error_data'] = $error_data;
			header('Location: ./index.php');
			exit;
		}

		// 登録
		$stmt = $mysqli->prepare('INSERT INTO schedules (' .
			'sdate, edate, network_id, room_id, applicant, purpose, ' .
			'cdate) VALUES (?, ?, ?, ?, ?, ?, now())');
		if ($stmt) {
			$stmt->bind_param("ssiiss", $sdate, $edate, $network_id, $room_id,
				$applicant, $purpose);
			$stmt->execute();
			if ($stmt->affected_rows != 1) {
				$error_data['error_message'] = "登録に失敗しました。2";
				session_start();
				$_SESSION['error_data'] = $error_data;
				$stmt->close();
				header('Location: ./index.php');
				exit;
			}
			$stmt->close();
		} else {
			$error_data['error_message'] = "登録に失敗しました。3";
			session_start();
			$_SESSION['error_data'] = $error_data;
			header('Location: ./index.php');
			exit;
		}
	} finally {
		$mysqli->close();
	}

	header('Location: ./index.php', true, 301);
	exit();
?>
