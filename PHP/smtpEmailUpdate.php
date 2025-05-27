<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

class SendEmail {
    
    public static function sendEmail($addAddressEmail, $body) {
        $mail = new PHPMailer(true);

        $returnEmail = array();

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   ='marcvincentvitto@gmail.com';
        $mail->Password   = 'elqjirtkbzmukmlr';
        $mail->SMTPSecure = 'tls';
        // $mail->SMTPDebug = 2;
        $mail->Port = 587;

        $mail->setFrom("marcvincentvitto@gmail.com");

        $mail->addAddress($addAddressEmail);

        $mail->isHTML(true);

        $mail->Subject = "Online Job Order Request Update";
        $mail->Body = $body;

        if ($mail->Send()) {
            $returnEmail["success"] = true;
            $returnEmail["message"] = "Message has been sent.";
        }
        else {
            $returnEmail["success"] = false;
            $returnEmail["message"] = $mail->ErrorInfo;
        }

        $mail->clearAddresses();
        $mail->clearAttachments();

        return $returnEmail;
    }
}

?>