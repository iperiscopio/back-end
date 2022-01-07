<?php

    require_once("config.php");

    class Captcha extends Config {

        public function newCaptcha( $user_ip ) {
            $query = $this->db->prepare("
                DELETE FROM captchas
                WHERE ip = ?
            ");

            $deletedIp = $query->execute([ $user_ip ]);

            if($deletedIp) {
                header("Content-Type: image/png");

                $image = imagecreate(117, 60);

                imagecolorallocate($image, 255, 160, 122);

                $font = __DIR__ . "/../../atwriter.ttf";

                $black = imagecolorallocate($image, 160, 160, 160);

                $text = bin2hex(random_bytes(3));

                $newCaptcha = $text;

                imagettftext($image, 20, 5, 17, 41, $black, $font, $text);

            
                $query = $this->db->prepare("
                    INSERT INTO captchas
                    (ip, captcha)
                    VALUES(?, ?)
                ");

                $query->execute([
                    $user_ip,
                    $newCaptcha
                ]);

                ob_start();
                imagepng($image);
                // Capture the output and clear the output buffer
                $imagedata = ob_get_clean();

                return [ 'captcha' => base64_encode($imagedata)];
            }
            
        }

        public function matched( $user_ip, $user_captcha ) {

            $query = $this->db->prepare("
                SELECT
                    *
                FROM captchas
                WHERE 
                    ip = ?
                    AND captcha = ?
            "); 

            $query->execute([
                $user_ip,
                $user_captcha,
            ]);
            
            $matched = $query->fetch( PDO::FETCH_ASSOC );

            if( $matched ) {
                return true;
            }
            return false;
        }
    }