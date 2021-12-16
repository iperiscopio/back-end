<?php

    require("models/project.php");

    $model = new Project();

    // User authentication through JWT
    // if( in_array($_SERVER["REQUEST_METHOD"], ["POST", "PUT", "DELETE"]) ) {
        
    //     $userId = $model->routeRequireValidation();

    //     if( empty( $userId ) ) {
    //         http_response_code(401);
    //         return '{"message":"Wrong or missing Auth Token"}';
    //     } 

    //     if( 
    //         !empty($id) &&
    //         empty( $model->getProjectByUser($id, $userId) )
    //     ) {
    //         http_response_code(403);
    //         die('{"message":"You do not have permission to perform this action"}');
    //     }
    
    // }

    // Validate and sanitize:
    function validatorPost($data) {
        
        $imagesArray;
        $textArray;

        $target_dir = "../images/";
        // $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
        // allowed image formats array
        $allowed_files_formats = [
            "jpg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
            "webp" => "image/webp",
            "svg+xml" => "image/svg"
        ];

        $decoded_image;
        $mime_type;

        //sanitize and decode each image in array
        for( $i = 0; $i < count($data["images"]); $i++ ) {

            $sanitize = trim(htmlspecialchars(strip_tags($data["images"][$i])));
            $replace = str_replace("data:image/jpeg;base64,", "", $sanitize);
            $decoded_image = base64_decode($replace);
            $data["images"][$i] = $decoded_image;

            $finfo = finfo_open();
            $mime_type = finfo_buffer($finfo, $data["images"][$i], FILEINFO_MIME_TYPE);
        
            $detected_format = finfo_file($finfo, $data["images"][$i]);
            
        }
        
        
        if ($data["images"]) {
            
            //validate each image
            foreach( $data["images"] as $image) {            
                
                if( $image ) {
                    var_dump($finfo);
                    $detected_format = finfo_file($finfo, $image);
                    $size = getimagesizefromstring($image);
                    
                    if(
                        // $image.error === 0 &&
                        $size > 0 &&
                        $size < 10000000
                    ) {
                        return $imagesArray = true;
                    } 
                    else {
                        return false;
                    }
                }

                if(in_array($detected_format, $allowed_files_formats)) {
    
                    $filename = $data["title"] . "_" . bin2hex(random_bytes(4));
                    
                    $extension = "." . array_search($detected_format, $allowed_files_formats);

                    $file_dir = $target_dir . uniqid() . '.' . $extension;
    
                    file_put_contents($file_dir, $image);
                    move_uploaded_file($image . $target_dir . $filename.$extension );
                    
                }
            }

        }
        
        //sanitize texts
        if( $data ) {
            $data["title"] = trim(htmlspecialchars(strip_tags($data["title"]))); 
            $data["location"] = trim(htmlspecialchars(strip_tags($data["location"]))); 
            $data["description"] = trim(htmlspecialchars(strip_tags($data["description"]))); 
        }

        // Validate text
        if( 
            !empty($data) &&
            isset($data["title"]) &&
            isset($data["location"]) &&
            isset($data["description"]) &&
            mb_strlen($data["title"]) >= 3 &&
            mb_strlen($data["title"]) <= 250 &&
            mb_strlen($data["location"]) >= 3 &&
            mb_strlen($data["location"]) <= 120 &&
            mb_strlen($data["description"]) >= 3 &&
            mb_strlen($data["description"]) <= 10000
        ) {

            return $textArray = true;

        }

        // check if all validation returned true
        if($textArray && $imagesArray) {

            return true;
            
        } else {

            return false;
        }


    };

    

    if($_SERVER["REQUEST_METHOD"] === "GET") {

        if( !empty( $id ) && !is_numeric( $id ) ) {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
            

        } else if( !empty($id) && is_numeric( $id ) ) {

            $project = $model->getProject( $id );
        
            if( !$project ){
                
                http_response_code(404);
                die('{"message": "404 Not Found"}');
                
            }

            http_response_code(202);
            echo json_encode( $project );
            

        } else {

            http_response_code(202);
            echo json_encode( $model->getProjects() );

        }
        
    
         

    } elseif($_SERVER["REQUEST_METHOD"] === "POST") { // falta validações images quando upload

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
            
        if( validatorPost($data) ) {
        
            $model->createProject( $data, $data["images"] );
    
            http_response_code(202);
            die('{"message": "Uploaded project ' . $data["title"] . ' with success"}');

        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }



        
    } else if($_SERVER["REQUEST_METHOD"] === "PUT") { // falta validação das imagens

        $data = json_decode( file_get_contents("php://input"), TRUE );

        if( 
            !empty($id)
        ) {

            $updateProject = $model->updateProject( $id, $data );

            if( $updateProject ) {
                http_response_code(202);

                echo json_encode( $updateProject );

                die('{"message": "Updated project ' . $id . ', ' . $data["title"] . ' with success"}');

            } else {
                http_response_code(404);
                die('{"message": "404 Not Found"}');
            }
            
            
    
        } else {
            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }




    } else if($_SERVER["REQUEST_METHOD"] === "DELETE") { 

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        if( !empty( $id ) && is_numeric( $id ) ) {

            $removeProject = $model->deleteProject($id);
            

            if( $removeProject ) { 

                http_response_code(202);
                die('{"message": "Deleted Project nº: ' . $id . ' ' . $data["title"] .'"}');
                
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

    }else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }

   