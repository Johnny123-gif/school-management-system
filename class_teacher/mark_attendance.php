<?php require_once __DIR__.'/../config.php'; require_role('class_teacher'); $uid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id,first_name,last_name,user_id FROM teachers WHERE user_id=? AND is_class_teacher=1');
$s->bind_param('i',$uid); $s->execute();
$t=$s->get_result()->fetch_assoc();
if(!$t){echo 'Not authorized'; exit;}
$tid=$t['id'];
$c=$conn->query("SELECT id,class_name FROM classes WHERE class_teacher_id=$tid")->fetch_assoc();
if(!$c){echo 'No class assigned'; exit;}
$cid=$c['id'];
$msg='';
if($_POST){
  $sid=$_POST['student_id']??0;$st=$_POST['status']??'';$d=$_POST['date']??'';
  if($sid&&$st&&$d){
    $s=$conn->prepare('INSERT INTO attendance(student_id,class_id,class_teacher_id,attendance_date,status) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE status=?');
    $s->bind_param('iiisss',$sid,$cid,$tid,$d,$st,$st);
    if($s->execute()){$msg='Attendance recorded';}
  }
}
$students=$conn->query("SELECT id,first_name,last_name FROM students WHERE class_id=$cid");
include '../includes/layout-start.php';?>
<h1>Mark Attendance</h1>
<p>Class: <?=e($c['class_name'])?></p>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Student<select name="student_id" required><?php while($st=$students->fetch_assoc()):?><option value="<?=$st['id']?>"><?=e($st['first_name'].' '.$st['last_name'])?></option><?php endwhile;?></select></label>
    <label>Status<select name="status" required><option>present</option><option>absent</option><option>late</option><option>excused</option></select></label>
    <label>Date<input type="date" name="date" required></label>
    <button>Record</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>