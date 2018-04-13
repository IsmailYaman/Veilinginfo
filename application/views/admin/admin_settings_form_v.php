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
												<label><?php echo $entry_site_title_text; ?></label>
												<input class="form-control" name="site_title" value="<?php echo isset($post['site_title']) ? $post['site_title'] : ''; ?>" required/>
											</div>
											
											
											<div class="form-group required">
												<label><?php echo $entry_email_from_address_text; ?></label>
												<input class="form-control" name="email_from_address" value="<?php echo isset($post['email_from_address']) ? $post['email_from_address'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_email_from_name_text; ?></label>
												<input class="form-control" name="email_from_name" value="<?php echo isset($post['email_from_name']) ? $post['email_from_name'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_site_language_text; ?></label>
												<select class="form-control" name="site_language">
													<option><?php echo $input_language_text; ?></option>
												<?php foreach($languages as $language){ ?>
													<?php if(isset($post['site_language']) && $post['site_language'] == $language->language_id){  ?>
													<option value="<?php echo $language->language_id; ?>" selected><?php echo $language->name; ?></option>
													<?php } else { ?>
													<option value="<?php echo $language->language_id; ?>"><?php echo $language->name; ?></option>
													<?php } ?>
												<?php } ?>
												</select>
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