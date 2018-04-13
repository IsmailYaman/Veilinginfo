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
								<form role="form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
									<div class="col-md-6">	
											<div class="form-group required">
												<label><?php echo $entry_title_text; ?></label>
												<input class="form-control" name="title" value="<?php echo isset($post['title']) ? $post['title'] : ''; ?>" required/>
											</div>
		
											<div class="form-group required">
												<label><?php echo $entry_body_text; ?></label>
												<textarea rows="10" class="form-control" name="body" required><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
											</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>

												<div class="form-group required">
													<label><?php echo $entry_link_text; ?></label>
													<input class="form-control" name="link" value="<?php echo isset($post['link']) ? $post['link'] : ''; ?>" required/>
												</div>
												<?php if($data_type == "edit" && isset($post['media'])){ ?>
												<div class="form-group required">
													<input type="hidden" name="media" value="<?php echo isset($post['media']) ? $post['media'] : ''; ?>" required/>
													<img src="<?php echo $post['media']; ?>" width="250" />
												</div>
												<?php } ?>
												
												<div class="form-group required">
													<label><?php echo $entry_media_text; ?></label>
													<input class="form-control" type='file' name='media_image' size='20' />
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