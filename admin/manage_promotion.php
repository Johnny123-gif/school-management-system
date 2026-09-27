<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; 

if($_GET['action']=='process'&&$_GET['id']){
  $rid=$_GET['id'];
  $r=$conn->query("SELECT student_id,recommendation FROM performance_reports WHERE id=$rid")->fetch_assoc();
  $sid=$r['student_id'];
  $rec=$r['recommendation'];
  
  $student=$conn->query("SELECT s.id,s.class_id,c.id cid FROM students s JOIN classes c ON s.class_id=c.id WHERE s.id=$sid")->fetch_assoc();
  $old_class=$student['class_id'];
  
  $new_class=$old_class;
  $promo_type=$rec;
  
  if($rec==='promote'){
    $nc=$conn->query("SELECT id FROM classes WHERE level=(SELECT level FROM classes WHERE id=$old_class) AND class_name>(SELECT class_name FROM classes WHERE id=$old_class) LIMIT 1");
    $new_class=$nc->fetch_assoc()['id']??$old_class;
  }
  elseif($rec==='demote'){
    $nc=$conn->query("SELECT id FROM classes WHERE level=(SELECT level FROM classes WHERE id=$old_class) AND class_name<(SELECT class_name FROM classes WHERE id=$old_class) ORDER BY class_name DESC LIMIT 1");
    $new_class=$nc->fetch_assoc()['id']??$old_class;
  }
  elseif($rec==='skip_class'){
    $nc=$conn->query("SELECT id FROM classes WHERE level=(SELECT level FROM classes WHERE id=$old_class) ORDER BY class_name ASC LIMIT 2 OFFSET 1");
    $rows=$nc->num_rows;
    if($rows>0){$new_class=$nc->fetch_assoc()['id'];}
  }
  
  $s=$conn->prepare('UPDATE students SET class_id=?,promotion_status=? WHERE id=?');
  $s->bind_param('isi',$new_class,$promo_type,$sid);
  if($s->execute()){
    $uid=$_SESSION['user_id'];
    $ay=date('Y');
    $s=$conn->prepare('INSERT INTO promotion_records(student_id,from_class_id,to_class_id,promotion_type,academic_year,approved_by) VALUES(?,?,?,?,?,?)');
    $s->bind_param('iiisii',$sid,$old_class,$new_class,$rec,$ay,$uid);
    $s->execute();
    $msg='Student promotion processed successfully';
  }
}

if($_GET['action']=='manual'&&$_POST){
  $sid=$_POST['student_id']??0;$nc=$_POST['new_class_id']??0;$pt=$_POST['promotion_type']??'';$rm=$_POST['admin_remarks']??'';
  if($sid&&$nc&&$pt){
    $student=$conn->query("SELECT class_id FROM students WHERE id=$sid")->fetch_assoc();
    $old_class=$student['class_id'];
    $s=$conn->prepare('UPDATE students SET class_id=?,promotion_status=? WHERE id=?');
    $s->bind_param('isi',$nc,$pt,$sid);
    if($s->execute()){
      $uid=$_SESSION['user_id'];
      $ay=date('Y');
      $s=$conn->prepare('INSERT INTO promotion_records(student_id,from_class_id,to_class_id,promotion_type,academic_year,admin_remarks,approved_by) VALUES(?,?,?,?,?,?,?)');
      $s->bind_param('iiiissi',$sid,$old_class,$nc,$pt,$ay,$rm,$uid);
      $s->execute();
      $msg='Manual promotion applied successfully';
    }
  }
}

$reports=$conn->query('SELECT pr.id,s.first_name,s.last_name,pr.performance_rating,pr.recommendation,pr.academic_term,pr.comments,u.email FROM performance_reports pr JOIN students s ON pr.student_id=s.id JOIN users u ON pr.reporter_id=u.id WHERE pr.id NOT IN (SELECT report_id FROM promotion_records) ORDER BY pr.report_date DESC');
$promotions=$conn->query('SELECT p.id,s.first_name,s.last_name,cf.class_name from_class,ct.class_name to_class,p.promotion_type,p.academic_year FROM promotion_records p JOIN students s ON p.student_id=s.id JOIN classes cf ON p.from_class_id=cf.id JOIN classes ct ON p.to_class_id=ct.id ORDER BY p.approved_date DESC');
$classes=$conn->query('SELECT id,class_name FROM classes ORDER BY level,class_name');
$students=$conn->query('SELECT id,first_name,last_name FROM students');

include '../includes/layout-start.php';?>
<h1>Manage Student Promotion/Demotion</h1>

<h2>📋 Pending Performance Reports</h2>
<div class="card"><table><tr><th>Student</th><th>Performance</th><th>Recommendation</th><th>Term</th><th>Reporter</th><th>Action</th></tr>
<?php while($r=$reports->fetch_assoc()):?><tr><td><?=e($r['first_name'].' '.$r['last_name'])?></td><td><?=ucfirst($r['performance_rating'])?></td><td><?=ucfirst(str_replace('_',' ',$r['recommendation']))?></td><td><?=e($r['academic_term'])?></td><td><?=e($r['email'])?></td><td><a class="button" href="?action=process&id=<?=$r['id']?>">Process</a></td></tr><?php endwhile;?></table></div>

<h2>🔧 Manual Promotion/Demotion</h2>
<div class="card"><form method="post" action="?action=manual">
  <label>Student<select name="student_id" required><?php while($st=$students->fetch_assoc()):?><option value="<?=$st['id']?>"><?=e($st['first_name'].' '.$st['last_name'])?></option><?php endwhile;?></select></label>
  <label>New Class<select name="new_class_id" required><?php while($c=$classes->fetch_assoc()):?><option value="<?=$c['id']?>"><?=e($c['class_name'])?></option><?php endwhile;?></select></label>
  <label>Promotion Type<select name="promotion_type" required><option value="promote">Promote</option><option value="demote">Demote</option><option value="skip">Skip Classes</option><option value="hold_back">Hold Back</option></select></label>
  <label>Admin Remarks<textarea name="admin_remarks" rows="2"></textarea></label>
  <button>Apply</button>
</form></div>

<h2>📊 Promotion History</h2>
<div class="card"><?php if($msg):?><div class="success"><?=e($msg)?></div><?php endif;?>
<table><tr><th>Student</th><th>From Class</th><th>To Class</th><th>Type</th><th>Year</th></tr>
<?php while($p=$promotions->fetch_assoc()):?><tr><td><?=e($p['first_name'].' '.$p['last_name'])?></td><td><?=e($p['from_class'])?></td><td><?=e($p['to_class'])?></td><td><?=ucfirst($p['promotion_type'])?></td><td><?=$p['academic_year']?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>