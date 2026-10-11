<?php require_once __DIR__.'/../config.php'; require_role('student'); $uid=$_SESSION['user_id'];
$grades=$conn->query("SELECT co.course_name,g.score,g.grade,g.academic_term,g.academic_year FROM grades g JOIN students s ON s.user_id=$uid AND s.id=g.student_id JOIN courses co ON co.id=g.course_id WHERE g.is_approved=1 ORDER BY g.academic_year DESC, g.academic_term DESC, co.course_name");
include '../includes/layout-start.php';?>
<h1>My Results</h1>
<a class="button" href="../result_export.php">Download Results (CSV)</a>
<div class="card"><table><tr><th>Course</th><th>Score</th><th>Grade</th><th>Term</th><th>Year</th></tr>
<?php while($g=$grades->fetch_assoc()):?><tr><td><?=e($g['course_name'])?></td><td><?=$g['score']?></td><td><?=$g['grade']?></td><td><?=e($g['academic_term'])?></td><td><?=$g['academic_year']?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>