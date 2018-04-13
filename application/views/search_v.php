<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>
<div class="container veilingen-lists">

<?php if(!isset($block) || count($block) == 0){ ?>

<div class="alert alert-info" role="alert">
  Wij hebben geen resultaten gevonden die aan uw zoekopdracht voldoen.
</div>

<?php } ?>

<?php if(isset($block[1])){ ?>
<div class="row">
	<?php foreach($block[1] as $title => $data){ ?>
	<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_1">
		<div class="list-group">
			<span class="list-group-item active break-word"><?php echo $title; ?></span>
			<?php foreach($data as $page){ ?>
			<a href="<?php echo $page['url']; ?>" title="<?php echo $page['description']; ?>" class="list-group-item"><?php echo $page['anchor']; ?></a>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
<?php } ?>

<?php if(isset($block[2])){ ?>
<div class="row">
	<?php foreach($block[2] as $title => $data){ ?>
	<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_1">
		<div class="list-group">
			<span class="list-group-item active break-word"><?php echo $title; ?></span>
			<?php foreach($data as $page){ ?>
			<a href="<?php echo $page['url']; ?>" title="<?php echo $page['description']; ?>" class="list-group-item"><?php echo $page['anchor']; ?></a>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
<?php } ?>

<?php if(isset($block[3])){ ?>
<div class="row">
	<?php foreach($block[3] as $title => $data){ ?>
	<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12 column_1">
		<div class="list-group">
			<span class="list-group-item active break-word"><?php echo $title; ?></span>
			<?php foreach($data as $page){ ?>
			<a href="<?php echo $page['url']; ?>" title="<?php echo $page['description']; ?>" class="list-group-item"><?php echo $page['anchor']; ?></a>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>
<?php } ?>

</div>


</div>