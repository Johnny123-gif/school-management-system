<?php require_once __DIR__.'/../config.php'; require_role('admin'); $cid=$_GET['id']??0; $msg=''; if($_POST){
  $tid=$_POST['teacher_id']??0;
  if($tid){
    $s=$conn->prepare('SELECT user_id FROM teachers WHERE id=?'); $s->bind_param('i',$tid); $s->execute();
    $uid=$s->get_result()->fetch_assoc()['user_id'];
    $s=$conn->prepare('UPDATE classes SET class_teacher_id=? WHERE id=?'); $s->bind_param('ii',$tid,$cid);
    if($s->execute()){$msg='Class teacher assigned';}
  }
}
$c=$conn->query("SELECT class_name FROM classes WHERE id=$cid")->fetch_assoc();
$teachers=$conn->query('SELECT id,first_name,last_name,is_class_teacher FROM teachers WHERE is_class_teacher=1');
include '../includes/layout-start.php';?>
<h1>Assign Class Teacher to <?=e($c['class_name']??'Class')?></h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Select Class Teacher<select name="teacher_id" required><?php while($t=$teachers->fetch_assoc()):?><option value="<?=$t['id']?>"><?=e($t['first_name'].' '.$t['last_name'])?></option><?php endwhile;?></select></label>
    <button>Assign</button>
  </form>
</div>
<?php include '../includes/layout-end.php';?>