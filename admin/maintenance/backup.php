
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

    <?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "sms_db";

// Function to display toast messages
function alert_toast($message, $type = 'success') {
    echo "<script>alert_toast('$message', '$type')</script>";
}
?>

<?php


// Start session to store messages


// Function to backup the database
function backupDatabase($servername, $username, $password, $database, $backup_filename) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        $_SESSION['error'] = "Connection failed: " . $conn->connect_error;
        return;
    }

    // Backup
    $backup_path =  '../Backup/' . $backup_filename;

    // Dump database to SQL file
    exec("mysqldump --user=$username --password=$password --host=$servername $database > $backup_path");

     // Capture output and log any errors
     $output = shell_exec("mysqldump --user=$username --password=$password --host=$servername $database > $backup_path 2>&1");
     if (!empty($output)) {
         // Log any output or errors from the command
         file_put_contents('backup_log.txt', $output, FILE_APPEND);
     }

    $_SESSION['success'] = "Database backed up successfully to $backup_filename";

    $conn->close();
}

// Function to restore the database
function restoreDatabase($servername, $username, $password, $database, $restore_path) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        $_SESSION['error'] = "Connection failed: " . $conn->connect_error;
        return;
    }

    // Restore
    if(!empty($restore_path)){
        // Execute the SQL file
        exec("mysql --user=$username --password=$password --host=$servername $database < $restore_path");

        $_SESSION['success'] = "Database restored successfully from uploaded file";
    } else {
        $_SESSION['error'] = "No file uploaded";
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

// Display toast messages
if(isset($_SESSION['error'])) {
    alert_toast($_SESSION['error'], 'error');
    unset($_SESSION['error']);
}
if(isset($_SESSION['success'])) {
    alert_toast($_SESSION['success']);
    unset($_SESSION['success']);
}

// Redirect to avoid form resubmission
//header("Location: {$_SERVER['REQUEST_URI']}");
exit();
?>


