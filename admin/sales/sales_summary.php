<?php
// Assuming you have already established a database connection and included necessary files

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filter = "";
    if(isset($_POST['filter_type'])) {
        $filter_type = $_POST['filter_type'];
        if($filter_type == 'daily') {
            $filter = "WHERE DATE(sl.date_created) = CURDATE()";
        } elseif($filter_type == 'monthly') {
            $filter = "WHERE MONTH(sl.date_created) = MONTH(CURDATE()) AND YEAR(sl.date_created) = YEAR(CURDATE())";
        } elseif($filter_type == 'custom') {
            $filter = "WHERE 1"; // Initialize the filter
            // Check if start and end dates are provided
            if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $filter .= " AND DATE(sl.date_created) BETWEEN '$start_date' AND '$end_date'";
            }
            // Check if customer type is selected
            if(isset($_POST['customer_type']) && $_POST['customer_type'] != 'all') {
                $customer_type = $_POST['customer_type'];
                $filter .= " AND sl.client = '$customer_type'";
            }
        }
    }

    // Query to fetch filtered sales data
    $qry = $conn->query("SELECT sl.client, sl.sales_code, s.quantity, s.unit, i.name AS item_name, s.price, s.total 
            FROM `sales_list` sl 
            JOIN `stock_list` s ON FIND_IN_SET(s.id, sl.stock_ids) 
            JOIN `item_list` i ON s.item_id = i.id 
            $filter
            ORDER BY sl.date_created DESC");
} else {
    // Default query to fetch all sales data
    $qry = $conn->query("SELECT sl.client, sl.sales_code, s.quantity, s.unit, i.name AS item_name, s.price, s.total 
            FROM `sales_list` sl 
            JOIN `stock_list` s ON FIND_IN_SET(s.id, sl.stock_ids) 
            JOIN `item_list` i ON s.item_id = i.id 
            ORDER BY sl.date_created DESC");
}
?>

<!-- HTML code for the sales summary page -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Sales Summary</h4>
    </div>
    <div class="card-body" id="print_out">
        <!-- Add filtering form -->
        <form method="POST">
            <div class="form-group">
                <label for="filter_type">Filter:</label>
                <select name="filter_type" id="filter_type" class="form-control">
                    <option value="all">All</option>
                    <option value="daily">Daily</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div id="custom_dates" style="display: none;">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <!-- Add dropdown for customer type -->
                <div class="form-group">
                    <label for="customer_type">Customer Type:</label>
                    <select name="customer_type" id="customer_type" class="form-control">
                        <option value="all">All</option>
                        <option value="fixed">Fixed</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>

        <h4 class="text-info">Summary</h4>
        <table class="table table-striped table-bordered" id="list">
            <!-- Table header -->
            <thead>
                <tr class="text-light bg-navy">
                    <th class="text-center py-1 px-2">Client Name</th>
                    <th class="text-center py-1 px-2">Sales Code</th>
                    <th class="text-center py-1 px-2">Qty</th>
                    
                    <th class="text-center py-1 px-2">Item</th>
                    <th class="text-center py-1 px-2">Cost</th>
                    <th class="text-center py-1 px-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display sales data
                $total = 0;
                while ($row = $qry->fetch_assoc()):
                    $total += $row['total'];
                ?>
                <tr>
                    <td class="py-1 px-2 text-center"><?php echo $row['client'] ?></td>
                    <td class="py-1 px-2 text-center"><?php echo $row['sales_code'] ?></td>
                    <td class="py-1 px-2 text-center"><?php echo number_format($row['quantity']) ?></td>
                   
                    <td class="py-1 px-2"><?php echo $row['item_name'] ?></td>
                    <td class="py-1 px-2 text-right"><?php echo number_format($row['price'], 2) ?></td>
                    <td class="py-1 px-2 text-right"><?php echo number_format($row['total'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <!-- Table footer with total -->
            <tfoot>
                <tr>
                    <th class="text-right py-1 px-2" colspan="5">Total</th>
                    <th class="text-right py-1 px-2 grand-total"><?php echo number_format($total, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    // Show/hide custom date inputs based on filter selection
    document.getElementById('filter_type').addEventListener('change', function() {
        var customDates = document.getElementById('custom_dates');
        if (this.value === 'custom') {
            customDates.style.display = 'block';
        } else {
            customDates.style.display = 'none';
        }
    });
</script>
