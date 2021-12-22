<?php

    use ReallySimpleJWT\Token;

    require("models/config.php");
    require("models/user.php");

    $config = new Config();

    $model = new User();

    // User authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["POST", "PUT", "DELETE"]) ) {
        
        $userId = $config->routeRequireValidation();
        var_dump($userId);

        if( empty( $userId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 

        if( 
            !empty($id)
        ) {
            http_response_code(403);
            die('{"message":"You do not have permission to perform this action"}');
        }
    
    }

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


    if( $_SERVER["REQUEST_METHOD"] === "GET") {

    
        http_response_code(202);
        echo json_encode($model->userInfo());


    } elseif( $_SERVER["REQUEST_METHOD"] === "POST") {

        $data = json_decode( file_get_contents("php://input"), true );

        if( validation( $data ) ) {

            $validEmail = $model->emailValidation( $data );

            if( $validEmail ) {
                $newUser = $model->register( $data );

                if(empty( $newUser )) {
                    http_response_code(400);
                    die('{"message":"Information not filled correctly"}');
                }
                
                http_response_code(202);
                die('{"message":"You are now a registered user"}');


            } else {

                http_response_code(409);
                die('{"message":"This email is already registered"}');

            }
    
            
        } else {

            http_response_code(400);
            echo '{"message":"Wrong Information"}';
            
        }
        
        

    } elseif($_SERVER["REQUEST_METHOD"] === "PUT") {

        $data = json_decode( file_get_contents("php://input"), true );

        if( !empty($id) &&
            validation( $data ) 
        ) {

            $changedUser = $model->updateUser( $data );

            if(empty( $changedUser )) {
                http_response_code(404);
                die('{"message":"Not Found"}');
            }
            
            http_response_code(202);
            die('{"message":"User information updated with success"}');

    
            
        } else {

            http_response_code(400);
            echo '{"message":"Wrong Information"}';
            
        }




    } elseif($_SERVER["REQUEST_METHOD"] === "DELETE") {

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        if( !empty( $id ) && is_numeric( $id ) ) {

            $removeUser = $model->deleteUser($id);
            
            if( $removeUser ) { 

                http_response_code(202);
                die('{"message": "Deleted user with success"}');
                
            } else {

                http_response_code(404);
                die('{"message": "404 Not Found"}'); 

            }
            
        } else {

             http_response_code(400);
            die('{"message": "400 Bad Request"}');

        }



    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

        http_response_code(202);

    } else {
        http_response_code(405);
        echo '{"message":"Method Not Allowed"}';
    }