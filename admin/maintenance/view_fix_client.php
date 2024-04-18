<?php
// Fetch the client ID and name from the URL parameter
$client_id = isset($_GET['id']) ? $_GET['id'] : '';
$client_name = '';

// Retrieve the client name based on the provided client ID
$customer_query = $conn->query("SELECT * FROM `fix_customer` WHERE `id` = '$client_id'");
$customer_row = $customer_query->fetch_assoc();
if ($customer_row) {
    $client_name = $customer_row['customer_name'];
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
    <h3 class="card-title">List of Sales for Fix Client: <?php echo $client_name != '' ? $client_name : 'ID: ' . $client_id; ?></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Sale Code</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    // Retrieve the client based on the provided client ID
                    $customer_query = $conn->query("SELECT * FROM `fix_customer` WHERE `id` = '$client_id'");
                    $customer_row = $customer_query->fetch_assoc();
                    // Check if the client exists
                    if ($customer_row) {
                        // Retrieve sales data associated with the client
                        $sales_ids = explode(',', $customer_row['sales_no']);
                        foreach ($sales_ids as $sale_id):
                            // Fetch sales data for the current client
                            $sales_query = $conn->query("SELECT * FROM `sales_list` WHERE `id` = '$sale_id'");
                            $sales_row = $sales_query->fetch_assoc();
                            if ($sales_row && isset($sales_row['stock_ids'])) {
                                $sales_row['items'] = count(explode(',', $sales_row['stock_ids']));
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo isset($sales_row['date_created']) ? date("Y-m-d H:i", strtotime($sales_row['date_created'])) : 'N/A'; ?></td>
                                    <td><?php echo isset($sales_row['sales_code']) ? $sales_row['sales_code'] : 'N/A'; ?></td>
                                    <td class="text-right"><?php echo isset($sales_row['items']) ? number_format($sales_row['items']) : '0'; ?></td>
                                    <td class="text-right"><?php echo isset($sales_row['amount']) ? number_format($sales_row['amount'], 2) : '0.00'; ?></td>
                                    <td align="center">
                                        <?php echo isset($sales_row['id']) ? '<a href="' . base_url . 'admin?page=sales/view_sale&id=' . $sales_row['id'] . '"><span class="fa fa-eye text-dark"></span> View</a>' : ''; ?>
                                        
                                    </td>
                                </tr>
                                <?php
                            }
                        endforeach;
                    } else {
                        // If the client doesn't exist, display a message
                        echo '<tr><td colspan="6" class="text-center">Client not found for ID '.$client_id.'</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Sales Record permanently?","delete_sale",[$(this).attr('data-id')])
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable();
    })
    function delete_sale($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_sale",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
