<?php

    require("models/captcha.php");

    $model = new Captcha();

    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {

        $userIp = $_SERVER["REMOTE_ADDR"];
    
        echo json_encode($model->newCaptcha($userIp));

    } elseif( $_SERVER["REQUEST_METHOD"] === "POST" ){

        $userIp = $_SERVER["REMOTE_ADDR"];

        $userCaptcha = json_decode( file_get_contents("php://input"), true );

        if( trim($userCaptcha) ) {
            $validCaptcha = $model->matched( $userIp, $userCaptcha);
            
            if( $validCaptcha ) {
                echo "yes";
            } else {
                echo "nooo";
            }
        }

    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }
?>