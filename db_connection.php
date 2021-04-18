<?php
function OpenCon(){
    $username = "user1";
    $password = "User1@pssw";
    $database = "school_recordings";
    $servername = "localhost";
    $conn = new mysqli($servername, $username, $password,$database) or die("Connect failed: %s\n". $conn -> error);

    return $conn;
}

function CloseCon($conn){
    $conn -> close();
}

?>