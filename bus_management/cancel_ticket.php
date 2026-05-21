<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); exit();
}
require 'config.php';

$id = $_GET['id'];
$userID = $_SESSION['user_id'];

// Admin can cancel any ticket, passenger can only cancel their own
if ($_SESSION['role'] == 'admin') {
    $stmt = mysqli_prepare($conn, "UPDATE Tickets SET Status = 'CANCELLED' WHERE TicketID = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);
} else {
    $stmt = mysqli_prepare($conn, "UPDATE Tickets SET Status = 'CANCELLED' WHERE TicketID = ? AND UserID = ?");
    mysqli_stmt_bind_param($stmt, "si", $id, $userID);
}

if (mysqli_stmt_execute($stmt)) {
    header("Location: tickets.php");
} else {
    echo "Error cancelling ticket!";
}
?>