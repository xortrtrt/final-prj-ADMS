<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

function sendClaimNotificationEmail($recipientEmail, $recipientName, $subject, $messageBody) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
        $mail->isSMTP();                                         // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                // Enable SMTP authentication
        $mail->Username   = 'patric.mapa@gmail.com';             // SMTP username
        $mail->Password   = 'zybr tzrw dkeq rkzj';              // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption
        $mail->Port       = 587;                                 // TCP port to connect to

        // Recipients
        $mail->setFrom('patric.mapa@gmail.com', 'Lost & Found System');
        $mail->addAddress($recipientEmail, $recipientName);      // Add a recipient

        // Content
        $mail->isHTML(true);                                     // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;
        $mail->AltBody = strip_tags($messageBody);               // Plain text version for non-HTML mail clients

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
