<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $pid=$_POST['parent_id']??0;$sid=$_POST['student_id']??0;
  if($pid&&$sid){
    $s=$conn->prepare('INSERT INTO parent_students(parent_id,student_id) VALUES(?,?)');
    $s->bind_param('ii',$pid,$sid);
    if($s->execute()){$msg='Student linked to parent';}
  }
}
$parents=$conn->query('SELECT id,first_name,last_name FROM parents');
$students=$conn->query('SELECT s.id,s.first_name,s.last_name,c.class_name FROM students s JOIN classes c ON s.class_id=c.id');
include '../includes/layout-start.php';?>
<h1>Link Parents to Students</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Parent<select name="parent_id" required><?php while($p=$parents->fetch_assoc()):?><option value="<?=$p['id']?>"><?=e($p['first_name'].' '.$p['last_name'])?></option><?php endwhile;?></select></label>
    <label>Student<select name="student_id" required><?php while($st=$students->fetch_assoc()):?><option value="<?=$st['id']?>"><?=e($st['first_name'].' '.$st['last_name']).' - '.e($st['class_name'])?></option><?php endwhile;?></select></label>
    <button>Link</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>