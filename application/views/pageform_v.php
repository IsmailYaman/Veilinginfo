<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>

<?php if(isset($msg_pageform_info)){ ?>
<div class="alert alert-info" role="alert">
  <?php echo $msg_pageform_info; ?>
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
				<label><?php echo $entry_page_request_text; ?></label>
				<input type="text" class="form-control" name="page" placeholder="<?php echo $input_page_request_text; ?>" value="<?php echo isset($post['page']) ? $post['page'] : ''; ?>" required>
			</div>
			<div class="form-group">
				<label><?php echo $entry_message_text; ?></label>
				<textarea class="form-control" name="message" placeholder="<?php echo $input_message_text; ?>" rows="5"><?php echo isset($post['message']) ? $post['message'] : ''; ?></textarea>
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