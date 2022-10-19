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


?>
