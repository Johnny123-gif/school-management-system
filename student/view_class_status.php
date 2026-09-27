<?php require_once __DIR__.'/../config.php'; require_role('student'); $uid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT s.id,s.first_name,s.last_name,c.class_name,s.promotion_status FROM students s JOIN classes c ON s.class_id=c.id WHERE s.user_id=?');
$s->bind_param('i',$uid);
$s->execute();
$student=$s->get_result()->fetch_assoc();

$promos=$conn->query("SELECT cf.class_name from_class,ct.class_name to_class,pr.promotion_type,pr.academic_year FROM promotion_records pr JOIN classes cf ON pr.from_class_id=cf.id JOIN classes ct ON pr.to_class_id=ct.id WHERE pr.student_id=".$student['id']." ORDER BY pr.approved_date DESC");
include '../includes/layout-start.php';?>
<h1>My Class & Promotion Status</h1>
<div class="card">
  <h2><?=e($student['first_name'].' '.$student['last_name'])?></h2>
  <p><strong>Current Class:</strong> <?=e($student['class_name'])?></p>
  <p><strong>Status:</strong> <span class="badge <?=($student['promotion_status']=='promoted'?'success':($student['promotion_status']=='demoted'?'danger':'info'))?>"><?=ucfirst($student['promotion_status'])?></span></p>
</div>
<h2>📜 Promotion History</h2>
<div class="card"><?php if($promos->num_rows==0):?><p>No promotion history</p><?php else:?>
<table><tr><th>From Class</th><th>To Class</th><th>Type</th><th>Year</th></tr>
<?php while($p=$promos->fetch_assoc()):?><tr><td><?=e($p['from_class'])?></td><td><?=e($p['to_class'])?></td><td><?=ucfirst($p['promotion_type'])?></td><td><?=$p['academic_year']?></td></tr><?php endwhile;?></table><?php endif;?></div>
<?php include '../includes/layout-end.php';?>