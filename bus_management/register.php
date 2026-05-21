<?php
session_start();
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = hash('sha256', $_POST['password']);
    $role = $_POST['role'];

    // Check if username exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE Username = '$username'");

    if (mysqli_num_rows($check) > 0) {

        $message = "❌ Username already exists!";

    } else {

        $sql = "INSERT INTO users (Username, Password, Role) VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);

        if (mysqli_stmt_execute($stmt)) {

            $message = "✅ Registration successful! 
            <br><br>
            <a href='login.php' style='color:white;'>Login here</a>";

        } else {

            $message = "❌ Registration failed. Try again.";

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - Bus Management System</title>

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

        /* Blurry Background */
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

        /* Register Box */
        .box {
            width: 350px;
            padding: 40px;
            border-radius: 15px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
            text-align: center;
            color: white;
        }

        h2 {
            margin-bottom: 25px;
        }

        input,
        select {
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
            background: #2196F3;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #1976D2;
        }

        .message {
            margin-bottom: 15px;
            color: white;
        }

        a {
            color: white;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>📝 Register</h2>

    <?php if ($message) echo "<p class='message'>$message</p>"; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <select name="role">
            <option value="passenger">Passenger</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Register</button>

    </form>

    <p style="margin-top:15px;">
        <a href="login.php">Back to Login</a>
    </p>

</div>

</body>
</html>