<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Cash Book</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <!-- Deposit Button -->
            <div class="row mb-3">
    <div class="col-md-12 text-right">
        <button class="btn btn-primary mr-2" onclick="viewCashBookSummary()">
            <i class="fas fa-list"></i> Cash Book Summary
        </button>
        
    </div>
</div>

            
            <!-- Table and Filter Section -->
           
            <div class="row mb-3">
                <!-- Your existing filter controls -->
                <div class="col-md-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <!-- End Date Input -->
                <div class="col-md-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <!-- Total Earnings Display -->
                <div class="col-md-3">
                    <label for="total_earnings">Total Earnings:</label>
                    <input type="text" id="total_earnings" class="form-control" readonly>
                </div>
                <!-- Submit and Clear Button -->
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary btn-block" onclick="clearAndSubmit()">Submit and Clear Records</button>
                </div>
            </div>
            <div class="row mb-3">
                <!-- Your existing filter controls -->
                <div class="col-md-3">
                    <label for="start_date">Deposite The Amount: </label>
                    <button class="btn btn-success" data-toggle="modal" data-target="#depositModal">Deposit</button>
                </div>
                </div>
            <!-- Cash Book Records Table -->
            <table class="table table-bordered table-striped">
                <!-- Table Headers -->
                <thead>
                    <tr>
                        <th>Date Created</th>
                        <th>Sales No</th>
                        <th>Received</th>
                        <th>Total Cost</th>
                        <th>Change</th>
                        <th>Earnings</th>
                    </tr>
                </thead>
                <!-- Table Body with ID for AJAX update -->
                <tbody id="cash_book_records">
                    <!-- PHP Generated Records -->
                    <?php
                    $cash_book_query = "SELECT * FROM cash_book ORDER BY date_created DESC";
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
                        echo "<tr><td colspan='6'>No records found in the cash book.</td></tr>";
                    }
                    ?>
                    <!-- End of PHP Generated Records -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">Deposit Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="depositAmount">Amount:</label>
                    <input type="number" id="depositAmount" class="form-control" placeholder="Enter amount">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveDeposit()">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
function viewCashBookSummary() {
    // Redirect to the Cash Book Summary page
    window.location.href = "<?php echo base_url ?>admin/?page=cash_book/cash_book_summary";
}

function saveDeposit() {
        var depositAmount = $('#depositAmount').val();
        if (depositAmount !== '') {
            $.ajax({
                url: 'cash_book/save_deposit.php',
                method: 'POST',
                data: {
                    deposit_amount: depositAmount
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert_toast(data.message); // Show success message
                        $('#depositModal').modal('hide'); // Hide modal after successful deposit
                        filterRecords(); // Refresh the table
                    } else {
                        alert_toast(data.message); // Show error message
                    }
                },
                error: function() {
                    alert_toast('An error occurred.'); // Show generic error message
                }
            });
        } else {
            alert_toast('Please enter a valid deposit amount.'); // Show error if deposit amount is empty
        }
    }
    function filterRecords() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $.ajax({
            url: 'cash_book/filter_cash_book.php',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#cash_book_records').html(response);
                calculateTotalEarnings();
            }
        });
    }

    function calculateTotalEarnings() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    $.ajax({
        url: 'cash_book/get_latest_record.php',
        method: 'POST',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            if (response.trim() !== '') {
                $('#total_earnings').val(parseFloat(response).toFixed(2));
            } else {
                $('#total_earnings').val('0.00');
            }
        },
        error: function() {
            $('#total_earnings').val('0.00');
        }
    });
}


function clearAndSubmit() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        $.ajax({
            url: 'cash_book/clear_and_submit_cash_book.php',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert_toast("Successfully added to Cash Book Summary."); // Show success message
                    filterRecords(); // Refresh the table
                } else {
                    alert_toast("Failed added to Cash Book Summary."); // Show error message
                }
            },
            error: function() {
                alert_toast('An error occurred.'); // Show generic error message
            }
        });
    }

    $(document).ready(function() {
        // Initial loading of records
        filterRecords();

        // Update records when dates are changed
        $('#start_date, #end_date').change(function() {
            filterRecords();
        });
    });
</script>
