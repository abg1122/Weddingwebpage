<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
header('Content-Type: application/json');
ob_start(); // Start output buffering to prevent unexpected output

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

// Database connection (update with your IONOS MySQL details)
$host = 'db5017272189.hosting-data.io';
$dbname = 'dbs13859053'; // Replace with your actual database name
$username = 'dbu1619888'; // Replace with your actual username
$password = 'Natham1223!'; // Replace with your actual password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Unable to connect to the database. Please try again later."]);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $guests = filter_var($_POST['guests'] ?? '', FILTER_VALIDATE_INT);
    $phone = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_STRING);
    $guest_phones = filter_var(trim($_POST['guest_phones'] ?? ''), FILTER_SANITIZE_STRING);

    if (empty($name) || !$email || !$guests || $guests < 1) {
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "Please fill in all required fields correctly."]);
        exit;
    }

    try {
        // Insert into database
        $sql = "INSERT INTO rsvp_responses (name, email, guests, phone, guest_phones, submission_date) VALUES (:name, :email, :guests, :phone, :guest_phones, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'guests' => $guests,
            'phone' => $phone,
            'guest_phones' => $guest_phones
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 2; // Enable verbose debug output (change to 0 in production)
            $mail->Host = 'smtp.ionos.co.uk'; // Use IONOS UK SMTP Server
            $mail->SMTPAuth = true;
            $mail->Username = 'seharandabsam@seharandabsam.com'; // Your IONOS email
            $mail->Password = 'Natham1223!'; // Your IONOS email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->AuthType = 'LOGIN';

            // Send email to host
            $mail->setFrom('seharandabsam@seharandabsam.com', 'Absam & Sehar Wedding');
            $mail->addAddress('seharandabsam@seharandabsam.com');
            $mail->Subject = "New RSVP Received";
            $mail->Body = "Name: $name\nEmail: $email\nGuests: $guests\nPhone: $phone\nGuest Phones: $guest_phones\nDate: " . date('Y-m-d H:i:s');
            if (!$mail->send()) {
                error_log("Host Email Send Error: " . $mail->ErrorInfo);
            }

            // Send confirmation email to guest
            $mail->clearAddresses(); // Clear previous recipient
            $mail->addAddress($email);
            $mail->Subject = "RSVP Confirmation - Absam & Sehar's Wedding";
            $mail->Body = "Dear $name,\n\nThank you for RSVPing for our wedding!\n\nEvent Details:\n- Wedding of Absam & Sehar\n- Date: August 21-23, 2026 (Mark your calendars!)\n- Location: Cycladic Gem Luxury Villa, Ios, Greece\n\nWe are absolutely thrilled that you will be joining us for our wedding celebration! Your presence means the world to us, and we cannot wait to share these special moments together. Get ready for a beautiful experience filled with joy, love, and unforgettable memories!\n\nWith love,\nAbsam & Sehar";
            if (!$mail->send()) {
                error_log("Guest Email Send Error: " . $mail->ErrorInfo);
            }

        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }

        ob_end_clean();
        echo json_encode(["success" => true, "message" => "Thank you for your RSVP! A confirmation has been sent to your email."]);
        exit;
    } catch (PDOException $e) {
        error_log("Database Insert Error: " . $e->getMessage());
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "An unexpected error occurred. Please try again later."]);
        exit;
    }
}
?>