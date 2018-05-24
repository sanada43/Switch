<?php
	require("common.php");

	try {
		$mysqli = create_connection();

		$error_message = "";

		$id = $_GET['id'];
		if (!preg_match("/^[0-9]+$/", $id)) {
			$error_message = "予約番号が無効です。";
		} else {
			$stmt = $mysqli->prepare(
				'DELETE FROM schedules where id=? AND static=0');
			if ($stmt) {
				$stmt->bind_param("i", $id);
				$stmt->execute();
				if ($stmt->affected_rows != 1) {
					$error_message = "削除に失敗しました。";
				}
				$stmt->close();
			} else {
				$error_message = "削除に失敗しました。";
			}
		}
	} finally {
		$mysqli->close();
	}

	header('Location: ./index.php', true, 301);
	exit();
?>
