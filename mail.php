<?php

$email_smtp = "smtp.gmail.com";
// $sender_email_id = "info@keshavmedia.com";
// $email_password = "ecqr kbsb bwgl icjl";
$sender_email_id = "demoraju123@gmail.com";
$email_password = "lrcl gilt iehx okme";
$email_port = "587";
$sender_name = "Keshav Media";
// $to ="";
// $subject ="Test Mail from PHPMailer";
// $message ="<h1>This is test mail from PHPMailer</h1><p>This mail is sent using SMTP server.</p>";
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/vendor/autoload.php';

function sendMail($to, $subject, $message, $attachmentFile = null, $cc = [], $bcc = [])
{
    global $email_smtp;
    global $sender_email_id;
    global $email_password;
    global $sender_name;
    global $email_port;

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Invalid Email Address'
        ];
    }

    $mail = new PHPMailer(true);
    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host       = $email_smtp;
        $mail->SMTPAuth   = true;
        $mail->Username   = $sender_email_id;
        $mail->Password   = $email_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $email_port;

        // Sender
        $mail->setFrom($sender_email_id, $sender_name);

        // Receiver
        $mail->addAddress($to);

        // CC
        if (!empty($cc)) {
            if (is_array($cc)) {
                foreach ($cc as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addCC($email);
                    }
                }
            } else {
                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($cc);
                }
            }
        }

        // BCC
        if (!empty($bcc)) {
            if (is_array($bcc)) {
                foreach ($bcc as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addBCC($email);
                    }
                }
            } else {
                if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addBCC($bcc);
                }
            }
        }

        // Attachment
        if ($attachmentFile != null && file_exists($attachmentFile)) {
            $mail->addAttachment($attachmentFile);
        }

        // Mail Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        return [
            'success' => true,
            'message' => 'Mail Sent Successfully'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $mail->ErrorInfo
        ];
    }
}