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
                    messages.message_date
            ");
            
            $query->execute();

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }
        
        
        public function createMessage( $client ) {
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


        public function deleteMessage( $id ){

            $query = $this->db->prepare("
                DELETE FROM messages
                WHERE messages_id = ?
            ");

            return $query->execute([ $id ]);
        }

        
    }