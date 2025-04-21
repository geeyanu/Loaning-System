<?php
require 'db.php';
session_start();

if ($_SESSION['role'] == 'officer') {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'approved' : 'denied';

    $stmt = $pdo->prepare("UPDATE loan_applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    header("Location: dashboard.php");
}
?>
