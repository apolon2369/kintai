<?php

// 出勤状況の確認
function get_attendance($db, $user_id) {
    $sql = "
        SELECT
            id,
            user_id,
            begin,
            finish
        FROM
            attendance
        WHERE
            user_id = ?
        ORDER BY
            id DESC
        LIMIT
            1
    ";

    return fetch_query($db, $sql, array($user_id));
}

// ログインしているuserの出退勤履歴を取得
function get_all_attendance_user($db, $user_id) {
    if(isset($_GET['year']) && isset($_GET['month'])) {
        $year = $_GET['year'];
        $month = $_GET['month'];

        if($_GET['month'] === '0') {
            $year = $_GET['year'] - 1;
            $month = 12;
        }

        if($_GET['month'] === '13') {
            $year = $_GET['year'] + 1;
            $month = 1;
        }
    } else {
        $year = date('Y');
        $month = date('m');
    }

    $sql = "
        SELECT
            id,
            user_id,
            YEAR(begin) AS year,
            MONTH(begin) AS month,
            DAY(begin) AS day,
            WEEKDAY(begin) AS weekday,
            TIME(begin) AS begin,
            TIME(finish) AS finish,
            CASE
            WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 6*60*60 then SEC_TO_TIME(0*60)
            WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 9*60*60 then SEC_TO_TIME(45*60)
            ELSE SEC_TO_TIME(60*60)
            END AS `rest`,
            SEC_TO_TIME(
                time_to_sec(timediff(finish,begin))
                -
                CASE
                WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 6*60*60 then 0*60
                WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 9*60*60 then 45*60
                ELSE 60*60
            END) AS `worktime`,
            SEC_TO_TIME(
                CASE
                WHEN (time_to_sec(timediff(finish,begin))
                    -
                    case
                    WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 6*60*60 then 0*60
                    WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 9*60*60 then 45*60
                    else 60*60
                    END) < 8*60*60 THEN 0
                ELSE time_to_sec(timediff(finish,begin))
                    -
                    case
                    WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 6*60*60 then 0*60
                    WHEN TIME_TO_SEC( TIMEDIFF(`finish`, `begin`) ) < 9*60*60 then 45*60
                    else 60*60
                    END - 8*60*60
            END) as `overtime`
        FROM
            attendance
        WHERE
            user_id = ?
        AND
            YEAR(begin) = ?
        AND
            MONTH(begin) = ?
        ORDER BY
            begin DESC
    ";

    return fetch_all_query($db, $sql, array($user_id, $year, $month));
}


// 出勤にするためINSERT文を実行
function begin_work($db, $user_id) {
    if(insert_attendance($db, $user_id)) {
        set_message('出勤しました。');
    } else {
        set_error('出勤処理ができませんでした。');
    }
}

// 上の関数に入れるINSERT文
function insert_attendance($db, $user_id) {
    $begin = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO
        attendance(
            user_id,
            begin,
            finish
        )
        VALUES(?, ?, NULL)
    ";

    return execute_query($db, $sql, array($user_id, $begin));
}

// 退勤にするためUPDATE文を実行
function finish_work($db, $user_id) {
    if(update_attendance($db, $user_id)) {
        set_message('退勤しました。');
    } else {
        set_error('退勤処理ができませんでした。');
    }
}

// 上の関数に入れるUPDATE文
function update_attendance($db, $user_id) {
    $finish = date('Y-m-d H:i:s');
    $sql = "
        UPDATE
            attendance
        SET
            finish = ?
        WHERE
            user_id = ?
        AND
            finish IS NULL
        LIMIT
            1
    ";

    return execute_query($db, $sql, array($finish, $user_id));
}

// データベースの曜日(数字)から曜日に変換するやつ
function weekday($histories) {
    $weekday = array('月', '火', '水', '木', '金', '土', '日');
    $day = $histories['weekday'];
    return $weekday[$day];
}

// 時間を秒に変換
function hour_to_sec($str) {
    $t = explode(":", $str);//配列（$t[0]（時間）、$t[1]（分）、$t[2]（秒））にする
    $h = $t[0];
    if (isset($t[1])) {//分の部分に値が入っているか確認
      $m = $t[1];
    }else{
      $m = "0";
    }
    if (isset($t[2])) {//秒の部分に値が入っているか確認
      $s = $t[2];
    }else{
      $s = "0";
    } 
    return ($h*60*60) + ($m*60) + $s;
}

// 秒⇒時間：分：秒へ変換(0:00:00表記)
function sec_to_hour($sec){
    $hours = floor($sec / 3600);//時間
    $minutes = floor(($sec / 60) % 60);//分
    $seconds = floor($sec % 60);//秒
    $hms = sprintf("%2d:%02d:%02d", $hours, $minutes, $seconds);
    return $hms;
}