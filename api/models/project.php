<?php

    require_once("config.php");

    class Project extends Config {

        
        //GET ALL PROJECTS IN DB:
        public function getAllProjects() {

            $query = $this->db->prepare("
                SELECT 
                    projects.project_id, 
                    projects.title,
                    projects.location,
                    projects.description,
                    images.project_id AS images,
                    images.image_id,
                    images.image
                FROM 
                    projects
                LEFT JOIN 
                    images USING(project_id)                   
            ");

            $query->execute();

            $results = $query->fetchAll( PDO::FETCH_ASSOC );

            $projects = [];
            $projectImages = [];
            $key = 0;

            foreach($results as $result => $value){
	
                if(!in_array($value["project_id"], $projectImages)){
                    ++$key;
                    $projects[$key]["project_id"] = $value["project_id"];
                    $projects[$key]["title"] = $value["title"];
                    $projects[$key]["location"] = $value["location"];
                    $projects[$key]["description"] = $value["description"];
                }
                if(!empty($value["image"])) {
                    $projects[$key]["images"][] = $value["image"];
                   
                }
                $projectImages[] = $value["project_id"];
                
            }

            return $projects;
        }
        
        // GET ALL PROJECTS WITH IMAGES: 
        public function getProjects() {

            $query = $this->db->prepare("
                SELECT 
                    projects.project_id, 
                    projects.title,
                    projects.location,
                    projects.description,
                    images.project_id AS images,
                    images.image_id,
                    images.image
                FROM 
                    projects
                INNER JOIN 
                    images USING(project_id)                   
            ");

            $query->execute();

            $results = $query->fetchAll( PDO::FETCH_ASSOC );

            $projects = [];
            $projectImages = [];
            $key = 0;

            foreach($results as $result => $value){
	
                if(!in_array($value["project_id"], $projectImages)){
                    ++$key;
                    $projects[$key]["project_id"] = $value["project_id"];
                    $projects[$key]["title"] = $value["title"];
                    $projects[$key]["location"] = $value["location"];
                    $projects[$key]["description"] = $value["description"];
                }
                if(!empty($value["image"])) {
                    $projects[$key]["images"][] = $value["image"];
                }
                $projectImages[] = $value["project_id"];
                
            }

            return $projects;
        }

        // GET A SINGLE PROJECT: 
        public function getProject( $id ) {

            $query = $this->db->prepare("
                SELECT 
                    projects.project_id,  
                    projects.title,
                    projects.location,
                    projects.description,
                    images.project_id AS images,
                    images.image_id,
                    images.image
                FROM 
                    projects
                LEFT JOIN
                    images USING(project_id)
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

        // POST A PROJECT: 
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

        // UPDATE A PROJECT: 
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

            if(!empty($data["images"])) {

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
            
            return $updatedProject;




        }

        // DELETE A PROJECT: 
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

            return $deletedProject;


        }
    } 