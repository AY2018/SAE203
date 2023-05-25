<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login2.php");
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

// Query user projects

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Welcome, <?php echo $userData['name']; ?>!</h1>
  <p>Your class: <?php echo $userData['class']; ?></p>
  <p>Your group: <?php echo $userData['groupe']; ?></p>
  <p>Your year of study: <?php echo $userData['year_of_study']; ?></p>
  <h2>Your Projects</h2>
  
</body>
</html>
