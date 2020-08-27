<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'attendance.php';

session_start();

if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

if(isset($_GET['select_user']) && is_admin($user) === true) {
  $user_id = $_GET['select_user'];
} else {
  $user_id = $user['user_id'];
}

// ユーザーが管理者であれば全ての履歴データを、それ以外はログイン中のユーザの履歴データを取得
if(is_admin($user) === true){
    $histories = get_all_attendance_user($db, $user_id);
    $users = get_all_user($db);
  } else {
    $histories = get_all_attendance_user($db, $user_id);
}

$total_worktime = 0;
foreach($histories as $history) {
  $total_worktime += hour_to_sec($history['worktime']);
}

$total_worktime = sec_to_hour($total_worktime);

$total_overtime = 0;
foreach($histories as $history) {
  $total_overtime += hour_to_sec($history['overtime']);
}

$total_overtime = sec_to_hour($total_overtime);

include_once VIEW_PATH . 'history_view.php';