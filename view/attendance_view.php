<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>出退勤</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'attendance.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

  <?php include VIEW_PATH . 'templates/messages.php'; ?>

  <div class="main mx-auto text-center">
    <p>打刻してください</p>
    <span id="view_clock" class="badge badge-dark p-3"></span>
    <form method="post" action="attendance_process.php" enctype="multipart/form-data">
      <?php if($status === 'finish') { ?>
        <input class="btn btn-success mt-3" type="submit" value="出勤">
        <input type="hidden" name="csrf_token" value="<?php print $token; ?>">
        <input type="hidden" name="status" value="<?php print $status; ?>">
      <?php } else { ?>
        <input class="btn btn-success mt-3" type="submit" value="退勤">
        <input type="hidden" name="csrf_token" value="<?php print $token; ?>">
        <input type="hidden" name="status" value="<?php print $status; ?>">
      <?php } ?>
    </form>
  </div>
<script src="<?php print(SCRIPT_PATH . 'main.js'); ?>"></script>
</body>
</html>