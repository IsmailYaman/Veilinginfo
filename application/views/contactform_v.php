
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-offset-2">

<?php if(isset($error_message)){ ?>
<div class="alert alert-warning text-center" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>

<?php if(isset($errors) && count($errors) > 0){  ?>
<div class="alert alert-warning text-center" role="alert">
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
	  
	  <div class="alert alert-success alert-dismissable text-center" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
	  
<?php } ?>

</div>
</div>
</div>

	<div class="container contact">
		<div class="col-sm-12 col-md-6 col-sm-6">
	
			<?php if(isset($msg_contactform_info)){ ?>
			<h3 class="pull-left">
			  <?php echo $msg_contactform_info; ?>
			</h3>
			<?php } ?>
			
			<br/>
			
			<form class="form-horizontal" method="post" action="" id="protected_form">
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
					<label><?php echo $entry_message_text; ?></label>
					<textarea class="form-control" name="message" rows="5" required><?php echo isset($post['message']) ? $post['message'] : ''; ?></textarea>
				</div>
				
				<div class="form-group">
					<label><?php echo $entry_captcha_text; ?></label>
					<div class="g-recaptcha" data-sitekey="6Lc6b0EUAAAAAO9vQkEuS3hMq02Ql3cC7437qfyz"></div>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-raised"><?php echo $btn_send_text; ?></button>
				</div>
			</fieldset>
			</form>
		</div>
	</div>

