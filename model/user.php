<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// SELECT文を作成し、user_idがマッチするものを配列で取得し、返す
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id,
      user_name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, array($user_id));
}

// SELECT文を作成し、nameがマッチするものを配列で取得し、返す
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id,
      user_name,
      password,
      type
    FROM
      users
    WHERE
      user_name = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, array($name));
}

// SELECT文を作成し、全てのユーザー情報を配列で取得し、返す
function get_all_user($db){
  $sql = "
    SELECT
      user_id,
      user_name,
      password,
      type
    FROM
      users
  ";

  return fetch_all_query($db, $sql);
}

// 上のnameで取得したものを$userとし、$userの内容が違ったりパスワードが違ったらfalseを返す。
// セッションに$_SESSION['user_id']として$userのuser_idを保存し、$userを返す
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || password_verify($password, $user['password']) === false){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

// login_user_idをセッションで渡ってきたidとしてそれを上のget_userに入れて配列として情報取得
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

// ユーザー名とパスワードをチェックし、問題なければ一番下の関数を使いINSERT文を実行する
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

// 管理者は1が入る
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

// 2つ上の関数に入れる。
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

// 上のis_valid_user関数に入れる。ユーザー名の長さをチェック、正規表現でチェックし問題なければtrueで返す。
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 上のis_valid_user関数に入れる。パスワードの長さをチェック、正規表現でチェック、確認用と一致するかチェックし問題なければtrueで返す。
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

// INSERT文でユーザー名とパスワードの値を入れ、実行
function insert_user($db, $name, $password){
  $password = password_hash($password, PASSWORD_DEFAULT);
  $type = 0;
  $created = date('Y-m-d H:i:s');
  $sql = "
    INSERT INTO
      users(
        user_name,
        password,
        type,
        created
      )
    VALUES (?, ?, ?, ?)
  ";

  return execute_query($db, $sql, array($name, $password, $type, $created));
}
