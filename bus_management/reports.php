<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}
require 'config.php';

// Bug 5 Fix — Use absolute path for transactions.txt
$fileTransactions = [];
$filePath = __DIR__ . '/transactions.txt';
if (file_exists($filePath)) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(' ', trim($line));
        // Bug 7 Fix — Validate both parts exist and amount is numeric
        if (count($parts) == 2 && is_numeric($parts[1])) {
            $fileTransactions[] = [
                'date' => $parts[0],
                'amount' => floatval($parts[1])
            ];
        }
    }
}

// Calculate total income from file
$totalFileIncome = array_sum(array_column($fileTransactions, 'amount'));

// Bug 1 Fix — Changed BOOKED to SOLD for all queries

// Get daily income from database
$dailyResult = mysqli_query($conn, "
    SELECT 
        TransactionDate, 
        COUNT(*) as TotalTickets, 
        SUM(Amount) as DailyTotal 
    FROM Transactions
    GROUP BY TransactionDate 
    ORDER BY TransactionDate DESC
");

// Get monthly income from database
$monthlyResult = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(TransactionDate, '%Y-%m') as Month, 
        COUNT(*) as TotalTickets, 
        SUM(Amount) as MonthlyTotal 
    FROM Transactions
    GROUP BY DATE_FORMAT(TransactionDate, '%Y-%m')
    ORDER BY Month DESC
");

// Get total income from database
$totalResult = mysqli_query($conn, "
    SELECT 
        SUM(Amount) as GrandTotal, 
        COUNT(*) as TotalTickets 
    FROM Transactions
");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRow['GrandTotal'] = $totalRow['GrandTotal'] ?? 0;
$totalRow['TotalTickets'] = $totalRow['TotalTickets'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Income Reports</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #3F51B5; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; margin: 3px; }
        .btn-blue { background: #2196F3; }
        .btn-red { background: #f44336; }
        .summary-box { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px; }
        .summary-card { background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); min-width: 200px; }
        .summary-card h3 { margin: 0; font-size: 14px; color: #666; }
        .summary-card p { margin: 10px 0 0 0; font-size: 28px; font-weight: bold; }
        .income { color: #4CAF50; }
        .tickets { color: #2196F3; }
        .file-income { color: #FF9800; }
        .tabs { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .tab { padding: 10px 20px; border-radius: 5px; cursor: pointer; background: #ddd; border: none; font-size: 14px; }
        .tab.active { background: #3F51B5; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; }
        .empty-msg { color: gray; text-align: center; padding: 20px; }
    </style>
</head>
<body>
<div class="card">
    <div class="header-bar">
        <h2>📊 Bus Income Reports</h2>
        <div>
            <a href="admin_dashboard.php" class="btn btn-blue">🏠 Dashboard</a>
            <a href="bus_report.php" class="btn btn-blue">🚌 Bus Report</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | Role: ADMIN</p>
</div>

<!-- Summary Cards -->
<div class="summary-box">
    <div class="summary-card">
        <h3>💰 Total Income </h3>
        <p class="income">₱<?php echo number_format($totalRow['GrandTotal'], 2); ?></p>
    </div>
    <div class="summary-card">
        <h3>🎫 Total Tickets Sold</h3>
        <p class="tickets"><?php echo $totalRow['TotalTickets']; ?></p>
    </div>
</div>

<!-- Tabs -->
<div class="card">
    <!-- Bug 2 Fix — Pass 'this' to showTab -->
    <div class="tabs">
        <button class="tab active" onclick="showTab('daily', this)">📅 Daily Report</button>
        <button class="tab" onclick="showTab('monthly', this)">📆 Monthly Report</button>
        <button class="tab" onclick="showTab('file', this)">📄 File Transactions</button>
        <button class="tab" onclick="showTab('all', this)">📋 All Transactions</button>
    </div>

    <!-- Daily Report -->
    <div id="daily" class="tab-content active">
        <h3>📅 Daily Income Report</h3>
        <?php
        // Bug 3 Fix — Check if results exist before data_seek
        if (mysqli_num_rows($dailyResult) == 0) {
            echo "<p class='empty-msg'>No sold tickets recorded yet.</p>";
        } else {
            mysqli_data_seek($dailyResult, 0);
        ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Total Tickets Sold</th>
                <th>Daily Income</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($dailyResult)) { ?>
            <tr>
                <td><?php echo $row['TransactionDate']; ?></td>
                <td><?php echo $row['TotalTickets']; ?></td>
                <td>₱<?php echo number_format($row['DailyTotal'], 2); ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </div>

    <!-- Monthly Report -->
    <div id="monthly" class="tab-content">
        <h3>📆 Monthly Income Report</h3>
        <?php
        // Bug 3 Fix — Check if results exist before data_seek
        if (mysqli_num_rows($monthlyResult) == 0) {
            echo "<p class='empty-msg'>No sold tickets recorded yet.</p>";
        } else {
            mysqli_data_seek($monthlyResult, 0);
        ?>
        <table>
            <tr>
                <th>Month</th>
                <th>Total Tickets Sold</th>
                <th>Monthly Income</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($monthlyResult)) { ?>
            <tr>
                <td><?php echo $row['Month']; ?></td>
                <td><?php echo $row['TotalTickets']; ?></td>
                <td>₱<?php echo number_format($row['MonthlyTotal'], 2); ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </div>

    <!-- File Transactions -->
    <div id="file" class="tab-content">
        <h3>📄 File Transactions </h3>
        <?php if (empty($fileTransactions)) { ?>
            <!-- Bug 7 Fix — Show message if file empty or missing -->
            <p class='empty-msg'>No transactions found in transactions.txt</p>
        <?php } else { ?>
        <table>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
            <?php foreach ($fileTransactions as $i => $t) { ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($t['date']); ?></td>
                <td>₱<?php echo number_format($t['amount'], 2); ?></td>
            </tr>
            <?php } ?>
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="2">Total</td>
                <td>₱<?php echo number_format($totalFileIncome, 2); ?></td>
            </tr>
        </table>
        <?php } ?>
    </div>

    <!-- All Transactions -->
    <div id="all" class="tab-content">
        <h3>📋 All Transactions</h3>
        <?php
        // Bug 6 Fix — Only show SOLD tickets in All Transactions
        $allResult = mysqli_query($conn, "
            SELECT t.*, tk.Status as TicketStatus
            FROM Transactions t
            INNER JOIN Tickets tk ON t.TicketID = tk.TicketID
            WHERE tk.Status = 'SOLD'
            ORDER BY t.TransactionDate DESC
        ");
        if (mysqli_num_rows($allResult) == 0) {
            echo "<p class='empty-msg'>No completed transactions yet.</p>";
        } else {
        ?>
        <table>
            <tr>
                <th>Transaction ID</th>
                <th>Bus ID</th>
                <th>Ticket ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($allResult)) { ?>
            <tr>
                <td><?php echo $row['TransactionID']; ?></td>
                <td><?php echo $row['BusID']; ?></td>
                <td><?php echo $row['TicketID']; ?></td>
                <td><?php echo $row['TransactionDate']; ?></td>
                <td>₱<?php echo number_format($row['Amount'], 2); ?></td>
                <td><span style="background:#2196F3; color:white; padding:3px 10px; border-radius:10px; font-size:12px;">SOLD</span></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </div>
</div>

<!-- Bug 2 Fix — Pass el parameter to fix undefined event -->
<script>
function showTab(tab, el) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tab).classList.add('active');
    el.classList.add('active');
}
</script>
</body>
</html>