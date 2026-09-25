<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $tid=$_POST['teacher_id']??0;$cid=$_POST['course_id']??0;$clid=$_POST['class_id']??0;
  if($tid&&$cid&&$clid){
    $s=$conn->prepare('INSERT INTO teacher_courses(teacher_id,course_id,class_id) VALUES(?,?,?)');
    $s->bind_param('iii',$tid,$cid,$clid);
    if($s->execute()){$msg='Course assigned';}
    else{$msg='Error: '.$s->error;}
  }
}
$teachers=$conn->query('SELECT id,first_name,last_name FROM teachers');
$courses=$conn->query('SELECT id,course_name FROM courses');
$classes=$conn->query('SELECT id,class_name FROM classes');
include '../includes/layout-start.php';?>
<h1>Assign Courses to Teachers</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Teacher<select name="teacher_id" required><?php while($t=$teachers->fetch_assoc()):?><option value="<?=$t['id']?>"><?=e($t['first_name'].' '.$t['last_name'])?></option><?php endwhile;?></select></label>
    <label>Course<select name="course_id" required><?php while($co=$courses->fetch_assoc()):?><option value="<?=$co['id']?>"><?=e($co['course_name'])?></option><?php endwhile;?></select></label>
    <label>Class<select name="class_id" required><?php while($cl=$classes->fetch_assoc()):?><option value="<?=$cl['id']?>"><?=e($cl['class_name'])?></option><?php endwhile;?></select></label>
    <button>Assign</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>