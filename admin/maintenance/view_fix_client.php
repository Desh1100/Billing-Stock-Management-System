<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Sales</h3>
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
                    $qry = $conn->query("SELECT * FROM `fix_customer` ORDER BY `customer_name` ASC");
                    while($row = $qry->fetch_assoc()):
                        $sales_ids = explode(',', $row['sales_no']);
                        foreach ($sales_ids as $sale_id):
                            $sales_query = $conn->query("SELECT * FROM `sales_list` WHERE `id` = $sale_id");
                            $sales_row = $sales_query->fetch_assoc();
                            $sales_row['items'] = count(explode(',', $sales_row['stock_ids']));
                    ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo date("Y-m-d H:i", strtotime($sales_row['date_created'])) ?></td>
                                <td><?php echo $sales_row['sales_code'] ?></td>
                               
                                <td class="text-right"><?php echo number_format($sales_row['items']) ?></td>
                                <td class="text-right"><?php echo number_format($sales_row['amount'], 2) ?></td>
                                <td align="center">
                                    <a href="<?php echo base_url . 'admin?page=sales/view_sale&id=' . $sales_row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>

                                </td>
                            </tr>
                    <?php 
                        endforeach;
                    endwhile; 
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
