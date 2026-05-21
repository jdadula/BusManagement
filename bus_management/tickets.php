<?php
session_start();
// Bug 5 Fix — Block drivers from accessing tickets
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'driver') {
    header("Location: login.php"); exit();
}
require 'config.php';

// Bug 2 Fix — Auto update to SOLD for ALL users not just admin
mysqli_query($conn, "
    UPDATE Tickets 
    SET Status = 'SOLD' 
    WHERE Status = 'BOOKED' 
    AND CONCAT(DepartureDate, ' ', DepartureTime) < NOW()
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ticket Management</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #FF9800; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; margin: 3px; cursor: pointer; border: none; font-size: 14px; }
        .btn-green { background: #4CAF50; }
        .btn-blue { background: #2196F3; }
        .btn-red { background: #f44336; }
        .btn-orange { background: #FF9800; }
        /* Bug 4 Fix — Added status-sold CSS */
        .status-booked { color: green; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .status-sold { color: blue; font-weight: bold; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    </style>
</head>
<body>

<div class="card">
    <div class="header-bar">
        <h2>🎫 Ticket Management System</h2>
        <div>
            <?php if ($_SESSION['role'] == 'passenger') { ?>
                <a href="book_ticket.php" class="btn btn-green">➕ Book New Ticket</a>
                <!-- Bug 1 Fix — Only ONE seats button for passenger -->
                <a href="seats.php" class="btn btn-blue">💺 View Available Seats</a>
            <?php } ?>
            <a href="<?php echo $_SESSION['role']; ?>_dashboard.php" class="btn btn-blue">🏠 Dashboard</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | Role: <strong><?php echo strtoupper($_SESSION['role']); ?></strong></p>
</div>

<?php if (isset($_GET['booked'])) { ?>
<div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:15px;">
    ✅ Ticket booked! ID: <strong><?php echo htmlspecialchars($_GET['booked']); ?></strong> |
    Seat: <strong><?php echo htmlspecialchars($_GET['seat']); ?></strong> |
    Price: <strong>₱<?php echo htmlspecialchars($_GET['price']); ?></strong>
</div>
<?php } ?>

<div class="card">
    <!-- Bug 7 Fix — Dynamic title based on role -->
    <h3>🎟️ <?php echo $_SESSION['role'] == 'admin' ? 'All Tickets' : 'My Tickets'; ?></h3>
    <?php
    if ($_SESSION['role'] == 'admin') {
        // Bug 3 Fix — Admin sees all tickets
        $result = mysqli_query($conn, "SELECT * FROM Tickets ORDER BY DepartureDate DESC");
    } else {
        // Bug 3 Fix — Use prepared statement for passenger
        $uid = $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM Tickets WHERE UserID = ? ORDER BY DepartureDate DESC");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (mysqli_num_rows($result) == 0) {
        echo "<p style='color:gray;'>No tickets found.</p>";
    } else {
    ?>
    <table>
        <tr>
            <th>Ticket ID</th>
            <th>Passenger Name</th>
            <th>Bus ID</th>
            <th>Destination</th>
            <th>Departure Date</th>
            <th>Departure Time</th>
            <th>Seat No.</th>
            <th>Price</th>
            <th>Actions</th>
            <th>Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['TicketID']); ?></td>
            <td><?php echo htmlspecialchars($row['PassengerName']); ?></td>
            <td><?php echo htmlspecialchars($row['BusID']); ?></td>
            <td><?php echo htmlspecialchars($row['Destination']); ?></td>
            <td><?php echo $row['DepartureDate']; ?></td>
            <td><?php echo $row['DepartureTime']; ?></td>
            <td><?php echo $row['SeatNumber']; ?></td>
            <td>₱<?php echo number_format($row['Price'], 2); ?></td>
            <td class="status-<?php echo strtolower($row['Status']); ?>">
                <?php echo $row['Status']; ?>
            </td>
            <td>
                <a href="view_ticket.php?id=<?php echo $row['TicketID']; ?>" class="btn btn-blue">👁 View</a>
                <?php if ($row['Status'] == 'BOOKED') { ?>
                    <!-- Only passengers can edit/cancel their own tickets -->
                    <?php if ($_SESSION['role'] == 'passenger') { ?>
                        <a href="edit_ticket.php?id=<?php echo $row['TicketID']; ?>" class="btn btn-orange">✏️ Edit</a>
                        <a href="cancel_ticket.php?id=<?php echo $row['TicketID']; ?>" class="btn btn-red" onclick="return confirm('Cancel this ticket?')">❌ Cancel</a>
                    <?php } else if ($_SESSION['role'] == 'admin') { ?>
                        <a href="cancel_ticket.php?id=<?php echo $row['TicketID']; ?>" class="btn btn-red" onclick="return confirm('Cancel this ticket?')">❌ Cancel</a>
                    <?php } ?>
                <!-- Bug 6 Fix — Show proper message for SOLD and CANCELLED -->
                <?php } elseif ($row['Status'] == 'SOLD') { ?>
                    <span style="color:blue; font-size:13px;">✅ Completed</span>
                <?php } else { ?>
                    <span style="color:red; font-size:13px;">❌ Cancelled</span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>
</div>
</body>
</html>