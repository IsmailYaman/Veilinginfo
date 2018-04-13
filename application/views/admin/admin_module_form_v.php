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
												<label><?php echo $entry_name_text; ?></label>
												<input class="form-control" name="name" value="<?php echo isset($post['name']) ? $post['name'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_machine_name_text; ?></label>
												<input class="form-control" name="machine_name" value="<?php echo isset($post['machine_name']) ? $post['machine_name'] : ''; ?>" required/>
											</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>

											<div class="form-group required">
												<label><?php echo $entry_column_text; ?></label>
												<input type="number" min="1" max="<?php echo $column_count; ?>" class="form-control input-md" name="column_row" value="<?php echo isset($post['column_row']) ? $post['column_row'] : 1; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_sort_order_text; ?></label>
												<input type="number" min="0" class="form-control input-md" name="sort_order" value="<?php echo isset($post['sort_order']) ? $post['sort_order'] : ''; ?>" required/>
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