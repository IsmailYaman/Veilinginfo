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
					<a href="<?php echo $href_add; ?>" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo $btn_add_group_text; ?></a>
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
													<th class="w3"><input type="checkbox" id="select_all" /></th>
													<th><?php echo $column_group_text; ?></th>
													<th class="w5"><?php echo $column_action_text; ?></th>
												</tr>
											</thead>
											
											<thead>
												<tr class="filters">
													<th>&nbsp;</th>
													<th><input type="text" class="form-control" name="filter_name" value="<?php echo $filter['name'] ? clean_output($filter['name']) : ''; ?>" /></th>
													<th>
													<a onclick="filter();" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo $btn_filter_text; ?></a>
													</th>
												</tr>
											</thead>
											
											<tbody>
											<?php if($groups){?>
											<?php foreach($groups as $group){ ?>
												<tr>
													<td><input type="checkbox" name="member_group_id[]" value="<?php echo $group['member_group_id']; ?>" /></td>
													<td><?php echo clean_output($group['name']); ?> (<?php echo $group['member_group_id']; ?>)</td>
													 <td>
													 <a href="<?php echo $group['href_edit']; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo $btn_edit_text; ?></a>
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
				$('#table-form').attr('action', '<?php echo $href_remove; ?>');
				$('#table-form').submit();		
			}
			
			function filter() {
				url = '<?php echo $href_filter; ?>';
				
				var filter_name = $('input[name=\'filter_name\']').val();
				if (filter_name) {
					url += '&filter_name=' + encodeURIComponent(filter_name);
				}

				location = url;
			}
			
			</script>