<?php
// Include your database connection file
require_once('../../config.php');

// Get the search query from the AJAX request
$query = $_POST['query'];

// Prepare and execute a query to fetch item names based on the search query
$stmt = $conn->prepare("SELECT name FROM item_list WHERE name LIKE CONCAT('%', ?, '%')");
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the item names and store them in an array
$item_names = array();
while ($row = $result->fetch_assoc()) {
    $item_names[] = $row['name'];
}

// Return the item names as JSON
header('Content-Type: application/json'); // Specify JSON content type
echo json_encode($item_names);

// Close the database connection and statement
$stmt->close();
$conn->close();
?>
