<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<title><?php echo $site_title; ?></title>
	
	<!-- BOOTSTRAP STYLES-->
	<link href="<?php echo base_url(); ?>assets/admin/css/bootstrap.css" rel="stylesheet" />
	<!-- FONTAWESOME STYLES-->
	<link href="<?php echo base_url(); ?>assets/admin/css/font-awesome.css" rel="stylesheet" />
	<!-- MORRIS CHART STYLES-->
	<link href="<?php echo base_url(); ?>assets/admin/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
	<!-- CUSTOM STYLES-->
	<link href="<?php echo base_url(); ?>assets/admin/css/custom.css" rel="stylesheet" />
	<!-- GOOGLE FONTS-->
	<link href="<?php echo base_url(); ?>assets/admin/css/font.css" rel='stylesheet' type='text/css' />

	<link href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap.css" rel="stylesheet" />

	<!-- JQUERY SCRIPTS -->
	<script src="<?php echo base_url(); ?>assets/admin/js/jquery-1.10.2.js"></script>
	
	<!-- BOOTSTRAP SCRIPTS -->
	<script src="<?php echo base_url(); ?>assets/admin/js/bootstrap.min.js"></script>
	

	<!-- JQUERY UI SCRIPTS -->
	<script src="<?php echo base_url(); ?>assets/admin/js/jquery-ui.js"></script>
	
	<script src="<?php echo base_url(); ?>assets/admin/ckeditor/ckeditor.js"></script>
	
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.css">

	
	<script>
	$( document ).ready(function() {
		
		
		
		$( ".navbar-header" ).mouseenter(function() {
			$(".navbar-side").show();
		});
		
		$( "#page-wrapper" ).mouseenter(function() {
			$(".navbar-side").hide();
		});

		$(".alert button.close").click(function (e) {
			$(this).parent().fadeOut('slow');
		});
		$('#select_all').change(function() {
			console.log('checked');
			var checkboxes = $(this).closest('form').find(':checkbox');
			if($(this).is(':checked')) {
				checkboxes.prop('checked', true);
			} else {
				checkboxes.prop('checked', false);
			}
		});
		$('form').submit(function(){
			if ($(this).attr('action').indexOf('delete',1) != -1) {
				if (!confirm('<?php echo $msg_confirm; ?>')) {
					return false;
				}
			}
		});
		
	});
	</script>

</head>
	<body>
		<div id="wrapper">
		
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php echo $href_dashboard; ?>"><?php echo $site_name; ?></a> 
				</div>
				<div style="color: white; padding: 15px 0 0px 20px; float: left;font-size: 16px;"> <?php echo $string_last_access; ?> : <?php echo date($date_last_access_format,$member_data['last_seen']); ?></div>
				<div class="pull-right" style="padding:15px 30px 10px 0;">
					<a class="btn btn-info square-btn-adjust" href="<?php echo $href_account; ?>">
					<?php echo clean_output($member_data['firstname'].' '.$member_data['lastname']); ?>
					</a>
			 
					<a href="/admin/auth/logout" class="btn btn-danger square-btn-adjust"><?php echo $string_logout; ?></a> 
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			
			<nav class="navbar-default navbar-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="main-menu">
						
					<?php echo $menu_items; ?>
					</ul>
				</div>
			</nav>  
			<!-- /. NAV SIDE  -->
			
			<div id="page-wrapper" >
				<div id="page-inner">
					<div class="row">
						<div class="col-md-12">
							<h2><?php echo $page_header_text; ?></h2>   
							<?php if(isset($welcome_text)){ ?><h5><?php echo clean_output($welcome_text); ?></h5> <?php } ?>
						</div>
					</div>
					<!-- /. ROW  -->
					<hr />
					<?php $this->load->view($content_view); ?>
				</div>
				<!-- /. PAGE INNER  -->
			</div>
			<!-- /. PAGE WRAPPER  -->
			
		</div>
		<!-- /. WRAPPER  -->

		<!-- METISMENU SCRIPTS -->
		<script src="<?php echo base_url(); ?>assets/admin/js/jquery.metisMenu.js"></script>
		<!-- MORRIS CHART SCRIPTS -->
		<script src="<?php echo base_url(); ?>assets/admin/js/morris/raphael-2.1.0.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/admin/js/morris/morris.js"></script>
		<!-- CUSTOM SCRIPTS -->
		<script src="<?php echo base_url(); ?>assets/admin/js/custom.js"></script>
		
	</body>
</html>