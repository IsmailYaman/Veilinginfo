<?php

class News extends MY_Controller
{
	private $rssFeed;
	
	public function __construct()
	{
		parent::__construct();
		$this->rssFeed  = "http://www.nu.nl/rss";
	}
	
	public function display_data($data = NULL)
	{
		$data['news_feed'] = $this->_get_news(4);

		return $this->load->view('news/news_v', $data, true);
	}

	public function _get_news($limit)
	{
		$feed = new DOMDocument();
		$feed->load($this->rssFeed);
		$feed_data = array();
		foreach ($feed->getElementsByTagName('item') as $item) {	
			$feed_data[] = array ('title' => $item->getElementsByTagName('title')->item(0)->nodeValue,'link' => $item->getElementsByTagName('link')->item(0)->nodeValue, 'image' => $item->getElementsByTagName('enclosure')->item(0)->getAttribute('url'));
		}
		
		$feed_data_cropped = array();
		$feed_limit = ($limit < count($feed_data) ? $limit : count($feed_data));
		for($i = 0; $i < $limit; $i++) {
			$title = htmlentities($feed_data[$i]['title']);
			$link  = $feed_data[$i]['link'];
			$image = $feed_data[$i]['image'];
			
			$feed_data_cropped[] = array("title" => $title, "link" => $link, "image" => $image);
		}
		
		return $feed_data_cropped;
	}
}