<?php require_once __DIR__.'/../config.php'; require_role(['teacher','class_teacher','head_teacher']);
$uid = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
$msg = '';

$stmt = $conn->prepare('SELECT id FROM teachers WHERE user_id = ?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$teacher_id = $teacher['id'] ?? 0;

if ($role === 'head_teacher') {
    $head = $conn->query("SELECT level FROM head_teachers WHERE user_id = $uid")->fetch_assoc();
    $level = $head['level'] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    $score = (float)($_POST['score'] ?? 0);
    $term = trim($_POST['academic_term'] ?? '');
    $year = (int)($_POST['academic_year'] ?? date('Y'));

    if ($student_id > 0 && $course_id > 0 && $score >= 0 && $score <= 100 && $term !== '') {
        $class_id = 0;
        $cls = $conn->query("SELECT class_id FROM students WHERE id = $student_id")->fetch_assoc();
        if ($cls) {
            $class_id = (int)$cls['class_id'];
        }

        $allowed = false;

        if ($role === 'teacher') {
            $check = $conn->query("SELECT 1 FROM teacher_courses WHERE teacher_id = $teacher_id AND course_id = $course_id AND class_id = $class_id LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $allowed = true;
            }
        }

        if ($role === 'class_teacher') {
            $check = $conn->query("SELECT 1 FROM classes WHERE class_teacher_id = $teacher_id AND id = $class_id LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $allowed = true;
            }
        }

        if ($role === 'head_teacher') {
            $check = $conn->query("SELECT 1 FROM classes WHERE id = $class_id AND level = '$level' LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $allowed = true;
            }
        }

        if ($allowed) {
            $grade = grade_letter($score);
            $stmt = $conn->prepare('INSERT INTO grades (student_id, course_id, teacher_id, class_id, score, grade, academic_term, academic_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), grade = VALUES(grade), teacher_id = VALUES(teacher_id), class_id = VALUES(class_id)');
            $stmt->bind_param('iiiidssi', $student_id, $course_id, $teacher_id, $class_id, $score, $grade, $term, $year);

            if ($stmt->execute()) {
                $msg = 'Grade saved successfully.';
            } else {
                $msg = 'Error saving grade: ' . $stmt->error;
            }
        } else {
            $msg = 'You are not assigned to this course/class/level.';
        }
    } else {
        $msg = 'Please fill in all required fields correctly.';
    }
}

if ($role === 'teacher') {
    $courses = $conn->query("SELECT DISTINCT c.id, c.course_name FROM teacher_courses tc JOIN courses c ON c.id = tc.course_id WHERE tc.teacher_id = $teacher_id ORDER BY c.course_name");
    $students = $conn->query("SELECT DISTINCT s.id, s.first_name, s.last_name FROM teacher_courses tc JOIN students s ON s.class_id = tc.class_id WHERE tc.teacher_id = $teacher_id ORDER BY s.first_name, s.last_name");
}

if ($role === 'class_teacher') {
    $courses = $conn->query("SELECT DISTINCT c.id, c.course_name FROM teacher_courses tc JOIN courses c ON c.id = tc.course_id JOIN classes cls ON cls.id = tc.class_id WHERE cls.class_teacher_id = $teacher_id ORDER BY c.course_name");
    $students = $conn->query("SELECT id, first_name, last_name FROM students WHERE class_id IN (SELECT id FROM classes WHERE class_teacher_id = $teacher_id) ORDER BY first_name, last_name");
}

if ($role === 'head_teacher') {
    $courses = $conn->query("SELECT DISTINCT c.id, c.course_name FROM teacher_courses tc JOIN courses c ON c.id = tc.course_id JOIN classes cls ON cls.id = tc.class_id WHERE cls.level = '$level' ORDER BY c.course_name");
    $students = $conn->query("SELECT s.id, s.first_name, s.last_name FROM students s JOIN classes cls ON cls.id = s.class_id WHERE cls.level = '$level' ORDER BY s.first_name, s.last_name");
}

include '../includes/layout-start.php';
?>
<h1>Upload Grades</h1>
<div class="card">
    <?php if ($msg): ?>
        <div class="success"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Student
            <select name="student_id" required>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </label>

        <label>Course
            <select name="course_id" required>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?= $course['id'] ?>"><?= e($course['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </label>

        <label>Score (0-100)
            <input type="number" name="score" min="0" max="100" step="0.01" required>
        </label>

        <label>Academic Term
            <input type="text" name="academic_term" placeholder="Term 1" required>
        </label>

        <label>Academic Year
            <input type="number" name="academic_year" value="<?= date('Y') ?>" required>
        </label>

        <button type="submit">Save Grade</button>
    </form>
</div>
<?php include '../includes/layout-end.php'; ?>
