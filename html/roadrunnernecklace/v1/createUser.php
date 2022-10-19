<?php

require 'functions.php';

switch ($_SERVER['REQUEST_METHOD']) {
    // CREATE (POST)
    case 'POST':
        // check if all required parameters are included in POST data
        if(empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['password'])){
            $response_code = 400;
            $response_message = 'Bad Request - Must provide firstname, lastname, email, and password to create user';
            send_http_response($response_code, $response_message);
        }
        
        // "clean" user inputs (to try to avoid sql injection)
        $firstName = addslashes($_POST['firstName']);
        $lastName = addslashes($_POST['lastName']);
        $email = addslashes($_POST['email']);
        $password = addslashes($_POST['password']);

        // form sql query string
        $sql="Insert into `Users` (`firstName`, `lastName`, `email`, `password`) values 
            ('$firstName', '$lastName', '$email', '$password')";
        
        // execute sql statement
        $result = $mysqli->query($sql);
        
        // send appropriate http response
        if(!$result){
            $response_code = 500;
            $response_message = 'Internal server error';
            echo "Something went wrong with ".$sql." ".$mysqli->error;
            send_http_response($response_code, $response_message);
        }
        
        $response_code = 201;
        $response_message = 'User has been created';
        send_http_response($response_code, $response_message);
        
        break;
    // READ (GET)
    case 'GET':
        break;
    // UPDATE (PUT)
    case 'PUT':
        break;
    // DELETE (DELETE)
    case 'DELETE':
        break;
    default:
        $response_code = 405;
        $response_message = 'Method not allowed';
        send_http_response($response_code, $response_message);

}


?>

