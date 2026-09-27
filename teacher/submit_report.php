<?php require_once __DIR__.'/../config.php'; require_role(['teacher','class_teacher','head_teacher']); $uid=$_SESSION['user_id']; $role=$_SESSION['user_role']; $msg='';

if($role==='class_teacher'||$role==='head_teacher'||$role==='teacher'){
  $s=$conn->prepare('SELECT id FROM teachers WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute();
  $tid=$s->get_result()->fetch_assoc()['id'];
  if(!$tid){echo 'Not a teacher'; exit;}
}

if($_POST){
  $sid=$_POST['student_id']??0;$at=$_POST['academic_term']??'';$ay=$_POST['academic_year']??0;$pr=$_POST['performance_rating']??'';$cm=$_POST['comments']??'';$rc=$_POST['recommendation']??'';
  if($sid&&$at&&$ay&&$pr&&$cm&&$rc){
    $s=$conn->prepare('INSERT INTO performance_reports(student_id,reporter_id,academic_term,academic_year,performance_rating,comments,recommendation) VALUES(?,?,?,?,?,?,?)');
    $s->bind_param('iisssss',$sid,$uid,$at,$ay,$pr,$cm,$rc);
    if($s->execute()){$msg='Performance report submitted successfully';}
  }
}

if($role==='head_teacher'){
  $ht=$conn->query("SELECT level FROM head_teachers WHERE user_id=$uid")->fetch_assoc();
  $students=$conn->query("SELECT s.id,s.first_name,s.last_name,c.class_name FROM students s JOIN classes c ON s.class_id=c.id WHERE c.level='".$ht['level']."'");
}
else{
  $s=$conn->query("SELECT class_id FROM classes WHERE class_teacher_id=$tid");
  $c=$s->fetch_assoc();
  $cid=$c['class_id']??0;
  $students=$conn->query("SELECT id,first_name,last_name FROM students WHERE class_id=$cid");
}

include '../includes/layout-start.php';?>
<h1>Submit Performance Report</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Student<select name="student_id" required><?php while($st=$students->fetch_assoc()):?><option value="<?=$st['id']?>"><?=e($st['first_name'].' '.$st['last_name']).(isset($st['class_name'])?' - '.$st['class_name']:'')?></option><?php endwhile;?></select></label>
    <label>Performance Rating<select name="performance_rating" required><option value="excellent">Excellent</option><option value="good">Good</option><option value="average">Average</option><option value="poor">Poor</option></select></label>
    <label>Term<input type="text" name="academic_term" placeholder="Term 1" required></label>
    <label>Year<input type="number" name="academic_year" value="<?=date('Y')?>" required></label>
    <label>Recommendation<select name="recommendation" required><option value="promote">Promote to Next Class</option><option value="demote">Demote to Previous Class</option><option value="skip_class">Skip 1-2 Classes Ahead</option><option value="hold_back">Hold Back (Repeat Class)</option><option value="maintain">Maintain Current Class</option></select></label>
    <label>Comments/Reasons<textarea name="comments" required rows="4"></textarea></label>
    <button>Submit Report</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>