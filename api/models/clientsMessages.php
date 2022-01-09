<?php

    require_once("config.php");

    class ClientsMessages extends Config {

        public function showMessages() {
            
            $query = $this->db->prepare("
                SELECT
                    clients.client_id,
                    clients.name,
                    clients.title,
                    clients.email,
                    clients.telephone,
                    messages.message_id,
                    messages.message,
                    messages.message_date
                FROM 
                    clients
                INNER JOIN 
                    messages USING(client_id)
                ORDER BY 
                    messages.message_date DESC
            ");
            
            $query->execute();

            $result = $query->fetchAll( PDO::FETCH_ASSOC );
            return $result;
        }
        
        
        public function createMessage( $client ) {
            // Check if client already exists
            $query = $this->db->prepare("
                SELECT 
                    email,
                    client_id
                FROM clients
                WHERE email = ?
            ");

            $query->execute([ $client["email"] ]);
            $regClient = $query->fetch( PDO::FETCH_ASSOC );
            
            
            if( $regClient["client_id"] ) {
                // Insert Client Message
                $query = $this->db->prepare("
                    INSERT INTO messages
                    (client_id, message)
                    VALUES(?, ?)
                ");
                
                $query->execute([
                    $regClient["client_id"],
                    $client["message"]
                ]);

            } else {
                // Insert Client Info
                $query = $this->db->prepare("
                    INSERT INTO clients
                    (name, title, email, telephone)
                    VALUES(?, ?, ?, ?)
                ");
                
                $query->execute([
                    $client["name"],
                    $client["title"],
                    $client["email"],
                    $client["telephone"],
                ]);

                $newClient = $this->db->lastInsertId();

                if( $newClient ) {
                    // Insert Client Message
                    $query = $this->db->prepare("
                        INSERT INTO messages
                        (client_id, message)
                        VALUES(?, ?)
                    ");
                    
                    $query->execute([
                        $newClient,
                        $client["message"]
                    ]);
                }
            }

            
        }


        public function deleteMessage( $id ){

            $query = $this->db->prepare("
                DELETE FROM messages
                WHERE message_id = ?
            ");

            $id = $query->execute([ $id ]);
            return $id;
        }

        
    }