<?php require_once __DIR__.'/../config.php'; require_role('student'); include '../includes/layout-start.php'; $uid=$_SESSION['user_id']; $s=$conn->prepare('SELECT first_name,last_name FROM students WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute(); $st=$s->get_result()->fetch_assoc(); ?>
<h1>Student Dashboard</h1>
<div class="card">
  <h2>Welcome, <?=e($st['first_name'].' '.$st['last_name'])?></h2>
  <a class="button" href="view_results.php">View My Results</a>
  <a class="button" href="view_attendance.php">View My Attendance</a>
  <a class="button" href="view_class_status.php">View My Class Status</a>
</div>
<?php include '../includes/layout-end.php';?>