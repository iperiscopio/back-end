<?php

    require_once("config.php");

    class SiteImage extends Config {
        
        public function allSiteImages() {
            $query = $this->db->prepare("
                SELECT 
                    image
                FROM 
                    site_images                
            ");

            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }
    }
?>