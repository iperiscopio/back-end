<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require("vendor/phpmailer/phpmailer/src/Exception.php");
    require("vendor/phpmailer/phpmailer/src/PHPMailer.php");
    require("vendor/phpmailer/phpmailer/src/SMTP.php");
    require_once("config.php");

    class Email extends Config {

        public function sendEMail( $admin, $data ) {

            $mail = new PHPMailer();

            $mail->isSMTP();

            $mail->Mailer = "smtp";


            $mail->SMTPDebug = 0;

            $mail->SMTPAuth = true;
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // <-- using port 587
            
            $mail->CharSet = 'UTF-8';
            
            $mail->Host = CONFIG["MAIL_HOST"];
            $mail->Port = CONFIG["MAIL_PORT"];
            $mail->Username = CONFIG["MAIL_USERNAME"];
            $mail->Password = CONFIG["MAIL_PASS"]; 

            
            $mail->setFrom( $admin[0]["email"], 'Ilhéu Atelier' );

            $mail->addAddress( $data["email"], $data["name"] );

            $mail->addReplyTo( $admin[0]["email"], 'Ilhéu Atelier' );

            $mail->Subject = $data["subject"];

            $mail->msgHTML( $data["message"] );

            for( $i= 0; $i < count($data["attachments"]); $i++ ) { 

                $mail->addAttachment( $data["attachments"][$i] );
                
            }
            

            if(!$mail->send()) {

                echo 'Mailer Error: ' . $email->ErrorInfo;
                return false;

            } else {

                return true;
                
            }
        }
    }

    