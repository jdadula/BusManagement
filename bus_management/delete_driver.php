<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: drivers.php");
    exit();
}

$id = intval($_GET['id']);

// Bug 3 Fix — Get Username directly from Drivers table
$getDriver = mysqli_prepare($conn, "SELECT Username FROM Drivers WHERE DriverID = ?");
mysqli_stmt_bind_param($getDriver, "i", $id);
mysqli_stmt_execute($getDriver);
$driverResult = mysqli_stmt_get_result($getDriver);
$driver = mysqli_fetch_assoc($driverResult);

if ($driver) {
    $username = $driver['Username'];

    // Delete from Drivers table
    $delDriver = mysqli_prepare($conn, "DELETE FROM Drivers WHERE DriverID = ?");
    mysqli_stmt_bind_param($delDriver, "i", $id);
    mysqli_stmt_execute($delDriver);

    // Soft delete — mark driver account as inactive
    $delUser = mysqli_prepare($conn, "UPDATE users SET is_active = 0 WHERE Username = ? AND Role = 'driver'");
    mysqli_stmt_bind_param($delUser, "s", $username);
    mysqli_stmt_execute($delUser);
}

header("Location: drivers.php");
exit();
