<?php

    use ReallySimpleJWT\Token;

    require("models/user.php");

    $model = new User();

    // Validation:
    function validateLogin( $data ) {
        
        // sanitization:
        foreach($data as $key => $value) {
            $data[$key] = trim(htmlspecialchars(strip_tags($value)));
        }

        if( 
            !empty($data) &&
            !empty($data["email"]) &&
            !empty($data["password"]) &&
            filter_var($data["email"], FILTER_VALIDATE_EMAIL) &&
            mb_strlen($data["password"]) >= 8 &&
            mb_strlen($data["password"]) <= 1000
        ) {

            return true;
        } 

        return false;
    }

    if( $_SERVER["REQUEST_METHOD"] === "POST") {

        $data = json_decode( file_get_contents("php://input"), true );

        if( validateLogin( $data ) ) {
            
            $user = $model->login($data);

            if(empty( $user )) {
                http_response_code(422);
                die('{"message":"Invalid email or password"}');
            }
            

            // criar jwt
            $payload = [
                "userId" => $user["user_id"],
                "email" => $user["email"],
                "name" => $user["name"],
                "iat" => time(),
                "exp" => time() + (60 * 120)
            ];

            $secret = CONFIG["SECRET_KEY"];

            $token = Token::customPayload($payload, $secret);

            
            header("X-Auth-Token: " . $token);
            
            http_response_code(202);

            echo json_encode([ 
                "message" => "You are now logged in",
                "token" => $token
            ]);


        } else {
            http_response_code(400);
            echo '{"message":"Wrong information"}';
        }
        
        

    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

        http_response_code(202);

    } else {
        http_response_code(405);
        echo '{"message":"Method Not Allowed"}';
    }