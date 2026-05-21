<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); exit();
}
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passenger = trim($_POST['passenger']);
    $busid = trim($_POST['busid']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Get bus fare as base price
    $busResult = mysqli_query($conn, "SELECT Fair, SeatCapacity FROM Bus WHERE Bus_no = '$busid'");
    $bus = mysqli_fetch_assoc($busResult);
    $price = $bus['Fair'];

    // Check available seats
    $bookedSeats = mysqli_query($conn, "SELECT SeatNumber FROM Tickets WHERE BusID = '$busid' AND Status = 'BOOKED'");
    $takenSeats = [];
    while ($s = mysqli_fetch_assoc($bookedSeats)) {
        $takenSeats[] = $s['SeatNumber'];
    }

    $totalSeats = $bus['SeatCapacity'];
    $availableSeat = null;
    for ($i = 1; $i <= $totalSeats; $i++) {
        if (!in_array($i, $takenSeats)) {
            $availableSeat = $i;
            break;
        }
    }

    if ($availableSeat === null) {
        $message = "<p style='color:red;'>❌ No available seats on this bus!</p>";
    } else {
        // Generate unique Ticket ID
        $ticketID = 'TKT-' . strtoupper(substr(uniqid(), -5));

        $userID = $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "INSERT INTO Tickets (TicketID, PassengerName, BusID, Destination, DepartureDate, DepartureTime, SeatNumber, Price, UserID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssssidi", $ticketID, $passenger, $busid, $destination, $date, $time, $availableSeat, $price, $userID);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "<p style='color:green;'>✅ Ticket booked! Ticket ID: <strong>$ticketID</strong> | Seat: <strong>$availableSeat</strong> | Price: <strong>₱$price</strong></p>";
        } else {
            $message = "<p style='color:red;'>❌ Booking failed: " . mysqli_error($conn) . "</p>";
        }
    }
}

$buses = mysqli_query($conn, "SELECT Bus_no, Source, Destination, Fair FROM Bus WHERE Status != 'OUT OF SERVICE'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 30px; border-radius: 10px; max-width: 550px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white; border: none; cursor: pointer; font-size: 14px; }
        .btn-green { background: #4CAF50; }
        .btn-blue { background: #2196F3; }
        label { font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>➕ Book a Ticket</h2>
    <?php echo $message; ?>
    <form method="POST">
        <label>Passenger Name:</label>
        <input type="text" name="passenger" placeholder="Enter full name" required>

        <label>Select Bus:</label>
        <select name="busid" required>
            <option value="">-- Select Bus --</option>
            <?php while ($bus = mysqli_fetch_assoc($buses)) { ?>
            <option value="<?php echo $bus['Bus_no']; ?>">
                <?php echo $bus['Bus_no'] . " | " . $bus['Source'] . " → " . $bus['Destination'] . " | ₱" . $bus['Fair']; ?>
            </option>
            <?php } ?>
        </select>

        <label>Destination:</label>
        <input type="text" name="destination" placeholder="Enter destination" required>

        <label>Departure Date:</label>
        <input type="date" name="date" required>

        <label>Departure Time:</label>
        <input type="time" name="time" required>

        <button type="submit" class="btn btn-green">🎫 Book Ticket</button>
        <a href="tickets.php" class="btn btn-blue" style="margin-left:10px;">Back</a>
    </form>
</div>
</body>
</html>