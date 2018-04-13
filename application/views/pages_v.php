<?php if(isset($error_message)){ ?>
<div class="alert alert-warning" role="alert">
  <strong>Oeps..</strong> <?php echo $error_message; ?>
</div>
<?php } ?>

<div class="row">

	<!-- Column 1 -->
	<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 column_1">
		<?php 
		if(isset($block[1])){ 
			foreach($block[1] as $title => $data)
			{
				echo '
				<div class="list-group">
				<span class="list-group-item active break-word">'.$title.'</span>';

				foreach($data as $page)
				{
					echo '<a href="' . $page['link'] . '" class="list-group-item">' . $page['url'] . '</a>';
				}
				
				echo '</div>';
				
			}
			
		} 
		
		?>
	</div>

	<!-- Column 2 -->
	<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 column_2">
		<?php 
		if(isset($block[2])){ 
			foreach($block[2] as $title => $data)
			{
				echo '
				<div class="list-group">
				<span class="list-group-item active break-word">'.$title.'</span>';

				foreach($data as $page)
				{
					echo '<a href="' . $page['link'] . '" class="list-group-item">' . $page['url'] . '</a>';
				}
				
				echo '</div>';
				
			}
			
		} 
		
		?>
	</div>


	<!-- Column 3 -->
	<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 column_3">
		<?php 
		if(isset($block[3])){ 
			foreach($block[3] as $title => $data)
			{
				echo '
				<div class="list-group">
				<span class="list-group-item active break-word">'.$title.'</span>';

				foreach($data as $page)
				{
					echo '<a href="' . $page['link'] . '" class="list-group-item">' . $page['url'] . '</a>';
				}
				
				echo '</div>';
				
			}
			
		} 
		
		?>
	</div>
	
	<!-- Column 4 -->
	<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 column_4">
		<?php 
		if(isset($block[4])){ 
			foreach($block[4] as $title => $data)
			{
				echo '
				<div class="list-group">
				<span class="list-group-item active break-word">'.$title.'</span>';

				foreach($data as $page)
				{
					echo '<a href="' . $page['link'] . '" class="list-group-item">' . $page['url'] . '</a>';
				}
				
				echo '</div>';
				
			}
			
		} 
		
		?>
	</div>

</div>