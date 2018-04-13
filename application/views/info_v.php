<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>

<div class="container over">
  <h2><strong><?php echo $info->title; ?></strong></h2>
  <p><?php echo $info->text; ?></p>
</div>