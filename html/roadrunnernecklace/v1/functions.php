<?php

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
    header($_SERVER['SERVER_PROTOCOL'].' '.$response_code.' '.$response_message);
    die();
}


?>

