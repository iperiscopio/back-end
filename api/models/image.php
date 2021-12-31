<?php

    require_once("config.php");

    class Image extends Config {

        
        // GET all images:
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

            foreach($results as $result => $value) {

                if(!in_array($value["project_id"], $images)){
                    ++$key;
                    $project_id[$key]["project_id"] = $value["project_id"];
                }
                if(!empty($value["image"])) {
                    $project_id[$key]["images"][] = [
                        $value["image"],
                        $value["image_id"]
                    ];
                        
                
                }
                $images[] = $value["project_id"];
            }

            return $project_id;
        }

        // DELETE AN IMAGE FROM A PROJECT: 
        public function deleteImageFromProject( $image_id ) {

            $query = $this->db->prepare("
                DELETE FROM images
                WHERE
                    image_id = ?
            ");

            $id = $query->execute([ $image_id ]);

            return $id;

        }

        
    } 