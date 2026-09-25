<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_POST){
  $fn=trim($_POST['first_name']??'');$ln=trim($_POST['last_name']??'');$em=trim($_POST['email']??'');$pw=$_POST['password']??'';
  if(empty($fn)||empty($ln)||empty($em)||strlen($pw)<6){$msg='Invalid input';}
  else{
    $s=$conn->prepare('SELECT id FROM users WHERE email=?'); $s->bind_param('s',$em); $s->execute();
    if($s->get_result()->num_rows>0){$msg='Email exists';}
    else{
      $hp=password_hash($pw,PASSWORD_DEFAULT);$r='parent';
      $s=$conn->prepare('INSERT INTO users(email,password,role) VALUES(?,?,?)'); $s->bind_param('sss',$em,$hp,$r);
      if($s->execute()){$uid=$conn->insert_id;
        $s=$conn->prepare('INSERT INTO parents(user_id,first_name,last_name) VALUES(?,?,?)'); $s->bind_param('iss',$uid,$fn,$ln);
        if($s->execute()){$msg='Parent registered';}
      }
    }
  }
}
$parents=$conn->query('SELECT p.id,p.first_name,p.last_name,u.email FROM parents p JOIN users u ON p.user_id=u.id');
include '../includes/layout-start.php';?>
<h1>Register Parent</h1>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
  <form method="post">
    <label>First name<input type="text" name="first_name" required></label>
    <label>Last name<input type="text" name="last_name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button>Register</button>
  </form>
</div>
<h2>Parents</h2>
<div class="card"><table><tr><th>Name</th><th>Email</th></tr>
<?php while($p=$parents->fetch_assoc()):?><tr><td><?=e($p['first_name'].' '.$p['last_name'])?></td><td><?=e($p['email'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>