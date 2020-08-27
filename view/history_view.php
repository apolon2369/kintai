<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>出退勤履歴</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'history.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1 class="text-center">履歴画面です</h1>
  <?php if(is_admin($user)){ ?>
    <form method="get">
      <select name="select_user">
        <?php foreach($users as $user_name){ ?>
          <option value="<?php print(h($user_name['user_id'])); ?>"><?php print(h($user_name['user_name'])); ?></option>
        <?php } ?>
      </select>
      <input type="submit" value="決定">
    </form>
  <?php } ?>
  <div class="container">
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($histories) > 0){ ?>
      <div class="d-flex justify-content-around">
        <a href="/history.php?year=<?php print(h($histories[0]['year'])); ?>&month=<?php print(h($histories[0]['month'] - 1)); ?>"><<</a>
        <p><?php print(h($histories[0]['year'])); ?>年  <?php print(h($histories[0]['month'])); ?>月</p>
        <a href="/history.php?year=<?php print(h($histories[0]['year'])); ?>&month=<?php print(h($histories[0]['month'] + 1)); ?>">>></a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered text-center history_table">
          <thead class="thead-light">
            <tr>
              <th>日</th>
              <th>曜日</th>
              <th>出勤時間</th>
              <th>退勤時間</th>
              <th>労働時間</th>
              <th>残業時間</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($histories as $history){ ?>
            <tr>
              <td><?php print(h($history['day'])); ?></td>
              <td><?php print(h(weekday($history))); ?></td>
              <td><?php print(h($history['begin'])); ?></td>
              <td><?php print(h($history['finish'])); ?></td>
              <td><?php print(h($history['worktime'])); ?></td>
              <td><?php print(h($history['overtime'])); ?></td>
            </tr>
            <?php } ?>
            <tr>
              <td colspan="4">合計時間</td>
              <td><?php print(h($total_worktime)); ?></td>
              <td><?php print(h($total_overtime)); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php } else { ?>
      <p>出退勤履歴はありません。</p>
    <?php } ?> 
  </div>
</body>
</html>