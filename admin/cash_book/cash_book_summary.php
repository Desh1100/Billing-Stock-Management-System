<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Cash Book Summary</h3>
    </div>
    <div class="card-body">
        <div class="container">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
               
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <h3>Total Earnings: <span id="total_earnings"></span></h3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date Created</th>
                                <th>Earnings</th>
                            </tr>
                        </thead>
                        <tbody id="cash_book_summary_records">
                        <?php
            // Assuming you have established a database connection
            require_once ('../config.php');



            // Query to retrieve summary records within the date range
            $query = "SELECT * FROM cash_book_summary";
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
            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <button class="btn btn-success" onclick="printSummary()">Print Summary</button>
    </div>
</div>
<script>
    function filterByDate() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $.ajax({
            url: 'cash_book/filter_summary.php', // Adjust the URL according to your backend script
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function (response) {
                $('#cash_book_summary_records').html(response);
                calculateTotalEarnings();
            }
        });
    }

    function calculateTotalEarnings() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $.ajax({
            url: 'cash_book/calculate_total_earnings.php', // Adjust the URL according to your backend script
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function (response) {
                $('#total_earnings').text(response);
            }
        });
    }

    function printSummary() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    var monthStart = new Date(startDate).toLocaleDateString('en-US', { month: 'long' });
    var monthEnd = new Date(endDate).toLocaleDateString('en-US', { month: 'long' });
    var yearStart = new Date(startDate).getFullYear();
    var yearEnd = new Date(endDate).getFullYear();
    var dayStart = new Date(startDate).getDate();
    var dayEnd = new Date(endDate).getDate();

    var invoiceContent = $('#cash_book_summary_records').html();
    var totalEarnings = $('#total_earnings').text();

    var invoiceHTML = `<!DOCTYPE html>
                        <html>
                        <head>
                            <title>Cash Book Summary</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                }
                                .container {
                                    max-width: 800px;
                                    margin: 0 auto;
                                    padding: 20px;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }
                                th, td {
                                    border: 1px solid #dddddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                                th {
                                    background-color: #f2f2f2;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="col-2 text-right">
                                        <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" alt="Company Logo">
                                    </div>
                                    <div class="col-10">
                                        <h2 class="text-center"><?php echo $_settings->info('name') ?></h2>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><b>Total Earnings:</b> ${monthStart}/${dayStart}/${yearStart}  <b>To</b>  ${monthEnd}/${dayEnd}/${yearEnd}</p>
                                        <p><b>Start Date:</b> ${startDate}</p>
                                        <p><b>End Date:</b> ${endDate}</p>
                                        <p><b>Total Earnings:</b> ${totalEarnings}</p>
                                    </div>
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date Created</th>
                                            <th>Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>${invoiceContent}</tbody>
                                </table>
                            </div>
                        </body>
                        </html>`;

    // Open a new window and write the HTML content
    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write(invoiceHTML);
    printWindow.document.close();
    printWindow.print();
}

    $(document).ready(function () {
        // Initial loading of records
        filterByDate();

        // Update records when dates are changed
        $('#start_date, #end_date').change(function () {
            filterByDate();
        });
    });
</script>