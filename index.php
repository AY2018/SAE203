<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Assuming you already have a connection to the database
$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION["id"];

// Query user projects

// Check if form is submitted
if(isset($_POST['ChangeImg'])) {
    $image = $_FILES['image']['name'];
    $target_dir = "./img/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Move the uploaded image to the uploads directory
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

mysqli_query($conn, "UPDATE users SET profile_picture='$image' WHERE id = '$userId'") or die(mysqli_error($conn));

}


// Retrieve the user data
$userData = mysqli_query($conn, "SELECT name, class, groupe, profile_picture FROM users WHERE id = '$userId'");
$userData = mysqli_fetch_assoc($userData);

// Retrieve the latest project
$latestProject = mysqli_query($conn, "SELECT * FROM projects WHERE user_id = '$userId' ORDER BY id DESC LIMIT 1");
$latestProject = mysqli_fetch_assoc($latestProject);

// Retrieve the ACs of the latest project
$acs = mysqli_query($conn, "SELECT assessment_criteria.name, project_grades.grade FROM project_grades INNER JOIN assessment_criteria ON project_grades.ac_id = assessment_criteria.id WHERE project_grades.project_id = '{$latestProject['id']}'");
$acs = mysqli_fetch_all($acs, MYSQLI_ASSOC);

// Retrieve the mean grade for each category
$categories = ['Comprendre', 'Concevoir', 'Exprimer', 'Développer', 'Entreprendre'];
$meanGrades = [];
foreach ($categories as $category) {
    $grades = mysqli_query($conn, "SELECT AVG(grade) as mean_grade FROM project_grades INNER JOIN projects ON project_grades.project_id = projects.id WHERE projects.category = '$category' AND projects.user_id = '$userId'");
    $grades = mysqli_fetch_assoc($grades);
    $meanGrades[$category] = $grades['mean_grade'];
}

// Extract the averages into separate variables
$comprendreAvg = $meanGrades['Comprendre'];
$concevoirAvg = $meanGrades['Concevoir'];
$exprimerAvg = $meanGrades['Exprimer'];
$developperAvg = $meanGrades['Développer'];
$entreprendreAvg = $meanGrades['Entreprendre'];

// Retrieve the rankings for each category
$rankings = [];
foreach ($categories as $category) {
    $ranks = mysqli_query($conn, "SELECT projects.user_id, AVG(grade) as mean_grade FROM project_grades INNER JOIN projects ON project_grades.project_id = projects.id WHERE projects.category = '$category' GROUP BY projects.user_id ORDER BY mean_grade DESC");

    $ranks = mysqli_fetch_all($ranks, MYSQLI_ASSOC);
    foreach ($ranks as $index => $rank) {
        if ($rank['user_id'] == $userId) {
            $rankings[$category] = $index + 1;
            break;
        }
    }
}

// Retrieve the number of students
$total_students_result = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as total_students FROM projects");
$total_students_data = mysqli_fetch_assoc($total_students_result);
$total_students = $total_students_data['total_students'];



$meanGradesJson = json_encode(array_values($meanGrades));

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link rel="stylesheet" href="./header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Dashboard Portfolio MMI</title>
</head>
<body>
    <!-------------------- HEADER START ------------------>
    
    <header>
        <section class="profile">
            <a href="#" class="settingsbutton" onclick="openpdp()"><i class="fa-solid fa-gear"></i></a>
            <section class="changeImg" id="changeImg">
                <form action="#" method="POST" enctype="multipart/form-data">
                    <i class="fa-solid fa-x" onclick="closepdp()"></i>
                    <label for="image">Photo de Profile</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <input type="submit" name="ChangeImg" value="Changer de Photo de Profile" class="btnChangerProfile">
                </form>
            </section>
            <img src="./img/<?php echo $userData['profile_picture']; ?>" alt="profilePicture">
            <h1><?php echo $userData['name']; ?></h1>
            <h2><?php echo $userData['class']; ?> - <?php echo $userData['groupe']; ?></h2>
        </section>
        <hr>

        <nav>
            <ul>
                <li class="active"><a href="#">Home</a></li>
                <li><a href="./mainPages/mesProjets.php">Projets</a></li>
                <li><a href="./mainPages/ajouter.php">Ajouter Projet</a></li>
            </ul>
        </nav>
        <hr>
        <a href="https://cas2.uvsq.fr/cas/login?service=https%3A%2F%2Fbulletins.iut-velizy.uvsq.fr%2Fservices%2FdoAuth.php%3Fhref%3Dhttps%253A%252F%252Fbulletins.iut-velizy.uvsq.fr%252F%253Flogin">
            <img src="./img/iutLogo.png" alt="logo of school">
        </a>
        

        <section class="buttons">
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
            <a href="./php/logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
        </section>
    </header>
    <!-------------------- HEADER END ------------------>

    <!-------------------- MAIN START ------------------>
    <main>
        <section class="main_heading">
            <h1>Portfolio Dashboard</h1>
            <ul>
                <li><?php echo $userData['name']; ?> | <?php echo $userData['class']; ?> | <?php echo $userData['groupe']; ?></li>
            </ul>
        </section>

        <article class="last_project">

            <section class="last_project_heading">
                <h1>Dernier Projet</h1>
                <a href="./mainPages/mesProjets.php">Mes Projets <i class="fa-solid fa-arrow-right"></i></a>
            </section>

            <img src="./img_projets/<?php echo $latestProject['main_image']; ?>">

            <section class="last_project_footer">
                <h1><?php echo $latestProject['title']; ?></h1>
                <h2>Développement Web</h2>
                <ul>
                    <?php foreach ($acs as $ac): ?>
                    <li><?php echo $ac['name']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

        </article>
        
        <article class="otherArticles">
            <section class="section1">
                <h1>Moyennes pour chaque compétence</h1>
                <canvas id="myChart"></canvas>
            </section>
            
            <div class="otherSections">
                <section class="section2">
                    <h1>Classement <span>(sur <?php echo $total_students; ?> élèves)</span></h1>
                    <ul class="classement_competences">
                        <?php foreach ($rankings as $category => $ranking): ?>
                        <li class="competence"><span><?php echo $category; ?></span> <span><?php echo $ranking; ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="section3">
                    <a href="#"><i class="fa-solid fa-envelope"></i></a>
                </section>
                <section class="section4">
                    <a href="#"><i class="fa-solid fa-right-from-bracket"></i></a>
                </section>
            </div>
            
        </article>
    </main>
    <!-------------------- MAIN END ------------------>
    
    

    <script src="./script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* Chart */

        // Get the PHP data

        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
            labels: ['Comprendre', 'Concevoir', 'Exprimer', 'Développer', 'Entreprendre'],
            datasets: [{
                label: 'Moyenne',
                data: [<?php
                        echo $comprendreAvg.', '.$concevoirAvg.', '.$exprimerAvg.', '.$developperAvg.', '.$entreprendreAvg;
                        ?>],
                        backgroundColor: [
                        'rgba(255, 99, 132, 0.6)'
                    ],
                }],
                borderWidth: 1
            },
            options: {
            responsive: true,
            scales: {
                x: {
                ticks: {
                    color: "white"
                }
                },
                y: {
                beginAtZero: true,
                max : 3,
                ticks: {
                    color: "white"
                }
                }
            }
            }
        });
    </script>
</body>
</html>