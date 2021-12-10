<?php

    header("Content-Type: application/json");

    require("vendor/autoload.php");

    define("CONFIG", parse_ini_file(".env"));

    define("ROOT",
        rtrim(
            str_replace(
                "\\", "//", dirname($_SERVER["SCRIPT_NAME"])
            ),
            "/"
        )
    );

    $url_parts = explode("/", $_SERVER["REQUEST_URI"]);

    $controllers = [
        "access",
        "images",
        "projects"
    ];

    $controller = $url_parts[1];

    // if(!empty($url_parts[3]) && is_numeric($url_parts[3])) {
    //     $id = intval($url_parts[3]);
    // }
    $id = !empty($url_parts[2]) ? $url_parts[2] : "";

    if( !in_array($controller, $controllers) ) {
        http_response_code(400);
        die('{"message": "rota inválida"}');
    }

    require("controllers/" . $controller . ".php");