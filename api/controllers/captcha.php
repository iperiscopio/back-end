<?php

    require("models/captcha.php");
    require("models/clientsMessages.php");

    $model = new Captcha();
    $client = new ClientsMessages();

    function validate( $data ) {
        foreach( $data as $key => $value) {
            $key = trim(htmlspecialchars(strip_tags($value)));
        }

        if(
            !empty($data) &&
            isset($data["name"]) &&
            isset($data["title"]) &&
            isset($data["email"]) &&
            isset($data["telephone"]) &&
            isset($data["message"]) &&
            isset($data["captcha"]) &&
            mb_strlen($data["name"]) >= 3 &&
            mb_strlen($data["name"]) <= 255 &&
            mb_strlen($data["title"]) >= 2 &&
            mb_strlen($data["title"]) <= 3 &&
            filter_var($data["email"], FILTER_VALIDATE_EMAIL) &&
            mb_strlen($data["telephone"]) >= 3 &&
            mb_strlen($data["telephone"]) <= 25 &&
            mb_strlen($data["message"]) >= 3 &&
            mb_strlen($data["message"]) <= 65535 &&
            mb_strlen($data["captcha"]) == 6
        ) {
            return true;
        }

        return false;
    }


    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {

        $userIp = $_SERVER["REMOTE_ADDR"];

        http_response_code(202);

        echo json_encode($model->newCaptcha($userIp));
        


    } else if( $_SERVER["REQUEST_METHOD"] === "POST" ){

        $userIp = $_SERVER["REMOTE_ADDR"];

        $data = json_decode( file_get_contents("php://input"), true );
        
        
        if( validate( $data ) ) {

            $validCaptcha = $model->matched( $userIp, $data["captcha"] );

            if( $validCaptcha ) {

                http_response_code(202);
                $client->createMessage( $data );
                
            } else {

                http_response_code(400);
                die('{"message":"Ooops wrong captcha or information not filled correctly"}');

            }


        } else {

            http_response_code(400);
            die('{"message":"Bad Request. Information not filled correctly"}');
        }



    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }
?>