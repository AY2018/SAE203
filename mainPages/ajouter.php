<?php
include '../php/header.php';

$acs_result = mysqli_query($conn, "SELECT id, name FROM assessment_criteria"); 
$acs = mysqli_fetch_all($acs_result, MYSQLI_ASSOC);
?>

<?php

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
    $trace_dir = "../traces/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);

    // Déplacer l'image téléchargée vers le dossier des téléchargements
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    mysqli_query($conn, "INSERT INTO projects (title, category, main_image, description, user_id) VALUES ('$title', '$category', '$image', '$description', '$userId')") or die(mysqli_error($conn));

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
            $trace_file = $trace_dir . basename($filename);
            move_uploaded_file($_FILES['traces']['tmp_name'][$index], $trace_file);
            $sql = "INSERT INTO project_traces (project_id, trace, trace_type) VALUES ('$projectId', '$filename', 'file')";
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }

        header("Location: projet.php?id=" . $projectId);
        exit;
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
    <title>Ajouter un Projet</title>
</head>
<body>
    <!-------------------- HEADER START ------------------>
    <header>
        <section class="profile">
            <img src="../img/<?php echo $userData['profile_picture']; ?>" alt="profilePicture">
            <h1><?php echo $userData['name']; ?></h1>
            <h2><?php echo $userData['class']; ?> - <?php echo $userData['groupe']; ?></h2>
        </section>
        <hr>

        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="./mesProjets.php">Projets</a></li>
                <li class="active"><a href="./ajouter.php">Ajouter Projet</a></li>
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
            <h1>Ajouter un Projet</h1>
            <ul>
                <li><?php echo $userData['name']; ?> | <?php echo $userData['class']; ?> | <?php echo $userData['groupe']; ?></li>
            </ul>
        </section>

        <article class="addProjectArticle">

            <form action="#" method="POST" enctype="multipart/form-data">

                <div class="titre">
                    <label for="title">Titre du Projet *</label> <br>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="competences_and_AC">

                    <div class="competences">
                        <label for="category">Compétence *</label> <br>
                        <select id="category" name="category" required>
                            <option value="Comprendre">C1 - Comprendre</option>
                            <option value="Concevoir">C2 - Concevoir</option>
                            <option value="Exprimer">C3 - Exprimer</option>
                            <option value="Développer">C4 - Développer</option>
                            <option value="Entreprendre">C5 - Entreprendre</option>
                        </select>

                    </div>
                </div>

                <div class="mainImg">
                    <label for="image">Image Principale *</label> <br>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <section class="restofForm">
                    
                    <div class="descriptionAndEval">

                        <div class="description">
                            <label for="description">Description *</label> <br>
                            <textarea name="description" id="description" required>

                            </textarea>
                        </div>

                        <div class="evaluation">
                            <h1>Evaluation ( / 3)</h1>

                            <label for="ac">Assessment Criteria:</label>
                                <div id="ac-fields">
                                    <div class="ac-field">
                                        <select class="ac select2" name="acs[]" required>
                                        <?php foreach ($acs as $ac): ?>
                                            <option value="<?php echo $ac['id']; ?>"><?php echo $ac['name']; ?></option>
                                        <?php endforeach; ?>
                                        </select>

                                        <input type="number" class="grade" name="grades[]" min="1" max="3" required>
                                    </div>
                                </div>
                                <button type="button" id="add-ac">Add Another AC</button>
                        </div>

                    </div>
                    


                    <div class="traces">
                        <label for="traces">Traces * </label> 
                        <p>Sélectionnez plusieurs fichiers en maintenant Cmd sur MAC ou Controle sur Windows</p>
                        <br>
                        <input type="file" id="traces" name="traces[]" accept="/" multiple>
                        <input type="text" name="link" id="link" placeholder="Ajouter un lien">
                    </div>
                </section>

                <input type="submit" name="BtnAjouterUnProjet" value="Ajouter Un Projet" class="submitBtn">
            </form>
        </article>
    </main>
    <!-------------------- MAIN END ------------------>
    
    

    <script src="../script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    

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