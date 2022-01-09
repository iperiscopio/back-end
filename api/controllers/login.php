<?php

    use ReallySimpleJWT\Token;

    require("models/admin.php");

    $model = new Admin();

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
            
            $admin = $model->login($data);

            if(empty( $admin )) {
                http_response_code(422);
                die('{"message":"Invalid email or password"}');
            }
            

            // criar jwt
            $payload = [
                "adminId" => $admin["admin_id"],
                "email" => $admin["email"],
                "name" => $admin["name"],
                "iat" => time(),
                "exp" => time() + (60 * 120) // 2 hours
            ];

            $secret = CONFIG["SECRET_KEY"];

            $token = Token::customPayload($payload, $secret);

            
            header("X-Auth-Token: " . $token);
            
            http_response_code(202);

            echo json_encode([ 
                "message" => "You are now logged in",
                "token" => $token,
                "exp" => $payload["exp"]
            ]);


        } else {
            
            http_response_code(400);
            die('{"message":"Wrong information"}');
        }
        
        

    } else {

        http_response_code(405);
        die('{"message":"Method Not Allowed"}');
    }