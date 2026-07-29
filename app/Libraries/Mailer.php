<?php

namespace App\Libraries;


require_once ROOTPATH . 'app/thirdparty/PHPMailer/src/Exception.php';
require_once ROOTPATH . 'app/thirdparty/PHPMailer/src/PHPMailer.php';
require_once ROOTPATH . 'app/thirdparty/PHPMailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer
{
    public function send($to, $subject, $message)
    {
        $mail = new PHPMailer(true);

        try {


            $mail->isSMTP();


            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPAuth = true;


            $mail->Username = 'rmaranan02.dolemimaropa@gmail.com';

            $mail->Password = 'ilsz kfdr ijix mizz';


            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = 587;



            // Sender
            $mail->setFrom(
                'rmaranan02.dolemimaropa@gmail.com',
                'DOLE MIMAROPA E-Portal'
            );


            // Prevent Reply
            $mail->addReplyTo(
                'noreply.dolemimaropa@gmail.com',
                'DOLE MIMAROPA E-Portal (No Reply)'
            );



            // Receiver
            $mail->addAddress($to);



            $mail->isHTML(true);


            $mail->Subject = $subject;


            $mail->Body = $message;



            $mail->send();


            return true;


        } catch (Exception $e) {


            log_message(
                'error',
                $mail->ErrorInfo
            );


            return false;

        }
    }
}