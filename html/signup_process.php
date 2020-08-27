<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

// if(is_vaild_csrf_token(get_post('csrf_token')) === false){
//   redirect_to(LOGIN_URL);
// }

// postで渡ってきた名前、パスワード、再確認用のパスワードを変数に
$name = get_post('name');
$password = get_post('password');
$password_confirmation = get_post('password_confirmation');

$db = get_db_connect();
$user = get_login_user($db);

// 管理者以外がアクセスした場合ログインページへリダイレクト
if(is_admin($user) === FALSE) {
  redirect_to(LOGIN_URL);
}

try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
redirect_to(HOME_URL);