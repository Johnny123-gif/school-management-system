<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $fn=trim($_POST['first_name']??'');$ln=trim($_POST['last_name']??'');$em=trim($_POST['email']??'');$pw=$_POST['password']??'';
  if(empty($fn)||empty($ln)||empty($em)||strlen($pw)<6){$msg='Invalid input';}else{
    $s=$conn->prepare('SELECT id FROM users WHERE email=?'); $s->bind_param('s',$em); $s->execute();
    if($s->get_result()->num_rows>0){$msg='Email exists';}
    else{$hp=password_hash($pw,PASSWORD_DEFAULT);$r='teacher';
      $s=$conn->prepare('INSERT INTO users(email,password,role) VALUES(?,?,?)'); $s->bind_param('sss',$em,$hp,$r); 
      if($s->execute()){$uid=$conn->insert_id;
        $s=$conn->prepare('INSERT INTO teachers(user_id,first_name,last_name) VALUES(?,?,?)'); $s->bind_param('iss',$uid,$fn,$ln);
        if($s->execute()){$msg='Teacher registered';}
      }
    }
  }
}
$teachers=$conn->query('SELECT t.id,t.first_name,t.last_name,t.is_class_teacher,u.email FROM teachers t JOIN users u ON t.user_id=u.id');
include '../includes/layout-start.php';?>
<h1>Register Teacher</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>First name<input type="text" name="first_name" required></label>
    <label>Last name<input type="text" name="last_name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button>Register</button>
  </form>
</div>
<h2>Teachers</h2>
<div class="card"><table><tr><th>Name</th><th>Email</th><th>Class teacher</th></tr>
<?php while($t=$teachers->fetch_assoc()):?><tr><td><?=e($t['first_name'].' '.$t['last_name'])?></td><td><?=e($t['email'])?></td><td><?=$t['is_class_teacher']?'Yes':'No'?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>