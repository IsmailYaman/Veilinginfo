               <div class="row">
                <div class="col-md-12">
                    <!-- Form Elements -->
					
					<?php $success = $this->session->flashdata('flash_message_success');
						  if($success){ ?>
						  
						  <div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
						  
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
					
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php echo $table_header_text; ?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
								<form role="form" action="<?php echo $action; ?>" method="post">
									<div class="col-md-6">	
											<div class="form-group required">
												<label><?php echo $entry_firstname_text; ?></label>
												<input class="form-control" name="firstname" value="<?php echo isset($post['firstname']) ? $post['firstname'] : ''; ?>" required/>
											</div>
											
											
											<div class="form-group required">
												<label><?php echo $entry_lastname_text; ?></label>
												<input class="form-control" name="lastname" value="<?php echo isset($post['lastname']) ? $post['lastname'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_email_text; ?></label>
												<input class="form-control" name="email" value="<?php echo isset($post['email']) ? $post['email'] : ''; ?>" required/>
											</div>	
											
											<div class="form-group required">
												<label><?php echo $entry_current_password_text; ?></label>
												<input type="password" class="form-control" name="current_password" placeholder="<?php echo $input_current_password_text; ?>" required/>
											</div>
											
									</div>
									
									<div class="col-md-6">	
											<div class="form-group required">
												<label><?php echo $entry_new_password_text; ?></label>
												<input type="password" class="form-control" name="password" placeholder="<?php echo $input_password_text; ?>"/>
											</div>
											
											
											<div class="form-group required">
												<label><?php echo $entry_new_password_confirm_text; ?></label>
												<input type="password" class="form-control" name="password2" placeholder="<?php echo $input_password_text; ?>"/>
											</div>
											
									</div>

									<div class="col-md-12">
									
										 <button type="submit" class="btn btn-default pull-right"><?php echo $btn_submit_text; ?></button>
									
									</div>
								</form>
                            </div>
                        </div>
                    </div>
                     <!-- End Form Elements -->
                </div>
            </div>