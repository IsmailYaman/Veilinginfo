               <div class="row">
                <div class="col-md-12">
                    <!-- Form Elements -->
					
					<?php echo validation_errors(); ?>
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
												<input class="form-control" name="firstname" value="<?php echo isset($post['firstname']) ? clean_output($post['firstname']) : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_lastname_text; ?></label>
												<input class="form-control" name="lastname" value="<?php echo isset($post['lastname']) ? clean_output($post['lastname']) : ''; ?>" required/>
											</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>
										
											<div class="form-group required">
												<label><?php echo $entry_email_text; ?></label>
												<input class="form-control" name="email" value="<?php echo isset($post['email']) ? clean_output(trim($post['email'])) : ''; ?>" required/>
											</div>

											<div class="form-group required">
												<label><?php echo $entry_group_text; ?></label>
												<select class="form-control" name="member_group_id" required>
													<option value="">-<?php echo $input_group_text; ?>-</option>
													<?php foreach($member_groups as $member_group){ ?>
													<?php $selected = ''; ?>
													<?php if(isset($post['member_group_id']) && $post['member_group_id'] == $member_group->member_group_id){ ?>
													<?php $selected = ' selected'; ?>
													<?php } ?>
													<option value="<?php echo $member_group->member_group_id; ?>" <?php echo $selected; ?>><?php echo clean_output($member_group->name); ?></option>
													<?php } ?>
												</select>
											</div>

											</fieldset>
								  

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