<?php
// Include your database connection file
require_once ('../config.php');

$items = array();

// Fetch item details including category name and cost from the database
$qry = $conn->query("SELECT i.*, c.category_name FROM `item_list` i 
                     INNER JOIN category_list c ON i.category_id = c.id 
                     ORDER BY i.name ASC");

// Store item details in the $items array
while ($row = $qry->fetch_assoc()) {
    $items[] = $row;
}

// Convert the PHP array to JSON format
$items_json = json_encode($items);

// Fetch fixed client names from the database
$qry_clients = $conn->query("SELECT * FROM fix_customer");

// Store client names in a PHP array
$clients = array();
while ($row = $qry_clients->fetch_assoc()) {
    $clients[] = $row['customer_name'];
    $clients_id[] = $row['id'];
}

// Convert the PHP array to JSON format
$clients_json = json_encode($clients);
$clients_id_json = json_encode($clients_id);

?>



<!-- HTML content with embedded PHP -->

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Billing</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <!-- Search bar for searching items -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="searchItem">Search Item</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchItem" placeholder="Enter item name">
                            <!-- Add to List button -->
                            <div class="input-group-append">
                                <button id="addToListBtn" class="btn btn-primary">Add to List</button>
                            </div>
                        </div>
                        <!-- Dropdown menu to display search items -->
                        <div class="dropdown mt-2">
                            <ul class="dropdown-menu" id="searchItemList">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table for displaying added items -->
            <!-- Table for displaying added items -->
            <table id="billingTable" class="table table-bordered table-stripped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <!-- For fixed customers, show amount paid and total amount columns -->

                        <th id="amountToPayHeader" style="display: none;">Amount Paid </th>
                        <th id="amountPaidHeader" style="display: none;">Due Amount</th>
                        <th id="totalAmountHeader" style="display: none;">Total Amount</th>
                        <th>Sub Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows for added items will be dynamically populated here -->
                </tbody>
                <tfoot>
                    <tr id="subTaxRow">
                        <td colspan="4"></td>
                        <td id="taxtxt"><strong>Tax <span id="taxPercentage">0</span>%</strong></td>
                        <td id="subTaxValue" colspan="1">0.00</td>
                    </tr>
                    <tr id="subDiscountRow">
                        <td colspan="4"></td>
                        <td id="distxt"><strong>Discount <span id="discountPercentage">0</span>%</strong></td>
                        <td id="subDiscountValue" colspan="1">0.00</td>
                    </tr>
                    <tr id="subtotalRow">
                        <td colspan="4"></td>
                        <td id="tottxt"><strong>Total</strong></td>
                        <td id="subtotalValue" colspan="1">0.00</td>

                    </tr>
                </tfoot>
            </table>


            <!-- Inputs for discounts and tax -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="discount">Discount (%)</label>
                        <input type="number" class="form-control" id="discount" placeholder="Enter discount">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tax">Tax (%)</label>
                        <input type="number" class="form-control" id="tax" placeholder="Enter tax">
                    </div>
                </div>
            </div>

            <!-- Select payment method -->
            <div class="form-group">
                <label for="paymentMethod">Payment Method</label>
                <select class="form-control" id="paymentMethod">
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <!-- Select customer type -->
            <div class="form-group">
                <label for="customerType">Customer Type</label>
                <select class="form-control" id="customerType">
                    <option value="normal">Normal</option>
                    <option value="fixed">Fixed</option>
                </select>
            </div>

            <!-- Search bar for selecting fixed clients -->
            <div class="form-group">
                <div id="fixedClientSearch" style="display: none;">
                    <label for="customerType">Fixed Customer Name</label>
                    <input type="text" class="form-control" id="searchFixedClient" placeholder="Search Fixed Clients">
                    <div id="fixedClientList"></div>
                </div>
            </div>

            <!-- Button to generate invoice -->
            <button id="generateInvoiceBtn" class="btn btn-primary">Generate Invoice</button>
            

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var clientId = 0;


        // Hide search item list initially
        $('#searchItemList').hide();

        // Define a variable to store the fetched items
        var items = <?php echo $items_json; ?>;
        console.log(items); // Check the structure of the items array

        function showFixedClientSearch() {
            $('#fixedClientSearch').show(); // Show the search bar for fixed clients
            $('#searchFixedClient').val(''); // Clear the search input field
            $('#fixedClientList').empty(); // Clear the client list

            var fixedClients = <?php echo $clients_json; ?>;

            var client_ids = <?php echo $clients_id_json; ?>;


            $('#searchFixedClient').on('focus keyup', function () {
                var input = $(this).val().toLowerCase();
                $('#fixedClientList').empty();

                fixedClients.forEach(function (client, index) {
                    if (client.toLowerCase().indexOf(input) > -1) {
                        $('#fixedClientList').append('<div class="dropdown-item" data-client-id="' + client_ids[index] + '">' + client + '</div>');
                    }
                });

                $('#fixedClientList').show();
            });

            // Event listener for selecting a client from the list
            $('#fixedClientList').on('click', '.dropdown-item', function () {
                var selectedClient = $(this).text();
                clientId = $(this).data('client-id');

                // Do something with the selected client and its ID
                console.log("Selected Client:", selectedClient);
                console.log("Client ID:", clientId);

                // Hide the fixed client list dropdown
                $('#fixedClientList').hide();
            });
        }
        $('#fixedClientList').on('click', '.dropdown-item', function () {
            var selectedClient = $(this).text();
            $('#searchFixedClient').val(selectedClient); // Set selected client to the search bar input field
            $('#fixedClientList').hide(); // Hide the fixed client list dropdown
        });
        // Show fixed client search when customer type is set to "fixed"
        $('#customerType').change(function () {
            if ($(this).val() === 'fixed') {
                showFixedClientSearch();
            } else {
                $('#fixedClientSearch').hide(); // Hide the search bar when customer type is not "fixed"
            }
        });


        // Display search item list on focus of search input
        $('#searchItem').on('focus keyup', function () {
            var input = $(this).val().toLowerCase();
            $('#searchItemList').empty();
            items.forEach(function (item) {
                if (item.name.toLowerCase().indexOf(input) > -1) {
                    $('#searchItemList').append('<li><a class="dropdown-item" href="#" data-category="' + item.category_name + '" data-cost="' + item.cost + '" data-itemid="' + item.id + '">' + item.name + '</a></li>');
                }
            });
            $('#searchItemList').show();
        });

        // Set selected item from dropdown to search input
        $('#searchItemList').on('click', 'a', function () {
            var itemId = $(this).data('itemid');
            var selected_item = $(this).text();
            var category = $(this).data('category');
            var unitCost = $(this).data('cost');
            console.log("Selected Item:", selected_item);
            console.log("Category:", category);
            console.log("Unit Cost:", unitCost);
            console.log("Item ID:", itemId);
            $('#searchItem').val(selected_item);
            $('#searchItemList').hide(); // Hide dropdown after selection

            // Store category and unit cost for later use
            $('#searchItem').data('category', category);
            $('#searchItem').data('cost', unitCost);
            $('#searchItem').data('itemid', itemId);
        });

        // Add to List button click event handler
        $('#addToListBtn').click(function () {
            // Get the item details from the search bar
            var itemName = $('#searchItem').val();
            var category = $('#searchItem').data('category'); // Retrieve category from stored data
            var unitCost = $('#searchItem').data('cost'); // Retrieve unit cost from stored data
            var itemId = $('#searchItem').data('itemid'); // Retrieve item ID from stored data


            console.log("Item Name:", itemName);
            console.log("Category:", category);
            console.log("Unit Cost:", unitCost);

            var index = $('#billingTable tbody tr').length + 1;
            // Add item to the billing table
            $('#billingTable tbody').append(
                '<tr>' +
                '<td>' + index + '</td>' +
                '<td>' + category + '<input type="hidden" class="item-id" value="' + itemId + '"></td>' +
                '<td>' + itemName + '</td>' +
                '<td><input type="number" class="form-control quantity" value="1"></td>' +
                '<td class="unit-cost" contenteditable="' + ($('#customerType').val() === 'fixed' ? 'true' : 'false') + '">' + unitCost + '</td>' +

                '<td class="amount-paid" style="display: none;"><input type="number" class="form-control paid-amount" value=""></td>' +

                '<td class="amount-to-pay" style="display: none;"></td>' +
                '<td class="total-cost">' + unitCost + '</td>' +
                '<td class="total-sub" style="display: none;"></td>' +
                '<td><button class="btn btn-danger btn-sm remove-item">Remove</button></td>' +
                '</tr>'
            );
            $('#searchItem').val('');

            // Update subtotal
            updateSubtotal();
        });


        $('#discount, #tax').on('input', function () {
            // Update subtotal to reflect changes in discount and tax
            updateSubtotal();
        });


        // Update subtotal function
        function updateSubtotal() {
            var subtotal = 0;
            var discount = parseFloat($('#discount').val()) || 0; // Get discount value
            var tax = parseFloat($('#tax').val()) || 0; // Get tax value

            $('#billingTable tbody tr').each(function () {
                var quantity = parseInt($(this).find('.quantity').val());
                var unitCost = parseFloat($(this).find('.unit-cost').text());
                var totalCost = quantity * unitCost;
                subtotal += totalCost;
            });

            // Apply discount
            var discountAmount = (subtotal * (discount / 100)).toFixed(2);
            subtotal -= parseFloat(discountAmount);

            // Apply tax
            var taxAmount = (subtotal * (tax / 100)).toFixed(2);
            subtotal += parseFloat(taxAmount);

            // Update subtotal value
            $('#subtotalValue').text(subtotal.toFixed(2));
            $('#subTaxValue').text(taxAmount);
            $('#subDiscountValue').text(discountAmount);
            $('#subDiscountValue').prev().find('#discountPercentage').text(discount); // Update discount percentage
            $('#subTaxValue').prev().find('#taxPercentage').text(tax); // Update tax percentage

            // If customer type is fixed, show amount paid, amount to be paid, and total amount
            if ($('#customerType').val() === 'fixed') {
                $('.amount-paid, .amount-to-pay, .total-sub').show();
                $('#amountPaidHeader, #amountToPayHeader, #totalAmountHeader').show();
                // Make unit cost and amount paid editable
                $('#billingTable tbody tr .unit-cost').attr('contenteditable', 'true');
                $('#billingTable tbody tr .amount-paid').show();
                $('#subTaxRow td').attr('colspan', '4');
                $('#subTaxValue').attr('colspan', '1');
                $('#subDiscountRow td').attr('colspan', '4');
                $('#subDiscountValue').attr('colspan', '1');
                $('#subtotalRow td').attr('colspan', '4');
                $('#subtotalValue').attr('colspan', '1');


            } else {
                $('.amount-paid, .amount-to-pay, .total-amount,.total-sub').hide();
                $('#amountPaidHeader, #amountToPayHeader, #totalAmountHeader').hide();
                // Make unit cost and amount paid non-editable
                $('#billingTable tbody tr .unit-cost').attr('contenteditable', 'false');
                $('#billingTable tbody tr .amount-paid').hide();
                $('#subTaxRow td').attr('colspan', '3');
                $('#subTaxValue').attr('colspan', '1');
                $('#taxtxt').attr('colspan', '2');
                $('#subDiscountRow td').attr('colspan', '3');
                $('#subDiscountValue').attr('colspan', '1');
                $('#distxt').attr('colspan', '2');
                $('#subtotalRow td').attr('colspan', '3');
                $('#subtotalValue').attr('colspan', '1');
                $('#tottxt').attr('colspan', '2');


            }


        }

        // Update total amount, amount paid, and amount to be paid on input change
        $(document).on('input', '.quantity, .unit-cost, .paid-amount', function () {
            var tr = $(this).closest('tr');
            var quantity = parseInt(tr.find('.quantity').val());
            var unitCost = parseFloat(tr.find('.unit-cost').text());
            var totalCost = quantity * unitCost;
            tr.find('.total-cost').text(totalCost.toFixed(2));

            // Update subtotal
            updateSubtotal();

            // Update amount to be paid and amount paid if customer type is fixed
            if ($('#customerType').val() === 'fixed') {

                var subtotal = totalCost.toFixed(2);
                var amountPaid = parseFloat(tr.find('.paid-amount').val()) || 0;
                var amountToPay = parseFloat(subtotal - amountPaid).toFixed(2);
                tr.find('.amount-to-pay').text(amountToPay);
                tr.find('.total-sub').text(subtotal);
            }
        });

        // Customer type change event
        $('#customerType').change(function () {
            updateSubtotal();
        });

        // Discount and tax input change event
        $('#discount, #tax').change(function () {
            $(document).find('.quantity, .unit-cost, .paid-amount').trigger('input');
        });

        // Trigger input event for unit cost when changed
        $(document).on('input', '.unit-cost', function () {
            $(this).closest('tr').find('.quantity').trigger('input');
        });

        // Add event listener to remove button
        $(document).on('click', '.remove-item', function () {
            $(this).closest('tr').remove(); // Remove the closest row when remove button is clicked
            updateSubtotal(); // Update subtotal after removing item
        });




        $('#generateInvoiceBtn').click(function () {
            // Collect data from the billing table
            var items = [];
            var totalDueAmount = 0;
            var totAmt = $('#subtotalValue').text();
            var discountAmount = $('#subDiscountValue').text();
            var taxAmount = $('#subTaxValue').text();
            var discountpercent = $('#discountPercentage').text();
            var taxpercent = $('#taxPercentage').text();
            $('#billingTable tbody tr').each(function () {
                var item = {
                    item_id: $(this).find('.item-id').val(),
                    quantity: $(this).find('.quantity').val(),
                    unit_cost: $(this).find('.unit-cost').text(),
                    total_cost: $(this).find('.total-cost').text(),
                    unit: 'null',
                };
                items.push(item);

                var amountPaid = parseFloat($(this).find('.paid-amount').val()) || 0;
                var totalCost = parseFloat($(this).find('.total-cost').text());

                var dueAmount = totalCost - amountPaid;
                totalDueAmount += dueAmount;

            });

            // Collect other necessary data
            var customerType = $('#customerType').val();

            // Get the corresponding customer ID from the client IDs array
            console.log(clientId);
            console.log(customerType);
            console.log(totalDueAmount);
            console.log(totAmt);

            // Prepare the data to be sent via AJAX for saving the sale
            var postData = {
                items: items,
                client: customerType,
                cust_id: clientId,
                amount: totAmt,
                amount_due: totalDueAmount,
                discount: discountAmount,
                dispercent: discountpercent,
                taxpercent: taxpercent,
                tax: taxAmount,
            };

            // Send the data to the server via AJAX for saving the sale
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_invoice",
                data: postData,
                method: 'POST',
                dataType: 'json',
                success: function (resp) {
                    if (resp.status === 'success') {
                        // If sale is saved successfully, proceed to generate invoice
                        $.ajax({
                            url: _base_url_ + 'classes/Master.php?f=generated_invoice',
                            method: 'POST',
                            data: { id: resp.id }, // Use the same data for generating invoice
                            dataType: 'json',
                            success: function (response) {
                                if (response.status === 'success') {
                                    var invoiceContent = response.data; // Get the HTML content of the invoice

                                    var newWindow = window.open('', '', 'width=1200,height=900,left=250,location=no,titlebar=yes');

                                    var invoiceHTML = '<!DOCTYPE html><html><head><title>Invoice</title></head><body>' +
    '<div class="d-flex justify-content-center">' +
    '<div class="col-1 text-right">' +
    '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" />' +
    '</div>' +
    '<div class="col-10">' +
    '<h4 class="text-center"><?php echo $_settings->info('name') ?></h4>' +invoiceContent+
    '</div>' +
    '</div>';

// Include customer name if the customer is a fixed customer
if ($('#customerType').val() === 'fixed') {
    var fixedClientName = $('#searchFixedClient').val();
    invoiceHTML += '<div><strong>Customer Name:</strong> ' + fixedClientName + '</div>';
}

// Include billing details section
invoiceHTML += '<div class="billing-details">' +
    '<table class="table">' +
    '<thead>' +
    '<tr>' +
    '<th>#  </th>' +
    '<th>   Item Name  </th>' +
    '<th>   Quantity   </th>' +
    '<th>   Unit Cost  </th>' +
    '<th>    Subtotal   </th>' +
    '</tr>' +
    '</thead>' +
    '<tbody>';

// Loop through each item in the billing table to include its details in the invoice
$('#billingTable tbody tr').each(function(index) {
    var itemName = $(this).find('td:nth-child(3)').text();
    var quantity = $(this).find('.quantity').val();
    var unitCost = $(this).find('.unit-cost').text();
    var subtotal = $(this).find('.total-cost').text();

    // Append the item details to the HTML content
    invoiceHTML += '<tr>' +
        '<td>'     + (index + 1) +        '</td>' +
        '<td>'     + itemName +           '</td>' +
        '<td>'     + quantity +           '</td>' +
        '<td>'     + unitCost +           '</td>' +
        '<td>'     + subtotal +           '</td>' +
        '</tr>';
});

// Close the table body and include the tfoot section for discount, tax, and total
var totalDiscount = $('#subDiscountValue').text();
var taxAmount = $('#subTaxValue').text();
var subtotalValue = $('#subtotalValue').text();
var discountPercentage = $('#discount').val() || 0;
var taxPercentage = $('#tax').val() || 0;

invoiceHTML += '</tbody>' +
    '<tfoot>' +
    '<tr>' +
    '<td colspan="4"><div><strong>Total Discount (' + discountPercentage + '%):</strong></div>' +
    '<div><strong>Tax (' + taxPercentage + '%):</strong></div>' +
    '<div><strong>Total:</strong></div></td>' +
    '<td><div>' + totalDiscount + '</div>' +
    '<div>' + taxAmount + '</div>' +
    '<div>' + subtotalValue + '</div></td>' +
    '</tr>' +
    '</tfoot>' +
    '</table>' +
    '</div>' +
    '</body></html>';


                                    // Write the concatenated HTML content to the new window document
                                    newWindow.document.write(invoiceHTML);
                                    newWindow.document.close();

                                    newWindow.print(); // Print the content

                                    setTimeout(() => {
                                        newWindow.close();
                                        end_loader();

                                        // Clear the form fields or HTML elements here
                                        $('#billingTable tbody').empty(); // Clear billing table
                                        $('#subtotalValue').text('0.00'); // Reset subtotal value
                                        $('#subDiscountValue').text('0.00'); // Reset discount value
                                        $('#subTaxValue').text('0.00'); // Reset tax value
                                        $('#discount').val(''); // Reset discount input field
                                        $('#tax').val(''); // Reset tax input field
                                        $('#customerType').val('normal'); // Reset customer type select field
                                        $('#searchItem').val(''); // Reset search item input field
                                        $('#discountPercentage').text('');
                            $('#taxPercentage').text('');
                            
                            // Display success toast message
                           
                                        // Clear other fields as needed
                                    }, 200);
                                    alert_toast("Invoice generated successfully.", 'success');

                                } else {
                                    // Display error message to the user
                                    alert('Failed to generate invoice: ' + response.msg);
                                }
                            },
                            error: function (xhr, status, error) {
                                // Display error message to the user
                                alert('An error occurred while generating invoice.');
                                console.error(xhr.responseText);
                            }
                        });
                    } else {
                        // Display error message if saving the sale fails
                        alert_toast("Failed to save sale: " + resp.msg, 'error');
                    }
                    end_loader(); // End loader regardless of success or failure
                },
                error: function (xhr, status, error) {
                    // Display error message if AJAX request fails
                    alert_toast("An error occurred while saving the sale.", 'error');
                    console.error(xhr.responseText);
                    end_loader(); // End loader in case of error
                }
            });
        });


       
    });




</script>