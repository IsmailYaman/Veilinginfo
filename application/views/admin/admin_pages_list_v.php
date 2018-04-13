			<?php $success = $this->session->flashdata('flash_message_success');
				  if($success){ ?>
				  
				  <div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo $success; ?></div>
				  
			<?php } ?>
			<?php $error = $this->session->flashdata('flash_message_error');
				  if($error){ ?>
				  
				  <div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $error; ?></div>
				  
			<?php } ?>

			<?php if($is_superadmin || $can_add_page){ ?>
			<div class="row">
				<div class="col-md-12">
					<a href="<?php echo $href_add; ?>" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo $btn_add_text; ?></a>
				</div>
			</div>

			<hr />
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
													<th><?php echo $column_url_text; ?></th>
													<th><?php echo $column_page_text; ?></th>
													<th class="w40"><?php echo $column_description_text; ?></th>
													<?php if($is_superadmin){ ?>
													<th><?php echo $column_member_text; ?></th>
													<?php } ?>
													<th class="w20" ><?php echo $column_action_text; ?></th>
												</tr>
											</thead>
											
											<thead>
												<tr class="filters">
													<th>&nbsp;</th>
													<th><input type="text" class="form-control" name="filter_url" value="<?php echo $filter['url'] ? clean_output($filter['url']) : ''; ?>" /></th>
													<th><input type="text" class="form-control" name="filter_page" value="<?php echo $filter['name'] ? clean_output($filter['name']) : ''; ?>" /></th>
													<th><input type="text" class="form-control" name="filter_description" value="<?php echo $filter['description'] ? clean_output($filter['description']) : ''; ?>" /></th>
													<?php if($is_superadmin){ ?>
													<th>
													<?php if($members){ ?>
													<select name="filter_member" class="form-control">
														<option></option>
														<option value="0" <?php if(isset($filter['member_id']) && strlen($filter['member_id']) > 0 && $filter['member_id'] == 0){ echo 'selected'; } ?>><?php echo $string_admin_text; ?>*</option>
													<?php foreach($members as $member){ ?>
													<?php $selected = '';?>
													<?php if(isset($filter['member_id']) && $filter['member_id'] == $member->member_id){ ?>
													<?php $selected = " selected"; ?>
													<?php } ?>
														<option value="<?php echo $member->member_id; ?>" <?php echo $selected; ?>><?php echo $member->name; ?></option>
													<?php } ?>
													</select>
													<?php } ?>
													</th>
													<?php } ?>
													<th>
													<a onclick="filter();" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo $btn_filter_text; ?></a>
													</th>
												</tr>
											</thead>
											
											<tbody>
											<?php if($pages){?>
											<?php foreach($pages as $page){ ?>
												<tr>
													<td><?php if($page['page_id'] > 1){ ?><input type="checkbox" name="page_id[]" value="<?php echo $page['page_id']; ?>" /><?php } ?></td>
													<td><a href="<?php echo $page['url'] ? format_page_url($page['url']) : base_url(); ?>" target="_blank" title="<?php echo clean_output($page['url']); ?>"><?php echo $page['url'] ? clean_output($page['url']) : clean_output($page['name']); ?></a></td>
													<td><?php echo clean_output($page['name']); ?></td>
													<td><?php echo clean_output($page['description']); ?><?php if(empty($page['description'])){ ?><div class="alert alert-warning alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $msg_empty_description; ?></div><?php } ?></td>
													<?php if($is_superadmin){ ?>
													<td><?php echo $page['member'] ? clean_output($page['member']) : '<strong>' . $string_admin_text . '</atrong>'; ?></td>
													<?php } ?>
													
													<td>
														<a href="<?php echo $page['href_edit']; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo $btn_edit_text; ?></a>
														<a href="<?php echo $page['href_category']; ?>" class="btn btn-info"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo $btn_link_view_text; ?></a>
													</td>
												</tr>
											<?php } ?>
											<?php } else { ?>
											<tr>
												<td colspan="7"><center><?php echo $text_no_results; ?></center></td>
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
			<script>
			
			function cat_del()
			{
				if(confirm("<?php echo $confirm_delete_text; ?>"))
				{
					$('#table-form').attr('action', '<?php echo $href_remove; ?>');
					$('#table-form').submit();		
				}

			}
			
			function filter() {
				url = '<?php echo $href_filter; ?>';
				
				var filter_description = $('input[name=\'filter_description\']').val();
				if (filter_description) {
					url += '&filter_description=' + encodeURIComponent(filter_description);
				}
				
				var filter_page = $('input[name=\'filter_page\']').val();
				if (filter_page) {
					url += '&filter_page=' + encodeURIComponent(filter_page);
				}	
				
				var filter_url = $('input[name=\'filter_url\']').val();
				if (filter_url) {
					url += '&filter_url=' + encodeURIComponent(filter_url);
				}	
				
				var filter_member = $('select[name=\'filter_member\']').val();
				if (filter_member) {
					url += '&filter_member=' + encodeURIComponent(filter_member);
				}	
				
				
				
				var filter_limit = $('select[name=\'limit\']').val();
				if (filter_limit) {
					url += '&limit=' + encodeURIComponent(filter_limit);
				}	

				location = url;
			}
			
			</script>