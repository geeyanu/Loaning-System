<?php
    session_start();
    require_once 'database.php';

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['lastName'] = $user['lastName'];

        //fetch user by email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = "Invalid email or password.";
        } else {
            //verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['userId'] = $user['userId'];
                $_SESSION['role'] = $user['role'];

                //debugging: Log session data
                error_log("Session set: userId=" . $_SESSION['userId'] . ", role=" . $_SESSION['role']);

                //redirect based on role
                if ($user['role'] == 'dbadmin') {
                } elseif ($user['role'] == 'user') {
                    header("Location: dashboard.php");
                } else {
                    $error = "Invalid role.";
                }
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        }
    }
?>
