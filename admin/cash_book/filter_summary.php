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

    // Query to retrieve summary records within the date range
    $query = "SELECT * FROM cash_book_summary WHERE date_created BETWEEN '$start_date' AND '$end_date'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // If there are summary records, loop through and display them
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['date_created'] . "</td>";
            echo "<td>" . $row['earnings'] . "</td>";
            echo "</tr>";
        }
    } else {
        // If no records found, display a message
        echo "<tr><td colspan='2'>No records found in the selected date range.</td></tr>";
    }
} else {
    // If start_date or end_date is not set in the POST request
    echo "<tr><td colspan='2'>Invalid request. Please provide start_date and end_date.</td></tr>";
}
?>
