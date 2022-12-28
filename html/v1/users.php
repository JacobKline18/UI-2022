<?php

// TODO: fix error message \n, figure out multiline strings
// SOLUTION: 
// send_http_response(400, "Bad Request - Must provide type, "
// ."price, and quantity to create necklace");

require 'functions.php';

switch ($_SERVER['REQUEST_METHOD']) {

    // CREATE (POST)
    case 'POST':

        if (!empty($_POST['isAdmin'])) {
            // confirm all required parameters are included in POST data
            if(empty($_POST['firstName']) || empty($_POST['lastName']) ||
            empty($_POST['email']) || empty($_POST['password']) || empty($_POST['isAdmin'])){
                send_http_response(400, "Bad Request - Must provide firstname, 
                lastname, email, password, and if they have admin rights to create user");
            }

            // TODO: Add check to ensure the email does not already exist in the db

            // form sql query string

            $sql = "INSERT INTO `Users` (`firstName`, `lastName`, `email`,
            `password`, `isAdmin`) values (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss", $_POST['firstName'], $_POST['lastName'], 
            $_POST['email'], $_POST['password'], $_POST['isAdmin']);

            // execute sql query and send appropriate http response
            if($stmt->execute()){
                send_http_response(201, "User has been created");
            }
            else if($mysqli->error){
                send_http_response(500, "Something went wrong with ".$sql." ".
                $mysqli->error);
            }
            else{
                send_http_response(500, "Internal Server Error");
            }
            break;
        }

        // confirm all required parameters are included in POST data
        if(empty($_POST['firstName']) || empty($_POST['lastName']) ||
        empty($_POST['email']) || empty($_POST['password'])){
            send_http_response(400, "Bad Request - Must provide firstname, 
            lastname, email, and password to create user");
        }

        // TODO: Add check to ensure the email does not already exist in the db

        // form sql query string

        $sql = "INSERT INTO `Users` (`firstName`, `lastName`, `email`,
        `password`) values (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $_POST['firstName'], $_POST['lastName'], 
        $_POST['email'], $_POST['password']);

        // execute sql query and send appropriate http response
        if($stmt->execute()){
            send_http_response(201, "User has been created");
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

        if (!empty($_GET['sid'])){
            $sql = "SELECT user_id from Sessions where sessionName = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $_GET['sid']);
        
            if($stmt->execute()){
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                if(empty($data)){
                    send_http_response(200, "No uid with sid: ".$_GET['sid']);
                }
                send_http_response(200, $data[0]["user_id"]);
            }
            else if($mysqli->error){
                send_http_response(500, "Something went wrong with ".$sql." ".$mysqli->error);
            }
            else{
                send_http_response(500, "Internal Server Error");
            }
        }

        // prepare sql statement
        $sql = [];
        $parameters = [];

        if (!empty($_GET['user_id'])){
            $sql[] = " user_id = ?";
            $parameters[] = $_GET['user_id'];
        }
        if (!empty($_GET['firstName'])){
            $sql[] = " firstName LIKE ?";
            $parameters[] = "%" . $_GET['firstName'] . "%";
        }
        if (!empty($_GET['lastName'])){
            $sql[] = " lastName LIKE ?";
            $parameters[] = "%" . $_GET['lastName'] . "%";
        }
        if (!empty($_GET['email'])){
            $sql[] = " email LIKE ?";
            $parameters[] = "%" . $_GET['email'] . "%";
        }

        // if no searchable parameters passed in, return all users
        if(empty($_GET['firstName']) && empty($_GET['lastName']) &&
        empty($_GET['email']) && empty($_GET['user_id'])){
            $sql[] = " 1";
        }

        $query = "SELECT * FROM Users";
        if ($sql) {
            $query .= ' WHERE ' . implode(' OR ', $sql);
        }
        $stmt = $mysqli->prepare($query);
        if ($parameters) {
            $stmt->bind_param(str_repeat('s', count($parameters)),
            ...$parameters);
        }
        if($stmt->execute()){
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            if(empty($data)){
                send_http_response(200, "No users found with given search "
                ."parameters");
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
        if(empty($_PUT['userId'])){
            send_http_response(400, "Bad Request - must provide userId of user
            to be modified");
        }

        // confirm an updatable parameter has been passed (for admin page)
        if(empty($_PUT['firstName']) && empty($_PUT['lastName']) &&
        empty($_PUT['email']) && empty($_PUT['isAdmin'])){
            send_http_response(400, "Bad Request - must provide email, first
            name, last name, or admin rights to update");
        }

        if(empty($_PUT['firstName']) && empty($_PUT['lastName']) &&
        empty($_PUT['password'])){
            send_http_response(400, "Bad Request - must provide first name, last name, or password to update");
        }

        $sql = [];
        $parameters = [];
            
        if (!empty($_PUT['firstName'])){
            $sql[] = " firstName = ?";
            $parameters[] = $_PUT['firstName'];
        }
        if (!empty($_PUT['lastName'])){
            $sql[] = " lastName = ?";
            $parameters[] = $_PUT['lastName'];
        }
        if (!empty($_PUT['email'])){
            $sql[] = " email = ?";
            $parameters[] = $_PUT['email'];
        }
        if (!empty($_PUT['password'])){
            $sql[] = " password = ?";
            $parameters[] = $_PUT['password'];
        }
        if (!empty($_PUT['isAdmin'])){
            $sql[] = " isAdmin = ?";
            $parameters[] = $_PUT['isAdmin'];
        }

        $query = "UPDATE Users";
        if ($sql) {
            $query .= ' SET ' . implode(', ', $sql);
        }
        $query .= ' WHERE user_id = ' . $_PUT['userId'];
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
                send_http_response(200, "No users updated");
            }
            send_http_response(200, $num_affected_rows . " users updated");
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

        if(empty($_DELETE['userId'])){
            send_http_response(400, "Bad Request - must provide userId of user
            to be deleted");
        }
        
        // form sql query string
        $sql = "DELETE from Users where user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $_DELETE['userId']);

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
