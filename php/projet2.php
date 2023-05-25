<?php
include '../php/header.php';
?>

<?php
// Retrieve the project information from the database based on the project ID
$projectId = 2;
$sql = "SELECT * FROM projects WHERE id = $projectId";
$result = mysqli_query($conn, $sql);
$project = mysqli_fetch_assoc($result);

if (!$project) {
    // Project with the specified ID does not exist
    echo "Project not found";
    exit;
}


$sql = "SELECT AVG(grade) AS average_grade FROM project_grades WHERE project_id = $projectId";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$averageGrade = round($row['average_grade'], 1);









// Display the project information
echo "<h1>{$project['title']}</h1>";
echo "<p>Category: {$project['category']}</p>";
echo "<p>Main Image: <img src='../img_projets/{$project['main_image']}' alt='Project Image'></p>";
echo "<p>Description: {$project['description']}</p>";

// Display the ACs and their grades
$sql = "SELECT * FROM project_grades WHERE project_id = $projectId";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>Assessment Criteria and Grades:</h2>";
$sql = "SELECT project_grades.grade, assessment_criteria.name AS ac_name 
        FROM project_grades 
        INNER JOIN assessment_criteria ON project_grades.ac_id = assessment_criteria.id
        WHERE project_grades.project_id = $projectId";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<p> {$row['ac_name']}</p>";
    echo "<p>Note: {$row['grade']}</p>";
}

} else {
    echo "<p>No assessment criteria and grades found for this project.</p>";
}


/* HEEEEEERE */

echo "<p>Your Grade: $averageGrade</p>";



// Display the Traces (files and/or links)
$sql = "SELECT * FROM project_traces WHERE project_id = $projectId";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>Traces:</h2>";
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['trace_type'] === 'link') {
            echo "<p><a href='{$row['trace']}'>Allez au site</a></p>";
        }
    }
} else {
    echo "<p>No traces found for this project.</p>";
}

// Add buttons for deleting and modifying the project
echo "<button onclick='deleteProject($projectId)'>Delete Project</button>";
echo "<button onclick='modifyProject($projectId)'>Modify Project</button>";

// Close the database connection
mysqli_close($conn);
?>
