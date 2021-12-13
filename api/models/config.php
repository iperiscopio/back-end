<?php

    use ReallySimpleJWT\Token;

    class Config {

        protected $db;

        public function __construct() {
            $this->db = new PDO("mysql:host=localhost;dbname=ilheu_atelier;charset=utf8mb4", "root", "");
        }

        // validação user/admin
        public function routeRequireValidation() {
            
            $headers = apache_request_headers();
            foreach($headers as $header => $value) {
                if( strtolower($header) === "X-Auth-Token" ) {
                    $token = trim( $value );
                }
            }

            // Validação Token
            $secret = CONFIG["SECRET_KEY"];

            $isValid = Token::validate($token, $secret);

            if($isValid) {
                $user = Token::getPayload($token, $secret);
            }
            
            if( isset($user) ) { 
                return $user["userId"];
            }

            return 0;
        }

    }
