<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); exit();
}
require 'config.php';

$busid = $_GET['bus'] ?? '';
$buses = mysqli_query($conn, "SELECT Bus_no, Source, Destination, SeatCapacity FROM Bus WHERE Status != 'OUT OF SERVICE'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Seats</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .seat-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .seat { width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        .seat-available { background: #4CAF50; color: white; }
        .seat-booked { background: #f44336; color: white; }
        select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-right: 10px; }
        .btn { padding: 10px 20px; border-radius: 5px; color: white; border: none; cursor: pointer; text-decoration: none; }
        .btn-blue { background: #2196F3; }
        .btn-orange { background: #FF9800; }
        .legend { display: flex; gap: 20px; margin-top: 15px; align-items: center; }
        .legend-box { width: 20px; height: 20px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="card">
    <h2>💺 Available Seats</h2>
    <form method="GET">
        <select name="bus">
            <option value="">-- Select Bus --</option>
            <?php while ($bus = mysqli_fetch_assoc($buses)) { ?>
            <option value="<?php echo $bus['Bus_no']; ?>" <?php if ($busid == $bus['Bus_no']) echo 'selected'; ?>>
                <?php echo $bus['Bus_no'] . " | " . $bus['Source'] . " → " . $bus['Destination']; ?>
            </option>
            <?php } ?>
        </select>
        <button type="submit" class="btn btn-blue">Check Seats</button>
        <a href="tickets.php" class="btn btn-orange">🎫 Back to Tickets</a>
    </form>
</div>

<?php if ($busid != '') { 
    $busInfo = mysqli_query($conn, "SELECT * FROM Bus WHERE Bus_no = '$busid'");
    $bus = mysqli_fetch_assoc($busInfo);
    $totalSeats = $bus['SeatCapacity'];

    $bookedResult = mysqli_query($conn, "SELECT SeatNumber FROM Tickets WHERE BusID = '$busid' AND Status = 'BOOKED'");
    $bookedSeats = [];
    while ($s = mysqli_fetch_assoc($bookedResult)) {
        $bookedSeats[] = $s['SeatNumber'];
    }
    $available = $totalSeats - count($bookedSeats);
?>
<div class="card">
    <h3>Bus: <?php echo $busid; ?> | <?php echo $bus['Source']; ?> → <?php echo $bus['Destination']; ?></h3>
    <p>Total Seats: <strong><?php echo $totalSeats; ?></strong> | 
       Available: <strong style="color:green"><?php echo $available; ?></strong> | 
       Booked: <strong style="color:red"><?php echo count($bookedSeats); ?></strong></p>

    <div class="legend">
        <div class="legend-box" style="background:#4CAF50;"></div> Available
        <div class="legend-box" style="background:#f44336;"></div> Booked
    </div>

    <div class="seat-grid">
        <?php for ($i = 1; $i <= $totalSeats; $i++) { 
            $class = in_array($i, $bookedSeats) ? 'seat-booked' : 'seat-available';
            $label = in_array($i, $bookedSeats) ? '❌' : '✅';
        ?>
        <div class="seat <?php echo $class; ?>">
            <?php echo $label; ?><br><?php echo $i; ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
</body>
</html>