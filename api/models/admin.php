<?php

    require_once("config.php");

    class Admin extends Config {

        // LOGIN:
        public function login($data) {
            $query = $this->db->prepare("
                SELECT 
                    admin_id,
                    name,
                    email,
                    password,
                    username
                FROM admins
                WHERE email = ?
            ");

            $query->execute([ $data["email"] ]);

            $admin = $query->fetch( PDO::FETCH_ASSOC );

            if( 
                !empty($admin) &&
                password_verify($data["password"], $admin["password"])
            ) {
                return $admin;
            }

            return 0;
        }


        // REGISTER:
        public function register( $admin ) {

            $query = $this->db->prepare("
                INSERT INTO admins
                (name, email, password, username)
                VALUES(?, ?, ?, ?)
            ");

            $newAdmin = $query->execute([
                $admin["name"],
                $admin["email"],
                password_hash($admin["password"], PASSWORD_DEFAULT),
                $admin["username"]
            ]);


            return $newAdmin ? $this->db->lastInsertId() : 0;

            
   
        }

        // EMAIL VALIDATION IN DB:
        public function emailValidation( $email ) {

            $query = $this->db->prepare("
                SELECT email
                FROM admins
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

        //GET LOGEDIN ADMIN INFO:
        public function adminInfo( $id ) {
            $query = $this->db->prepare("
                SELECT 
                    admin_id,
                    name,
                    email,
                    password,
                    username
                FROM admins
                WHERE admin_id = ?
            ");

            $query->execute([ $id ]);

            $adminInfo = $query->fetch( PDO::FETCH_ASSOC );
            
            return [$adminInfo];
        }

        // UPDATE LOGEDIN ADMIN:
        public function updateAdmin( $id, $admin ) {
            $query = $this->db->prepare("
                UPDATE admins
                SET
                    name = ?,
                    email = ?,
                    password = ?,
                    username = ?
                WHERE
                    admin_id = ?
            ");

            return $query->execute([ 
                $admin["name"],
                $admin["email"],
                password_hash($admin["password"], PASSWORD_DEFAULT),
                $admin["username"],
                $id
             ]);
        }

        // DELETE LOGEDIN ADMIN:
        public function deleteAdmin( $id ) {
            $query = $this->db->prepare("
                DELETE FROM admins
                WHERE admin_id = ?
            ");

            $id = $query->execute([ $id ]);

            return $id;
        }
    }
