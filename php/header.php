<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

// Assuming you already have a connection to the database
$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION["id"];

// Query user info
$sqlUser = "SELECT * FROM users WHERE id='$userId'";
$resultUser = mysqli_query($conn, $sqlUser);
$userData = mysqli_fetch_assoc($resultUser);

?>