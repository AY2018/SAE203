<?php
include '../php/header.php';
?>

<?php
// Retrieve the project information from the database based on the project ID
$projectId = $_GET['id'];

$sql = "SELECT * FROM projects WHERE id = $projectId";
$result = mysqli_query($conn, $sql);
$project = mysqli_fetch_assoc($result);

if (!$project) {
    // Project with the specified ID does not exist
    echo "Project not found";
    exit;
}

/* Moyenne */
$sql = "SELECT AVG(grade) AS average_grade FROM project_grades WHERE project_id = $projectId";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$averageGrade = round($row['average_grade'], 1);

// Display the Traces (files and/or links)
$sql = "SELECT * FROM project_traces WHERE project_id = $projectId AND trace_type = 'link'";
$result = mysqli_query($conn, $sql);

// Link

$lienDuSite = "<p>Pas de lien</p>"; // Initialize with 'no link' message

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);  // Fetch the first row
    $lienDuSite = "<a target='_blank' href='{$row['trace']}'>Aller au site</a>";
} 


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../projet.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>PHP</title>
</head>
<body>
    <!-------------------- HEADER START ------------------>
    <header>
        <section class="profile">
            <a href="#" class="settingsbutton"><i class="fa-solid fa-gear"></i></a>
            <img src="../img/<?php echo $userData['profile_picture']; ?>" alt="profilePicture">
            <h1><?php echo $userData['name']; ?></h1>
            <h2><?php echo $userData['class']; ?> - <?php echo $userData['groupe']; ?></h2>
        </section>
        <hr>

        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li class="active"><a href="./mesProjets.php">Projets</a></li>
                <li><a href="./ajouter.php">Ajouter Projet</a></li>
            </ul>
        </nav>
        <hr>
        <a href="https://cas2.uvsq.fr/cas/login?service=https%3A%2F%2Fbulletins.iut-velizy.uvsq.fr%2Fservices%2FdoAuth.php%3Fhref%3Dhttps%253A%252F%252Fbulletins.iut-velizy.uvsq.fr%252F%253Flogin" target="_blank">
            <img src="../img/iutLogo.png" alt="logo of school">
        </a>
        

        <section class="buttons">
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
            <a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
        </section>
    </header>
    <!-------------------- HEADER END ------------------>

    <!-------------------- MAIN START ------------------>
    <main>
        <section class="tracesShowcase" id="tracesShowcase">
            <article class="traces">
                <i class="fa-solid fa-x" onclick="closeTraces()"></i>
                <section class="gallery">
                    <h1>Gallerie</h1>
                    <div class="gallerieShowcase">
                        <?php
                        $sql = "SELECT * FROM project_traces WHERE project_id = $projectId";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($row['trace_type'] === 'file') {
                                    // Get file extension
                                    $fileExtension = strtolower(pathinfo($row['trace'], PATHINFO_EXTENSION));
                                    // Define array of image extensions
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    // Check if file is an image
                                    if (in_array($fileExtension, $imageExtensions)) {
                                        echo "<img src='../traces/{$row['trace']}' alt='image travail'>";
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                    
                </section>

                <div class="secondRow">
                    <section class="fichiers">
                        <h1>Fichiers</h1>
                        <?php
                        $sql = "SELECT * FROM project_traces WHERE project_id = $projectId";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($row['trace_type'] === 'file') {
                                    // Get file extension
                                    $fileExtension = strtolower(pathinfo($row['trace'], PATHINFO_EXTENSION));
                                    // Define array of image extensions
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    // Check if file is not an image
                                    if (!in_array($fileExtension, $imageExtensions)) {
                                        echo "<div class='rowFichiers'>
                                                <p>{$row['trace']}</p>
                                                <a href='../traces/{$row['trace']}' download><i class='fa-solid fa-download'></i></a>
                                            </div>";
                                    }
                                }
                            }
                        }
                        ?>
                </section>

                </div>
            </article>
        </section>

        <section class="tracesShowcase" id="SupprimerArticle">
            <article class="suppSection">
                <h1>Êtes-vous sûr de vouloir supprimer ce projet ?</h1>
                <div class="secondRow">
                    <form method="post" action="../php/delete_project.php">
                        <input type="hidden" name="id" value="<?php echo $projectId; ?>">
                        <button type="submit" class="BtnSupp">Oui</button>
                    </form>
                    <button onclick="closeSupprimer()">Non</button>
                </div>
            </article>
        </section>

        <section class="main_heading">
            <h1>
                <a href="./mesProjets.php"><i class="fa-solid fa-arrow-left"></i></a>
                <?php echo $project['title']; ?></h1>
            <ul>
                <li class="ProjetSoloCompetence"><?php echo $project['category']; ?></li>
                <li><?php echo $userData['name']; ?> | <?php echo $userData['class']; ?> | <?php echo $userData['groupe']; ?></li>
            </ul>
        </section>

        <article class="contentProjetSolo">
            
            <section class="ProjetSoloText">
                <h1><?php echo $project['category']; ?></h1>
                <div class="acSection">
                <?php 
                    $sql = "SELECT project_grades.grade, assessment_criteria.name AS ac_name 
                            FROM project_grades 
                            INNER JOIN assessment_criteria ON project_grades.ac_id = assessment_criteria.id
                            WHERE project_grades.project_id = $projectId";
                    $result = mysqli_query($conn, $sql);

                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="row">
                            <p><?php echo $row['ac_name']; ?></p>
                            <div class="grade">
                            <?php
                                for($i=0; $i < $row['grade']; $i++){ echo '<i class="fa-solid fa-star"></i>'; }
                                for($i=$row['grade']; $i < 3; $i++){ echo '<i class="fa-regular fa-star"></i>'; }
                            ?>
                            </div>
                        </div>
                <?php } ?>
                </div>
            </section>

            <section class="mainImg">
                <img src="../img_projets/<?php echo $project['main_image']; ?>" alt="Main Image of the Project">
            </section>
        </article>

        <section class="extras">
            <div class="firstColumn">
                <div class="firstColumnFirstRow">
                    <section class="moyenne">
                        <h1><?php echo $averageGrade; ?> / 3</h1>
                    </section>
                    <section class="classement">
                        <?php echo $lienDuSite; ?>
                    </section>
                </div>
                <section class="firstColumnLastRow description">
                    <h1>Description</h1>
                    <p><?php echo $project['description']; ?></p>
                </section>
            </div>

            <div class="secondColumn">

                <a href="#" class="secondColumnFirstRow" onclick="openTraces()">Traces</a>

                <section class="secondColumnLastRow">
                    <a href="./modifier.php?id=<?php echo $projectId; ?>" class="change"><i class="fa-solid fa-gear"></i></a>

                    
                    <a href="#" class="delete" onclick="openSupprimer()"><i class="fa-sharp fa-solid fa-trash"></i></a>
                </section>

            </div>

        </section>
    </main>
    <!-------------------- MAIN END ------------------>
    
    

    <script src="../script.js"></script>
    
</body>
</html>