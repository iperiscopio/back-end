<?php

    require("config.php");

    class Captcha extends Config {

        public function newCaptcha( $user_ip ) {
            $query = $this->db->prepare("
                DELETE FROM captcha
                WHERE ip = ?
            ");

            $deletedIp = $query->execute([ $user_ip ]);

            if($deletedIp) {
                header("Content-Type: image/png");

                $image = imagecreate(163, 60);

                imagecolorallocate($image, 190, 190, 190);

                $font = __DIR__ . "/../../atwriter.ttf";

                $black = imagecolorallocate($image, 0, 0, 0);

                $text = bin2hex(random_bytes(5));

                $newCaptcha = $text;

                imagettftext($image, 20, 0, 12, 38, $black, $font, $text);

            
                $query = $this->db->prepare("
                    INSERT INTO captcha
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
                    ip
                    captcha
                FROM captcha
                WHERE 
                    ip = ?
                    captcha = ?
            ");

            $query->execute([
                $user_ip,
                $user_captcha
            ]);

            $matched = $query->fetch( PDO::FETCH_ASSOC );

            if( $matched ) {
                return true;
            }
            return false;
        }
    }