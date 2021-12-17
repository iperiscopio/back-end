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
        "accountsManager",
        "login",
        "images",
        "projects",
        "projectManager",
        "siteImages"
    ];

    $controller = $url_parts[2];

    // if(!empty($url_parts[3]) && is_numeric($url_parts[3])) {
    //     $id = intval($url_parts[3]);
    // }
    $id = !empty($url_parts[3]) ? $url_parts[3] : "";

    if( !in_array($controller, $controllers) ) {
        http_response_code(400);
        die('{"message": "rota inválida"}');
    }

    require("controllers/" . $controller . ".php");