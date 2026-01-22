<?php
class Youtube_channel{
	function __construct(){
	
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->library('session');
		$this->CI->load->model('basic');
		$this->user_id=$this->CI->session->userdata("user_id");		
		
	}

	public function get_videos($channel_id='', $api_key='') 
	{
		if($channel_id=='')
		return array('success'=>'0','error_message'=>$this->CI->lang->line("Channel ID can not be empty."));

		if($api_key=='')
		return array('success'=>'0','error_message'=>$this->CI->lang->line("YouTube API key can not be empty."));

		$response=array();
		
		// YouTube Data API v3 endpoint to get channel uploads
		// First, get the uploads playlist ID from the channel
		$channel_url = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=" . $channel_id . "&key=" . $api_key;
		
		$channel_data = $this->curl_call($channel_url);
		$channel_json = json_decode($channel_data, true);
		
		if(!isset($channel_json['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
			$response['success']='0';
			$response['error_message']=$this->CI->lang->line("Unable to get channel information. Please check channel ID and API key.");
			return $response;
		}
		
		$uploads_playlist_id = $channel_json['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
		
		// Now get videos from the uploads playlist
		$playlist_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=" . $uploads_playlist_id . "&maxResults=50&key=" . $api_key;
		
		$playlist_data = $this->curl_call($playlist_url);
		$playlist_json = json_decode($playlist_data, true);
		
		if(!isset($playlist_json['items']) || empty($playlist_json['items'])) {
			$response['success']='0';
			$response['error_message']=$this->CI->lang->line("No videos found in this channel.");
			return $response;
		}
		
		$element_list=array();
		$i=0;
		
		foreach($playlist_json['items'] as $item) 
		{
			if(!isset($item['snippet']['resourceId']['videoId'])) continue;
			
			$video_id = $item['snippet']['resourceId']['videoId'];
			$video_url = "https://www.youtube.com/watch?v=" . $video_id;
			
			$element_list[$i]['title']= isset($item['snippet']['title']) ? (string) $item['snippet']['title'] : '';
			$element_list[$i]['link']= $video_url;
			$element_list[$i]['description']= isset($item['snippet']['description']) ? (string) $item['snippet']['description'] : '';
			$element_list[$i]['pubDate']= isset($item['snippet']['publishedAt']) ? date('D, d M Y H:i:s O', strtotime($item['snippet']['publishedAt'])) : '';
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
