<?php

    use ReallySimpleJWT\Token;

    require("models/clientsMessages.php");

    $model = new ClientsMessages();

    if( in_array($_SERVER["REQUEST_METHOD"], ["GET", "DELETE"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {

            http_response_code(401);
            die('{"message":"Wrong or missing Auth Token"}');
        } 

    }

    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {

        http_response_code(202);

        echo json_encode( $model->showMessages() );
        


    } elseif($_SERVER["REQUEST_METHOD"] === "DELETE") {

        $data = json_decode( file_get_contents("php://input", TRUE ));

        if( !empty( $id ) && is_numeric( $id ) ) {

            $removeMessage = $model->deleteMessage( $id );
            
            if( $removeMessage ) { 

                http_response_code(202);
                die('{"message": "Message deleted"}');
                
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
        die('{"message": "Method Not Allowed"}');

    }