<?php
require_once('../../config.php');

$id = isset($_GET['id']) ? $_GET['id'] : '';
$due_amount = 0;
$paying_amount = 0;
if($id > 0){
    $qry = $conn->query("SELECT * from `fix_customer` where id = '$id' ");
    if($qry->num_rows > 0){
        $customer_data = $qry->fetch_assoc();
        $customer_id = $customer_data['cust_ids'];
        $customer_name = $customer_data['customer_name'];
        $stock_ids = $customer_data['stock_ids'];
        $total_amount = $customer_data['total_amount'];
        $due_amount = $customer_data['due_amount'];
        $sales_no = $customer_data['sales_no'];
        $client_type = $customer_data['client_type'];
    }
   
}
?>
<?php echo $due_amount;

 ?>
<form action="" id="customer-form">
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div class="form-group">
        <label for="customer_name" class="control-label">Customer Name</label>
        <input type="text" name="customer_name" id="customer_name" class="form-control rounded-0" value="<?php echo isset($customer_name) ? $customer_name : ''; ?>">
    </div>
   
    <label for="due_amount" class="control-label"><?php echo ($id > 0) ? 'Paying Amount' : 'Due Amount'; ?></label>

    <input type="text" name="due_amount" id="due_amount" class="form-control rounded-0" value="<?php echo ($id > 0) ? max(0, $due_amount ) : $due_amount; ?>">

    <input type="hidden" name="total_amount" id="total_amount" class="form-control rounded-0" value="<?php echo ($id > 0) ? max(0, $total_amount) : 0; ?>">

    </div>
</form>
<div class="form-group">
      
<script>
    $(document).ready(function(){
        $('#customer-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_+"classes/Master.php?f=save_fix_client",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        location.reload();
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        end_loader();
                    }else{
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
