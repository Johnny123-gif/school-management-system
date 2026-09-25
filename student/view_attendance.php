<?php require_once __DIR__.'/../config.php'; require_role('student'); $uid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id FROM students WHERE user_id=?'); $s->bind_param('i',$uid); $s->execute();
$sid=$s->get_result()->fetch_assoc()['id'];
$att=$conn->query("SELECT attendance_date,status FROM attendance WHERE student_id=$sid ORDER BY attendance_date DESC");
include '../includes/layout-start.php';?>
<h1>My Attendance</h1>
<div class="card"><table><tr><th>Date</th><th>Status</th></tr>
<?php while($a=$att->fetch_assoc()):?><tr><td><?=$a['attendance_date']?></td><td><?=ucfirst($a['status'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>