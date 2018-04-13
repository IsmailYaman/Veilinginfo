<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="description" content="<?php if(isset($description)){ echo $description; } ?>">
    <meta name="author" content="<?php if(isset($site_name)){ echo $site_name; } ?>">
    <link rel="icon" href="<?php echo base_url(); ?>assets/images/icons/favicon.ico">

    <title><?php echo $site_title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">-->

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>assets/css/global.css" rel="stylesheet">
	
	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	 
	<script src="https://www.google.com/recaptcha/api.js"></script>
	
  </head>

	<body>
		<div class="container offset">
		  <!-- Static navbar -->
		  <nav class="navbar navbar-default">
			<div class="container-fluid">
			  <div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				  <span class="sr-only">Toggle navigation</span>
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo base_url(); ?>"><?php echo $site_name; ?></a>
			  </div>
			  <div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
				  <?php echo $menu_items['left']; ?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
				  <?php echo $menu_items['right']; ?>
				</ul>
			  </div><!--/.nav-collapse -->
			</div><!--/.container-fluid -->
		  </nav>
		  
		  <?php $this->load->view($content_view); ?>
		</div> <!-- /container -->
		
		<footer class="footer">
			<div class="container">
				<p class="text-muted">
					&copy; <?php echo date('Y'); ?> - <?php echo $site_name; ?> 
					<span class="pull-right"><?php echo $footer_menu; ?></span>
				</p>
			</div>
		</footer>

		<!-- Bootstrap core JavaScript -->
		<script src="<?php echo base_url(); ?>assets/js/tether.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/global.js"></script>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="<?php echo base_url(); ?>assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>