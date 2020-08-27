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
$user_id = $user['user_id'];

// attendanceテーブルから情報取得
$attendance = get_attendance($db, $user_id);

if($attendance['finish'] === NULL) {
    $status = 'begin';
} else {
    $status = 'finish';
}

// トークンの生成
$token = get_csrf_token();

include_once VIEW_PATH . 'attendance_view.php';