<?php

    use ReallySimpleJWT\Token;

    require("models/user.php");

    $model = new User();

    // validation:
    function validation( $data ) {
        
        // sanitization:
        foreach($data as $key => $value) {
            $data[$key] = trim(htmlspecialchars(strip_tags($value)));
        }

        if( 
            !empty($data) &&
            !empty($data["first_name"]) &&
            !empty($data["last_name"]) &&
            !empty($data["email"]) &&
            !empty($data["password"]) &&
            !empty($data["username"]) &&
            mb_strlen($data["first_name"]) >= 3 &&
            mb_strlen($data["first_name"]) <= 120 &&
            mb_strlen($data["last_name"]) >= 3 &&
            mb_strlen($data["last_name"]) <= 120 &&
            filter_var($data["email"], FILTER_VALIDATE_EMAIL) &&
            mb_strlen($data["password"]) >= 8 &&
            mb_strlen($data["password"]) <= 1000 &&
            mb_strlen($data["username"]) >= 3 &&
            mb_strlen($data["username"]) <= 60 
        ) {

            return true;
        } 

        return false;
    }


    if( $_SERVER["REQUEST_METHOD"] === "POST") {

    
        $data = json_decode( file_get_contents("php://input"), true );
        // print_r($data);

        if( validation( $data ) ) {
            
            $newUser = $model->register( $data );

            if(empty( $newUser )) {
                http_response_code(400);
                die('{"message":"Information not filled correctly"}');
            }
            
            http_response_code(202);
            die('{"message":"You are now a registered user"}');
            
        } else {

            http_response_code(400);
            echo '{"message":"Wrong Information"}';
            
        }
        
        

    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

        http_response_code(202);

    } else {
        http_response_code(405);
        echo '{"message":"Method Not Allowed"}';
    }