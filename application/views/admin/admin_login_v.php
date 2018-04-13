	<div class="row ">
		<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
			<?php if(isset($warning)){ ?>  
			<div class="alert alert-danger alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>Error!</strong> <?php echo $warning; ?></div>	  
			<?php } ?>
			<?php $success = $this->session->flashdata('flash_message_success');
				  if($success){ ?>
				  <div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
			<?php } ?>
			<?php echo validation_errors(); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong><?php echo $string_login_text; ?></strong>  
				</div>
				<div class="panel-body">
					<form role="form" action="" method="post">
						<br />
						<div class="form-group input-group">
							<span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
							<input type="text" class="form-control" placeholder="<?php echo $input_email_text; ?>" name="s_email" value="<?php echo isset($post['s_email']) ? $post['s_email'] : ''; ?>" autofocus required/>
						</div>
						<div class="form-group input-group">
							<span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
							<input type="password" class="form-control"  placeholder="<?php echo $input_password_text; ?>" name="s_password" required />
						</div>
						<div class="form-group">
							<span class="pull-right">
								   <a href="<?php echo $href_reset; ?>" title="<?php echo $string_forgot_password; ?>"><?php echo $string_forgot_password; ?></a> 
							</span>
						</div>
						 <button class="btn btn-primary "><?php echo $btn_login; ?></button>
					</form>
				</div>
			</div>
		</div>
	</div>