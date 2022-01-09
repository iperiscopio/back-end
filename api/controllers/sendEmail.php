<?php

    use ReallySimpleJWT\Token;

    require("models/admin.php");
    require("models/sendEmail.php");

    $model = new Email();
    $findAdmin = new Admin();

    // Admin authentication through JWT
    if( in_array($_SERVER["REQUEST_METHOD"], ["POST"]) ) {
        
        $adminId = $model->routeRequireValidation();

        if( empty( $adminId ) ) {
            http_response_code(401);
            return '{"message":"Wrong or missing Auth Token"}';
        } 
    
    }

    // Email sanitization from admin to client 
    function sanitize( $data ) {

        if( !empty($data) ) {

            $data["message_id"] = trim(htmlspecialchars(strip_tags($data["message_id"])));
            $data["title"] = trim(htmlspecialchars(strip_tags($data["title"])));
            $data["name"] = trim(htmlspecialchars(strip_tags($data["name"])));
            $data["email"] = trim(htmlspecialchars(strip_tags($data["email"])));
            $data["subject"] = trim(htmlspecialchars(strip_tags($data["subject"])));
            $data["message"] = trim($data["message"]);

            for( $i = 0; $i < count($data["attachments"]); $i++ ) {

                $sanitize = trim(htmlspecialchars(strip_tags($data["attachments"][$i])));

            }

            return $data;
        }

        return false;
    }

    // Email validation from admin to client 
    function validate( $sanitizedData ) {

        if( !empty($sanitizedData) ) {

            if( empty($sanitizedData["attachments"]) ) {

                if( 
                    !empty($sanitizedData["message_id"]) &&
                    !empty($sanitizedData["title"]) &&
                    !empty($sanitizedData["name"]) &&
                    !empty($sanitizedData["email"]) &&
                    !empty($sanitizedData["subject"]) &&
                    !empty($sanitizedData["message"]) &&
                    is_numeric($sanitizedData["message_id"]) &&
                    mb_strlen($sanitizedData["title"]) >= 2 &&
                    mb_strlen($sanitizedData["title"]) <= 3 &&
                    mb_strlen($sanitizedData["name"]) >= 3 &&
                    mb_strlen($sanitizedData["name"]) <= 255 &&
                    filter_var($sanitizedData["email"], FILTER_VALIDATE_EMAIL) &&
                    mb_strlen($sanitizedData["subject"]) >= 3 &&
                    mb_strlen($sanitizedData["subject"]) <= 250 &&
                    mb_strlen($sanitizedData["message"]) >= 10 &&
                    mb_strlen($sanitizedData["message"]) <= 65535
                ) {
                    return true;
                }

            } else {

                for( $i = 0; $i < count($sanitizedData["attachments"]); $i++ ) {

                    $size = strlen($sanitizedData["attachments"][$i]);

                    if( 
                        !empty($sanitizedData["message_id"]) &&
                        !empty($sanitizedData["title"]) &&
                        !empty($sanitizedData["name"]) &&
                        !empty($sanitizedData["email"]) &&
                        !empty($sanitizedData["subject"]) &&
                        !empty($sanitizedData["message"]) &&
                        is_numeric($sanitizedData["message_id"]) &&
                        mb_strlen($sanitizedData["title"]) >= 2 &&
                        mb_strlen($sanitizedData["title"]) <= 3 &&
                        mb_strlen($sanitizedData["name"]) >= 3 &&
                        mb_strlen($sanitizedData["name"]) <= 255 &&
                        filter_var($sanitizedData["email"], FILTER_VALIDATE_EMAIL) &&
                        mb_strlen($sanitizedData["subject"]) >= 3 &&
                        mb_strlen($sanitizedData["subject"]) <= 250 &&
                        mb_strlen($sanitizedData["message"]) >= 10 &&
                        mb_strlen($sanitizedData["message"]) <= 65535 &&
                        $size > 0 &&
                        $size < 25000000
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;

    }

    // Transform attachments data:
    function attachmentsTransformation($sanitizedData) {
    
        $target_dir = "../uploads/";

        $allowed_files_formats = [
            "pdf" => "application/pdf",
            "odt" => "application/vnd.oasis.opendocument.text",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "doc" => "application/msword",
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "rar" => "application/x-rar-compressed",
            "zip" => "application/zip",
            "txt" => "text/plain",
            "jpg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
            "webp" => "image/webp",
            // "svg" => "image/svg+xml" <-- removed because PHP MIME TYPE returned "image/svg" instead of "image/svg+xml"
        ];
        
        for( $i = 0; $i < count($sanitizedData["attachments"]); $i++ ) {

            $decoded_attachment = base64_decode($sanitizedData["attachments"][$i]);

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $detected_format = $finfo->buffer($decoded_attachment);

            if(in_array($detected_format, $allowed_files_formats)) {

                $filename = $sanitizedData["name"] . "_" . $sanitizedData["subject"] . "_" . bin2hex(random_bytes(1));

                $extension = "." . array_search($detected_format, $allowed_files_formats);

                $file_dir = $target_dir . $filename . $extension;

                $temp = file_put_contents( $file_dir, $decoded_attachment );
                
                if( $temp ) {

                    $finfo = finfo_open(FILEINFO_NONE);
                    $tempFile = finfo_file( $finfo, $file_dir );

                }
            }
                
            
            $sanitizedData["attachments"][$i] = $file_dir;
        }
        
        return $sanitizedData;
    }


    if( $_SERVER["REQUEST_METHOD"] === "POST") {

        $adminId = $model->routeRequireValidation();

        $admin = $findAdmin->adminInfo( $adminId );
        
        $data = json_decode(file_get_contents("php://input"), TRUE);

        $sanitizedData = sanitize($data);

        $transformedData = attachmentsTransformation($sanitizedData);

        if(  validate( $sanitizedData )  && !empty( $admin ) ) {

            $sentEmail = $model->sendEmail( $admin, $transformedData );
            
            foreach( $transformedData["attachments"] as $attachments ) {

                unlink( $attachments );

            }

            if( $sentEmail ) {

                http_response_code(202);
                die('{"message":"Email sent with success"}');

            } else {

                http_response_code(400);
                die('{"message":"Ooops something went wrong"}');
            }

        } else {

            http_response_code(400);
            die('{"message":"Wrong Information"}');
        }


        

    } else {

        http_response_code(405);
        die('{"message": "Method Not Allowed"}');

    }