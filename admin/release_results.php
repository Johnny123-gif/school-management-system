<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; $grades=$conn->query("SELECT gs.id,gs.student_id,gs.average_score,gs.position,gs.is_released,s.first_name,s.last_name,c.class_name,gs.academic_term FROM grade_summaries gs JOIN students s ON gs.student_id=s.id JOIN classes c ON gs.class_id=c.id WHERE gs.is_released=0 ORDER BY gs.academic_term DESC");
if($_GET['action']=='release'&&$_GET['id']){
  $gsid=$_GET['id'];
  $s=$conn->prepare('UPDATE grade_summaries SET is_released=1,released_date=NOW() WHERE id=?');
  $s->bind_param('i',$gsid);
  if($s->execute()){$msg='Results released to parents and students';}
}
include '../includes/layout-start.php';?>
<h1>Release Results</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
<table><tr><th>Student</th><th>Class</th><th>Term</th><th>Average</th><th>Position</th><th>Status</th><th>Action</th></tr>
<?php while($g=$grades->fetch_assoc()):?><tr><td><?=e($g['first_name'].' '.$g['last_name'])?></td><td><?=e($g['class_name'])?></td><td><?=e($g['academic_term'])?></td><td><?=number_format($g['average_score'],2)?></td><td><?=$g['position']?></td><td><?=$g['is_released']?'Released':'Pending'?></td><td><?php if(!$g['is_released']):?><a class="button" href="?action=release&id=<?=$g['id']?>">Release</a><?php endif;?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>