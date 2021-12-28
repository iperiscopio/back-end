<?php

    use ReallySimpleJWT\Token;

    class Config {

        protected $db;

        public function __construct() {
            $this->db = new PDO("mysql:host=localhost;dbname=ilheu_atelier;charset=utf8mb4", "root", "");
        }

        // admin validation 
        public function routeRequireValidation() {
            
            $headers = apache_request_headers();

            foreach($headers as $header => $value) {
                if( strtolower($header) === "x-auth-token" ) {
                    $token = trim( $value );
                }
            }
            // Token validation
            $secret = CONFIG["SECRET_KEY"];
            
            $isValid = Token::validate($token, $secret);
            
            if($isValid) {
                $admin = Token::getPayload($token, $secret);
            }
            
            if( isset($admin) ) { 
                return $admin["userId"];
            }

            return 0;
        }

    }
