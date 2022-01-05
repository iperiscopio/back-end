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


            $mail->SMTPDebug = 0; // normalmente a 0, mas 4 mostra todas as mensagens, incluindo de erros caso haja

            $mail->SMTPAuth = true;
            
            $mail->SMTPSecure = "tls";
            
            $mail->CharSet = 'UTF-8';
            
            $mail->Host = CONFIG["MAIL_HOST"];
            $mail->Port = CONFIG["MAIL_PORT"];
            $mail->Username = CONFIG["MAIL_USERNAME"];
            $mail->Password = CONFIG["MAIL_PASS"]; 

            // $mail->IsHttp(true);
            
            $mail->addAddress( $data["email"], $data["name"] );

            $mail->setFrom( $admin[0]["email"], 'Ilhéu Atelier' );

            $mail->AddReplyTo( $admin[0]["email"], 'Ilhéu Atelier' );

            $mail->Subject = $data["subject"];

            $mail->msgHTML( $data["message"] );

            if(!$mail->send()) {

                echo 'Mailer Error: ' . $email->ErrorInfo;
                return false;

            } else {

                return true;
                
            }
        }
    }

    