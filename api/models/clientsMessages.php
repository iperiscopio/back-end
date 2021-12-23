<?php

    require("config.php");

    class ClientsMessages extends Config {

        public function createMessage( $client ) {
            // Insert Client Info
            $querie = $this->db->prepare("
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
                $querie = $this->db->prepare("
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

        public function showMessages() {
            $querie = $this->db->prepare("
                SELECT
                    clients.client_id,
                    clients.name,
                    clients.title,
                    clients.email,
                    clients.telefone,
                    messages.message,
                    messages.message_date
                FROM clients
                INNER JOIN messages USING(client_id)
                ORDER BY messages.message_date
            ");
            
            $query->execute([
                $client["name"],
                $client["title"],
                $client["email"],
                $client["telephone"],
            ]);

            return $query->fetchAll( PDO::FETCH_ASSOC );
        }
    }