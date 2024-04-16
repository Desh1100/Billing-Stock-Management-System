<?php 
$qry = $conn->query("SELECT * FROM sales_list r WHERE id = '{$_GET['id']}'");
if($qry->num_rows > 0){
    $row = $qry->fetch_assoc();
    $id = $row['id'];
    $sales_code = $row['sales_code'];
    $client = $row['client'];
    $amount = $row['amount'];
    $remarks = $row['remarks'];
    $stock_ids = $row['stock_ids'];
    $discount = $row['discount'];
    $tax = $row['tax'];
    $dispercent = $row['dispercent'];
    $taxpercent = $row['taxpercent'];

    // Check if client type is 'fixed' to fetch the customer name
    if ($row['client'] == 'fixed') {
        // Fetch sales_no from fix_customer table using the id
        $fix_customer_qry = $conn->query("SELECT sales_no FROM fix_customer WHERE id = '{$row['id']}'");
        if ($fix_customer_qry->num_rows > 0) {
            $fix_customer_row = $fix_customer_qry->fetch_assoc();
            $sales_no_ids = explode(',', $fix_customer_row['sales_no']);

            // Fetch customer name from sales_list based on sales_no_ids
            $customer_name = "";
            foreach ($sales_no_ids as $sale_id) {
                $sales_qry = $conn->query("SELECT * FROM sales_list WHERE id = '$sale_id'");
                if ($sales_qry->num_rows > 0) {
                    $sales_row = $sales_qry->fetch_assoc();
                    // Append customer name
                    $customer_name = $sales_row['customer_name'];
                    break; // Exit the loop once the customer name is found
                }
            }

            // Display Customer Name
            echo "<div><strong>Customer Name:</strong> $customer_name</div>";
        }
    }
}
?>  


<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Sales Record - <?php echo $sales_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Sales Code</label>
                    <div><?php echo isset($sales_code) ? $sales_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="client" class="control-label text-info">Client Type</label>
                        <div><?php echo isset($client) ? $client : '' ?></div>
                    </div>
                </div>
            </div>
            <h4 class="text-info">Items</h4>
            <table class="table table-striped table-bordered" id="list">
                <colgroup>
                    <col width="10%">
                    <col width="25%">
                    <col width="20%">
                    <col width="20%">
                    <col width="25%">
                </colgroup>
                <thead>
                    <tr class="text-light bg-navy">
                        <th class="text-center py-1 px-2">Qty</th>
                        <th class="text-center py-1 px-2">Category</th>
                        <th class="text-center py-1 px-2">Item</th>
                        <th class="text-center py-1 px-2">Cost</th>
                        <th class="text-center py-1 px-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    $qry = $conn->query("SELECT s.*, i.name, i.description, c.category_name FROM `stock_list` s INNER JOIN item_list i ON s.item_id = i.id INNER JOIN category_list c ON i.category_id = c.id WHERE s.id IN ({$stock_ids})");
                    while($row = $qry->fetch_assoc()):
                        $total += $row['total'];
                    ?>
                    <tr>
                        <td class="py-1 px-2 text-center"><?php echo number_format($row['quantity']) ?></td>
                        <td class="py-1 px-2 text-center"><?php echo ($row['category_name']) ?></td>
                        <td class="py-1 px-2">
                            <?php echo $row['name'] ?> <br>
                            <?php echo $row['description'] ?>
                        </td>
                        <td class="py-1 px-2 text-right"><?php echo number_format($row['price']) ?></td>
                        <td class="py-1 px-2 text-right"><?php echo number_format($row['total']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Subtotal</th>
                        <th class="text-right py-1 px-2"><?php echo $total ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Discount (<?php echo $dispercent ?>%)</th>
                        <th class="text-right py-1 px-2"><?php echo number_format($discount, 2) ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Tax (<?php echo $taxpercent ?>%)</th>
                        <th class="text-right py-1 px-2"><?php echo number_format($tax, 2) ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Total</th>
                        <th class="text-right py-1 px-2 grand-total"><?php echo isset($amount) ? number_format($amount, 2) : 0 ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Remarks</label>
                        <p><?php echo isset($remarks) ? $remarks : '' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Print</button>
        <a class="btn btn-flat btn-primary" href="<?php echo base_url.'/admin?page=sales/manage_sale&id='.(isset($id) ? $id : '') ?>">Edit</a>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=sales' ?>">Back To List</a>
    </div>
</div>
<script>
    
    $(function(){
        $('#print').click(function(){
            start_loader()
            var _el = $('<div>')
            var _head = $('head').clone()
                _head.find('title').text("Sales Record - Print View")
            var p = $('#print_out').clone()
            p.find('tr.text-light').removeClass("text-light bg-navy")
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">'+
                      '<div class="col-1 text-right">'+
                      '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" />'+
                      '</div>'+
                      '<div class="col-10">'+
                      '<h4 class="text-center"><?php echo $_settings->info('name') ?></h4>'+
                      '<h4 class="text-center">Sales Record</h4>'+
                      '</div>'+
                      '<div class="col-1 text-right">'+
                      '</div>'+
                      '</div><hr/>')
            _el.append(p.html())
            var nw = window.open("","","width=1200,height=900,left=250,location=no,titlebar=yes")
                     nw.document.write(_el.html())
                     nw.document.close()
                     setTimeout(() => {
                         nw.print()
                         setTimeout(() => {
                            nw.close()
                            end_loader()
                         }, 200);
                     }, 500);
        })
    })
</script>
