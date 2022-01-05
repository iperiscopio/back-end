<?php

    require("models/admin.php");
    require("models/sendEmail.php");

    $model = new Email();
    $findAdmin = new Admin();

    // Admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["POST"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 
    
    }

    // Email validation from admin to client 
    function validate( $data ) {

        if( !empty($data) ) {

            foreach( $data as $key=>$value ) {
                if($data[$key] = $data["message"]) {

                    $data[$key] = trim($value);

                } else {

                    $data[$key] = trim(htmlspecialchars(strip_tags($value)));

                }
            }

            if( 
                !empty($data["message_id"]) &&
                !empty($data["title"]) &&
                !empty($data["name"]) &&
                !empty($data["email"]) &&
                !empty($data["subject"]) &&
                !empty($data["message"]) &&
                is_numeric($data["message_id"]) &&
                mb_strlen($data["title"]) >= 2 &&
                mb_strlen($data["title"]) <= 3 &&
                mb_strlen($data["name"]) >= 3 &&
                mb_strlen($data["name"]) <= 255 &&
                filter_var($data["email"], FILTER_VALIDATE_EMAIL) &&
                mb_strlen($data["subject"]) >= 3 &&
                mb_strlen($data["subject"]) <= 250 &&
                mb_strlen($data["message"]) >= 10 &&
                mb_strlen($data["message"]) <= 65535
            ) {
                return true;
            }
   
        }

        return false;
    }


    if( $_SERVER["REQUEST_METHOD"] === "POST") {

        $adminId = $model->routeRequireValidation();

        $admin = $findAdmin->adminInfo( $adminId );
        
        $data = json_decode(file_get_contents("php://input"), TRUE);
        

        if(  validate( $data )  && !empty( $admin ) ) {

            $sendEmail = $model->sendEmail( $admin, $data );

            if( $sendEmail ) {

                http_response_code(202);
                die('{"message":"Email sent with success"}');

            } else {

                http_response_code(400);
                die('{"message":"Ooops something went wrong"}');
            }

        } else {

            http_response_code(400);
            die('{"message":"Wrong Information"}');
        }


        

    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }