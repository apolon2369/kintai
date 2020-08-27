<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'attendance.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

if(is_vaild_csrf_token(get_post('csrf_token')) === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);
$user_id = $user['user_id'];

// attendanceテーブルから情報取得
$attendance = get_attendance($db, $user_id);

// postのstatusがfinish(退勤)の時はINSERT文を実行し出勤
if(get_post('status') === 'finish') {
    begin_work($db, $user_id);
}

// postのstatusがbegin(出勤)の時はUPDATE文を実行し退勤
if(get_post('status') === 'begin') {
    finish_work($db, $user_id);
}

redirect_to(HOME_URL);