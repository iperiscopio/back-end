<?php

    use ReallySimpleJWT\Token;

    require("models/project.php");

    $model = new Project();

    // admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["GET", "POST", "PUT", "DELETE"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            die('{"message":"Wrong or missing Auth Token"}');
        } 

    }
     

     // Sanitize:
     function sanitize($data) {

        if( !empty($data) ) {
            $data["title"] = trim(htmlspecialchars(strip_tags($data["title"]))); 
            $data["location"] = trim(htmlspecialchars(strip_tags($data["location"]))); 
            $data["description"] = trim($data["description"]); // <-- removed 
            // htmlspecialchars(strip_tags($data["description"])) in order to CKEditor 
            // could save in db html characters and tags, as I'm returning the data bellow
            // for the next functions

            for( $i = 0; $i < count($data["images"]); $i++ ) {

                $sanitize = trim(htmlspecialchars(strip_tags($data["images"][$i])));

            } 
            
            return $data;
        }
        return false;
    }

    // Validate:
    function validator($sanitizedData) {

        if( !empty($sanitizedData) ) {

            if( empty($sanitizedData["images"]) ) {
                if( 
                    isset($sanitizedData["title"]) &&
                    isset($sanitizedData["location"]) &&
                    isset($sanitizedData["description"]) &&
                    mb_strlen($sanitizedData["title"]) >= 3 &&
                    mb_strlen($sanitizedData["title"]) <= 250 &&
                    mb_strlen($sanitizedData["location"]) >= 3 &&
                    mb_strlen($sanitizedData["location"]) <= 120 &&
                    mb_strlen($sanitizedData["description"]) >= 10 &&
                    mb_strlen($sanitizedData["description"]) <= 65535
                ) {
                    
                    return true;
                }
            } else {

                for( $i = 0; $i < count($sanitizedData["images"]); $i++ ) {

                    $size = strlen($sanitizedData["images"][$i]);

                    if( 
                        isset($sanitizedData["title"]) &&
                        isset($sanitizedData["location"]) &&
                        isset($sanitizedData["description"]) &&
                        mb_strlen($sanitizedData["title"]) >= 3 &&
                        mb_strlen($sanitizedData["title"]) <= 250 &&
                        mb_strlen($sanitizedData["location"]) >= 3 &&
                        mb_strlen($sanitizedData["location"]) <= 120 &&
                        mb_strlen($sanitizedData["description"]) >= 10 &&
                        mb_strlen($sanitizedData["description"]) <= 65535 &&
                        $size > 0 &&
                        $size < 10000000
                    ) {
                        
                        return true;
                    } 
                    
                }
            } 
            
        }   
        return false;

    }

    // Transform images data:
    function imageTransformation($sanitizedData) {
        
        $target_dir = "/images/";
        
        // allowed image formats array
        $allowed_files_formats = [
            "jpg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
            "webp" => "image/webp",
            // "svg" => "image/svg+xml" <-- removed because PHP MIME TYPE returned "image/svg" instead of "image/svg+xml"
        ];

        for( $i = 0; $i < count($sanitizedData["images"]); $i++ ) {

            $decoded_image = base64_decode($sanitizedData["images"][$i]);

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $detected_format = $finfo->buffer($decoded_image);

            if(in_array($detected_format, $allowed_files_formats)) {

                $filename = str_replace(" ", "_", $sanitizedData["title"]) . "_" . bin2hex(random_bytes(4));
                
                $extension = "." . array_search($detected_format, $allowed_files_formats);

                $file_dir = $target_dir . $filename . $extension;

                file_put_contents(".." . $file_dir, $decoded_image);
                
            }
            $sanitizedData["images"][$i] = $file_dir;
        }
        
        return $sanitizedData;
    }


    if($_SERVER["REQUEST_METHOD"] === "GET") {

        http_response_code(202);
        echo json_encode( $model->getAllProjects() );




    } elseif($_SERVER["REQUEST_METHOD"] === "POST") { 

        $data = json_decode( file_get_contents("php://input"), TRUE );

        $sanitizedData = sanitize($data);
        $transformedData = imageTransformation($sanitizedData);
        
        if( validator($sanitizedData) ) {
            
            $model->createProject( $transformedData );
    
            http_response_code(202);
            die('{"message": "Uploaded project ' . $transformedData["title"] . ' with success"}');

        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }



        
    } else if($_SERVER["REQUEST_METHOD"] === "PUT") { 

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        $sanitizedData = sanitize($data);
        $transformedData = imageTransformation($sanitizedData);

        if( 
            !empty($id) &&
            validator($sanitizedData)
        ) {
            $updateProject = $model->updateProject( $id, $transformedData );
            
            if( $updateProject ) {
                
                http_response_code(202);
                die('{"message": "Updated project ' . $id . ', ' . $transformedData["title"] . ' with success"}');

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

        
    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }


        
