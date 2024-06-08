<?php
require_once('../../config.php');
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$cash_book_query = "SELECT * FROM cash_book WHERE date_created BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59' ORDER BY date_created DESC";
$cash_book_result = $conn->query($cash_book_query);
if ($cash_book_result && $cash_book_result->num_rows > 0) {
    while ($row = $cash_book_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['date_created'] . "</td>";
        echo "<td>" . $row['sales_no'] . "</td>";
        echo "<td>" . $row['received'] . "</td>";
        echo "<td>" . $row['total_cost'] . "</td>";
        echo "<td>" . $row['change'] . "</td>";
        echo "<td>" . $row['total_earning'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No records found in the cash book for the selected date range.</td></tr>";
}
?>
