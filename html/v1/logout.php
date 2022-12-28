<?php

// TODO: fix error message \n, figure out multiline strings
// SOLUTION: 
// send_http_response(400, "Bad Request - Must provide type, "
// ."price, and quantity to create necklace");

require 'functions.php';

switch ($_SERVER['REQUEST_METHOD']) {

    // DELETE (DELETE)
    case 'DELETE':

        $_DELETE = json_decode(file_get_contents("php://input"), true);

        if(empty($_DELETE['sessionName'])){
            send_http_response(400, "Bad Request - must provide userId of user
            to be deleted");
        }
        
        // form sql query string
        $sql = "DELETE from Sessions where sessionName = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $_DELETE['sessionName']);

        // execute sql query and send appropriate http response
        if($stmt->execute()){
            send_http_response(200, "Session has been deleted");
        }
        else if($mysqli->error){
            send_http_response(500, "Something went wrong with ".$sql." ".
            $mysqli->error);
        }
        else{
            send_http_response(500, "Internal Server Error");
        }

        break;

    default:
        send_http_response(405, 'Method not allowed');

    }
?>