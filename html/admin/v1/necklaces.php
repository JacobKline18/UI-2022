<?php

require 'functions.php';

switch ($_SERVER['REQUEST_METHOD']) {

    // CREATE (POST)
    case 'POST':

        if(empty($_POST['type']) || empty($_POST['price']) ||
        empty($_POST['quantity'])){
            send_http_response(400, "Bad Request - Must provide type, "
            ."price, and quantity to create necklace");
        }

        $sql = [];
        $parameters = [];

        if (!empty($_POST['brand'])){
            $sql[] = "brand";
            $parameters[] = $_POST['brand'];
        }
        if (!empty($_POST['type'])){
            $sql[] = "type";
            $parameters[] = $_POST['type'];
        }
        if (!empty($_POST['material'])){
            $sql[] = "material";
            $parameters[] = $_POST['material'];
        }
        if (!empty($_POST['gem'])){
            $sql[] = "gem";
            $parameters[] = $_POST['gem'];
        }
        if (!empty($_POST['imgPath'])){
            $sql[] = "imgPath";
            $parameters[] = $_POST['imgPath'];
        }
        if (!empty($_POST['description'])){
            $sql[] = "description";
            $parameters[] = $_POST['description'];
        }
        $sql[] = "price";
        $parameters[] = $_POST['price'];
        $sql[] = "quantity";
        $parameters[] = $_POST['quantity'];

        $query = "INSERT INTO Necklaces";
        if ($sql) {
            $query .= ' (' . implode(', ', $sql).") values ("
                .str_repeat('?, ', (count($parameters) - 1)) . "?)";
        }
        $stmt = $mysqli->prepare($query);
        if ($parameters) {
            $stmt->bind_param(str_repeat('s', (count($parameters) - 1)) 
                . "i", ...$parameters);
        }

        if($stmt->execute()){
            send_http_response(201, "Necklace has been created");
        }
        else if($mysqli->error){
            send_http_response(500, "Something went wrong with ".$sql." ".
            $mysqli->error);
        }
        else{
            send_http_response(500, "Internal Server Error");
        }

        break;

    // READ (GET)
    case 'GET':

        $sql = [];
        $parameters = [];

        if (empty($_GET)){
            // if no search params passed, return all necklaces
            $sql[] = " 1";
        }
        elseif (!empty($_GET['necklace_id'])){
            // if necklace_id is passed return only that necklace
            $sql[] = " necklace_id = ?";
            $parameters[] = $_GET['necklace_id'];            
        }
        elseif(empty($_GET['necklace_id'])){
            // combine passed params, search, and return results
            if (!empty($_GET['type'])){
                $sql[] = " type LIKE ?";
                $parameters[] = "%" . $_GET['type'] . "%";
            }
            if (!empty($_GET['material'])){
                $sql[] = " material LIKE ?";
                $parameters[] = "%" . $_GET['material'] . "%";
            }
            if (!empty($_GET['gem'])){
                $sql[] = " gem LIKE ?";
                $parameters[] = "%" . $_GET['gem'] . "%";
            }

        }
        else{
            send_http_response(400, "Bad Request - Invalid search parameters");
        }

        $query = "SELECT * FROM Necklaces";
        if ($sql) {
            $query .= ' WHERE ' . implode(' OR ', $sql);
        }
        $stmt = $mysqli->prepare($query);
        if ($parameters) {
            if (!empty($_GET['necklace_id'])){
                $stmt->bind_param("i", $parameters);
            }
            else{
                $stmt->bind_param(str_repeat('s', count($parameters)),
                ...$parameters);
            }
        }

        // execute sql query and send appropriate http response
        if($stmt->execute()){
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            if(empty($data)){
                send_http_response(202, "No necklace found with parameters: ".
                    implode(',', $parameters));
            }
            send_http_response(200, $data);
        }
        else if($mysqli->error){
            send_http_response(500, "Something went wrong with ".$sql." ".
            $mysqli->error);
        }
        else{
            send_http_response(500, "Internal Server Error");
        }

        break;

    // UPDATE (PUT)
    case 'PUT':

        $_PUT = json_decode(file_get_contents("php://input"), true);
    
        // confirm that user id is passed
        if(empty($_PUT['necklaceId'])){
            send_http_response(400, "Bad Request - must provide necklaceId of necklace
            to be modified");
        }

        // confirm an updatable parameter has been passed (for admin page)
        if(empty($_PUT['brand']) && empty($_PUT['type']) &&
        empty($_PUT['material']) && empty($_PUT['gem']) && empty($_PUT['imgPath']) &&
        empty($_PUT['description']) && empty($_PUT['price']) && empty($_PUT['quantity'])){
            send_http_response(400, "Bad Request - must provide at least one parameter to update");
        }

        $sql = [];
        $parameters = [];
            
        if (!empty($_PUT['brand'])){
            $sql[] = " brand = ?";
            $parameters[] = $_PUT['brand'];
        }
        if (!empty($_PUT['type'])){
            $sql[] = " type = ?";
            $parameters[] = $_PUT['type'];
        }
        if (!empty($_PUT['material'])){
            $sql[] = " material = ?";
            $parameters[] = $_PUT['material'];
        }
        if (!empty($_PUT['gem'])){
            $sql[] = " gem = ?";
            $parameters[] = $_PUT['gem'];
        }
        if (!empty($_PUT['imgPath'])){
            $sql[] = " imgPath = ?";
            $parameters[] = $_PUT['imgPath'];
        }
        if (!empty($_PUT['description'])){
            $sql[] = " description = ?";
            $parameters[] = $_PUT['description'];
        }
        if (!empty($_PUT['price'])){
            $sql[] = " price = ?";
            $parameters[] = $_PUT['price'];
        }
        if (!empty($_PUT['quantity'])){
            $sql[] = " quantity = ?";
            $parameters[] = $_PUT['quantity'];
        }

        $query = "UPDATE Necklaces";
        if ($sql) {
            $query .= ' SET ' . implode(', ', $sql);
        }
        $query .= ' WHERE necklace_id = ' . $_PUT['necklaceId'];
        echo "Query is " . $query;
        $stmt = $mysqli->prepare($query);
        if ($parameters) {
            $stmt->bind_param(str_repeat('s', count($parameters)),
            ...$parameters);
        }

        // execute sql query and send appropriate http response
        if($stmt->execute()){
            $num_affected_rows = $mysqli -> affected_rows;
            if($num_affected_rows == 0){
                send_http_response(200, "No necklaces updated");
            }
            send_http_response(200, $num_affected_rows . " necklaces updated");
        }
        else if($mysqli->error){
            send_http_response(500, "Something went wrong with ".$sql." ".
            $mysqli->error);
        }
        else{
            send_http_response(500, "Internal Server Error");
        }
        break;

    // DELETE (DELETE)
    case 'DELETE':

        $_DELETE = json_decode(file_get_contents("php://input"), true);

        if(empty($_DELETE['necklaceId'])){
            send_http_response(400, "Bad Request - must provide userId of user
            to be deleted");
        }
        
        // form sql query string
        $sql = "DELETE from Necklaces where necklace_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $_DELETE['necklaceId']);

        // execute sql query and send appropriate http response
        if($stmt->execute()){
            send_http_response(200, "User has been deleted");
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
