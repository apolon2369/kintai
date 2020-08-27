<?php
// var_dumpを行う
function dd($var){
  var_dump($var);
  exit();
}

// 指定したurlにリダイレクトする
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

// $nameがGETで渡ってきたら返す。なければ空文字で返す。
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

// $nameがPOSTで渡ってきたら返す。なければ空文字で返す。
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

// $nameがFILEで渡ってきたら返す。なければ空の配列で返す。
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

// セッションで$nameがあれば返す
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

// セッションに値を入れる
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

// ランダムな30字の文字列を生成し、セッションに保存して返す。
function get_csrf_token(){
  $token = get_random_string(30);
  set_session('csrf_token', $token);
  return $token;
}

// $tokenが空ならばfalse,セッションで取得した値と$tokenを見比べてtrue,falseを返す
function is_vaild_csrf_token($token){
  if($token === '') {
    return false;
  }
  return $token === get_session('csrf_token');
}

// エラーを入れる
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

// セッションでエラーが空ならば空の配列で返し、あればその中身を返す
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

// $_SESSION['__errors']があり、0でないものを返す
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// $messageをセッションに入れる
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

// セッションでメッセージが空ならば空の配列で返し、あればその中身を返す
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

// get_sessionでのuser_idが空でなければ返す
function is_logined(){
  return get_session('user_id') !== '';
}

// is_valid_upload_imageで画像の形式、正式にアップロードされた画像かを調べ、falseの場合は空文字を返す。
// trueの場合はランダム生成した数字に画像拡張子をつけて返す
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

// 20文字でランダムに生成する
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// 画像を保存
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename); // アップロードされたファイルを新しい位置に移動する関数
}

// ファイルの削除を行う
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){ // ファイルが存在するかチェックする関数
    unlink(IMAGE_DIR . $filename); // ファイルを削除する関数
    return true;
  }
  return false;
  
}

// 文字列の長さ
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// 正規表現でチェック（半角英数字）
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// 正規表現でチェック（0か自然数）
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// 正規表現でマッチするかチェック。マッチすれば返す。
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

// POSTよりアップロードされたものでない場合にエラーを返し、ファイル形式を調べてjpg,png以外はエラーを返し、両方を満たせばtrueが返る
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){ // HTTP POST でアップロードされたファイルかどうかを調べる関数
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']); // イメージの型を定義する関数
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

// 特殊文字を HTML エンティティに変換する
function h($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}