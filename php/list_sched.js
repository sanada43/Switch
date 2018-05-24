$(function() {
	function display_sched() {
		$.ajax({
			url: 'list_sched.php',
			dataType: 'json',
		})
		.done(function (response) {
			{
				$('#date').text(response['date'] + "現在");
			}
			{
				$('#schedules').empty();
				var table = $("<table>");
				var thr = $("<tr>")
					.append($("<th>").text("開始日時"))
					.append($("<th>").text("終了日時"))
					.append($("<th>").text("ネットワーク"))
					.append($("<th>").text("会議室"))
					.append($("<th>").text("申請者"))
					.append($("<th>").text("利用目的"))
					.append($("<th>").text("終了"))
					.append($("<th>").text("変更・削除"));
				table.append(thr);
				for (let schedule of response['schedules']) {
					var ssdate = schedule['ssdate'];
					var sedate = schedule['sedate'];
					var tr = $("<tr>");
					tr
						.append($("<td>").text(ssdate.substring(0, 4) + '/' +
							ssdate.substring(4, 6) + '/' +
							ssdate.substring(6, 8) + ' ' +
							ssdate.substring(8, 10) + ':' +
							ssdate.substring(10, 12)))
						.append($("<td>").text(sedate.substring(0, 4) + '/' +
							sedate.substring(4, 6) + '/' +
							sedate.substring(6, 8) + ' ' +
							sedate.substring(8, 10) + ':' +
							sedate.substring(10, 12)))
						.append($("<td>").text(schedule['nname']))
						.append($("<td>").text(schedule['rname']))
						.append($("<td>").text(schedule['sapplicant']))
						.append($("<td>").text(schedule['spurpose']));
					if (schedule['sstatic'] == 0) {
						if (schedule['nowon'] == 1) {
							tr.append($("<td>")
								.append(
									$("<A>")
										.attr('href', './do_finish.php?id=' +
											schedule['sid'])
										.attr('class', 'finish')
										.text('終了')
								)
							);
						} else {
							tr.append($("<td>")
								.append('-')
							);
						}
						tr.append($("<td>")
							.append(
								$("<A>")
									.attr('href',
										'./update.php?id=' + schedule['sid'])
									.text('変更')
							)
							.append('・')
							.append(
								$("<A>")
									.attr('href',
										'./do_remove.php?id=' + schedule['sid'])
									.attr('class', 'remove')
									.text('削除')
							)
						);
					} else {
						tr.append($("<td>")
							.append('-')
						);
						tr.append($("<td>")
							.append('-')
						);
					}
					table.append(tr);
				}
				$('#schedules').append(table);

				$('.finish').on('click', function() {
					if (confirm('終了します。よろしいですか？')) {
						return true;
					} else {
						return false;
					}
				});

				$('.remove').on('click', function() {
					if (confirm('削除します。よろしいですか？')) {
						return true;
					} else {
						return false;
					}
				});
			}
			{
				$('#connections').empty();
				var table = $("<table>");
				var thr = $("<tr>")
					.append($("<th>").text("ネットワーク"))
					.append($("<th>").text(""))
					.append($("<th>").text("会議室・応接室"))
				table.append(thr);
				for (let connection of response['connections']) {
					var tr = $("<tr>");
					tr
						.append($("<td>").append($("<p>")
							.attr('class', 'circle')
							.text(connection['nname'])
						))
						.append($("<td>").append($("<p>")
							.attr('class', 'arrow')
						))
						.append($("<td>").append($("<p>")
							.attr('class', 'circle')
							.text(connection['rname'])
						));
					table.append(tr);
				}
				$('#connections').append(table);
			}
		})
		.fail(function () {
		});
	}

	display_sched();
	setInterval(display_sched, 10000);
});
