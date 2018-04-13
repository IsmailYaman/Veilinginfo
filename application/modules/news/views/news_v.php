	<style>
		.news-item {}
		.news-item img{float:left;width:25px;}
		.news-item a{font-size: 13px;margin-left: 35px;width: 90%;display: block;}
	</style>
	<?php foreach($news_feed as $news_item){ ?>
	<div class="list-group-item news-item">
		<img src="<?php echo $news_item['image']; ?>" /> 
		<a href="<?php echo $news_item['link']; ?>" target="_blank" rel="nofollow" title="<?php echo $news_item['title']; ?>"><?php echo $news_item['title']; ?></a>
	</div>
	<?php } ?>
	<a href="http://www.nu.nl" class="list-group-item" target="_blank">> Meer nieuws</a>