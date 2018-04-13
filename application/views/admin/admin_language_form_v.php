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
												<label><?php echo $entry_language_text; ?></label>
												<input class="form-control" name="name" value="<?php echo isset($post['name']) ? $post['name'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_machine_name_text; ?></label>
												<input class="form-control" name="machine_name" value="<?php echo isset($post['machine_name']) ? $post['machine_name'] : ''; ?>" required/>
											</div>
											
											<hr />
											
											<div class="form-group required">
												<label><?php echo $entry_aliases_text; ?></label>
												<?php foreach($aliases as $alias){ ?>
												<input class="form-control" name="alias[<?php echo $alias['name']; ?>]" placeholder="<?php echo $alias['field']; ?>" value="<?php echo isset($post['alias'][$alias['name']]) ? $post['alias'][$alias['name']] : ''; ?>" required/><br/>
												<?php } ?>
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