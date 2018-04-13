			<?php $success = $this->session->flashdata('flash_message_success');
				  if($success){ ?>
				  
				  <div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
				  
			<?php } ?>
			<?php $error = $this->session->flashdata('flash_message_error');
				  if($error){ ?>
				  
				  <div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $error; ?></div>
				  
			<?php } ?>

			<div class="row">
				<div class="col-md-12">
					<form action="<?php echo $action_filter; ?>" method="get">
						<div class="form-group pull-left">
							<select class="form-control" onchange="this.form.submit();" name="limit">
								<?php foreach($limit_list as $limits){?>
								<?php if($limits == $limit){?>
								<option value="<?php echo $limits; ?>" selected><?php echo $limits; ?></option>
								<?php } else { ?>
								<option value="<?php echo $limits; ?>"><?php echo $limits; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
						<div class="form-group pull-right" style="width:50%;">
							<div class="input-group">
								<input type="text" class="search form-control" placeholder="<?php echo $input_search_text; ?>" value="<?php echo $filter['search'] ? clean_output($filter['search']) : ''; ?>" name="search">
								<span class="form-group input-group-btn">
									<button class="btn btn-default" type="submit"><?php echo $btn_search_text; ?></button>
								</span>
							</div>
						</div>
						<input type="hidden" name="token" value="<?php echo $token; ?>" />
					</form>
				</div>
			</div>

            <div class="row">
                <div class="col-md-12">

                  <!--   Kitchen Sink --> 
                    <div class="panel panel-default">
                        <div class="panel-heading">
							<?php echo $table_header_text; ?>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
								<div class="form-inline">
									<form action="" method="post" id="table-form">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th class="w3"><input type="checkbox" id="select_all" /></th>
												<th><?php echo $column_type_text; ?></th>

												
												<th><?php echo $column_name_text; ?></th>
												<th><?php echo $column_email_text; ?></th>
												<th><?php echo $column_message_text; ?></th>
												
												<th><?php echo $column_data_name_text; ?></th>
												<th><?php echo $column_data_url_text; ?></th>
												
												<th class="w20"><?php echo $column_action_text; ?></th>
											</tr>
										</thead>
										
										<thead>
											<tr class="filters">
												<th>&nbsp;</th>
												<th>
													<select name="filter_type" class="form-control">
														<option></option>
														<option value="1" <?php if(isset($filter['type']) && $filter['type'] == 1){ ?>selected<?php } ?>><?php echo $text_page; ?></option>
														<option value="2" <?php if(isset($filter['type']) && $filter['type'] == 2){ ?>selected<?php } ?>><?php echo $text_link; ?></option>
														<option value="3" <?php if(isset($filter['type']) && $filter['type'] == 3){ ?>selected<?php } ?>><?php echo $text_contact; ?></option>
													</select>
												</th>
												<th><input type="text" class="form-control" name="filter_name" value="<?php echo $filter['name'] ? clean_output($filter['name']) : ''; ?>" /></th>
												<th><input type="text" class="form-control" name="filter_email" value="<?php echo $filter['email'] ? clean_output($filter['email']) : ''; ?>" /></th>
												
												<th><input type="text" class="form-control" disabled="" /></th>
												<th><input type="text" class="form-control" disabled="" /></th>
												<th><input type="text" class="form-control" disabled="" /></th>
												<th>
												<a onclick="filter();" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo $btn_filter_text; ?></a>
												</th>
											</tr>
										</thead>
										
										<tbody>
										<?php if($messages){?>
										<?php foreach($messages as $message){ ?>
											<tr>
												<td><input type="checkbox" name="message_id[]" value="<?php echo $message['message_id']; ?>" /></td>
												
												<td><?php echo ($message['type'] == 1) ? $text_page : (($message['type'] == 2) ? $text_link : (($message['type'] == 3) ? $text_contact : 'Error')); ?></td>
												
												<td><?php echo clean_output($message['firstname']); ?> <?php echo clean_output($message['lastname']); ?></td>
												<td><?php echo clean_output($message['email']); ?></td>
												<td><?php echo $message['message'] ? clean_output($message['message']) : $string_no_message; ?> <?php if(isset($message['message'])){ ?><a href="javascript:void(0);" onclick="details(this)" data-message="<?php echo nl2br(clean_output($message['message_full'])); ?>" data-name="<?php echo clean_output($message['firstname']); ?> <?php echo clean_output($message['lastname']); ?>"><?php echo $btn_read_more_text; ?></a><?php } ?></td>
												
												<td><?php echo clean_output($message['data_name']); ?></td>
												<td><?php echo clean_output($message['data_link']); ?></td>
												
												 <td>
												 <?php if($status == 0){ ?>
												 <a href="<?php echo $message['href_approve']; ?>" class="btn btn-success" style="margin-top:5px;" id="approve<?php echo $message['data_id']; ?>"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $btn_approve_text; ?></a>
												 <a onclick="<?php echo ($message['type'] == 1) ? 'get_page_detail('.$message['data_id'].')' : (($message['type'] == 2) ? 'get_link_detail('.$message['data_id'].')' : (($message['type'] == 3) ? 'get_contact_detail('.$message['message_id'].')' : 'Error')); ?>" class="btn btn-primary" style="margin-top:5px;"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $btn_view_text; ?></a>
												<?php } ?>
												</td>
											</tr>
										<?php } ?>
										<?php } else { ?>
										<tr>
											<td colspan="8"><center><?php echo $text_no_results; ?></center></td>
										</tr>
										<?php } ?>
										</tbody>
									</table>
									</form>
								</div>
							</div>	
							
							<div class="pull-left">
								<a class="btn btn-danger" onclick="cat_del()"><i class="fa fa-trash" aria-hidden="true"></i> <?php echo $btn_delete_selected_text; ?></a>
							</div>
							
							<ul class="pagination pull-right">
							<?php echo $pagination; ?>
							</ul>
                        </div>


                    </div>
                     <!-- End  Kitchen Sink -->
                </div>
            </div>
            <!-- /. ROW  -->
			
			<div id="dialog" style="display:none;">
				
			</div>
			
			<div id="dialog_link" style="display:none;">
					<div class="panel panel-default">
                        <div class="panel-body">
							<ul class="details">
                            <li><label><?php echo $string_anchor; ?>:</label> <span id="link_anchor"></span></li>
                            <li><label><?php echo $string_link; ?>:</label> <span id="link_href"></span></li>
                            <li><label><?php echo $string_backlink; ?>:</label> <span id="link_back"></span></li>

                            <li class="title"><?php echo $string_place_on; ?>:</li>
							
                            <li><label><?php echo $string_page; ?>:</label> <span id="link_page_name"></span></li>
                            <li><label><?php echo $string_category; ?>:</label> <span id="link_cat_name"></span></li>
							</ul>
                        </div>
                    </div>
					<a class="btn btn-success pull-right" style="color:#fff;text-decoration:none;margin-bottom:5px;" id="link_approve"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $btn_approve_text; ?></a>
			</div>
			
			<div id="dialog_page" style="display:none;">
					<div class="panel panel-default">
                        <div class="panel-body">
							<ul class="details">
                            <li><label><?php echo $string_page_name; ?>:</label> <span id="page_name"></span></li>
                            <li><label><?php echo $string_page_url; ?>:</label> <span id="page_url"></span></li>
							</ul>
                        </div>
                    </div>
					<a class="btn btn-success pull-right" style="color:#fff;text-decoration:none;margin-bottom:5px;" id="page_approve"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $btn_approve_text; ?></a>
			</div>
			
			<script>
				var token = "?token=<?php echo $token; ?>";

				function cat_del()
				{
					$('#table-form').attr('action', '<?php echo $href_remove; ?>');
					$('#table-form').submit();		
				}
				
				function filter() {
					url = '<?php echo $href_filter; ?>';
					
					var filter_type = $('select[name=\'filter_type\']').val();
					if (filter_type) {
						url += '&filter_type=' + encodeURIComponent(filter_type);
					}
					
					var filter_name = $('input[name=\'filter_name\']').val();
					if (filter_name) {
						url += '&filter_name=' + encodeURIComponent(filter_name);
					}
					
					var filter_email = $('input[name=\'filter_email\']').val();
					if (filter_email) {
						url += '&filter_email=' + encodeURIComponent(filter_email);
					}	

					var filter_limit = $('select[name=\'limit\']').val();
					if (filter_limit) {
						url += '&limit=' + encodeURIComponent(filter_limit);
					}	

					location = url;
				}
				
				function details(element){
					
					$( "#dialog" ).html($(element).data('message'));
					$( "#dialog" ).dialog({
						width: 400,
						title: $(element).data('name'),
						draggable: false,
						modal:	true
					});

				}
				
				function get_link_detail(link_id)
				{

					var send_data = {
						uid: link_id
					}

					$.ajax({
						url: "/admin/ajax/get_link_details" + token,
						type: 'post',
						dataType: 'json',
						success: function (data) {
							if ( data.length == 0 ) {
								console.log('no data');
							}
							$("#link_anchor").html(data.anchor);
							$("#link_href").html(data.url);
							$("#link_back").html(data.backlink);
							$("#link_cat_name").html(data.cat_name);
							$("#link_page_name").html(data.page_name);
							$("#link_approve").attr('href', $("#approve"+link_id).attr('href'));
							
							$( "#dialog_link" ).dialog({
									width: 500,
									title: "Link Details",
									draggable: false,
									modal:	true
							});
							
						},
						data: send_data
					});
				}
				
				function get_page_detail(page_id)
				{

					var send_data = {
						uid: page_id
					}

					$.ajax({
						url: "/admin/ajax/get_page_details" + token,
						type: 'post',
						dataType: 'json',
						success: function (data) {
							if ( data.length == 0 ) {
								console.log('no data');
							}
							$("#page_name").html(data.page_name);
							$("#page_url").html(data.url);
							$("#page_approve").attr('href', $("#approve"+page_id).attr('href'));
							
							$( "#dialog_page" ).dialog({
									width: 500,
									title: "Page Details",
									draggable: false,
									modal:	true
							});
							
						},
						data: send_data
					});
				}
			
			</script>