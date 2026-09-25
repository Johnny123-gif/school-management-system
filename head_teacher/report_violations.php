<?php require_once __DIR__.'/../config.php'; require_role('head_teacher'); $uid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id,level FROM head_teachers WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute();
$h=$s->get_result()->fetch_assoc();
$hid=$h['id'];
$msg='';
if($_POST){
  $rt=$_POST['report_type']??'';$sv=$_POST['severity']??'';$d=$_POST['description']??'';
  if($rt&&$sv&&$d){
    $s=$conn->prepare('INSERT INTO violation_reports(head_teacher_id,report_type,severity,description) VALUES(?,?,?,?)');
    $s->bind_param('isss',$hid,$rt,$sv,$d);
    if($s->execute()){$msg='Report submitted';}
  }
}
include '../includes/layout-start.php';?>
<h1>Report Violation</h1>
<p>Level: <?=ucfirst($h['level'])?></p>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Report Type<select name="report_type" required><option>teacher_behavior</option><option>student_behavior</option><option>teacher_regularity</option><option>attendance_issue</option></select></label>
    <label>Severity<select name="severity" required><option>low</option><option>medium</option><option>high</option></select></label>
    <label>Description<textarea name="description" required></textarea></label>
    <button>Submit</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>