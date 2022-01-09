  <?php

    require("models/project.php");

    $model = new Project();


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
        
    
         

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }

