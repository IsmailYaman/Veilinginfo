<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <?php echo $error_message; ?>
</div>
<?php } ?>

<div class="container">
  <h2 class="header-text"><span><?php echo $lang_txt_header_1; ?></span></h2>
  <?php echo $spotlight; ?>
</div>

<div class="container veilingen-lists">
	<h2 class="header-text"><span><?php echo $lang_txt_header_2; ?></span></h2>
	<div class="row">
		<!-- Column 1 -->
		<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_1">
			<?php if(isset($block['column_1'])){ echo $block['column_1']; }?>
		</div>
		
		<!-- Column 2 -->
		<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_2">
			<?php if(isset($block['column_2'])){ echo $block['column_2']; }?>
		</div>

		<!-- Column 3 -->
		<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_3">
			<?php if(isset($block['column_3'])){ echo $block['column_3']; }?>
		</div>
	</div>
</div>