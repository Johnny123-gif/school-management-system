<?php require_once __DIR__.'/../config.php'; require_role('parent'); include '../includes/layout-start.php'; $pid=$_SESSION['user_id']; $s=$conn->prepare('SELECT first_name,last_name FROM parents WHERE user_id=?'); $s->bind_param('i',$pid); $s->execute(); $p=$s->get_result()->fetch_assoc(); ?>
<h1>Parent Dashboard</h1>
<div class="card">
  <h2>Welcome, <?=e($p['first_name'].' '.$p['last_name'])?></h2>
  <a class="button" href="view_results.php">View Child Results</a>
  <a class="button" href="view_attendance.php">View Child Attendance</a>
  <a class="button" href="view_class_promotion.php">View Class & Promotion</a>
</div>
<?php include '../includes/layout-end.php';?>