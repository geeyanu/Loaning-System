<?php
    session_start();
    require_once 'database.php';
    $error = "";

    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $email = $_POST['email'];
        $password = $_POST['password'];

        /*Stops SQL injection 
          p.s. don't try "' OR '1'='1" other wise it'll be true and ruin the system
        */
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($result && password_verify($password, $result['password'])){
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];

            if ($result['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($result['role'] == 'cashier') {
                header("Location: cashier_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
?>