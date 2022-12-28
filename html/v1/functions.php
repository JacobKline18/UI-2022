<?php

// TODO: in create_user_session, loop to check for collisions and regenerate 
// TODO: add function to hash password
// TODO: add a function to validate sessionName

$hostname="localhost";
$username="azureuser";
$password="uiProject4393!";
$db="uiProject";
$mysqli=new mysqli($hostname,$username,$password,$db);
if (mysqli_connect_errno())
{
    die("Error connecting to database: ".mysqli_connect_errno());    
}

function send_http_response($response_code, $response_message){
    http_response_code($response_code);
    die(json_encode(array(
        "status" => $response_code,
        "response" => $response_message), JSON_FORCE_OBJECT));
}

function create_user_session($mysqli, $user_id){
    $sessionName = random_str();
    $createTime = time();
    $expireTime = $createTime + 86400;

    $sql = "INSERT INTO Sessions (user_id, sessionName, createTime, expireTime)
        values (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("isii", $user_id, $sessionName, $createTime, $expireTime);

    if($stmt->execute()){
        return $sessionName;
    }
    else if($mysqli->error){
        send_http_response(500, "Something went wrong with ".$sql." ".
        $mysqli->error);
    }
    else{
        send_http_response(500, "Internal Server Error");
    }

}

function random_str(){
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ';
    $length = 64;
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

?>
