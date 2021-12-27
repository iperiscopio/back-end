<?php

    require_once("config.php");

    class User extends Config {

        // LOGIN:
        public function login($data) {
            $query = $this->db->prepare("
                SELECT 
                    user_id,
                    name,
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

            return 0;
        }


        // REGISTER:
        public function register( $user ) {

            $query = $this->db->prepare("
                INSERT INTO users
                (name, email, password, username)
                VALUES(?, ?, ?, ?)
            ");

            $newUser = $query->execute([
                $user["name"],
                $user["email"],
                password_hash($user["password"], PASSWORD_DEFAULT),
                $user["username"]
            ]);


            return $newUser ? $this->db->lastInsertId() : 0;

            
   
        }

        // EMAIL VALIDATION IN DB:
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

        //GET LOGEDIN USER INFO:
        public function userInfo( $id ) {
            $query = $this->db->prepare("
                SELECT 
                    user_id,
                    name,
                    email,
                    password,
                    username
                FROM users
                WHERE user_id = ?
            ");

            $query->execute([ $id ]);

            $userInfo = $query->fetch( PDO::FETCH_ASSOC );
            
            return [$userInfo];
        }

        // UPDATE LOGEDIN USER:
        public function updateUser( $id, $user ) {
            $query = $this->db->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    password = ?,
                    username = ?
                WHERE
                    user_id = ?
            ");

            return $query->execute([ 
                $user["name"],
                $user["email"],
                password_hash($user["password"], PASSWORD_DEFAULT),
                $user["username"],
                $id
             ]);
        }

        // DELETE LOGEDIN USER:
        public function deleteUser( $id ) {
            $query = $this->db->prepare("
                DELETE FROM users
                WHERE user_id = ?
            ");

            $query->execute([ $id ]);

            $id = $query->fetch();

            if( $id ) {
                return true;

            } else {
                return false;
            }
        }
    }
