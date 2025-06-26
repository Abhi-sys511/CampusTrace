<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT *, COALESCE((SELECT `name` FROM `category_list` where `category_list`.`id` = `item_list`. `category_id` ) ,'N/A') as `category` from `item_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		echo '<script>alert("item ID is not valid."); location.replace("./?page=items")</script>';
	}
}else{
	echo '<script>alert("item ID is Required."); location.replace("./?page=items")</script>';
}
?>
<h1 class="pageTitle text-center">Claim Item</h1>
<hr class="mx-auto bg-primary border-primary opacity-100" style="width:50px">
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
        <div class="card">
            <div class="card-body py-4">
            <dl>
                <div class="form-group d-flex justify-content-center">
                            <img src="<?php echo validate_image(isset($image_path) ? $image_path :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                        </div>
                  <dt class="text-muted">Description</dt>
                  <dd class="ps-4"><?= isset($description) ? str_replace("\n", "<br>", ($description)) : "" ?></dd>
            </dl>
                <h4 class="pageTitle">Please fill all the required fields</h4>
                <form action="" id="item-form">
                    <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
                    <input type="hidden" name="founder">    
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="claimname" class="control-label">Name</label>
                            <input type="text" name="claimname" id="claimname" class="form-control form-control-sm rounded-0" value=""  autofocus required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="contact" class="control-label">ID Number</label>
                            <input type="text" name="contact" id="contact" class="form-control form-control-sm rounded-0" value=""  required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="fullname" class="control-label">Contact Info</label>
                            <input type="text" name="fullname" id="fullname" class="form-control form-control-sm rounded-0" value=""  autofocus required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="explanation" class="control-label">Explanation</label>
                            <input type="text" name="explanation" id="explanation" class="form-control form-control-sm rounded-0" value=""  autofocus required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="" class="control-label">Identification Image</label>
                            <div class="custom-file">
                            <input type="file" class="form-control" id="customFile" name="image" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="col-lg-4 col-md-6 col-sm-10 col-12 mx-auto">
                    <button class="btn btn-primary btn-sm w-100" form="item-form"><i class="bi bi-send"></i> Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			$('#cimg').attr('src', "<?php echo validate_image(isset($meta['image_path']) ? $meta['image_path'] :'') ?>");
		}
	}
$(document).ready(function(){
    $('#category_id').select2({
        placeholder: 'Please Select Here',
        width: '100%'
    })
    $('#item-form').submit(function(e){
        e.preventDefault();
        var _this = $(this)
            $('.err-msg').remove();
        setTimeout(() => {
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=claim_item",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured",'error');
                    end_loader();
                },
                success:function(resp){
                    if(typeof resp =='object' && resp.status == 'success'){
                        location.replace('./?page=found')
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").scrollTop(0);
                            end_loader()
                    }else{
                        alert_toast("An error occured",'error');
                        end_loader();
                        console.log(resp)
                    }
                }
            })
        }, 200);
        
    })
})
</script>