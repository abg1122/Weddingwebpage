<?php
$to = "seharandabsam@gmail.com"; // Change this to your personal email
$subject = "Test Email from IONOS";
$message = "If you receive this, outgoing emails are working!";
$headers = "From: no-reply@seharandabsam.com\r\n";
$headers .= "Reply-To: seharandabsam@seharandabsam.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email.";
}
?>
