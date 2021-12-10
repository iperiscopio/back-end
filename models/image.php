<?php

    require_once("config.php");

    class Image extends Config {
        
        
        // GET ALL IMAGES OF A PROJECT: 
        public function getImages( $id ) {

            $query = $this->db->prepare("
                SELECT 
                    image_id,
                    project_id,
                    image
                FROM 
                    images                  
            ");

            $query->execute([]);

            return $query->fetchAll( PDO::FETCH_ASSOC );

           
        }

        // GET A SINGLE Image: (confirma funcionar)
        public function getImage( $id ) {

            $query = $this->db->prepare("
                SELECT 
                    image_id,
                    project_id,
                    image
                FROM 
                    images
                LEFT JOIN
                    projects USING(project_id)
                WHERE 
                    project_id = ?    
            ");

            $query->execute([ $id ]);

            $results = $query->fetchAll( PDO::FETCH_ASSOC );

            $project = [];
            $projectImages = [];
            $key = 0;

            foreach($results as $result => $value){
	
                if(!in_array($value["project_id"], $projectImages)){
                    $project[$key]["project_id"] = $value["project_id"];
                    $project[$key]["title"] = $value["title"];
                    $project[$key]["location"] = $value["location"];
                    $project[$key]["description"] = $value["description"];
                }
                $project[$key]["images"][$result] = $value["image"];
                $projectImages[] = $value["project_id"];
        
            }

            return $project;
        }

        // POST A PROJECT: (confirma funcionar - falta guardar fotos directorio especifico)
        public function createProject( $data ) {

            $query = $this->db->prepare("
                INSERT INTO projects
                (title, description, location)
                VALUES(?, ?, ?) 
            ");

            $query->execute([
                $data["title"],
                $data["description"],
                $data["location"]
            ]);

            
            $newProject = $this->db->lastInsertId();

            if( $newProject ) {

                foreach($data["images"] as $image) {
                    $query = $this->db->prepare("
                        INSERT INTO images
                        (project_id, image)
                        VALUES(?, ?)
                    ");
                
                    $query->execute([
                        $newProject,
                        $image
                    ]);

                }
                
            }

        }

        // UPDATE A PROJECT: (confirma funcionar - falta guardar fotos directorio especifico)
        public function updateProject( $id, $data ) {

            // query to update projects table
            $query = $this->db->prepare("
                UPDATE 
                    projects
                SET
                    title = ?,
                    location = ?,
                    description = ?
                WHERE
                    project_id = ?

            ");

            $updatedProject = $query->execute([
                $data["title"],
                $data["location"],
                $data["description"],
                $id
            ]);

            // query to delete images table with id
            if( $updatedProject ) {

                $query = $this->db->prepare("
                    DELETE FROM images
                    WHERE project_id = ?
                ");

                $deletedImages = $query->execute([ $id ]);
                
                // query to insert updated images to table
                if( $deletedImages ) {
                    
                    foreach( $data["images"] as $image) {
                        $query = $this->db->prepare("
                            INSERT INTO images
                            (project_id, image)
                            VALUES(?, ?)
                        ");

                        $query->execute([
                            $id,
                            $image
                        ]);
                    }

                }
            
            }

            return $updatedProject;




        }

        // DELETE A PROJECT: (confirma funcionar)
        public function deleteProject( $id ) {

            $query = $this->db->prepare("
                DELETE FROM projects
                WHERE project_id = ?
            ");

            $deletedProject = $query->execute([ $id ]);

            if( $deletedProject ) {

                $query = $this->db->prepare("
                    DELETE FROM images
                    WHERE project_id = ?
                ");

                $query->execute([ $id ]);
            }
        }
    } 
    

    /*
    <!-- (A) UPLOAD FORM -->
    <form method="post" enctype="multipart/form-data">
    <input type="file" name="upload" accept=".png,.gif,.jpg,.webp" required>
    <input type="submit" name="submit" value="Upload Image">
    </form>

    <?php
    // (B) SAVE IMAGE INTO DATABASE
    if (isset($_FILES["upload"])) {
    try {
        // (B1) CONNECT To DATABASE
        require "2-connect-db.php";

        // (B2) READ IMAGE FILE & INSERT
        $stmt = $pdo->prepare("INSERT INTO `images` (`img_name`, `img_data`) VALUES (?,?)");
        $stmt->execute([$_FILES["upload"]["name"], file_get_contents($_FILES["upload"]["tmp_name"])]);
        echo "OK";
    } catch (Exception $ex) { echo $ex->getMessage(); }
    }
    ?>
    */