<?php
include '../php/header.php';

$sort_by = $_GET['sort_by'] ?? 'title'; // default sort by title
$sort_order = $_GET['sort_order'] ?? 'asc'; // default sort order is ascending

$allowed_columns = ['title', 'category', 'average_grade'];
$allowed_orders = ['asc', 'desc'];

if (!in_array($sort_by, $allowed_columns) || !in_array($sort_order, $allowed_orders)) {
    die("Invalid sorting parameters.");
}

// adjust your SQL to include the necessary join and sorting
$sql = "SELECT projects.*, AVG(project_grades.grade) AS average_grade 
        FROM projects 
        LEFT JOIN project_grades ON projects.id = project_grades.project_id 
        WHERE projects.user_id = ? 
        GROUP BY projects.id 
        ORDER BY {$sort_by} {$sort_order}";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultProjects = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../mesProjets.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Mes Projets</title>
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
        <section class="main_heading">
            <h1>Mes Projets</h1>
            <ul>
                <li><?php echo $userData['name']; ?> | <?php echo $userData['class']; ?> | <?php echo $userData['groupe']; ?></li>
            </ul>
        </section>

        <article class="myProjectsGallery">
            <section class="ProjectsSortBts">
                
                <a href="#" onclick="listAppear()">
                    <i class="fa-solid fa-list"></i>
                </a>
                <a href="#" class="displayIconsBtn" onclick="iconsAppear()">
                    <i class="fa-regular fa-square"></i>
                    <i class="fa-regular fa-square"></i>
                    <i class="fa-regular fa-square"></i>
                    <i class="fa-regular fa-square"></i>
                </a>

                <a href="#" class="deleteBtn" id="deleteBtn" onclick="afficherSupp()"><i class="fa-sharp fa-solid fa-trash"></i></a>
                <a href="./ajouter.php" class="addProject"><i class="fa-solid fa-plus"></i></a>
            </section>

            <article class="galleryList" id="galleryList">
             

                <form method="post" action="../php/delete_project.php">
                    <table>
                        <thead>
                        <tr>
                            <th class="checkbox"></th>
                            <th><a href="?sort_by=title&sort_order=<?php echo $sort_by == 'title' && $sort_order == 'asc' ? 'desc' : 'asc'; ?>">Titre</a></th>
                            <th><a href="?sort_by=category&sort_order=<?php echo $sort_by == 'category' && $sort_order == 'asc' ? 'desc' : 'asc'; ?>">Compétences</a></th>
                            <th>AC</th>
                            <th><a href="?sort_by=average_grade&sort_order=<?php echo $sort_by == 'average_grade' && $sort_order == 'asc' ? 'desc' : 'asc'; ?>">Note</a></th>
                        </tr>
                    </thead>
                    
                         <tbody>
                         <?php
                        // Loop through all projects
                        mysqli_data_seek($resultProjects, 0);
                        while ($project = mysqli_fetch_assoc($resultProjects)) {
                            $projectId = $project['id'];
                            
                            // Get the average grade of the project
                            $sql = "SELECT AVG(grade) AS average_grade FROM project_grades WHERE project_id = $projectId";
                            $resultGrade = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($resultGrade);
                            $averageGrade = round($row['average_grade'], 1);

                            // Get the ACs for this project
                            $sql = "SELECT project_grades.grade, assessment_criteria.name AS ac_name 
                                    FROM project_grades 
                                    INNER JOIN assessment_criteria ON project_grades.ac_id = assessment_criteria.id
                                    WHERE project_grades.project_id = $projectId";
                            $resultACs = mysqli_query($conn, $sql);
                            
                            // Prepare a string of all ACs, only displaying the first 7 characters of each AC
                            $acDisplay = "";
                            while ($row = mysqli_fetch_assoc($resultACs)) {
                                $acDisplay .= substr($row['ac_name'], 0, 7) . ', ';
                            }
                            // Remove the last comma and space from the ACs string
                            $acDisplay = rtrim($acDisplay, ', ');

                            // Display project row with clickable link
                            echo "<tr onclick='window.location=\"./projet.php?id={$project['id']}\"'>";
                            echo "<td class='checkbox'><input type='checkbox' name='project_ids[]' onclick='event.stopPropagation()' value='{$project['id']}' /></td>";
                            echo "<td>{$project['title']}</td>";
                            echo "<td>{$project['category']}</td>";
                            echo "<td>{$acDisplay}</td>";
                            echo "<td>$averageGrade</td>";
                            echo "</tr>";
                        }
                        
                        ?>
                    </tbody>
                    </table>
                    <input type="submit" name="delete" value="Delete" class="checkbox checkboxBtn"/>
                </form>

            </article>

            <article class="gallery galleryIcons" id="galleryIcons" >
                <?php
                mysqli_data_seek($resultProjects, 0);
                // Loop through all projects
                while ($project = mysqli_fetch_assoc($resultProjects)) {
                    $projectId = $project['id'];
                    
                    // Get the average grade of the project
                    $sql = "SELECT AVG(grade) AS average_grade FROM project_grades WHERE project_id = $projectId";
                    $resultGrade = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($resultGrade);
                    $averageGrade = round($row['average_grade'], 1);

                    // Get the ACs for this project
                    $sql = "SELECT project_grades.grade, assessment_criteria.name AS ac_name 
                            FROM project_grades 
                            INNER JOIN assessment_criteria ON project_grades.ac_id = assessment_criteria.id
                            WHERE project_grades.project_id = $projectId";
                    $resultACs = mysqli_query($conn, $sql);

                    // Start card
                    echo "<section class='projectIcon'>";
                    
                    // Display main image
                    echo "<img src='../img_projets/{$project['main_image']}' alt='Image Projet'>";

                    echo "<a href='./projet.php?id={$project['id']}' class='projectIconText'>";

                    echo "<div class='projectIconText_section'>";



                    echo "<div class='porjectIconText_Title'>";

                    echo "<h1>{$project['category']}</h1>";

                    echo "<h2>{$project['title']}</h2>";

                    echo "</div>";

                    echo "<p class='porjectIconText_Note'>$averageGrade / 3</p>";

                    echo "</div>";
                    // Display the ACs and their grades

                    echo "<div class='projectIconText_Grade'>";
                    echo "<table>";
                    echo "<tbody>";
                    while ($row = mysqli_fetch_assoc($resultACs)) {
                        echo "<tr>";
                        echo "<td class='projectIconCompetence'>{$row['ac_name']}</td> 
                        <td>{$row['grade']}</td>";
                        echo "</tr>";
                    }

                    // End card
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                    echo "</a>";
                    echo "</section>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </article>
            
        </article>
    </main>
    <!-------------------- MAIN END ------------------>
    
    

    <script src="../script.js"></script>
    
</body>
</html>