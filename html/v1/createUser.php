<?php

require 'functions.php';

// $sql="Select * from `Users`";

// $results=$mysqli->query($sql) or 
//     die("Something went wrong with $sql ".$mysqli->error);

// while ($rows = $results->fetch_assoc()){
//     echo "{$rows['firstname']}";
// }

// echo "You made it to the createUser endpoint!"

$sql="Insert into `Users` (`firstName`, `lastName`, `email`, `password`) values 
    ('".$_POST['firstName']."', '".$_POST['lastName']."', '".$_POST['email']."', '".$_POST['password']."')";

$mysqli->query($sql) or 
    die("Something went wrong with $sql ".$mysqli->error);

echo "<p>Executed $sql</p>";

$mysqli->close();
?>
