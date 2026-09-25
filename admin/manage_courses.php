<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; $cid=0; if($_POST){
  $cn=trim($_POST['course_name']??'');$cc=trim($_POST['course_code']??'');
  if(empty($cn)||empty($cc)){$msg='Invalid input';}
  else{
    $s=$conn->prepare('INSERT INTO courses(course_name,course_code) VALUES(?,?)');
    $s->bind_param('ss',$cn,$cc);
    if($s->execute()){$msg='Course created';}
  }
}
$courses=$conn->query('SELECT id,course_name,course_code FROM courses ORDER BY course_name');
include '../includes/layout-start.php';?>
<h1>Manage Courses</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Course name<input type="text" name="course_name" required></label>
    <label>Course code<input type="text" name="course_code" required></label>
    <button>Create</button>
  </form>
</div>
<h2>Courses</h2>
<div class="card"><table><tr><th>Name</th><th>Code</th></tr>
<?php while($c=$courses->fetch_assoc()):?><tr><td><?=e($c['course_name'])?></td><td><?=e($c['course_code'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>