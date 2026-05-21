<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $license = trim($_POST['license']);
    $contact = trim($_POST['contact']);
    $busid = trim($_POST['busid']);
    $busid = empty($busid) ? null : $busid;

    if (empty($username) || empty($password) || empty($firstname) || empty($lastname) || empty($license) || empty($contact)) {
        $message = "<p style='color:red;'>❌ All fields are required!</p>";

    } elseif (strlen($password) < 6) {
        $message = "<p style='color:red;'>❌ Password must be at least 6 characters!</p>";

    } elseif (!preg_match('/^[0-9]{10,11}$/', $contact)) {
        $message = "<p style='color:red;'>❌ Contact number must be 10-11 digits!</p>";

    } else {
        // Check if license already exists
        $checkLicense = mysqli_prepare($conn, "SELECT * FROM Drivers WHERE LicenseNumber = ?");
        mysqli_stmt_bind_param($checkLicense, "s", $license);
        mysqli_stmt_execute($checkLicense);
        $licenseResult = mysqli_stmt_get_result($checkLicense);

        // Check if username already exists
        $checkUser = mysqli_prepare($conn, "SELECT * FROM users WHERE Username = ?");
        mysqli_stmt_bind_param($checkUser, "s", $username);
        mysqli_stmt_execute($checkUser);
        $userResult = mysqli_stmt_get_result($checkUser);

        if (mysqli_num_rows($licenseResult) > 0) {
            $message = "<p style='color:red;'>❌ License number already exists!</p>";

        } elseif (mysqli_num_rows($userResult) > 0) {
            $message = "<p style='color:red;'>❌ Username already exists!</p>";

        } else {
            // Step 1 — Create login account in users table FIRST
            $hashedPassword = hash('sha256', $password);
            $role = 'driver';
            $userStmt = mysqli_prepare($conn, "INSERT INTO users (Username, Password, Role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($userStmt, "sss", $username, $hashedPassword, $role);

            if (mysqli_stmt_execute($userStmt)) {
                // Step 2 — Get the auto-generated User_ID
                $newUserID = mysqli_insert_id($conn);

                // Step 3 — Insert into Drivers table with same Username
                $stmt = mysqli_prepare($conn, "INSERT INTO Drivers (Username, FirstName, LastName, LicenseNumber, ContactNumber, BusID) VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssssss", $username, $firstname, $lastname, $license, $contact, $busid);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "<p style='color:green;'>
                        ✅ Driver registered successfully!<br>
                        User ID: <strong>#$newUserID</strong><br>
                        Username: <strong>$username</strong><br>
                        Password: <strong>$password</strong>
                    </p>";
                } else {
                    // Rollback user creation if driver insert fails
                    mysqli_query($conn, "DELETE FROM users WHERE User_ID = $newUserID");
                    $message = "<p style='color:red;'>❌ Error saving driver: " . mysqli_error($conn) . "</p>";
                }
            } else {
                $message = "<p style='color:red;'>❌ Error creating account: " . mysqli_error($conn) . "</p>";
            }
        }
    }
}

$buses = mysqli_query($conn, "SELECT Bus_no FROM Bus");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Driver</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; border: none; cursor: pointer; font-size: 14px; }
        .btn-green { background: #4CAF50; }
        .btn-blue { background: #2196F3; }
        label { font-weight: bold; }
        .info-box { background: #e3f2fd; border-left: 4px solid #2196F3; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
<div class="card">
    <h2>➕ Register New Driver</h2>

    <div class="info-box">
        ℹ️ The driver will use this username and password to log in to the Driver Dashboard.
    </div>

    <?php echo $message; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password (min 6 characters)" required>

        <label>First Name:</label>
        <input type="text" name="firstname" placeholder="Enter first name" required>

        <label>Last Name:</label>
        <input type="text" name="lastname" placeholder="Enter last name" required>

        <label>License Number:</label>
        <input type="text" name="license" placeholder="Enter license number" required>

        <label>Contact Number:</label>
        <input type="text" name="contact" placeholder="Enter 10-11 digit contact number" required>

        <label>Assign Bus:</label>
        <select name="busid">
            <option value="">-- No Bus Assigned --</option>
            <?php while ($bus = mysqli_fetch_assoc($buses)) { ?>
            <option value="<?php echo $bus['Bus_no']; ?>">
                <?php echo $bus['Bus_no']; ?>
            </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn btn-green">Register Driver</button>
        <a href="drivers.php" class="btn btn-blue" style="margin-left:10px;">Back</a>
    </form>
</div>
</body>
</html>