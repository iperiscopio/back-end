<?php

    require("models/image.php");

    $model = new Image();

    if($_SERVER["REQUEST_METHOD"] === "GET") {

        if( !empty( $id ) && !is_numeric( $id ) ) {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
            

        } else if( !empty($id) && is_numeric( $id ) ) {

            $image = $model->getImage( $id );
        
            if( !$image ){
                
                http_response_code(404);
                die('{"message": "404 Not Found"}');
                
            }

            echo json_encode( $image );
            http_response_code(202);
            

        } else {

            echo json_encode( $model->getImages() );
            http_response_code(202);

        }
    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');
    }