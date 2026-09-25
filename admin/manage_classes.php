<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $cn=trim($_POST['class_name']??'');$lv=$_POST['level']??'';$cap=$_POST['capacity']??50;
  if(empty($cn)||empty($lv)){$msg='Invalid input';}
  else{
    $s=$conn->prepare('INSERT INTO classes(class_name,level,capacity) VALUES(?,?,?)'); $s->bind_param('ssi',$cn,$lv,$cap);
    if($s->execute()){$msg='Class created';}
  }
}
$classes=$conn->query('SELECT c.id,c.class_name,c.level,c.capacity,t.first_name,t.last_name FROM classes c LEFT JOIN teachers t ON c.class_teacher_id=t.id ORDER BY c.level,c.class_name');
include '../includes/layout-start.php';?>
<h1>Manage Classes</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>Class name<input type="text" name="class_name" required></label>
    <label>Level<select name="level" required><option>Primary</option><option>Secondary</option></select></label>
    <label>Capacity<input type="number" name="capacity" value="50" min="1"></label>
    <button>Create</button>
  </form>
</div>
<h2>Classes</h2>
<div class="card"><table><tr><th>Name</th><th>Level</th><th>Capacity</th><th>Class Teacher</th><th>Action</th></tr>
<?php while($c=$classes->fetch_assoc()):?><tr><td><?=e($c['class_name'])?></td><td><?=ucfirst($c['level'])?></td><td><?=$c['capacity']?></td><td><?=$c['first_name']?e($c['first_name'].' '.$c['last_name']):'None'?></td><td><a class="button" href="assign_class_teacher.php?id=<?=$c['id']?>">Assign</a></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>