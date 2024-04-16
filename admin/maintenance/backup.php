<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "sms_db";

// Function to backup the database
function backupDatabase($servername, $username, $password, $database, $backup_filename) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Backup
    $backup_path = __DIR__ . '/' . $backup_filename;

    // Dump database to SQL file
    exec("mysqldump --user=$username --password=$password --host=$servername $database > $backup_path");

    echo "Database backed up successfully to $backup_filename";

    $conn->close();
}

// Function to restore the database
function restoreDatabase($servername, $username, $password, $database, $restore_path) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Restore
    if(!empty($restore_path)){
        // Execute the SQL file
        exec("mysql --user=$username --password=$password --host=$servername $database < $restore_path");

        echo "Database restored successfully from uploaded file";
    } else {
        echo "No file uploaded";
    }

    $conn->close();
}

// Check if backup button is clicked
if (isset($_POST['backup'])) {
    $backup_filename = $_POST['backup_filename'] ?? 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    backupDatabase($servername, $username, $password, $database, $backup_filename);
}

// Check if restore button is clicked
if (isset($_POST['restore'])) {
    restoreDatabase($servername, $username, $password, $database, $_FILES['restore_file']['tmp_name']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Database Backup & Restore</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-6">Backup Database</h2>
        <form action="" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="backup_filename" placeholder="Backup File Name">
            </div>
            <button type="submit" class="btn btn-primary" name="backup">Backup Database</button>
        </form>

        <h2 class="mt-5 mb-6">Restore Database</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" class="form-control-file" name="restore_file">
            </div>
            <button type="submit" class="btn btn-primary" name="restore">Restore Database</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
