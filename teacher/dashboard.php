<?php require_once __DIR__.'/../config.php'; require_role(['teacher','class_teacher','head_teacher']); include '../includes/layout-start.php'; $uid=$_SESSION['user_id']; $role=$_SESSION['user_role']; $s=$conn->prepare('SELECT first_name,last_name FROM teachers WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute(); $t=$s->get_result()->fetch_assoc(); ?>
<h1>Teacher Dashboard</h1>
<div class="card">
  <h2>Welcome, <?=e($t['first_name'].' '.$t['last_name'])?></h2>
  <p>You can upload grades and submit performance reports.</p>
  <a class="button" href="upload_grades.php">Upload Grades</a>
  <a class="button" href="submit_report.php">Submit Performance Report</a>
</div>
<?php include '../includes/layout-end.php';?>