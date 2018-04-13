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
												<label><?php echo $entry_page_text; ?></label>
												<input class="form-control" name="page_name" value="<?php echo isset($post['page_name']) ? $post['page_name'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_description_text; ?></label>
												<textarea rows="5" class="form-control" name="description" required><?php echo isset($post['description']) ? $post['description'] : ''; ?></textarea>
											</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>
											<?php if($is_superadmin){ ?>
											<?php if(isset($page_id) && $page_id > 1 || (!isset($page_id))){ ?>
											<div class="form-group required">
												<label><?php echo $entry_url_text; ?></label>
												<input class="form-control" name="url" value="<?php echo isset($post['url']) ? trim($post['url']) : ''; ?>" required/>
											</div>
											<?php } ?>
											<?php } ?>
											<?php if($is_superadmin){ ?>
											<div class="form-group required">
												<label><?php echo $entry_member_text; ?></label>
												<select class="form-control" id="page_selector" name="member_id" required>
													<option value="">-<?php echo $input_member_text; ?>-</option>
													<option value="0" <?php if(isset($post['member_id']) && $post['member_id'] == 0){ echo 'selected'; } ?>><?php echo $string_admin_text; ?>*</option>
													<?php foreach($members as $member){ ?>
													<?php $selected = ''; ?>
													<?php if(isset($post['member_id']) && $post['member_id'] == $member->member_id){ ?>
													<?php $selected = ' selected'; ?>
													<?php } ?>
													<option value="<?php echo $member->member_id; ?>" <?php echo $selected; ?>><?php echo $member->name; ?></option>
													<?php } ?>
												</select>
											</div>
											<?php } ?>

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
			$( document ).ready(function() {
				
				var token = "?token=<?php echo $token; ?>";
				var data_type = "<?php echo $data_type; ?>";
								
				get_max_sort($('#page_selector').val(), $('#col_val').val());

				$('#page_selector').on('change', function() {
					get_max_sort($('#page_selector').val(), $('#col_val').val());
				});
	
				$('#col_val').on('change', function() {
					get_max_sort($('#page_selector').val(), $(this).val());
					console.log($(this).val());
				});
	
				function get_max_sort(page_id, col){
					var send_data = {
						cid: page_id,
						col: col,
					}

					$.ajax({
						url: "/admin/ajax/max_category_sort" + token,
						type: 'post',
						dataType: 'json',
						success: function (data) {
							if ( data.length == 0 ) {
								console.log('no data');
							}
							var max_value = data.max;

							if(data_type == 'add')
							{
								max_value = +max_value + 1;
								$("#sort_order").val(max_value);
							}
							//console.log(max_value);
							$("#sort_order").attr('max', max_value);
							if($("#sort_order").val() > max_value)
							{
								$("#sort_order").val(max_value);
							}
							
						},
						data: send_data
					});
				}	
			});
			</script>