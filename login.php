<?php
// Commencer une session
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


// Si l'utilisateur a soumis le formulaire de connexion
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $mdp = $_POST['password']; 

    // Connection 
    $conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

    // Query the database for user
    $sql = "SELECT id FROM users WHERE username = '$username' AND password = '$mdp'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $row["id"];
    $_SESSION["username"] = $username;

    header("Location: index.php");
    exit();
} 

else {
    echo "<p class='failedLogin'> <i class='fa-solid fa-x'></i> Connexion échouée</p>";
}


    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Login</title>
</head>
<body>
    <form action="login.php" method="POST" class="loginForm">
    <p>
        <label>Nom d'utilisateur</label>
        <input type="text" name="username" required>
    </p>
    <p>
        <label>Mot de Passe</label>
        <input type="text" name="password" required>
    </p>
    <p>
        <input type="submit" value="Login" id="btn">
    </p>
</form>
</body>
</html>
