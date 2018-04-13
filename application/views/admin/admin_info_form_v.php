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
												<label><?php echo $entry_title_text; ?></label>
												<input class="form-control" name="title" value="<?php echo isset($post['title']) ? $post['title'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_text; ?></label>
												<textarea rows="5" class="form-control" name="text" id="editor1" required><?php echo isset($post['text']) ? $post['text'] : ''; ?></textarea>
											</div>
											
												<div class="form-group required">
													<label><?php echo $entry_description_text; ?></label>
													<textarea rows="5" class="form-control" name="description" required><?php echo isset($post['description']) ? $post['description'] : ''; ?></textarea>
												</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>

												<div class="form-group required">
													<label><?php echo $entry_slug_text; ?></label>
													<input class="form-control" name="slug" value="<?php echo isset($post['slug']) ? $post['slug'] : ''; ?>" required/>
												</div>
												
												<div class="form-group required">
													<label><?php echo $entry_menu_text; ?></label>
													<input class="form-control" name="menu_title" value="<?php echo isset($post['menu_title']) ? $post['menu_title'] : ''; ?>" required/>
												</div>
												
												<div class="form-group required">
													<label><?php echo $entry_status_text; ?></label>
													<select name="active" class="form-control" required>
														<option value="0"><?php echo $string_inactive; ?></option>
														<option value="1" <?php if(isset($post['active']) && $post['active'] == 1){ echo 'selected'; } ?>><?php echo $string_active; ?></option>
													</select>
												</div>

												<div class="form-group required">
													<label><?php echo $entry_menutype_text; ?></label>
													<select name="menutype" class="form-control" required>
														<?php if($menus){ ?>
															<?php foreach($menus as $menu){ ?>
																<?php if(isset($post['menutype']) && $post['menutype'] == $menu->menu_id){ ?>
																<option value="<?php echo $menu->menu_id; ?>" selected><?php echo $menu->menu_title; ?></option>
																<?php } else { ?>
																<option value="<?php echo $menu->menu_id; ?>"><?php echo $menu->menu_title; ?></option>
																<?php } ?>
															<?php } ?>
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
			<script>
				CKEDITOR.replace( 'editor1' );
			</script>