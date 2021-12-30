<?php

    require_once("config.php");

    class Stats extends Config {

        //Projects, images and admin:
        public function countP() {

            $query = $this->db->prepare("
                SELECT 
                    COUNT(*) AS totalProjects                   
                FROM 
                    projects  
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }

        public function countI() { 
            $query = $this->db->prepare("
                SELECT
                    COUNT(*) AS totalImages
                FROM images
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }

        public function countA() { 
            $query = $this->db->prepare(" 
                SELECT
                    COUNT(*) AS totalAdmins
                FROM admins   
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }

        public function countC() { 
            $query = $this->db->prepare(" 
                SELECT
                    COUNT(*) AS totalClients
                FROM clients   
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }

        public function countM() { 
            $query = $this->db->prepare(" 
                SELECT
                    COUNT(*) AS totalMessages
                FROM messages  
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }



    }