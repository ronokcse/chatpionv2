<?php
class Wordpress_post{
	function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");		
		
	}

	public function get_posts($blog_url='') 
	{
		if($blog_url=='')
		return array('success'=>'0','error_message'=>$this->CI->lang->line("Blog URL can not be empty."));

		// WordPress blogs typically have RSS feeds at /feed or /feed/rss
		$feed_url = rtrim($blog_url, '/') . '/feed';
		
		// Try to get the RSS feed
		$response=array();
	    $content = $this->curl_call($feed_url);
	    
	    // If /feed doesn't work, try /feed/rss
	    if(empty($content) || strpos($content, '<?xml') === false) {
	    	$feed_url = rtrim($blog_url, '/') . '/feed/rss';
	    	$content = $this->curl_call($feed_url);
	    }
	    
	    try
	    {
	    	$x = @new SimpleXmlElement($content);
	    }
	    catch(Exception $e)
	    {
	    	 $response['success']='0';
	    	 $response['error_message']=$e->getMessage();
	    	 return $response;
	    }

	    $element_list=array();
	     
	    $i=0;

	    if(!isset($x->channel->item)){

	    	$response['success']='0';
	    	$response['error_message']=$this->CI->lang->line("WordPress blog has not any posts or RSS feed is not available.");
	    	return $response;
	    }

	    foreach($x->channel->item as $entry) 
	    {
	    	$element_list[$i]['title']= (string) $entry->title;
	    	$element_list[$i]['link']= (string) $entry->link;
	    	$element_list[$i]['description']= isset($entry->description) ? (string) $entry->description : '';
	    	$element_list[$i]['pubDate']= isset($entry->pubDate) ? (string) $entry->pubDate : (isset($entry->published) ? (string) $entry->published : '');
	    	$i++;
	    }

	    $response['success']='1';
	    $response['element_list']=$element_list;
	    return $response;
	}

	private function curl_call($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$content = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if($http_code != 200) {
			return '';
		}
		
		return $content;
	}
}
