<?php require_once __DIR__.'/../config.php'; require_role('parent'); $pid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id FROM parents WHERE user_id=?'); $s->bind_param('i',$pid); $s->execute();
$pid=$s->get_result()->fetch_assoc()['id'];
$children=$conn->query("SELECT s.id,s.first_name,s.last_name,c.class_name,s.promotion_status,pr.from_class_id,cf.class_name from_class,ct.class_name to_class FROM students s JOIN classes c ON s.class_id=c.id LEFT JOIN promotion_records pr ON s.id=pr.student_id LEFT JOIN classes cf ON pr.from_class_id=cf.id LEFT JOIN classes ct ON pr.to_class_id=ct.id WHERE s.id IN (SELECT student_id FROM parent_students WHERE parent_id=$pid)");
include '../includes/layout-start.php';?>
<h1>Children Classes & Promotions</h1>
<div class="card"><?php if($children->num_rows==0):?><p>No children linked</p><?php else:?>
<table><tr><th>Child</th><th>Current Class</th><th>Status</th><th>Previous→New Class</th></tr>
<?php while($c=$children->fetch_assoc()):?><tr><td><?=e($c['first_name'].' '.$c['last_name'])?></td><td><?=e($c['class_name'])?></td><td><span class="badge <?=($c['promotion_status']=='promoted'?'success':($c['promotion_status']=='demoted'?'danger':'info'))?>"><?=ucfirst($c['promotion_status'])?></span></td><td><?=$c['from_class']?e($c['from_class'].' → '.$c['to_class']):'No change'?></td></tr><?php endwhile;?></table><?php endif;?></div>
<?php include '../includes/layout-end.php';?>