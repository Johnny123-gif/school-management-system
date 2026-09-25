<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $fn=trim($_POST['first_name']??'');$ln=trim($_POST['last_name']??'');$em=trim($_POST['email']??'');$pw=$_POST['password']??'';$cid=$_POST['class_id']??0;
  if(empty($fn)||empty($ln)||empty($em)||strlen($pw)<6||!$cid){$msg='Invalid input';}
  else{
    $s=$conn->prepare('SELECT id FROM users WHERE email=?'); $s->bind_param('s',$em); $s->execute();
    if($s->get_result()->num_rows>0){$msg='Email exists';}
    else{
      $hp=password_hash($pw,PASSWORD_DEFAULT);$r='student';$an='STU-'.date('Y').'-'.rand(10000,99999);
      $s=$conn->prepare('INSERT INTO users(email,password,role) VALUES(?,?,?)'); $s->bind_param('sss',$em,$hp,$r);
      if($s->execute()){$uid=$conn->insert_id;
        $s=$conn->prepare('INSERT INTO students(user_id,first_name,last_name,admission_number,class_id) VALUES(?,?,?,?,?)'); $s->bind_param('isssi',$uid,$fn,$ln,$an,$cid);
        if($s->execute()){$msg="Student registered. Admission: $an";}
      }
    }
  }
}
$classes=$conn->query('SELECT id,class_name,level FROM classes ORDER BY level,class_name');
$students=$conn->query('SELECT s.first_name,s.last_name,s.admission_number,c.class_name FROM students s JOIN classes c ON s.class_id=c.id');
include '../includes/layout-start.php';?>
<h1>Register Student</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>First name<input type="text" name="first_name" required></label>
    <label>Last name<input type="text" name="last_name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <label>Class<select name="class_id" required><?php while($c=$classes->fetch_assoc()):?><option value="<?=$c['id']?>"><?=e($c['class_name'])?> (<?=ucfirst($c['level'])?>)</option><?php endwhile;?></select></label>
    <button>Register</button>
  </form>
</div>
<h2>Students</h2>
<div class="card"><table><tr><th>Name</th><th>Admission</th><th>Class</th></tr>
<?php while($st=$students->fetch_assoc()):?><tr><td><?=e($st['first_name'].' '.$st['last_name'])?></td><td><?=e($st['admission_number'])?></td><td><?=e($st['class_name'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>