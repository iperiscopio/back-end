<?php

    use ReallySimpleJWT\Token;
    
    require("models/image.php");

    $model = new Image();

    // admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["GET", "DELETE"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            
            http_response_code(401);
            die('{"message":"Wrong or missing Auth Token"}');
        } 

    }

    if($_SERVER["REQUEST_METHOD"] === "GET") {

        http_response_code(202);
        echo json_encode( $model->getAll() );

    } else if($_SERVER["REQUEST_METHOD"] === "DELETE") {

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        if( !empty( $id ) && is_numeric( $id ) ) {

            $removeAdmin = $model->deleteImageFromProject( $id );
            
            if( $removeAdmin ) { 

                http_response_code(202);
                die('{"message": "Deleted image with success"}');
                
            } else {

                http_response_code(404);
                die('{"message": "404 Not Found"}'); 

            }
            
        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');

        }

    }  else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');
    }
?>

