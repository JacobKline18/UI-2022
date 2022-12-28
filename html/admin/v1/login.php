<?php

    require 'functions.php';

    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        send_http_response(405, 'Method not allowed');
    }

    if(empty($_POST['email']) || empty($_POST['password'])){
        send_http_response(400, "Bad Request - Missing email or password");
    }

    $sql = "SELECT password, user_id, isAdmin from Users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $_POST['email']);

    if($stmt->execute()){
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        if(empty($data) || (sizeof($data) != 1)){
            send_http_response(400, "User does not exist");
        }
    }
    else if($mysqli->error){
        send_http_response(500, "Something went wrong with ".$sql." ".
        $mysqli->error);
    }
    else{
        send_http_response(500, "Internal Server Error");
    }

    if($_POST["password"] != $data[0]["password"] || 
        $data[0]["isAdmin"] != "yes"){
        send_http_response(401, "Invalid login credentials");
    }

    $sessionName = create_user_session($mysqli, $data[0]["user_id"]);

    send_http_response(200, "$sessionName");

?>
