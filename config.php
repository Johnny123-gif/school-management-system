<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
const DB_HOST='localhost'; const DB_USER='root'; const DB_PASS=''; const DB_NAME='school_management';
$conn=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME); $conn->set_charset('utf8mb4');
if(session_status()===PHP_SESSION_NONE) session_start();
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function logged_in(){return isset($_SESSION['user_id'],$_SESSION['user_role']);}
function require_login(){if(!logged_in()){header('Location: '.BASE_URL.'index.php');exit;}}
function require_role($roles){require_login();$roles=(array)$roles;if(!in_array($_SESSION['user_role'],$roles,true)){http_response_code(403);exit('Access denied');}}
function go_role(){ $map=['admin'=>'admin/dashboard.php','teacher'=>'teacher/dashboard.php','class_teacher'=>'class_teacher/dashboard.php','head_teacher'=>'head_teacher/dashboard.php','parent'=>'parent/dashboard.php','student'=>'student/dashboard.php']; header('Location: '.BASE_URL.($map[$_SESSION['user_role']]??'index.php'));exit;}
function grade_letter($score){return $score>=90?'A':($score>=80?'B':($score>=70?'C':($score>=60?'D':'F')));}
const BASE_URL='http://localhost/school-management-system/';
?>