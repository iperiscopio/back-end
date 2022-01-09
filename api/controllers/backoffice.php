<?php

    use ReallySimpleJWT\Token;

    require("models/stats.php");

    $model = new Stats();

    // Admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["GET"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 
    
    }

    if( $_SERVER["REQUEST_METHOD"] === "GET") {

        $countP = $model->countP();
        $countI = $model->countI();
        $countA = $model->countA();
        $countC = $model->countC();
        $countM = $model->countM();

        $counts = [$countP, $countI, $countA, $countC, $countM];
        
        http_response_code(202);
        echo json_encode($counts);


    }   else {
        
        http_response_code(405);
        die('{"message":"Method Not Allowed"}');
    }