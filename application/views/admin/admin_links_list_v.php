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
					<a href="<?php echo $href_add; ?>" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo $btn_add_text; ?></a>
				</div>
			</div>

			<hr />

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
												<th><input type="checkbox" id="select_all" /></th>
												<th><?php echo $column_anchor_text; ?></th>
												<th><?php echo $column_url_text; ?></th>
												<th><?php echo $column_backlink_text; ?></th>
												<th><?php echo $column_category_text; ?></th>
												<th><?php echo $column_page_text; ?></th>
												<th><?php echo $column_expire_text; ?></th>
												<th><?php echo $column_action_text; ?></th>
											</tr>
										</thead>
										
										<thead>
											<tr class="filters">
												<th>&nbsp;</th>
												<th><input type="text" class="form-control" name="filter_anchor" value="<?php echo $filter['anchor'] ? clean_output($filter['anchor']) : ''; ?>" /></th>
												<th><input type="text" class="form-control" name="filter_url" value="<?php echo $filter['url'] ? clean_output($filter['url']) : ''; ?>" /></th>
												<th><input type="text" class="form-control" name="filter_backlink" value="<?php echo $filter['backlink'] ? clean_output($filter['backlink']) : ''; ?>" /></th>
												<th>
												<?php if($categories){ ?>
												<select name="filter_category" class="form-control">
													<option></option>
												<?php foreach($categories as $category){ ?>
												<?php $selected = '';?>
												<?php if(isset($filter['category_id']) && $filter['category_id'] == $category->category_id){ ?>
												<?php $selected = " selected"; ?>
												<?php } ?>
													<option value="<?php echo $category->category_id; ?>" <?php echo $selected; ?>><?php echo $category->name; ?></option>
												<?php } ?>
												</select>
												<?php } ?>
												</th>
												<th>
												<?php if($pages){ ?>
												<select name="filter_page" class="form-control">
													<option></option>
												<?php foreach($pages as $page){ ?>
												<?php $selected = '';?>
												<?php if(isset($filter['page_id']) && $filter['page_id'] == $page->page_id){ ?>
												<?php $selected = " selected"; ?>
												<?php } ?>
													<option value="<?php echo $page->page_id; ?>" <?php echo $selected; ?>><?php echo $page->name; ?></option>
												<?php } ?>
												</select>
												<?php } ?>
												</th>
												<td>&nbsp;</td>
												<td><a onclick="filter();" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo $btn_filter_text; ?></a></td>
											</tr>
										</thead>
										
										<tbody>
										<?php if($links){?>
										<?php foreach($links as $link){ ?>
											<tr>
												<td><input type="checkbox" name="link_id[]" value="<?php echo $link['link_id']; ?>" /></td>
												<td><?php echo clean_output($link['anchor']); ?></td>
												 <td><a href="<?php echo clean_output($link['url']); ?>" target="_blank"><?php echo clean_output($link['url']); ?></a></td>
												 <td><?php echo $link['backlink'] ? '<a href="' . clean_output($link['backlink']) . '" target="_blank">' . clean_output($link['backlink']) . '</a></td>' : $text_no_backlink; ?>
												 <td><?php echo clean_output($link['category']); ?></td>
												 <td><a href="<?php echo $link['page_url'] ? format_page_url($link['page_url']) : base_url(); ?>" target="_blank" title="<?php echo clean_output($link['page']); ?>"><?php echo clean_output($link['page']); ?></a></td>
												 <td class="<?php echo $link['expire_date'] <= time() ? 'red' : ''; ?>"><?php echo date('m/d/Y', $link['expire_date']); ?></td>
												 <td><a href="<?php echo $link['href_edit']; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo $btn_edit_text; ?></a></td>
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
								<a class="btn btn-danger" onclick="$('#table-form').attr('action', '<?php echo $href_remove; ?>'); $('#table-form').submit();"><i class="fa fa-trash" aria-hidden="true"></i> <?php echo $btn_delete_selected_text; ?></a>
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
			
			function filter() {
				url = '<?php echo $href_filter; ?>';
				
				var filter_anchor = $('input[name=\'filter_anchor\']').val();
				console.log(filter_anchor);
				if (filter_anchor) {
					url += '&filter_anchor=' + encodeURIComponent(filter_anchor);
				}
				
				var filter_url = $('input[name=\'filter_url\']').val();
				if (filter_url) {
					url += '&filter_url=' + encodeURIComponent(filter_url);
				}
				
				var filter_backlink = $('input[name=\'filter_backlink\']').val();
				if (filter_backlink) {
					url += '&filter_backlink=' + encodeURIComponent(filter_backlink);
				}
				
				var filter_category = $('select[name=\'filter_category\']').val();
				if (filter_category) {
					url += '&filter_category=' + encodeURIComponent(filter_category);
				}	
				
				var filter_page = $('select[name=\'filter_page\']').val();
				if (filter_page) {
					url += '&filter_page=' + encodeURIComponent(filter_page);
				}	
				
				var filter_limit = $('select[name=\'limit\']').val();
				if (filter_limit) {
					url += '&limit=' + encodeURIComponent(filter_limit);
				}	


				location = url;
			}
			
			</script>