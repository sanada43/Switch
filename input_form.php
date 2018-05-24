<table id="input_form">

<?php
	if ($error_message) {
?>
<tr>
<td align="center" colspan="2">
<font color="#FF0000"><?php echo $error_message; ?></font>
</td>
</tr>
<?php
	}
?>

<tr>
<th class="t_top">開始日時</th>
<td class="t_top"><select id="start_date" name="start_date">
<?php
	foreach ($start_dates as $value => $name) {
		if (strcmp($start_date, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>
<select id="start_hour" name="start_hour">
<?php
	foreach ($hours as $value => $name) {
		if (strcmp($start_hour, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>時
<select id="start_min" name="start_min">
<?php
	foreach ($mins as $value => $name) {
		if (strcmp($start_min, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>分
<?php
	if ($start_date_msg) {
		echo "<br /><font color='#FF0000'>" . $start_date_msg . "</font>";
	}
?>
</td>
</tr>

<tr>
<th>終了日時</th>
<td><select id="end_date" name="end_date">
<?php
	foreach ($end_dates as $value => $name) {
		if (strcmp($end_date, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>
<select id="end_hour" name="end_hour">
<?php
	foreach ($hours as $value => $name) {
		if (strcmp($end_hour, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>時
<select id="end_min" name="end_min">
<?php
	foreach ($mins as $value => $name) {
		if (strcmp($end_min, $value) == 0) {
			echo "<option value='" . $value . "' selected>" . $name . "</option>\n";
		} else {
			echo "<option value='" . $value . "'>" . $name . "</option>\n";
		}
	}
?>
</select>分
<?php
	if ($end_date_msg) {
		echo "<br /><font color='#FF0000'>" . $end_date_msg . "</font>";
	}
?>
</td>
</tr>

<tr>
<th>ネットワーク</th>
<td>
<?php
	foreach ($networks as $network) {
		if (strcmp($network['id'], $network_id) == 0) {
			echo "<input type='radio' name='network_id' value='" . $network['id'] . "' checked />" . $network['name'] . "<br/>\n";
		} else {
			echo "<input type='radio' name='network_id' value='" . $network['id'] . "' />" . $network['name'] . "<br/>\n";
		}
	}
?>
<br />
<?php
	if ($network_id_msg) {
		echo "<br /><font color='#FF0000'>" . $network_id_msg . "</font>";
	}
?>
</td>
</tr>

<tr>
<th>会議室・応接室</th>
<td>
<?php
	foreach ($rooms as $room) {
		if (strcmp($room['id'], $room_id) == 0) {
			echo "<input type='radio' name='room_id' value='" . $room['id'] . "' checked />" . $room['name'] . "\n";
		} else {
			echo "<input type='radio' name='room_id' value='" . $room['id'] . "'>" . $room['name'] . "\n";
		}
	}
?>
<br />
<?php
	if ($room_id_msg) {
		echo "<br /><font color='#FF0000'>" . $room_id_msg . "</font>";
	}
?>
</td>
</tr>

<tr>
<th>申請者</th>
<td><input type="text" name="applicant" value="<?php echo htmlspecialchars($applicant, ENT_QUOTES); ?>" />
<?php
	if ($applicant_msg) {
		echo "<br /><font color='#FF0000'>" . $applicant_msg . "</font>";
	}
?>
</td>
</tr>

<tr>
<th>利用目的</th>
<td><textarea name="purpose"><?php echo htmlspecialchars($purpose, ENT_QUOTES); ?></textarea>
<?php
	if ($purpose_msg) {
		echo "<br /><font color='#FF0000'>" . $purpose_msg . "</font>";
	}
?>
</td>
</tr>

</table>
