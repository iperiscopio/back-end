<?php

    use ReallySimpleJWT\Token;

    require("models/admin.php");
    require("models/stats.php");

    $model = new Admin();
    $stats = new Stats();

    // Admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["GET", "POST", "PUT", "DELETE"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 
    
    }

    // validation:
    function postValidation( $data ) {
        
        // sanitization:
        foreach($data as $key => $value) {
            $data[$key] = trim(htmlspecialchars(strip_tags($value)));
        }

        if( 
            !empty($data) &&
            !empty($data["name"]) &&
            !empty($data["email"]) &&
            !empty($data["password"]) &&
            !empty($data["username"]) &&
            mb_strlen($data["name"]) >= 3 &&
            mb_strlen($data["name"]) <= 180 &&
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

    function putValidation( $data ) {
        
        // sanitization:
        foreach($data as $key => $value) {
            $data[$key] = trim(htmlspecialchars(strip_tags($value)));
        }

        if( 
            !empty($data) &&
            !empty($data["password"]) &&
            mb_strlen($data["name"]) >= 3 &&
            mb_strlen($data["name"]) <= 180 &&
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

        if( $adminId = $model->routeRequireValidation() ) {

            http_response_code(202);

            echo json_encode($model->adminInfo( $adminId ));
        }

        

    } else if( $_SERVER["REQUEST_METHOD"] === "POST") {

        $data = json_decode( file_get_contents("php://input"), true );

        if( postValidation( $data ) ) {

            $validEmail = $model->emailValidation( $data );

            if( $validEmail ) {
                $newAdmin = $model->register( $data );

                if(empty( $newAdmin )) {
                    http_response_code(400);
                    die('{"message":"Information not filled correctly"}');
                }
                
                http_response_code(202);
                die('{"message":"You are now a registered Admin"}');


            } else {

                http_response_code(409);
                die('{"message":"This email is already registered"}');

            }
    
            
        } else {

            http_response_code(400);
            die('{"message": "Wrong Information"}');
            
        }
        
        

    } else if($_SERVER["REQUEST_METHOD"] === "PUT") {

        $data = json_decode( file_get_contents("php://input"), true );

        if( !empty($id) && putValidation( $data ) ) {

            $changedAdmin = $model->updateAdmin( $id, $data );

            if(empty( $changedAdmin )) {
                http_response_code(404);
                die('{"message":"Not Found"}');
            }
            
            http_response_code(202);
            die('{"message":"Admin information updated with success"}');

    
            
        } else {

            http_response_code(400);
            die('{"message": "Wrong Information"}');;
            
        }




    } else if($_SERVER["REQUEST_METHOD"] === "DELETE") {

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        if( !empty( $id ) && is_numeric( $id ) ) {

            if( $stats->countA() <= 1 ) {

                http_response_code(403);
                die('{"message": "403 Forbidden"}');

            }

            $removeAdmin = $model->deleteAdmin($id);
            
            if( $removeAdmin ) { 

                http_response_code(202);
                die('{"message": "Deleted Admin with success"}');
                
            } else {

                http_response_code(404);
                die('{"message": "404 Not Found"}'); 

            }
            
        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');

        }



    } else {
        http_response_code(405);
        die('{"message":"Method Not Allowed"}');
    }