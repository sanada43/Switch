<?php
	require("common.php");

	try {
		$mysqli = create_connection();
		require("prepare_info.php");

		$error_messages = array();
		$is_listing = 1;

		# ページ
		if (!isset($_GET['page'])) {
			$page = 1;
		} else {
			$page = $_GET['page'];
			if (!preg_match("/^[1-9][0-9]{0,4}$/", $page)) {
				$error_messages[] = "ページ数が無効です。";
				$page = 1;
			} else {
				$page_msg = "";
			}
		}

		# 件数
		if (!isset($_GET['perpage'])) {
			$perpage = 50;
		} else {
			$perpage = $_GET['perpage'];
			if (!preg_match("/^[1-9][0-9]{0,4}$/", $perpage)) {
				$error_messages[] = "ページ毎件数が無効です。";
				$perpage = 50;
			} else {
				$perpage_msg = "";
			}
		}

		# スキップ件数
		$skip = ($page - 1) * $perpage;

		$year = "";
		if (isset($_GET['year']) && strlen($_GET['year']) > 0) {
			$year = $_GET['year'];
			if (!preg_match("/^[12][0-9]{3}$/", $year)) {
				$error_messages[] = "年が無効です。";
				$year = "";
			}
		}

		$month = "";
		if (isset($_GET['month']) && strlen($_GET['month']) > 0) {
			$month = $_GET['month'];
			if ($month < 1 || 12 < $month) {
				$error_messages[] = "月が無効です。";
				$month = "";
			} else {
				if (strlen($month) == 1)
					$month = "0" . $month;
			}
		}

		$day = "";
		if (isset($_GET['day']) && strlen($_GET['day']) > 0) {
			$day = $_GET['day'];
			if ($day < 1 || 31 < $day) {
				$error_messages[] = "日が無効です。";
				$day = "";
			} else {
				if (strlen($day) == 1)
					$day = "0" . $day;
			}
		}

		# リンクパラメータ＆WHERE作成
		$like = "";
		$parameters = "?perpage=";
		$parameters .= htmlspecialchars($perpage);
		if (strlen($year) > 0) {
			$parameters .= "&year=";
			$parameters .= htmlspecialchars($year);
			$like .= $year;
		} else {
			$like .= "____";
		}
		if (strlen($month) > 0) {
			$parameters .= "&month=";
			$parameters .= htmlspecialchars($month);
			$like .= $month;
		} else {
			$like .= "__";
		}
		if (strlen($day) > 0) {
			$parameters .= "&day=";
			$parameters .= htmlspecialchars($day);
			$like .= $day;
		} else {
			$like .= "__";
		}

		$where = 's.static=0 AND s.edate <= DATE_FORMAT(now(), "%Y%m%d%H%i")';
		if (strcmp($like, "________")) {
			$where .= " AND (s.sdate like '" . $like .
				"____' OR s.edate like '" . $like . "____')";
		}

		// 全行カウント
		$sql1 = 'SELECT COUNT(id) AS count FROM schedules s WHERE ' . $where;
		$stmt = $mysqli->prepare($sql1);
		if ($stmt) {
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($count);
			$stmt->fetch();
		}
		$pages = floor(($count + $perpage - 1) / $perpage);
		$stmt->close();

		$sql2 = 
			'SELECT s.id as sid, s.sdate as ssdate, s.edate as sedate, ' . 
				'n.id as nid, n.name as nname, ' .
				'r.id as rid, r.name as rname, ' .
				's.static as sstatic, ' .
				'LEFT(s.applicant, 10) as sapplicant, ' .
				'LEFT(s.purpose, 10) as spurpose ' .
			'FROM schedules s, network_master n, room_master r ' .
			'WHERE ' . $where .
				' AND s.network_id=n.id ' .
				'AND s.room_id=r.id ' .
			'ORDER BY s.sdate desc, s.edate desc, s.room_id ' .
			'LIMIT ?, ?';
		$stmt = $mysqli->prepare($sql2);
		if ($stmt) {
			$stmt->bind_param("ii", $skip, $perpage);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($sid, $ssdate, $sedate, $nid, $nname, $rid,
				$rname, $sstatic, $sapplicant, $spurpose);
			$histories = array();
			while ($stmt->fetch()) {
				$history = array();
				$history['sid'] = $sid;
				$history['ssdate'] =
					substr($ssdate, 0, 4) . '/' .
					substr($ssdate, 4, 2) . '/' .
					substr($ssdate, 6, 2) . ' ' .
					substr($ssdate, 8, 2) . ':' .
					substr($ssdate, 10, 2);
				$history['sedate'] =
					substr($sedate, 0, 4) . '/' .
					substr($sedate, 4, 2) . '/' .
					substr($sedate, 6, 2) . ' ' .
					substr($sedate, 8, 2) . ':' .
					substr($sedate, 10, 2);
				$history['nid'] = $nid;
				$history['nname'] = $nname;
				$history['rid'] = $rid;
				$history['rname'] = $rname;
				$history['sstatic'] = $sstatic;
				$history['sapplicant'] = $sapplicant;
				$history['spurpose'] = $spurpose;
				$histories[] = $history;
			}
			$stmt->close();
			if (count($histories) == 0) {
				$error_messages[] = "該当するレコードはありませんでした。";
				$is_listing = 0;
			}
		} else {
			$error_messages[] = "検索に失敗しました。1";
			$is_listing = 0;
		}
	} finally {
		$mysqli->close();
	}

	$page_start = $page - 1;
	if ($page_start < 1)
		$page_start = 1;
	$page_end = $page + 1;
	if ($page_end > $pages)
		$page_end = $pages;

	$prev = ($page <= 1)? 0: 1;
	$next = ($page >= $pages)? 0: 1;

	$first = ($page - 1 > 2)? 1: 0;
	$first_one = ($page - 1 > 1)? 1: 0;
	$last = ($pages - $page > 2)? 1: 0;
	$last_one = ($pages - $page > 1)? 1: 0;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="./jquery-3.2.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./style.css" />  
</head>
<body>

<hr class="hr-text" data-content="履歴" />

<form method="get" action="./history.php">

<table>
<?php
	if (count($error_messages)) {
?>
<?php
		foreach ($error_messages as $error_message) {
?>
<tr>
<td colspan="2">
<font color="#FF0000"><?php echo $error_message; ?></font>
</td>
</tr>
<?php
		}
?>
<?php
	}
?>
</table>

<table id="input_form">
<tr>
<th class="t_top">日付</th>
<td class="t_top">

<select name="year">
<option value="">指定なし</option>
<?php
	foreach ($years as $value => $name) {
		if ($value == $year) {
?>
<option value="<?php echo $value ?>" selected><?php echo $name ?></option>
<?php
		} else {
?>
<option value="<?php echo $value ?>"><?php echo $name ?></option>
<?php
		}
	}
?>
</select>年

<select name="month">
<option value="">指定なし</option>
<?php
	foreach ($months as $value => $name) {
		if ($value == $month) {
?>
<option value="<?php echo $value ?>" selected><?php echo $name ?></option>
<?php
		} else {
?>
<option value="<?php echo $value ?>"><?php echo $name ?></option>
<?php
		}
	}
?>
</select>月

<select name="day">
<option value="">指定なし</option>
<?php
	foreach ($days as $value => $name) {
		if ($value == $day) {
?>
<option value="<?php echo $value ?>" selected><?php echo $name ?></option>
<?php
		} else {
?>
<option value="<?php echo $value ?>"><?php echo $name ?></option>
<?php
		}
	}
?>
</select>日

</td>
</tr>

</table>

<input type="submit" value="検索" id="search" />
</form>

<br />
<a href="./index.php">トップ</a>

<?php
	if ($is_listing) {
?>

<div class="pager">
    <ul>

<?php
	if ($prev) {
?>
        <li><a href="./history.php?page=<?php echo htmlspecialchars($page - 1) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>">prev</a></li>
<?php
	}
?>

<?php
	if ($first) {
?>
    <li><a href="./history.php?page=1&perpage=<?php echo htmlspecialchars($perpage) ?>">1</a></li>
	<li><span>...</span></li>
<?php
	} else if ($first_one) {
?>
    <li><a href="./history.php?page=<?php echo htmlspecialchars($page - 2) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>"><?php echo htmlspecialchars($page - 2) ?></a></li>
<?php
	}
?>

<?php
	for ($i = $page_start; $i <= $page_end; $i++) {
		if ($i == $page) {
?>
        <li class="current"><span><?php echo htmlspecialchars($page) ?></span></li>
<?php
		} else {
?>
        <li><a href="./history.php?page=<?php echo htmlspecialchars($i) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>"><?php echo htmlspecialchars($i) ?></a></li>
<?php
		}
	}
?>

<?php
	if ($last) {
?>
	<li><span>...</span></li>
    <li><a href="./history.php?page=<?php echo htmlspecialchars($pages) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>"><?php echo htmlspecialchars($pages) ?></a></li>
<?php
	} else if ($last_one) {
?>
        <li><a href="./history.php?page=<?php echo htmlspecialchars($page + 2) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>"><?php echo htmlspecialchars($page + 2) ?></a></li>
<?php
	}
?>

<?php if ($next) { ?>
        <li><a href="./history.php?page=<?php echo htmlspecialchars($page + 1) ?>&perpage=<?php echo htmlspecialchars($perpage) ?>">next</a></li>
<?php } ?>

    </ul>
</div>

<div id="histories">
<table>
<tr>
<th>開始日時</th>
<th>終了日時</th>
<th>ネットワーク</th>
<th>会議室</th>
<th>申請者</th>
<th>利用目的</th>
<th>詳細</th>
</tr>

<?php
	foreach ($histories as $history) {
?>
<tr>
<td><?php echo htmlspecialchars($history['ssdate']) ?></td>
<td><?php echo htmlspecialchars($history['sedate']) ?></td>
<td><?php echo htmlspecialchars($history['nname']) ?></td>
<td><?php echo htmlspecialchars($history['rname']) ?></td>
<td><?php echo htmlspecialchars($history['sapplicant']) ?></td>
<td><?php echo htmlspecialchars($history['spurpose']) ?></td>
<td><a href="./detail.php?id=<?php echo htmlspecialchars($history['sid']) ?>">詳細</a></td>
</tr>
<?php
	}
?>
</table>
</div>

<?php
	}
?>

</body>
</html>
