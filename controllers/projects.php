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
    function validator($data) {

        // foreach($data as $key => $value) {
        //     $data[$key] = trim(htmlspecialchars(strip_tags($value))); 
        // }

        // $finfo = finfo_open(FILEINFO_MIME_TYPE);
        // $detected_format = finfo_file($finfo, $_FILES["images"]["tmp_name"]);

        //array de formatos de ficheiros aceites neste form
        // $allowed_files_formats = [
        //     "jpg" => "image/jpeg",
        //     "png" => "image/png",
        //     "gif" => "image/gif",
        //     "webp" => "image/webp",
        //     "svg+xml" => "image/svg"
        // ];

        if( 
            // isset($_POST["send"]) && // <-------- necessário?
            !empty($data) &&
            isset($data["title"]) &&
            isset($data["location"]) &&
            isset($data["description"]) &&
            mb_strlen($data["title"]) >= 3 &&
            mb_strlen($data["title"]) <= 250 &&
            mb_strlen($data["location"]) >= 3 &&
            mb_strlen($data["location"]) <= 120 &&
            mb_strlen($data["description"]) >= 3 &&
            mb_strlen($data["description"]) <= 10000 &&
            is_array($data["images"]) 
            // $_FILES["images"]["error"] === 0 &&
            // $_FILES["images"]["size"] > 0 &&
            // $_FILES["images"]["error"] < 10000000 &&
            // in_array($detected_format, $allowed_files_formats)

        ) {

            // $filename = $data["title"] . "_" . bin2hex(random_bytes(4));
            
            // $extension = "." . array_search($detected_format, $allowed_files_formats);

            // move_uploaded_file($_FILES["images"]["tmp_name"], "C:/xampp/htdocs/api/images/" . $ilename.$extension );

            return true;

        }

        return false;
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
            
        if( $data ) {
        
            $model->createProject( $data, $data["images"] );
    
            http_response_code(202);
            die('{"message": "Uploaded project ' . $data["title"] . ' with success"}');

        } else {

            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }

        
    } else if($_SERVER["REQUEST_METHOD"] === "PUT") { //not working

        $data = json_decode( file_get_contents("php://input"), TRUE );

        if( 
            !empty($id)
        ) {

            $updateProject = $model->updateProject( $id, $data );// actualiza mas return NULL

            if( $updateProject ) {
                http_response_code(202);

                echo json_encode( $updateProject );

                die('{"message": "Updated project ' . $id . ' ' . $data["title"] . ' with success"}');

            } else {
                http_response_code(404);
                die('{"message": "404 Not Found"}');
            }
            
            
            
    
        } else {
            http_response_code(400);
            die('{"message": "400 Bad Request"}');
        }

            

        
            

        
        




    } else if($_SERVER["REQUEST_METHOD"] === "DELETE") { // não está fechado

        $data = json_decode( file_get_contents("php://input"), TRUE );
        
        if( empty( $id ) || !is_numeric( $id ) ) {
            
            http_response_code(400);
            die('{"message": "400 Bad Request"}');

            
        } else {

            $deletesProject = $model->deleteProject($id); // returns boolean true mesmo que não exista id. Apaga sempre da DB
            
            if( $deletesProject ) { 

                http_response_code(202);
                die('{"message": "Deleted Project nº: ' . $id . ' ' . $data["title"] .'"}');
                
            } else {

                http_response_code(404);
                die('{"message": "404 Not Found"}');

            }

             

        }

            

        
        
        
    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    }else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }