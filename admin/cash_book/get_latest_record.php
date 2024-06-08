<?php
// Assuming you have established a database connection
require_once('../../config.php');
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Retrieve the latest record within the given date range
$cash_book_query = "SELECT * FROM cash_book WHERE date_created BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' ORDER BY date_created DESC LIMIT 1";
$cash_book_result = $conn->query($cash_book_query);

if ($cash_book_result && $cash_book_result->num_rows > 0) {
    $latestRecord = $cash_book_result->fetch_assoc();
    // Send the earnings value of the latest record
    echo $latestRecord['total_earning'];
} else {
    // Send 0 if no record is found
    echo '0.00';
}
?>
