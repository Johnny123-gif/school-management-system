<?php require_once __DIR__.'/../config.php'; require_role('parent'); $pid=$_SESSION['user_id'];
$s=$conn->prepare('SELECT id FROM parents WHERE user_id=?'); $s->bind_param('i',$pid); $s->execute();
$pid=$s->get_result()->fetch_assoc()['id'];
$grades=$conn->query("SELECT s.first_name,s.last_name,co.course_name,g.score,g.grade,g.academic_term FROM grades g JOIN parent_students ps ON ps.parent_id=$pid AND ps.student_id=g.student_id JOIN students s ON s.id=g.student_id JOIN courses co ON co.id=g.course_id WHERE g.is_approved=1 ORDER BY g.academic_term DESC,s.last_name");
include '../includes/layout-start.php';?>
<h1>Child Results</h1>
<div class="card"><table><tr><th>Child</th><th>Course</th><th>Score</th><th>Grade</th><th>Term</th></tr>
<?php while($g=$grades->fetch_assoc()):?><tr><td><?=e($g['first_name'].' '.$g['last_name'])?></td><td><?=e($g['course_name'])?></td><td><?=$g['score']?></td><td><?=$g['grade']?></td><td><?=e($g['academic_term'])?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>