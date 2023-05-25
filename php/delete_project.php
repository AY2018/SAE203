<?php
// Assuming you already have a connection to the database
$conn = mysqli_connect("localhost", "ayoubFinal", "mdp", "dashboardBDD");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $projectId = $_POST['id'];

        // Get the main image filename
        $result = mysqli_query($conn, "SELECT main_image FROM projects WHERE id = $projectId");
        $project = mysqli_fetch_assoc($result);
        $imagePath = "../img_projets/" . $project['main_image'];

        // Delete the main image file
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Get the trace filenames
        $result = mysqli_query($conn, "SELECT trace FROM project_traces WHERE project_id = $projectId AND trace_type = 'file'");
        while ($trace = mysqli_fetch_assoc($result)) {
            $filePath = "../traces/" . $trace['trace'];

            // Delete the trace file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete all related data
        $sql = "DELETE FROM project_grades WHERE project_id = $projectId";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $sql = "DELETE FROM project_traces WHERE project_id = $projectId";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        // Delete the project
        $sql = "DELETE FROM projects WHERE id = $projectId";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        // Redirect to the project list page
        header("Location: ../mainPages/mesProjets.php");
        exit;
    }
}



if (isset($_POST['delete'])){
    foreach($_POST['project_ids'] as $id){
        // Get the main image filename
        $result = mysqli_query($conn, "SELECT main_image FROM projects WHERE id = $id");
        $project = mysqli_fetch_assoc($result);
        $imagePath = "../img_projets/" . $project['main_image'];

        // Delete the main image file
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Get the trace filenames
        $result = mysqli_query($conn, "SELECT trace FROM project_traces WHERE project_id = $id AND trace_type = 'file'");
        while ($trace = mysqli_fetch_assoc($result)) {
            $filePath = "../traces/" . $trace['trace'];

            // Delete the trace file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete traces for the project
        $sql = mysqli_query($conn, "DELETE FROM project_traces WHERE project_id = '$id'") or die(mysqli_error($conn));

        // Delete corresponding project grades
        $sql = mysqli_query($conn, "DELETE FROM project_grades WHERE project_id = '$id'") or die(mysqli_error($conn));

        // Delete the project
        $sql = mysqli_query($conn, "DELETE FROM projects WHERE id = '$id'") or die(mysqli_error($conn));
    }
    header("Location: ../mainPages/mesProjets.php");
    exit();
}

?>
