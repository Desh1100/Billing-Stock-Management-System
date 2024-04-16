

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Fixed Customers</h3>
        <div class="card-tools">
            <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Add New</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hovered table-striped">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="25%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Date Modified</th>
                        <th>Customer Name</th>
                        <th>No of Items</th>
                        <th>Total Amount</th>
                        <th>Due Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT * FROM `fix_customer` ORDER BY `customer_name` ASC");
                    while($row = $qry->fetch_assoc()):
                        $row['items'] = count(explode(',',$row['stock_ids']));
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo $row['date_created'] ?></td>
                        <td><?php echo $row['date_modified'] ?></td>
                        <td><?php echo $row['customer_name'] ?></td>
                        <td class="text-center"><?php echo number_format($row['items']) ?></td>
                        <td><?php echo ($row['total_amount'] !== null) ? $row['total_amount'] : 0; ?></td>
                        <td><?php echo ($row['due_amount'] !== null) ? $row['due_amount'] : 0; ?></td>
                        <td align="center">
                            <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                Action
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item" href="<?php echo base_url.'admin?page=maintenance/view_fix_client&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this item permanently?","delete_fix_client",[$(this).attr('data-id')])
        });
        $('#create_new').click(function(){
            uni_modal("<i class='fa fa-plus'></i> Add New Fixed Customer","maintenance/manage_fix_client.php","mid-large")
        });
        $('.edit_data').click(function(){
            uni_modal("<i class='fa fa-edit'></i> Edit Fixed Customer Details","maintenance/manage_fix_client.php?id="+$(this).attr('data-id'),"mid-large")
        });
        $('.view_data').click(function(){
            uni_modal("<i class='fa fa-box'></i> Fixed Customer Details","maintenance/view_fix_client.php?id="+$(this).attr('data-id'),"")
        });
        $('.table td,.table th').addClass('py-1 px-2 align-middle');
        $('.table').dataTable();
    });
    function delete_fix_client($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_fix_client",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err);
                alert_toast("An error occurred.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occurred.",'error');
                    end_loader();
                }
            }
        });
    }
</script>
