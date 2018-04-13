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
												<label><?php echo $entry_group_name_text; ?></label>
												<input class="form-control" name="name" value="<?php echo isset($post['name']) ? $post['name'] : ''; ?>" required/>
											</div>
											
							<div class="form-group">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Functie</th>
												<th>Lezen</th>
												<th>Schrijven</th>
												<th>Verwijderen</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($permission_list as $permission){ ?>
											<tr>
												<td><?php echo $permission; ?></td>
												<td><input type="checkbox" name="read[]" value="<?php echo $permission; ?>" <?php if(isset($post['permissions']['read']) && in_array($permission, $post['permissions']['read'])){ ?> checked <?php } ?> /></td>
												<td><input type="checkbox" name="write[]" value="<?php echo $permission; ?>" <?php if(isset($post['permissions']['write']) && in_array($permission, $post['permissions']['write'])){ ?> checked <?php } ?> /></td>
												<td><input type="checkbox" name="delete[]" value="<?php echo $permission; ?>" <?php if(isset($post['permissions']['delete']) && in_array($permission, $post['permissions']['delete'])){ ?> checked <?php } ?> /></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							
							<div class="form-group required">
								<label><?php echo $entry_group_moderator_text; ?></label>
								<select class="form-control" name="moderator" required>
									<option value="0" <?php if(isset($post['moderator']) && $post['moderator'] == 0){ ?> selected <?php } ?>><?php echo $string_no; ?></option>
									<option value="1" <?php if(isset($post['moderator']) && $post['moderator'] == 1){ ?> selected <?php } ?>><?php echo $string_yes; ?></option>
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