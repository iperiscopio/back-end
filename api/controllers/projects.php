  <?php

    require("models/project.php");

    $model = new Project();


    // Sanitize:
    function sanitize($data) {
        if( !empty($data) ) {
            $data["title"] = trim(htmlspecialchars(strip_tags($data["title"]))); 
            $data["location"] = trim(htmlspecialchars(strip_tags($data["location"]))); 
            $data["description"] = trim(htmlspecialchars(strip_tags($data["description"])));

            for( $i = 0; $i < count($data["images"]); $i++ ) {

                $sanitize = trim(htmlspecialchars(strip_tags($data["images"][$i])));
                $data["images"][$i] = str_replace("data:image/jpeg;base64,", "", $sanitize);
            } 

            return $data;
        }

        return false;
    }

    // Validate:
    function validator($sanitizedData) {

        if( !empty($sanitizedData) ) {

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
                    mb_strlen($sanitizedData["description"]) >= 3 &&
                    mb_strlen($sanitizedData["description"]) <= 10000 &&
                    $size > 0 &&
                    $size < 10000000
                ) {
                
                    return true;
                } 
                
            }
            
        }   

        return false;

    }

    function imageTransformation($sanitizedData) {
        
        $target_dir = "/images/";
        
        // allowed image formats array
        $allowed_files_formats = [
            "jpg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
            "webp" => "image/webp",
            "svg" => "image/svg+xml"
        ];

        for( $i = 0; $i < count($sanitizedData["images"]); $i++ ) {

            $decoded_image = base64_decode($sanitizedData["images"][$i]);

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $detected_format = $finfo->buffer($decoded_image);

            if(in_array($detected_format, $allowed_files_formats)) {

                $filename = $sanitizedData["title"] . "_" . bin2hex(random_bytes(4));
                
                $extension = "." . array_search($detected_format, $allowed_files_formats);

                $file_dir = $target_dir . $filename . $extension;

                file_put_contents(".." . $file_dir, $decoded_image);
                
            }
            $sanitizedData["images"][$i] = $file_dir;
        }
        
        return $sanitizedData;
    }
   

    

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

                echo json_encode( $updateProject );

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

        
    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    }else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }

