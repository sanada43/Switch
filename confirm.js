$(function() {

	function check() {
		var msg = "";
		if ($('[name="network_id"]:checked').val() === undefined) {
			msg += "ネットワークを選択してください\n";
		}
		if ($('[name="room_id"]:checked').val() === undefined) {
			msg += "会議室・応接室を選択してください\n";
		}
		if ($('[name="applicant"]').val() === "") {
			msg += "申請者を入力してください\n";
		}
		if ($('[name="purpose"]').val() === "") {
			msg += "利用目的を入力してください\n";
		}
		if (msg !== "") {
			alert(msg);
			return false;
		}
		return true;
	}

	$('#regist').on('click', function() {
		if (!check()) {
			return false;
		}
		if (confirm('登録します。よろしいですか？')) {
			return true;
		} else {
			return false;
		}
	});

	$('#update').on('click', function() {
		if (!check()) {
			return false;
		}
		if (confirm('変更します。よろしいですか？')) {
			return true;
		} else {
			return false;
		}
	});

	$('#start_now').on("click", function() {
		date = new Date();
		ndate = new Date(Math.floor(date.getTime() / (15 * 60 * 1000)) *
			(15 * 60 * 1000));
		start_date = String(ndate.getFullYear()) +
			('00' + (ndate.getMonth() + 1)).slice(-2) +
			('00' + ndate.getDate()).slice(-2);
		$('#start_date').val(start_date);
		start_time = ('00' + ndate.getHours()).slice(-2) +
			('00' + ndate.getMinutes()).slice(-2);
		$('#start_time').val(start_time);
	});

	$('#end_now').on("click", function() {
		date = new Date();
		ndate = new Date(Math.floor(date.getTime() / (15 * 60 * 1000)) *
			(15 * 60 * 1000));
		start_date = String(ndate.getFullYear()) +
			('00' + (ndate.getMonth() + 1)).slice(-2) +
			('00' + ndate.getDate()).slice(-2);
		$('#end_date').val(start_date);
		start_time = ('00' + ndate.getHours()).slice(-2) +
			('00' + ndate.getMinutes()).slice(-2);
		$('#end_time').val(start_time);
	});
});
