<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); exit();
}
require 'config.php';

$id = $_GET['id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM Tickets WHERE TicketID = ?");
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Ticket</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .ticket-box { border: 2px dashed #FF9800; border-radius: 10px; padding: 20px; margin: 15px 0; }
        table { width: 100%; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; color: #555; width: 45%; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; }
        .btn-blue { background: #2196F3; }
    </style>
</head>
<body>
<div class="card">
    <h2>🎫 Ticket Details</h2>
    <div class="ticket-box">
        <table>
            <tr><td>Ticket ID</td><td><?php echo $ticket['TicketID']; ?></td></tr>
            <tr><td>Passenger Name</td><td><?php echo $ticket['PassengerName']; ?></td></tr>
            <tr><td>Bus ID</td><td><?php echo $ticket['BusID']; ?></td></tr>
            <tr><td>Destination</td><td><?php echo $ticket['Destination']; ?></td></tr>
            <tr><td>Departure Date</td><td><?php echo $ticket['DepartureDate']; ?></td></tr>
            <tr><td>Departure Time</td><td><?php echo $ticket['DepartureTime']; ?></td></tr>
            <tr><td>Seat Number</td><td><?php echo $ticket['SeatNumber']; ?></td></tr>
            <tr><td>Price</td><td>₱<?php echo number_format($ticket['Price'], 2); ?></td></tr>
            <tr><td>Status</td><td><strong><?php echo $ticket['Status']; ?></strong></td></tr>
        </table>
    </div>
    <a href="tickets.php" class="btn btn-blue">⬅ Back to Tickets</a>
</div>
</body>
</html>