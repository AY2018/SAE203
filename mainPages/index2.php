<?php
include '../php/header.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

// Connect to the database
$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
    die("Connection failed: " + $conn->connect_error);
}

$userId = $_SESSION["id"];

// Retrieve the user data
$userData = mysqli_query($conn, "SELECT name, class, groupe FROM users WHERE id = '$userId'");
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



// Close the database connection
mysqli_close($conn);

?>

<!-- Display the information -->
<html>
<!-- Some head elements here -->
<body>
    <h1>Welcome, <?php echo $userData['name']; ?></h1>
    <p>Class: <?php echo $userData['class']; ?></p>
    <p>Group: <?php echo $userData['groupe']; ?></p>

    <h2>Your latest project</h2>
    <h3><?php echo $latestProject['title']; ?></h3>
    <img src="../img_projets/<?php echo $latestProject['main_image']; ?>">
    <p><?php echo $latestProject['description']; ?></p>
    <p>ACs:</p>
    <ul>
    <?php foreach ($acs as $ac): ?>
        <li><?php echo $ac['name']; ?>:: Grade <?php echo $ac['grade']; ?></li>
    <?php endforeach; ?>
    </ul>

    <h2>Your mean grades</h2>
    <?php foreach ($meanGrades as $category => $meanGrade): ?>
        <p><?php echo $category; ?>: <?php echo $meanGrade; ?></p>
    <?php endforeach; ?>

    <h2>Your rankings</h2>
    <?php foreach ($rankings as $category => $ranking): ?>
        <p><?php echo $category; ?>: <?php echo $ranking; ?> / <?php echo $total_students; ?></p>
    <?php endforeach; ?>
</body>
</html>
