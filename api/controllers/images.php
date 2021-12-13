<?php

    require("models/image.php");

    $model = new Image();

    function imageValidator( $data ) {
        if( isset($data) ) {

            // validação de tipo de ficheiro internamente no servidor
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_format = finfo_file($finfo, $_FILES["images"]["tmp_name"]);
    
            //array de formatos de ficheiros aceites neste form
            $allowed_files_formats = [
                "jpg" => "image/jpeg",
                "png" => "image/png",
                "gif" => "image/gif",
                "webp" => "image/webp",
                "svg+xml" => "image/svg"
            ];
    
            if( !empty($data["project_id"]) &&
                is_numeric($data["project_id"]) &&
                $_FILES["images"]["error"] === 0 &&
                $_FILES["images"]["size"] > 0 &&
                $_FILES["images"]["size"] < 10000000 &&
                in_array($detected_format, $allowed_files_formats)
            ) {
    
                //criar nome aleatório com timestamp inicial
                $filename = date("YmdHis") . "_" . bin2hex(random_bytes(4));
                //concatenar a extensão do ficheiro
                $extension = "." . array_search($detected_format, $allowed_files_formats);
    
                // mover do directorio temporario para o destino final
                move_uploaded_file($_FILES["images"]["tmp_name"], "gallery/" . $filename.$extension );
    
                return true;

            } else {

                return false;
            }

        }
    }

    if($_SERVER["REQUEST_METHOD"] === "GET") {

        if( !empty( $id ) && !is_numeric( $id ) ) {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
            

        } else if( !empty($id) && is_numeric( $id ) ) {

            $projectImages = $model->getImages( $id );
        
            if( !$projectImages ){
                
                http_response_code(404);
                die('{"message": "404 Not Found"}');
                
            } else {

                echo json_encode( $projectImages );
                http_response_code(202);

            }
            

        } else {

            http_response_code(202);
            echo json_encode( $model->getAll() );

        }

        


    } elseif($_SERVER["REQUEST_METHOD"] === "POST") {

        $data = json_decode( file_get_contents("php://input"), TRUE );
        print_r($data);
            
        if( imageValidator($data) ) {
        
            $model->createImage( $id, $data );
    
            http_response_code(202);
            die('{"message": "Uploaded image with success"}');

        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }

    } elseif($_SERVER["REQUEST_METHOD"] === "PUT") {

    } elseif($_SERVER["REQUEST_METHOD"] === "DELETE") {

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');
    }
?>

