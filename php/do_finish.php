<?php
	require("common.php");

	$is_input_error = 0;
	$error_message = "";

	$id = $_GET['id'];

	$error_data['id'] = $id;

	if (!preg_match("/^[0-9]+$/", $id)) {
		$error_data['error_message'] = "予約番号が無効です。";
		$error_data['id'] = $id;
		session_start();
		$_SESSION['error_data'] = $error_data;
		header('Location: ./index.php');
	} else {

		try {
			$mysqli = create_connection();

			// 開催中か
			$stmt = $mysqli->prepare('SELECT id FROM schedules WHERE id=? ' .
				'AND DATE_FORMAT(now(), "%Y%m%d%H%i") BETWEEN sdate ' .
				'AND edate AND static=0');
			if ($stmt) {
				$stmt->bind_param("i", $id);
				$stmt->execute();
				$stmt->store_result();
				if (!$stmt->fetch()) {
					$error_data['error_message'] =
						"開催中の会議ではありません。";
					$error_data['id'] = $id;
					session_start();
					$_SESSION['error_data'] = $error_data;
					$stmt->close();
					header('Location: ./index.php');
					exit;
				}
				$stmt->close();
			} else {
				$error_data['error_message'] = "変更に失敗しました。1";
				$error_data['id'] = $id;
				session_start();
				$_SESSION['error_data'] = $error_data;
				header('Location: ./update.php');
				exit;
			}

			// 終了
			$stmt = $mysqli->prepare('UPDATE schedules SET ' .
				'edate=DATE_FORMAT(now(), "%Y%m%d%H%i"), udate=now() ' .
				'WHERE id=? AND static=0');
			if ($stmt) {
				$stmt->bind_param("i", $id);
				$stmt->execute();
				if ($stmt->affected_rows != 1) {
					$error_data['error_message'] = "更新に失敗しました。2";
					$error_data['id'] = $id;
					session_start();
					$_SESSION['error_data'] = $error_data;
					$stmt->close();
					header('Location: ./index.php');
					exit;
				}
				$stmt->close();
			} else {
				$error_data['error_message'] = "変更に失敗しました。3";
				$error_data['id'] = $id;
				session_start();
				$_SESSION['error_data'] = $error_data;
				header('Location: ./index.php');
				exit;
			}
		} finally {
			$mysqli->close();
		}
	}

	header('Location: ./index.php');
	exit();
?>
