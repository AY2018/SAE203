<?php
session_start();

$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = mysqli_real_escape_string($conn, $_POST["username"]);
  $password = mysqli_real_escape_string($conn, $_POST["password"]);

  $sql = "SELECT id FROM users WHERE username = '$username' AND password = '$password'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $row["id"];
    $_SESSION["username"] = $username;

    header("location: dashboard.php"); // redirect to user dashboard
  } else {
    echo "<p class='failedLogin'> <i class='fa-solid fa-x'></i> Connexion échouée</p>";
    }
}

mysqli_close($conn);
?>



<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>
  <form action="login2.php" method="post">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br>
    <input type="submit" value="Login">
  </form>
</body>
</html>
