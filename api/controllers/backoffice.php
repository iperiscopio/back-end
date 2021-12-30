<?php

    require("models/stats.php");

    $model = new Stats();

    if( $_SERVER["REQUEST_METHOD"] === "GET") {

        $countP = $model->countP();
        $countI = $model->countI();
        $countA = $model->countA();
        $countC = $model->countC();
        $countM = $model->countM();

        $counts = [$countP, $countI, $countA, $countC, $countM];
        
        http_response_code(202);
        echo json_encode($counts);


    }  elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

        http_response_code(202);

    } else {
        http_response_code(405);
        echo '{"message":"Method Not Allowed"}';
    }