<?php
// Assuming you have established a database connection
require_once('../../config.php');

// Retrieve the deposit amount from the POST request
$received = $_POST['deposit_amount'];

// Set other variables
$sales_code = 'Deposit'; // Set sales code to 'Deposit'
$total_cost = 0; // Set total cost to 0 for deposits
$change = 0; // Set change to 0 for deposits

// Retrieve the existing total earning
$select_total_earning_sql = "SELECT total_earning FROM cash_book ORDER BY date_created DESC LIMIT 1";
$select_total_earning_result = $conn->query($select_total_earning_sql);

if ($select_total_earning_result && $select_total_earning_result->num_rows > 0) {
    $row = $select_total_earning_result->fetch_assoc();
    $existing_total_earning = $row['total_earning'];
} else {
    // If there are no existing records, set existing_total_earning to 0
    $existing_total_earning = 0;
}

// Calculate the new total earning
$new_total_earning = $existing_total_earning + $received - $change;

// Prepare the INSERT query to add a new row for the deposit
$cash_book_sql = "INSERT INTO cash_book (date_created, sales_no, received, total_cost, `change`, total_earning) VALUES (NOW(), '$sales_code', '$received', '$total_cost', '$change', '$new_total_earning')";

// Execute the INSERT query
$cash_book_save = $conn->query($cash_book_sql);

if ($cash_book_save) {
    // Return a success message
    echo json_encode(array('status' => 'success', 'message' => 'Deposit successfully added to cash book.'));
} else {
    // Return an error message
    echo json_encode(array('status' => 'error', 'message' => 'Failed to add deposit to cash book: ' . $conn->error));
}
?>
