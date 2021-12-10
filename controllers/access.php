<?php

    use ReallySimpleJWT\Token;

    require("models/user.php");

    $model = new User();

    // validation and sanitization:
    function validation( $data ) {

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
            filter_var($data["email"], FILTER_VALIDATE_EMAIL)&&
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

        if( $id === "register") {

            $data = json_decode( file_get_contents("php://input"), true );

            if( validation( $data )) {
                
                $newUser = $model->register( $data );

                if(empty( $newUser )) {
                    http_response_code(400);
                    die('{"message":"Information not filled correctly"}');
                }
                
                http_response_code(202);
                die('{"message":"You are now a registered user"}');
            }
        }

        if( $id === "login" ) {
            $data = json_decode( file_get_contents("php://input"), true );

            if(  $data ) {
                
                $user = $model->login($data);

                if(empty( $user )) {
                    http_response_code(400);
                    die('{"message":"Incorrect Login Information"}');
                }

                // criar jwt
                $payload = [
                    "userId" => $user["user_id"],
                    "username" => $user["username"],
                    "firstName" => $user["first_name"],
                    "iat" => time()
                ];

                $secret = CONFIG["SECRET_KEY"];

                $token = Token::customPayload($payload, $secret);

                
                header("X-Auth-Token: " . $token);

                echo '{"X-Auth-Token":"' . $token . '"}';


                http_response_code(202);
                die('{"message":"You are now logged in"}');


            } else {
                http_response_code(400);
                echo '{"message":"Wrong Information"}';
            }
        }
        

    } else {
        http_response_code(405);
        echo '{"message":"Method Not Allowed"}';
    }