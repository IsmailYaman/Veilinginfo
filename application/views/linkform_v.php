<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>

<?php if(isset($msg_linkform_info)){ ?>
<div class="alert alert-info" role="alert">
  <?php echo $msg_linkform_info; ?>
</div>
<?php } ?>

<?php if(isset($errors) && count($errors) > 0){  ?>
<div class="alert alert-warning" role="alert">
	<div>
		<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
	</div>
	<ul>
	<?php foreach($errors as $error){  ?>
	<li><?php echo $error; ?></li>
	<?php } ?>
	</ul>
</div>
<?php } ?>

<?php $success = $this->session->flashdata('flash_message_success');
	  if($success){ ?>
	  
	  <div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
	  
<?php } ?>

<div class="container">

<div class="row">

	<!-- Column 1 -->
	<div class="col-lg-6 col-md-6 col-xs-12 col-sm-12" style="margin-left: 15px;">
		<form class="form-horizontal" method="post" action="">
		<fieldset>
			<div class="form-group">
				<label><?php echo $entry_firstname_text; ?></label>
				<input type="text" name="firstname" class="form-control" placeholder="<?php echo $entry_firstname_text; ?>" value="<?php echo isset($post['firstname']) ? $post['firstname'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_lastname_text; ?></label>
				<input type="text" name="lastname" class="form-control" placeholder="<?php echo $entry_lastname_text; ?>" value="<?php echo isset($post['lastname']) ? $post['lastname'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_email_text; ?></label>
				<input type="email" name="email" class="form-control" placeholder="<?php echo $entry_email_text; ?>" value="<?php echo isset($post['email']) ? $post['email'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_anchor_text; ?></label>
				<input type="text" name="anchor" class="form-control" placeholder="" value="<?php echo isset($post['anchor']) ? $post['anchor'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_link_text; ?></label>
				<input type="text" name="url" class="form-control" placeholder="<?php echo $input_link_text; ?>" value="<?php echo isset($post['url']) ? $post['url'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_backlink_text; ?></label>
				<input type="text" name="backlink" class="form-control" placeholder="<?php echo $input_backlink_text; ?>" value="<?php echo isset($post['backlink']) ? $post['backlink'] : ''; ?>" required>
			</div>
			<div class="form-group">
			  <label><?php echo $entry_page_text; ?></label>
				<select name="page_id" class="form-control" id="page_selector" required>
					<option>-<?php echo $input_page_text; ?>-</option>
					<?php foreach($pages as $page){ ?>
					<?php $selected = ''; ?>
					<?php if((isset($post['page_id']) && $post['page_id'] == $page->page_id) || ($page_id == $page->page_id)){ ?>
					<?php $selected = ' selected'; ?>
					<?php } ?>
					<option value="<?php echo $page->page_id; ?>" <?php echo $selected; ?>><?php echo $page->name; ?> - <?php echo $page->description; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
			  <label><?php echo $entry_category_text; ?></label>
				<select name="category_id" class="form-control" id="category_selector" required>
				  <option>-<?php echo $input_category_text; ?>-</option>
				</select>
			</div>
			<div class="form-group">
				<label><?php echo $entry_message_text; ?></label>
				<textarea class="form-control" name="messqage" placeholder="<?php echo $input_message_text; ?>" rows="5"><?php echo isset($post['message']) ? $post['message'] : ''; ?></textarea>
			</div>
			
			<div class="form-group">
				<label><?php echo $entry_captcha_text; ?></label>
				<div class="g-recaptcha" data-sitekey="6LemKAcUAAAAABEOfRTm4zooxDxwheDMQ2tN9KnL"></div>
			</div>
			
			<div class="form-group">
				<button type="submit" class="btn btn-primary"><?php echo $btn_request_text; ?></button>
			</div>
		</fieldset>
		</form>
	</div>

</div>

</div>

<script>

	$( document ).ready(function() {

		get_categories($('#page_selector').val());
		$('#page_selector').on('change', function() {
			get_categories(this.value);
		});
	
	});

	function get_categories(page_id)
	{
				
		$("#category_selector").html('<option>-<?php echo $input_category_text; ?>-</option>');

		var send_data = {
			uid: page_id
		}

		$.ajax({
			url: "/ajax/get_categories",
			type: 'post',
			dataType: 'json',
			success: function (data) {
				if ( data.length == 0 ) {
					console.log('no data');
				}

				var categories = [];
				$.each( data, function( key, val ) {
					var html_option = "<option value='" + key + "'>" + val + "</option>";
					<?php if(isset($post['category_id'])){ ?>
					
					var category_id = '<?php echo $post['category_id']; ?>';
					if(key == category_id){
						var html_option = "<option value='" + key + "' selected>" + val + "</option>";
					}
					
					<?php } ?>
					
					categories.push(html_option);
					
				});
				
				$("#category_selector").html(categories.join( "" ));	
			},
			data: send_data
		});
	}
</script>