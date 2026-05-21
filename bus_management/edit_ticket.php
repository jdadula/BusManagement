<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); exit();
}
require 'config.php';

$id = $_GET['id'];
$message = "";

if ($_SESSION['role'] == 'admin') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM Tickets WHERE TicketID = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);
} else {
    $userID = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM Tickets WHERE TicketID = ? AND UserID = ?");
    mysqli_stmt_bind_param($stmt, "si", $id, $userID);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$ticket = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passenger = trim($_POST['passenger']);
    $busid = trim($_POST['busid']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Get new bus fare as updated price
    $busStmt = mysqli_prepare($conn, "SELECT Fair FROM Bus WHERE Bus_no = ?");
    mysqli_stmt_bind_param($busStmt, "s", $busid);
    mysqli_stmt_execute($busStmt);
    $busResult = mysqli_stmt_get_result($busStmt);
    $bus = mysqli_fetch_assoc($busResult);
    $newPrice = $bus['Fair'];

    // Reassign seat on new bus
    $seatStmt = mysqli_prepare($conn, "SELECT SeatNumber FROM Tickets WHERE BusID = ? AND Status = 'BOOKED' AND TicketID != ?");
    mysqli_stmt_bind_param($seatStmt, "ss", $busid, $id);
    mysqli_stmt_execute($seatStmt);
    $seatResult = mysqli_stmt_get_result($seatStmt);
    $takenSeats = [];
    while ($s = mysqli_fetch_assoc($seatResult)) {
        $takenSeats[] = $s['SeatNumber'];
    }

    // Get total seat capacity of new bus
    $capStmt = mysqli_prepare($conn, "SELECT SeatCapacity FROM Bus WHERE Bus_no = ?");
    mysqli_stmt_bind_param($capStmt, "s", $busid);
    mysqli_stmt_execute($capStmt);
    $capResult = mysqli_stmt_get_result($capStmt);
    $capRow = mysqli_fetch_assoc($capResult);
    $totalSeats = $capRow['SeatCapacity'];

    // Find next available seat
    $newSeat = null;
    for ($i = 1; $i <= $totalSeats; $i++) {
        if (!in_array($i, $takenSeats)) {
            $newSeat = $i;
            break;
        }
    }

    if ($newSeat === null) {
        $message = "<p style='color:red;'>❌ No available seats on the selected bus!</p>";
    } else {
        $update = mysqli_prepare($conn, "UPDATE Tickets SET PassengerName=?, BusID=?, Destination=?, DepartureDate=?, DepartureTime=?, Price=?, SeatNumber=? WHERE TicketID=?");
        mysqli_stmt_bind_param($update, "sssssdis", $passenger, $busid, $destination, $date, $time, $newPrice, $newSeat, $id);

        if (mysqli_stmt_execute($update)) {
            $message = "<p style='color:green;'>✅ Ticket updated! New Seat: <strong>$newSeat</strong> | New Price: <strong>₱$newPrice</strong></p>";
            header("refresh:2;url=tickets.php");
        } else {
            $message = "<p style='color:red;'>❌ Update failed!</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Ticket</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border-radius: 5px; color: white; border: none; cursor: pointer; text-decoration: none; }
        .btn-orange { background: #FF9800; }
        .btn-blue { background: #2196F3; }
        label { font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>✏️ Edit Ticket</h2>
    <?php echo $message; ?>
    <form method="POST">
        <label>Passenger Name:</label>
        <input type="text" name="passenger" value="<?php echo $ticket['PassengerName']; ?>" required>

        <label>Select Bus:</label>
        <select name="busid">
            <?php
            $buses = mysqli_query($conn, "SELECT Bus_no, Source, Destination, Fair FROM Bus WHERE Status != 'OUT OF SERVICE'");
            while ($bus = mysqli_fetch_assoc($buses)) { ?>
            <option value="<?php echo $bus['Bus_no']; ?>"
                <?php if ($bus['Bus_no'] == $ticket['BusID']) echo 'selected'; ?>>
                <?php echo $bus['Bus_no'] . " | " . $bus['Source'] . " → " . $bus['Destination'] . " | ₱" . $bus['Fair']; ?>
            </option>
            <?php } ?>
        </select>

        <label>Destination:</label>
        <input type="text" name="destination" value="<?php echo $ticket['Destination']; ?>" required>

        <label>Departure Date:</label>
        <input type="date" name="date" value="<?php echo $ticket['DepartureDate']; ?>" required>

        <label>Departure Time:</label>
        <input type="time" name="time" value="<?php echo $ticket['DepartureTime']; ?>" required>

        <button type="submit" class="btn btn-orange">Update Ticket</button>
        <a href="tickets.php" class="btn btn-blue" style="margin-left:10px;">Cancel</a>
    </form>
</div>
</body>
</html>