<?php require_once __DIR__.'/../config.php'; require_role('admin'); $msg=''; if($_GET['action']=='approve'&&$_GET['id']){
  $gid=$_GET['id'];$aid=$_SESSION['user_id'];
  $s=$conn->prepare('UPDATE grades SET is_approved=1,approved_by=?,approved_at=NOW() WHERE id=?');
  $s->bind_param('ii',$aid,$gid);
  if($s->execute()){
    $g=$conn->query("SELECT student_id,course_id,class_id,academic_term,academic_year FROM grades WHERE id=$gid")->fetch_assoc();
    $sid=$g['student_id'];$at=$g['academic_term'];$ay=$g['academic_year'];$cid=$g['class_id'];
    $avg=$conn->query("SELECT AVG(score) avg FROM grades WHERE student_id=$sid AND is_approved=1 AND academic_term='$at' AND academic_year=$ay")->fetch_assoc()['avg'];
    $cnt=$conn->query("SELECT COUNT(DISTINCT student_id) cnt FROM grades WHERE class_id=$cid AND is_approved=1 AND academic_term='$at' AND academic_year=$ay")->fetch_assoc()['cnt'];
    $rank=$conn->query("SELECT COUNT(*) pos FROM (SELECT student_id,AVG(score) a FROM grades WHERE class_id=$cid AND is_approved=1 AND academic_term='$at' AND academic_year=$ay GROUP BY student_id) t WHERE a>(SELECT AVG(score) FROM grades WHERE student_id=$sid AND is_approved=1 AND academic_term='$at' AND academic_year=$ay)")->fetch_assoc()['pos']+1;
    $s=$conn->prepare('INSERT INTO grade_summaries(student_id,class_id,academic_term,academic_year,total_courses,total_score,average_score,position) VALUES(?,?,?,?,1,?,?,?) ON DUPLICATE KEY UPDATE average_score=?,position=?');
    $s->bind_param('iiiiddid',$sid,$cid,$at,$ay,$avg,$avg,$rank,$avg,$rank);
    $s->execute();
    $msg='Grade approved and ranking updated';
  }
}
$grades=$conn->query('SELECT g.id,s.first_name sf,s.last_name sl,co.course_name,g.score,g.is_approved FROM grades g JOIN students s ON g.student_id=s.id JOIN courses co ON g.course_id=co.id WHERE g.is_approved=0 ORDER BY g.id DESC');
include '../includes/layout-start.php';?>
<h1>Approve Grades</h1>
<div class="card"><table><tr><th>Student</th><th>Course</th><th>Score</th><th>Status</th><th>Action</th></tr>
<?php while($g=$grades->fetch_assoc()):?><tr><td><?=e($g['sf'].' '.$g['sl'])?></td><td><?=e($g['course_name'])?></td><td><?=$g['score']?></td><td><?=$g['is_approved']?'Approved':'Pending'?></td><td><?php if(!$g['is_approved']):?><a class="button" href="?action=approve&id=<?=$g['id']?>">Approve</a><?php endif;?></td></tr><?php endwhile;?></table></div>
<?php include '../includes/layout-end.php';?>