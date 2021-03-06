<?php
	class shareCount {
		private $url,$timeout;
		function __construct($url,$timeout=100) {
			$this->url=rawurlencode($url);
			$this->timeout=$timeout;
		}
		function get_plusones()  {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($this->url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
			$curl_results = curl_exec ($curl);
			curl_close ($curl);
			$json = json_decode($curl_results, true);
			return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
		}
	}
	
	function share($url){
		$obj=new shareCount($url);		
		$json=file_get_contents("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=".$url);
		$result= json_decode($json);
		if(!empty($result->result->views)){
			$upon = $result->result->views;
		}else {
			$upon = 0;
		}
		$arr = array('google' => $obj->get_plusones(),'upon' => $upon);
		return $arr;
	}
	
	if(!empty($_GET['url'])){
		echo json_encode(share($_GET['url']));
	}
?>
