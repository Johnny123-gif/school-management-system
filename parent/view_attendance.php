<?php require_once __DIR__.'/../config.php'; require_role('parent'); $pid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id FROM parents WHERE user_id=?'); $s->bind_param('i',$pid); $s->execute();
$pid=$s->get_result()->fetch_assoc()['id'];
$att=$conn->query("SELECT s.first_name,s.last_name,a.attendance_date,a.status FROM attendance a JOIN parent_students ps ON ps.parent_id=$pid AND ps.student_id=a.student_id JOIN students s ON s.id=a.student_id ORDER BY a.attendance_date DESC");
include '../includes/layout-start.php';?>
<h1>Child Attendance</h1>
<div class="card"><table><tr><th>Child</th><th>Date</th><th>Status</th></tr>
<?php while($a=$att->fetch_assoc()):?><tr><td><?=e($a['first_name'].' '.$a['last_name'])?></td><td><?=$a['attendance_date']?></td><td><?=ucfirst($a['status'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>