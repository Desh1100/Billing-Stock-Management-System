<?php
// Assuming you have established a database connection
require_once('../../config.php');

// Check if start_date and end_date are set in the POST request
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    // Retrieve start and end dates from POST
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Sanitize and validate the dates (you may need to use a more robust validation method)
    $start_date = mysqli_real_escape_string($conn, $start_date);
    $end_date = mysqli_real_escape_string($conn, $end_date);

    // Query to calculate the total earnings within the date range
    $query = "SELECT SUM(earnings) AS total_earnings FROM cash_book_summary WHERE date_created BETWEEN '$start_date' AND '$end_date'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // If the query returns results, fetch the total earnings
        $row = $result->fetch_assoc();
        $total_earnings = $row['total_earnings'];
        
        // Output the total earnings
        echo $total_earnings;
    } else {
        // If no records found, return 0
        echo '0.00';
    }
} else {
    // If start_date or end_date is not set in the POST request
    echo '0.00';
}
?>
