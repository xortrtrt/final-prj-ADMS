<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  

function sendClaimNotificationEmail($recipientEmail, $recipientName, $subject, $messageBody) {
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        $mail->isSMTP();  
        $mail->Host       = 'smtp.gmail.com';   
        $mail->SMTPAuth   = true;  
        $mail->Username   = 'patric.mapa@gmail.com';  
        $mail->Password   = 'zybr tzrw dkeq rkzj';     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
        $mail->Port       = 587; 

        //Recipients
        $mail->setFrom('patric.mapa@gmail.com', 'Lost & Found System');
        $mail->addAddress($recipientEmail, $recipientName);  

        // Content  
        $mail->isHTML(true);  
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        
        return false;
    }
}
?>
