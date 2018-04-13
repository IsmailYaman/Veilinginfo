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
												<input class="form-control" name="category_name" value="<?php echo isset($post['category_name']) ? $post['category_name'] : ''; ?>" required/>
											</div>
											
									</div>
									
									<div class="col-md-6">
											<fieldset>
										
											<div class="form-group required">
												<label><?php echo $entry_column_text; ?></label>
												<input type="number" min="1" max="<?php echo $column_count; ?>" class="form-control input-md" name="column_row" id="col_val" value="<?php echo isset($post['column_row']) ? $post['column_row'] : 1; ?>" required/>
											</div>
											
											<div class="form-group required">
												<label><?php echo $entry_sort_order_text; ?></label>
												<input type="number" min="0" class="form-control input-md" name="sort_order" id="sort_order" value="<?php echo isset($post['sort_order']) ? $post['sort_order'] : ''; ?>" required/>
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