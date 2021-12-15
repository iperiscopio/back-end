<?php

    require_once("config.php");

    class User extends Config {

        // LOGIN:
        public function login($data) {
            $query = $this->db->prepare("
                SELECT 
                    user_id,
                    first_name,
                    last_name,
                    email,
                    password,
                    username
                FROM users
                WHERE email = ?
            ");

            $query->execute([ $data["email"] ]);

            $user = $query->fetch( PDO::FETCH_ASSOC );

            if( 
                !empty($user) &&
                password_verify($data["password"], $user["password"])
            ) {
                return $user;
            }

            return ;
        }


        // REGISTER:
        public function register( $user ) {

            $query = $this->db->prepare("
                INSERT INTO users
                (first_name, last_name, email, password, username)
                VALUES(?, ?, ?, ?, ?)
            ");

            $newUser = $query->execute([
                $user["first_name"],
                $user["last_name"],
                $user["email"],
                password_hash($user["password"], PASSWORD_DEFAULT),
                $user["username"],
            ]);


            return $newUser ? $this->db->lastInsertId() : 0;

            
   
        }

        public function emailValidation( $email ) {

            $query = $this->db->prepare("
                SELECT email
                FROM users
                WHERE email = ?
            ");

            $query->execute([ $email["email"] ]);

            $availableEmail = $query->fetch();

            if( !$availableEmail ) {

                return true;
                
            } else {

                return false;
            }

        }
    }
