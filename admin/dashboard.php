<?php require_once __DIR__.'/../config.php'; require_role('admin'); include '../includes/layout-start.php'; $counts=[];foreach(['users'=>'SELECT COUNT(*) c FROM users','teachers'=>'SELECT COUNT(*) c FROM teachers','students'=>'SELECT COUNT(*) c FROM students','parents'=>'SELECT COUNT(*) c FROM parents','classes'=>'SELECT COUNT(*) c FROM classes'] as $k=>$q){$counts[$k]=$conn->query($q)->fetch_assoc()['c'];} ?>
<h1>Admin Dashboard</h1>
<div class="grid"><?php foreach($counts as $k=>$v):?><div class="card"><div><?=ucfirst($k)?></div><div class="stat"><?=$v?></div></div><?php endforeach;?></div>
<div class="card">
  <h2>Quick Actions</h2>
  <a class="button" href="register_teacher.php">Register Teacher</a>
  <a class="button" href="register_student.php">Register Student</a>
  <a class="button" href="register_parent.php">Register Parent</a>
  <a class="button" href="manage_classes.php">Manage Classes</a>
  <a class="button" href="approve_grades.php">Approve Grades</a>
  <a class="button" href="manage_promotion.php">Promotions</a>
</div>
<?php include '../includes/layout-end.php';?>