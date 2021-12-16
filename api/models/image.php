<?php

    require_once("config.php");

    class Image extends Config {
        
        public function getAll() {
            $query = $this->db->prepare("
                SELECT 
                    image_id,
                    project_id,
                    image
                FROM 
                    images                
            ");

            $query->execute([]);

            $results = $query->fetchAll( PDO::FETCH_ASSOC );
            $project_id = [];
            $images = []; 
            $key = 0;

            foreach($results as $result => $value){
	
                if(!in_array($value["project_id"], $images)){
                    ++$key;
                    $project_id[$key]["project_id"] = $value["project_id"];
                }
                
                $project_id[$key]["images"][$result] = $value["image"];
                $images[] = $value["project_id"];
            }

            return $project_id;
        }
        
        // GET ALL IMAGES OF A PROJECT: 
        public function getImages( $id ) {

            $query = $this->db->prepare("
                SELECT 
                    image_id,
                    project_id,
                    image
                FROM 
                    images
                WHERE
                    project_id = ?                  
            ");

            $query->execute([ $id ]);

            return $query->fetchAll( PDO::FETCH_ASSOC );

            // $images = [];
            // $key = 0;

            // foreach($results as $result => $value){
	
            //     if(!in_array($value["project_id"], $images)){
            //         $images[$key]["project_id"] = $value["project_id"];
            //         $images[$key]["image"] = $value["image"];
            //     }
            //     $image[$key]["images"][$result] = $value["image"];
               
            // }

            // return $images;

           
        }

        

        // POST An Image: 
        public function createImage( $id  ) {

            $query = $this->db->prepare("
                INSERT INTO images
                (project_id, image)
                VALUES(?, ?) 
            ");

            $query->execute([
                $id,
                $_FILES["images"]["name"]
            ]);




        }

        // UPDATE AN IMAGE FROM A PROJECT: 
        public function updateImageFromProject( $project_id, $image_id ) {

            $query = $this->db->prepare("
                UPDATE 
                    images
                SET
                    image = ?
                WHERE
                    project_id = ?
                    AND image_id = ?

            ");

            return $query->execute([
                $_FILES["images"]["name"],
                $project_id, 
                $image_id 
            ]);




        }


        //DELETE ALL IMAGES FROM A PROJECT:
        public function deleteImages( $id ) {
            $query = $this->db->prepare("
                    DELETE FROM images
                    WHERE project_id = ?
                ");

            return $query->execute([ $id ]);
        }
        

        // DELETE AN IMAGE FROM A PROJECT: 
        public function deleteImageFromProject( $project_id, $image_id ) {

            $query = $this->db->prepare("
                DELETE FROM images
                WHERE 
                    project_id = ? AND image_id = ?
            ");

            return $query->execute([ 
                $project_id, 
                $image_id 
            ]);

        }
    } 