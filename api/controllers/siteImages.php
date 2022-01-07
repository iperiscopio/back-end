<?php

    require("models/siteImage.php");

    $model = new SiteImage();

    if( $_SERVER["REQUEST_METHOD"] === "GET") {

        http_response_code(202);
        echo json_encode($model->allSiteImages());


    } else {
        
        http_response_code(405);
        die('{"message":"Method Not Allowed"}');
    }