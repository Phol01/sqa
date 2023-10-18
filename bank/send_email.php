<?php
require 'PHPMailer/PHPMailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email'];
    $subject = "Payment Confirmation";
    $message = "Your payment has been successfully processed.";
    $headers = "joelapuk11@gmail.com"; // Replace with your email

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'joelapuk11@gmail.com'; // Replace with your email address
        $mail->Password = 'yamahavegamutor69'; // Replace with your email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('joelapuk11@gmail.com', 'Your Name'); // Replace with your name and email address
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
        echo "Email sent successfully!";
    } catch (Exception $e) {
        echo "Failed to send email. Error: " . $mail->ErrorInfo;
    }
}
?>
