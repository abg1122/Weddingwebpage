<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

// Database connection details
$host = "db5017272189.hosting-data.io"; // Your MariaDB host
$port = 3306; // Default MySQL/MariaDB port
$username = "dbu1619888"; // Your database username
$password = "Natham1223!"; // Replace with your actual DB password
$database = "dbs13859053"; // Replace with your actual database name

// Connect to MariaDB
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clear RSVP responses
if (isset($_POST['clear_data'])) {
    $conn->query("DELETE FROM rsvp_responses");
    header("Location: dashboard.php");
    exit;
}

// Fetch RSVP responses
$sql = "SELECT id, name, email, guests, submission_date, phone, guest_phones FROM rsvp_responses";
$result = $conn->query($sql);

// Export to CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rsvp_list.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Guests', 'Submission Date', 'Phone', 'Guest Phones']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to match site styling -->
    <style>
        body { font-family: 'Open Sans', sans-serif; text-align: center; background-color: #f9f5f0; color: #4a5e4d; }
        table { width: 90%; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #e8d8d8; }
        button { background: #4a5e4d; color: white; padding: 10px; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #a68b8b; }
        .btn-container { margin: 20px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background: #4a5e4d; color: white; }
        .menu-button { font-size: 20px; cursor: pointer; background: none; border: none; color: white; }
        .login-link { text-decoration: none; color: white; font-weight: bold; }
        @media (max-width: 600px) {
            table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <button class="menu-button">☰</button>
        <a href="login.php" class="login-link">Login</a>
    </div>
    <h2>RSVP Dashboard</h2>
    <div class="btn-container">
        <a href="?export=1" class="button">Download CSV</a>
        <form method="post" style="display:inline; margin-left: 10px;">
            <button type="submit" name="clear_data" onclick="return confirm('Are you sure you want to clear all RSVP responses?');">Clear RSVP Data</button>
        </form>
    </div>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Guests</th>
            <th>Submission Date</th>
            <th>Phone</th>
            <th>Guest Phones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['guests']; ?></td>
            <td><?php echo $row['submission_date']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['guest_phones']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="index.html" class="button">Back to Home</a>
</body>
</html>

<?php
$conn->close();
?>