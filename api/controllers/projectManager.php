<?php

    require("models/project.php");
    require("models/user.php");

    $model = new Project();


    if($_SERVER["REQUEST_METHOD"] === "GET") {

        http_response_code(202);
        echo json_encode( $model->getAllProjects() );


    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    }else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }


        
