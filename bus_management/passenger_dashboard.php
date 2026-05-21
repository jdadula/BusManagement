<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'passenger') {
    header("Location: login.php"); exit();
}
require 'config.php';
?>
<!DOCTYPE html>
<html>
<head><title>Passenger Dashboard</title>
<style>
body { font-family: Arial; background: #f0f0f0; padding: 30px; }
.card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ddd; padding: 8px; }
th { background: #FF9800; color: white; }
a.btn { padding: 8px 15px; background: red; color: white; border-radius: 5px; text-decoration: none; }
</style>
</head>
<body>
<div class="card">
    <h2>🎫 Passenger Dashboard</h2>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong>! Role: <strong>PASSENGER</strong></p>
    <a href="tickets.php" style="padding:8px 15px; background:#FF9800; color:white; border-radius:5px; text-decoration:none; margin-right:5px;">🎫 My Tickets</a>
    <a href="logout.php" class="btn">Logout</a>
</div>
<div class="card">
    <h3>🚌 Available Bus Routes</h3>
    <table>
        <tr><th>Bus No</th><th>Source</th><th>Destination</th><th>Couch Type</th><th>Fare</th><th>Available Seats</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM Bus WHERE Status != 'OUT OF SERVICE'");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$row['Bus_no']}</td><td>{$row['Source']}</td><td>{$row['Destination']}</td><td>{$row['Couch_type']}</td><td>₱{$row['Fair']}</td><td>{$row['SeatCapacity']}</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>