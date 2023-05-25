<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION["id"];

if (isset($_POST['BtnAjouterUnProjet'])){
    $conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

    $title = $_POST["title"];
    $category = $_POST["category"];

    $description = $_POST["description"];

    // Insérer image 
    $image = $_FILES['image']['name'];
    $target_dir = "../img_projets/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);


    // Déplacer l'image téléchargée vers le dossier des téléchargements
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    mysqli_query($conn, "INSERT INTO projects (title, category, main_image, description, user_id) VALUES ('$title', '$category', '$image', '$description', '$userId')") or die(mysqli_error($conn));


    if (isset($_POST['BtnAjouterUnProjet'])){

    // Get the project's ID
    $projectId = mysqli_insert_id($conn);

    // Insert each AC and its grade
    foreach ($_POST['acs'] as $index => $acId) {
        $grade = $_POST['grades'][$index];
        $sql = "INSERT INTO project_grades (project_id, ac_id, grade) VALUES ('$projectId', '$acId', '$grade')";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }

    // Insert the link as a trace
    if (!empty($_POST['link'])) {
        $link = $_POST['link'];
        $sql = "INSERT INTO project_traces (project_id, trace, trace_type) VALUES ('$projectId', '$link', 'link')";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }

    // Insert each file as a trace
    foreach ($_FILES['traces']['name'] as $index => $filename) {
        $target_file = $target_dir . basename($filename);
        move_uploaded_file($_FILES['traces']['tmp_name'][$index], $target_file);
        $sql = "INSERT INTO project_traces (project_id, trace, trace_type) VALUES ('$projectId', '$filename', 'file')";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }
}

}


$sqlACs = "SELECT * FROM assessment_criteria";
$resultACs = mysqli_query($conn, $sqlACs);
$acs = mysqli_fetch_all($resultACs, MYSQLI_ASSOC);

$projectId = 7;

$sql = "SELECT ROUND(AVG(grade), 1) AS average_grade FROM project_grades WHERE project_id = $projectId";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $averageGrade = $row['average_grade'];
    echo "The average grade for the project is: " . $averageGrade;
} else {
    echo "No grades found for this project";
}


mysqli_close($conn);


?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../ajouter.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    


    <title>Ajouter un Projet</title>
</head>
<body>
    <!-------------------- HEADER START ------------------>
    <header>
        <section class="profile">
            <a href="#" class="settingsbutton"><i class="fa-solid fa-gear"></i></a>
            <img src="../img/tpPhp.png" alt="profilePicture">
            <h1>Ayoub Kahfy</h1>
            <h2>MMI 1 - B2</h2>
        </section>
        <hr>

        <nav>
            <ul>
                <li><a href="../index.html">Home</a></li>
                <li><a href="./mesProjets.html">Projets</a></li>
                <li class="active"><a href="#">Ajouter Projet</a></li>
                <li><a href="#">Compétences</a></li>
            </ul>
        </nav>
        <hr>
        <a href="https://cas2.uvsq.fr/cas/login?service=https%3A%2F%2Fbulletins.iut-velizy.uvsq.fr%2Fservices%2FdoAuth.php%3Fhref%3Dhttps%253A%252F%252Fbulletins.iut-velizy.uvsq.fr%252F%253Flogin" target="_blank">
            <img src="../img/iutLogo.png" alt="logo of school">
        </a>
        

        <section class="buttons">
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
            <a href="#"><i class="fa-solid fa-right-from-bracket"></i></a>
        </section>
    </header>
    <!-------------------- HEADER END ------------------>

    <!-------------------- MAIN START ------------------>
    <main>
        <section class="main_heading">
            <h1>Ajouter un Projet</h1>
            <ul>
                <li>Ayoub Kahfy | MMI 1 | B2</li>
                <li>2022 - 2023</li>
            </ul>
        </section>
        <article class="addProjectArticle">
            <form action="addProject.php" method="post" enctype="multipart/form-data">
                
        <label for="title">Project Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="Comprendre">C1 - Comprendre</option>
            <option value="Concevoir">C2 - Concevoir</option>
            <option value="Exprimer">C3 - Exprimer</option>
            <option value="Développer">C4 - Développer</option>
            <option value="Entreprendre">C5 - Entreprendre</option>
        </select><br>

        <label for="image">Main Image:</label>
        <input type="file" id="image" name="image"><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br>

        <label for="ac">Assessment Criteria:</label>
            <div id="ac-fields">
                <div class="ac-field">
                    <select class="ac" name="acs[]">
                    <?php foreach ($acs as $ac): ?>
                        <option value="<?php echo $ac['id']; ?>"><?php echo $ac['name']; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <input type="number" class="grade" name="grades[]" min="1" max="3">
                </div>
            </div>

    <button type="button" id="add-ac">Add Another AC</button><br>
        <label for="traces">Traces:</label>
        <input type="file" id="traces" name="traces[]" multiple><br>
        <label for="link">Link:</label>
        <input type="text" id="link" name="link"><br>
        <input type="submit" value="Add Project" name="BtnAjouterUnProjet">
    </form>
        </article>
    </main>
    <!-------------------- MAIN END ------------------>
    
    

    <script src="../script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });


        document.getElementById('add-ac').addEventListener('click', function() {
    // Create new select and input elements for the new AC and grade
    var newAcField = document.createElement('div');
    newAcField.classList.add('ac-field');

    var newAc = document.createElement('select');
    newAc.name = 'acs[]';
    newAc.classList.add('ac');
    newAcField.appendChild(newAc);

    var newGrade = document.createElement('input');
    newGrade.type = 'number';
    newGrade.name = 'grades[]';
    newGrade.min = '1';
    newGrade.max = '3';
    newGrade.classList.add('grade');
    newAcField.appendChild(newGrade);

    // Append the new select and input elements to the ac-fields div
    document.getElementById('ac-fields').appendChild(newAcField);

    // Add options to the new select element
    var options = document.querySelector('.ac').innerHTML;
    newAc.innerHTML = options;
});

    </script>

    
</body>
</html>