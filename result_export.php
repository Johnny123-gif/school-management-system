<?php
require_once __DIR__ . '/config.php';
require_login();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="school-results.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Student', 'Child', 'Class', 'Course', 'Score', 'Grade', 'Term', 'Year']);

if ($_SESSION['user_role'] === 'student') {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT s.first_name, s.last_name, c.class_name, co.course_name, g.score, g.grade, g.academic_term, g.academic_year FROM students s JOIN classes c ON c.id = s.class_id JOIN grades g ON g.student_id = s.id JOIN courses co ON co.id = g.course_id WHERE s.user_id = ? AND g.is_approved = 1 ORDER BY g.academic_year DESC, g.academic_term DESC, co.course_name');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['first_name'] . ' ' . $row['last_name'],
            $row['first_name'] . ' ' . $row['last_name'],
            $row['class_name'],
            $row['course_name'],
            $row['score'],
            $row['grade'],
            $row['academic_term'],
            $row['academic_year']
        ]);
    }
}

if ($_SESSION['user_role'] === 'parent') {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT id FROM parents WHERE user_id = ?');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $parent = $stmt->get_result()->fetch_assoc();

    if ($parent) {
        $parent_id = (int)$parent['id'];
        $stmt = $conn->prepare('SELECT s.first_name, s.last_name, c.class_name, co.course_name, g.score, g.grade, g.academic_term, g.academic_year FROM parent_students ps JOIN students s ON s.id = ps.student_id JOIN classes c ON c.id = s.class_id JOIN grades g ON g.student_id = s.id JOIN courses co ON co.id = g.course_id WHERE ps.parent_id = ? AND g.is_approved = 1 ORDER BY s.last_name, s.first_name, g.academic_year DESC, g.academic_term DESC');
        $stmt->bind_param('i', $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['first_name'] . ' ' . $row['last_name'],
                $row['first_name'] . ' ' . $row['last_name'],
                $row['class_name'],
                $row['course_name'],
                $row['score'],
                $row['grade'],
                $row['academic_term'],
                $row['academic_year']
            ]);
        }
    }
}

fclose($output);
exit;
