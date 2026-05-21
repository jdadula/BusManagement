<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}
require 'config.php';

$id = $_GET['id'];
$message = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM Drivers WHERE DriverID = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$driver = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $license = trim($_POST['license']);
    $contact = trim($_POST['contact']);
    $busid = trim($_POST['busid']);

    $update = mysqli_prepare($conn, "UPDATE Drivers SET FirstName=?, LastName=?, LicenseNumber=?, ContactNumber=?, BusID=? WHERE DriverID=?");
    mysqli_stmt_bind_param($update, "sssssi", $firstname, $lastname, $license, $contact, $busid, $id);
    if (mysqli_stmt_execute($update)) {
        $message = "<p style='color:green;'>✅ Driver updated successfully!</p>";
        header("refresh:1;url=drivers.php");
    } else {
        $message = "<p style='color:red;'>❌ Update failed!</p>";
    }
}

$buses = mysqli_query($conn, "SELECT Bus_no FROM Bus");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Driver</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; border: none; cursor: pointer; }
        .btn-orange { background: #FF9800; }
        .btn-blue { background: #2196F3; }
        label { font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>✏️ Edit Driver Information</h2>
    <?php echo $message; ?>
    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="firstname" value="<?php echo $driver['FirstName']; ?>" required>

        <label>Last Name:</label>
        <input type="text" name="lastname" value="<?php echo $driver['LastName']; ?>" required>

        <label>License Number:</label>
        <input type="text" name="license" value="<?php echo $driver['LicenseNumber']; ?>" required>

        <label>Contact Number:</label>
        <input type="text" name="contact" value="<?php echo $driver['ContactNumber']; ?>" required>

        <label>Assign Bus:</label>
        <select name="busid">
            <?php while ($bus = mysqli_fetch_assoc($buses)) { ?>
            <option value="<?php echo $bus['Bus_no']; ?>" <?php if ($bus['Bus_no'] == $driver['BusID']) echo 'selected'; ?>>
                <?php echo $bus['Bus_no']; ?>
            </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn btn-orange">Update Driver</button>
        <a href="drivers.php" class="btn btn-blue" style="margin-left:10px;">Cancel</a>
    </form>
</div>
</body>
</html>