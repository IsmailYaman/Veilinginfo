	<style>
		.card {margin-bottom: 10px;position: relative;}
		.card-wrapper {border: 1px solid #a8c7e0;}
		.card-content {padding: 15px 12px;position: relative;}
		.incident {position: relative;padding-right: 70px;color: #010050;margin-top: 5px;border-top: 1px dotted;padding-top: 5px;}
		.incident.first{margin-top:0; border: none; padding-top: 0;}
		.incident-title{display:block;font-weight:bold;margin-bottom:10px}
		.incident-length{font-weight:bold}
		.incident-road,.incident-time{position:absolute;right:0;top:50%;margin-top:-18px;padding:10px;font-weight:bold}
		.incident-road{color:white;background:#023a8c;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px}
		.incident-road,.incident-time{right:10px}
		.incident-heading{display:block;font-weight:bold}
	</style>
	<div class="card list-group-item">
		<div class="card-wrapper">
			<div class="card-content">
				<?php $e=1; foreach ($traffic_info as $info) { ?>
					<?php if(isset($info['events']['trafficJams'][0])){ ?>
				<div class="incident<?php if($e==1){ ?> first<?php } ?>">
					<span class="incident-title"><?php echo $info['events']['trafficJams'][0]['location']; ?></span>
					<?php if(isset($info['events']['trafficJams'][0]['distance'])){ ?><span class="incident-length"><?php echo round($info['events']['trafficJams'][0]['distance']/1000, 0); ?>km</span><?php } ?>
					<span class="incident-description"> <?php echo $info['events']['trafficJams'][0]['description']; ?></span>
					<span class="incident-road"><?php echo $info['road']; ?></span>
				</div>
					<?php $e++; } ?>
				<?php } ?>
			</div>
		</div>
	</div>