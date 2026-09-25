<?php require_once __DIR__.'/../config.php'; require_role('teacher'); $uid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id FROM teachers WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute();
$tid=$s->get_result()->fetch_assoc()['id'];
$msg='';
if($_POST){
  $sid=$_POST['student_id']??0;$coid=$_POST['course_id']??0;$sc=$_POST['score']??0;$at=$_POST['academic_term']??'';$ay=$_POST['academic_year']??0;
  if($sid&&$coid&&$sc>0&&$sc<=100&&$at&&$ay){
    $gr=grade_letter($sc);
    $s=$conn->prepare('INSERT INTO grades(student_id,course_id,teacher_id,class_id,score,grade,academic_term,academic_year) SELECT ?,?,?,s.class_id,?,?,?,? FROM students s WHERE s.id=? ON DUPLICATE KEY UPDATE score=?,grade=?');
    $s->bind_param('iiidssisidss',$sid,$coid,$tid,$sc,$gr,$at,$ay,$sid,$sc,$gr);
    if($s->execute()){$msg='Grade recorded';}
  }
}
$courses=$conn->query("SELECT DISTINCT c.id,c.course_name FROM teacher_courses tc JOIN courses c ON tc.course_id=c.id WHERE tc.teacher_id=$tid");
$students=$conn->query("SELECT DISTINCT s.id,s.first_name,s.last_name FROM students s JOIN teacher_courses tc ON s.class_id=tc.class_id WHERE tc.teacher_id=$tid");
include '../includes/layout-start.php';?>
<h1>Upload Grades</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Student<select name="student_id" required><?php while($st=$students->fetch_assoc()):?><option value="<?=$st['id']?>"><?=e($st['first_name'].' '.$st['last_name'])?></option><?php endwhile;?></select></label>
    <label>Course<select name="course_id" required><?php while($co=$courses->fetch_assoc()):?><option value="<?=$co['id']?>"><?=e($co['course_name'])?></option><?php endwhile;?></select></label>
    <label>Score (0-100)<input type="number" name="score" min="0" max="100" step="0.01" required></label>
    <label>Term<input type="text" name="academic_term" placeholder="Term 1" required></label>
    <label>Year<input type="number" name="academic_year" value="<?=date('Y')?>" required></label>
    <button>Submit</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>