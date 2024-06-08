<?php
// Assuming you have established a database connection
require_once('../../config.php');

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Retrieve latest record within the given date range
$cash_book_query = "SELECT total_earning FROM cash_book WHERE date_created BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' ORDER BY date_created DESC LIMIT 1";
$cash_book_result = $conn->query($cash_book_query);

if ($cash_book_result && $cash_book_result->num_rows > 0) {
    $latestRecord = $cash_book_result->fetch_assoc();
    $latestEarnings = $latestRecord['total_earning'];

    // Insert the latest earnings into cash_book_summary table
    $insert_query = "INSERT INTO cash_book_summary (date_created, earnings) VALUES (NOW(), '$latestEarnings')";
    $insert_result = $conn->query($insert_query);

    if ($insert_result) {
        // Delete records from cash_book table
        $delete_query = "DELETE FROM cash_book WHERE date_created BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
        $delete_result = $conn->query($delete_query);

        if ($delete_result) {
            echo json_encode(array('status' => 'success', 'message' => 'Latest earnings record successfully saved to cash_book_summary table and cleared from cash_book table.'));
            exit;
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to clear records from cash_book table.'));
            exit;
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Failed to save latest earnings record to cash_book_summary table.'));
        exit;
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'No records found in the given date range.'));
    exit;
}
?>
