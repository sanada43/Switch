memo
	各種メモ
etc
	Basic認証のパスワードファイル
cron
	cron起動Perlスクリプト
common
	非公開PHPライブラリ
php
	PHPプログラム、最新バージョン

===== memo =====

env_techno_dev.txt
	開発環境インストールメモ
env_techno_honban.txt
	本番環境インストールメモ
dlink.png
	スイッチ設定後画面

===== etc ===== 

htpasswd
	Basic認証のパスワードファイル

===== cron =====

chosei.pl
	DB読み込んでスイッチの設定を調整するPerlスクリプト
env.pl
	chosei.plから環境設定を抽出したもの
chosei.sh
	cronから起動されてchosei.plを起動するもの。標準出力を捨てるため。

===== common =====

common.php
	環境、共通HTMLヘッダ、DBコネクション取得

input_form.php
	入力画面出力

prepare_info.php
	入力画面のSELECT項目などの作成

===== php =====

index.php
	新規入力とスケジュール出力

do_regist.php
	新規入力後index.phpに遷移

update.php
	変更画面ととスケジュール出力

do_update.php
	変更後index.phpに遷移

do_remove.php
	削除後index.phpに遷移

do_finish.php
	終了後index.phpに遷移

list_sched.js
	スケジュールと現在の接続状況を取得、表示するJavaScript

list_sched.php
	list_sched.jsから呼び出されるサーバサイドPHP

confirm.js
	確認ダイアログ表示JavaScript

style.css
	デザインcss

history.php
	履歴一覧画面

detail.php
	履歴詳細画面
