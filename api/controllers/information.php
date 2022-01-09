<?php

    use ReallySimpleJWT\Token;
    
    require("models/information.php");

    $model = new Info();

    // Admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["POST", "PUT", "DELETE"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 
    
    }

    // User data validation
    function validate( $data ) {

        if( !empty($data) ) {

            foreach( $data as $key=>$value ) {
                $data[$key] = trim(htmlspecialchars(strip_tags($value)));
            }

            if( 
                !empty($data["title"]) &&
                !empty($data["info"]) &&
                mb_strlen($data["title"]) >= 3 &&
                mb_strlen($data["title"]) <= 120 &&
                mb_strlen($data["info"]) >= 3 &&
                mb_strlen($data["info"]) <= 65535
            ) {
                return true;
            }
   
        }

        return false;
    }

    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {

        if( !empty( $id ) && !is_numeric( $id ) ) {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
            

        } else if( !empty($id) && is_numeric( $id ) ) {

            $info = $model->getInfo( $id );
        
            if( !$info ){
                
                http_response_code(404);
                die('{"message": "404 Not Found"}');
                
            }

            http_response_code(202);
            echo json_encode( $info );
            

        } else {

            http_response_code(202);
            echo json_encode($model->getAllInfo());

        }

        

    } elseif ( $_SERVER["REQUEST_METHOD"] === "POST" ) {

        $data = json_decode(file_get_contents("php://input"), TRUE);

        if( validate( $data ) ) {

            $newInfo = $model->postInfo( $data );

            http_response_code(202);
            die('{"message": "Information added with success"}');

        } else {
                
            http_response_code(400);
            die('{"message": "400 Bad Request"}');

        }

        

    } elseif ( $_SERVER["REQUEST_METHOD"] === "PUT" ) {

        $data = json_decode(file_get_contents("php://input"), TRUE);

        if( !empty( $id ) && validate( $data ) ) {

            $updatedInfo = $model->updateInfo( $id, $data );

            if( $updatedInfo ) {

                http_response_code(202);

                die('{"message": "Information updated with success"}');

            } else {

                http_response_code(404);

                die('{"message": "404 Not Found"}');
            }

            

        } else {

            http_response_code(400);

            die('{"message": "400 Bad Request"}');

        }


    } elseif ( $_SERVER["REQUEST_METHOD"] === "DELETE" ) {

        $data = json_decode(file_get_contents("php://input"), TRUE);

        if( !empty( $id ) && is_numeric( $id ) ) {

            $deletedInfo = $model->deleteInfo( $id );

            if( $deletedInfo ) {

                http_response_code(202);

                die('{"message": "Information deleted with success"}');

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
        die('{"message": "405 Method Not Allowed"}');
    }