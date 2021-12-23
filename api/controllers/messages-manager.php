<?php

    require("models/clientsMessages.php");

    $model = new ClientsMessages();

    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {

        http_response_code(202);

        echo json_encode($model->showMessages());
        


    } elseif($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(202);

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }