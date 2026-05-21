<?php
session_start();
require 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = hash('sha256', $_POST['password']);

    $sql = "SELECT * FROM users WHERE Username = ? AND Password = ? AND is_active = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $_SESSION['user_id'] = $row['User_ID'];
        $_SESSION['username'] = $row['Username'];
        $_SESSION['role'] = $row['Role'];

        // Redirect based on role
        if ($row['Role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($row['Role'] == 'driver') {
            header("Location: driver_dashboard.php");
        } else {
            header("Location: passenger_dashboard.php");
        }

        exit();

    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bus Management System - Login</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Blurry Background Image */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('photo_2026-05-14_01-35-00.jpg') no-repeat center center/cover;
            filter: blur(6px);
            transform: scale(1.1);
            z-index: -1;
        }

        /* Login Box */
        .login-box {
            width: 350px;
            padding: 40px;
            border-radius: 15px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
            text-align: center;
            color: white;
        }

        h2 {
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #45a049;
        }

        .error {
            color: #ffb3b3;
            margin-bottom: 10px;
        }

        .register-link {
            margin-top: 15px;
        }

        .register-link a {
            color: white;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h2>🚌 Bus Management System</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

    <div class="register-link">
        <a href="register.php">Don't have an account? Register</a>
    </div>

</div>

</body>
</html>