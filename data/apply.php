<?php
require 'db.php';
session_start();

if ($_SESSION['role'] == 'applicant' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $stmt = $pdo->prepare("INSERT INTO loan_applications (user_id, applicant_name, amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $amount]);
    header("Location: dashboard.php");
}
?>
