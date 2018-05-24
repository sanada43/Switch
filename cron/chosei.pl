#!/usr/bin/perl

use DBI;
use LWP::UserAgent;

require "./env.pl";

my $ua = LWP::UserAgent->new;
$ua->timeout(10);

my $challenge, $cookie;

#####
# ログイン画面へアクセス
#####
my $req = HTTP::Request->new(GET => $base_url . '/login2.htm');
my $res = $ua->request($req);
if (! $res->is_success) {
	print $res->status_line . "\n";
	exit 1;
}

# Challenge取得
# print $res->content;
if ($res->content =~ /<input type="hidden" name="Challenge" value="([^"]+)">/) {
	$challenge = $1;
} else {
	print "No Challenge.\n";
	exit 1;
}
print "challenge = $challenge\n";

#####
# ログイン
#####
my $req = HTTP::Request->new(GET => $base_url . '/cgi/login.cgi?pass=' .
	$switch_pass . '&Challenge=' . $challenge);
my $res = $ua->request($req);
if (! $res->is_success) {
	print $res->status_line . "\n";
	exit 1;
}

# Cookie取得
# print $res->header('Set-Cookie') . "\n";
if ($res->header('Set-Cookie') =~ /ICPlusCookie=([^;]+); /) {
	$cookie = $1;
} else {
	print "No cookie.\n";
	exit 1;
}
print "cookie = $cookie\n";
if (ord(substr($cookie, 0, 1)) == 0) {
	print "Login failure.\n";
	exit 1;
}

my $portnum;	# スイッチのポート数
my %vlanindex;	# vlan名 -> リスト上のindex
my %vlanids;	# vlan名 -> VID

my %arubeki;	# DBから得られた、あるべきVLAN設定値
my %baseset;	# 現在のVLAN設定値
my %genzai;		# 現在のVLAN設定値


#####
# 現在のVLAN設定取得
#####
my $req = HTTP::Request->new(GET => $base_url . '/QVLAN.js');
$req->header('Cookie', 'ICPlusCookie=' . $cookie);
my $res = $ua->request($req);
if (! $res->is_success) {
	print $res->status_line . "\n";
	exit 1;
}

print $res->content . "\n";

# ポート数
if ($res->content =~ /Port_num=(\d+);/) {
	$portnum = $1;
} else {
	print "No portnum.\n";
	exit 1;
}
print "portnum = $portnum\n";

# 現在設定値取得
$content = $res->content;
$vlanc = 0;
while ($content =~ /\[(\d+), '([^']+)', '([0TU]+)'\]/g) {
	print "genzai=" . $1 . ":" . $2 . ":" . $3 . "\n";
	$vlanindex{$2} = $vlanc++;
	$vlanids{$2} = $1;
	$genzai{$2} = $3;
}

# DB
$db = DBI->connect($db_url, $db_user, $db_pass);

# ネットワークマスター
$sth = $db->prepare("SELECT vlan, baseset, name FROM network_master");
$sth->execute;
while(my @row = $sth->fetchrow_array) {
	$arubeki{$row[0]} = $row[1];
	$baseset{$row[0]} = $row[1];
	print "arubeki=". $row[0] . ":" . $arubeki{$row[0]} . "\n";
}
$sth->finish;

# 現在の会議室スケジュール
my %rooms;
$sth = $db->prepare('SELECT n.vlan as nvlan, r.portno as rportno ' .
	'FROM schedules s, network_master n, room_master r ' .
	'WHERE s.sdate <= DATE_FORMAT(now(), "%Y%m%d%H%i") AND ' .
	'DATE_FORMAT(now(), "%Y%m%d%H%i") < s.edate AND s.network_id = n.id AND ' .
	's.room_id = r.id ORDER BY s.static');
$sth->execute;
while (my @row = $sth->fetchrow_array) {
	# portno(会議室・応接室)で重複があった時は予約優先
	if (!exists($rooms{$row[1]})) {
		substr($arubeki{$row[0]}, $row[1] - 1, 1, "U");
		print "arubeki-" . $row[0] . "=" . $arubeki{$row[0]} . "\n";
		$rooms{$row[1]} = $row[0];
	}
}
$sth->finish;

#####
# VLAN設定
#####
foreach my $key (keys(%arubeki)) {
	print "arubeki-" . $key . "=" . $arubeki{$key} . "\n";
	print "genzai-" . $key . "=" . $genzai{$key} . "\n";

	# ネットワークマスターに登録されているものの現在値があれば
	if (exists($genzai{$key})) {

		# 現在値とDB値に差異があれば変更
		if ($arubeki{$key} ne $genzai{$key}) {
			my $pmembs = $arubeki{$key};
			my $vindex = $vlanindex{$key};
			my $vid = $vlanids{$key};
			my $url = $base_url .
				"/cgi/q_vlan_edit.cgi?tag_id=" . $vindex .
				"&VID=" . $vid .
				"&VlanName=" . $key;
			for ($i = 0; $i < $portnum; $i++) {
				$url .= "&C";
				$url .= ($i + 1);
				$url .= "=";
				$url .= substr($pmembs, $i, 1);;
			}
			print $url . "\n";
			my $req = HTTP::Request->new(GET => $url);
			$req->header('Cookie', 'ICPlusCookie=' . $cookie);
			my $res = $ua->request($req);
			if (! $res->is_success) {
				print $res->status_line . "\n";
				exit 1;
			}
		}
	}
}

#####
# 最新の接続状況確認
#####
$db->{AutoCommit} = 0;
$sth = $db->prepare('DELETE FROM vlanstatus');
$sth->execute;
$sth-finish;

my $req = HTTP::Request->new(GET => $base_url . '/QVLAN.js');
$req->header('Cookie', 'ICPlusCookie=' . $cookie);
my $res = $ua->request($req);
if (! $res->is_success) {
	print $res->status_line . "\n";
	exit 1;
}
# 現在設定値取得
$content = $res->content;
$sth = $db->prepare('INSERT INTO vlanstatus (vlan, portno) VALUES (?, ?)');
while ($content =~ /\[(\d+), '([^']+)', '([0TU]+)'\]/g) {
	print "genzai=" . $1 . ":" . $2 . ":" . $3 . "\n";
	if (exists($arubeki{$2})) {
		my $genzai = $3;
		my $bases = $baseset{$2};
		print "bases=" . $1 . ":" . $2 . ":" . $bases . "\n";
		for (my $i = 0; $i < $portnum; $i++) {
			if (substr($genzai, $i, 1) ne "0" &&
				substr($genzai, $i, 1) ne substr($bases, $i, 1)) {
				print "exec=" . $2 . ":" . ($i + 1) . "\n";
				$sth->execute($2, $i + 1);
			}
		}
	}
}
$sth-finish;

$db->commit;
$db->disconnect;

# ログアウト
my $req = HTTP::Request->new(GET => $base_url . '/cgi/logout.cgi');
$req->header('Cookie', 'ICPlusCookie=' . $cookie);
my $res = $ua->request($req);
if (! $res->is_success) {
	print $res->status_line . "\n";
	exit 1;
}
