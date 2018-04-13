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
												<select class="form-control" id="page_selector" name="page_id" required>
													<option value="">-<?php echo $input_page_text; ?>-</option>
													<?php foreach($pages as $page){ ?>
													<?php $selected = ''; ?>
													<?php if(isset($post['page_id']) && $post['page_id'] == $page->page_id){ ?>
													<?php $selected = ' selected'; ?>
													<?php } ?>
													<option value="<?php echo $page->page_id; ?>" <?php echo $selected; ?>><?php echo $page->name; ?> - <?php echo $page->description; ?></option>
													<?php } ?>
												</select>
											</div>
											
										   <div class="form-group required">
												<label><?php echo $entry_category_text; ?></label>
												<select class="form-control" id="category_selector" name="category_id" required>
													<option value="">-<?php echo $input_category_text; ?>-</option>
												</select>
											</div>
										
											<div class="form-group required">
												<label><?php echo $entry_anchor_text; ?></label>
												<input class="form-control" name="anchor" value="<?php echo isset($post['anchor']) ? $post['anchor'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_description_text; ?></label>
												<textarea rows="5" class="form-control" name="description" required><?php echo isset($post['description']) ? $post['description'] : ''; ?></textarea>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_url_text; ?></label>
												<input class="form-control" name="url" placeholder="<?php echo $input_url_text; ?>" value="<?php echo isset($post['url']) ? $post['url'] : ''; ?>" required/>
											</div>
											
											<div class="form-group">
												<label><?php echo $entry_backlink_text; ?></label>
												<input class="form-control" name="backlink" placeholder="<?php echo $input_backlink_text; ?>" value="<?php echo isset($post['backlink']) ? $post['backlink'] : ''; ?>" />
											</div>

									 
									</div>
									
									<div class="col-md-6">
											<fieldset>
										
											<div class="form-group">
												<label><?php echo $entry_email_text; ?></label>
												<input class="form-control" name="email" placeholder="<?php echo $input_email_text; ?>" value="<?php echo isset($post['email']) ? $post['email'] : ''; ?>" />
											</div>
											
											<div class="form-group">
												<input type="checkbox" value="1" name="no_follow" <?php echo isset($post['no_follow']) ? 'checked' : ''; ?>/>
												<label><?php echo $entry_no_follow_text; ?></label>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_sort_order_text; ?></label>
												<input type="number" min="0" class="form-control input-md" name="sort_order" id="sort_order" value="<?php echo isset($post['sort_order']) ? $post['sort_order'] : ''; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_expire_date_text; ?></label>
												<select class="form-control" id="select_expire_date" name="expire_date" required>
												<?php foreach($expire_list as $n=>$v){ ?>
													<?php $selected = ''; ?>
													<?php if(isset($post['expire_date']) && $post['expire_date'] == $n){ ?>
													<?php $selected = ' selected'; ?>
													<?php } ?>
													<option value="<?php echo $n; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
												<?php } ?>
												</select>
											</div>
											
											<div class="form-group" id="custom_expire_date" style="display:none;">
												<input class="form-control" placeholder="<?php echo $input_custom_date_text; ?>" id="select_custom_expire_date" onfocus="this.blur()" name="custom_expire_date" value="<?php echo isset($post['custom_expire_date']) ? $post['custom_expire_date'] : ''; ?>" />
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
			$( document ).ready(function() {
				
				var token = "?token=<?php echo $token; ?>";
				var data_type = "<?php echo $data_type; ?>";
								
				get_categories($('#page_selector').val());
				show_expire_field($('#select_expire_date').val());
				
				$( "#select_custom_expire_date" ).datepicker();
				
				$('#page_selector').on('change', function() {
					get_categories(this.value);
				});
				
				$("#category_selector").on('change', function() {
					get_max_sort(this.value);
				});
				

				$('#select_expire_date').on('change', function() {
					show_expire_field($('#select_expire_date').val());
				});
				
				function show_expire_field(id){
					if(id == "custom"){
						$("#custom_expire_date").show();
					} else {
						$("#custom_expire_date").hide();
					}
				}
				
				function get_categories(page_id)
				{
					$("#category_selector").html('<option>-<?php echo $input_category_text; ?>-</option>');

					var send_data = {
						uid: page_id
					}

					$.ajax({
						url: "/admin/ajax/get_categories" + token,
						type: 'post',
						dataType: 'json',
						success: function (data) {
							if ( data.length == 0 ) {
								console.log('no data');
							}

							var categories = [];
							$.each( data, function( key, val ) {
								var html_option = "<option value='" + key + "'>" + val + "</option>";
								<?php if(isset($post['category_id'])){ ?>
								
								var category_id = '<?php echo $post['category_id']; ?>';
								if(key == category_id){
									var html_option = "<option value='" + key + "' selected>" + val + "</option>";
								}
								
								<?php } ?>
								
								categories.push(html_option);
								
							});
							
							$("#category_selector").html(categories.join( "" ));
							get_max_sort($('#category_selector').val());
							
						},
						data: send_data
					});
				}
				
				function get_max_sort(category_id){
					var send_data = {
						cid: category_id
					}

					$.ajax({
						url: "/admin/ajax/max_link_sort" + token,
						type: 'post',
						dataType: 'json',
						success: function (data) {
							if ( data.length == 0 ) {
								console.log('no data');
							}
							var max_value = data.max;
							if(data_type == 'add')
							{
								max_value = +max_value;
								$("#sort_order").val(max_value);
							}

							$("#sort_order").attr('max', max_value);
							
						},
						data: send_data
					});
				}	
			});
			</script>