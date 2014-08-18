<?php
Class Common 
{
	//获取api
	public static function getAll($dir)
	{	
		$api = array();
		
		$files = scandir($dir);
		foreach ($files as $fname) {
			$pos = strpos($fname, '.php');
			if($pos === false) continue;
			$key = strtolower(substr($fname, 0, $pos));
			$api[$key] = require $dir . '/' . $fname;
		}
		return $api;
	}

	public static function doRequest($url, $method, $data, $cookie)
	{
		$curl = new Curl;

	    if (preg_match_all('/%(\w+)%/', $url, $match)) {
	        $replaceData = array();
	        foreach($match[1] as $key => $item) {
	            if (!isset($_POST[$item])) continue;
	            $replaceData[$match[0][$key]] = $_POST[$item];
	            unset($_POST[$item]);
	        }
	        $url = str_replace(array_keys($replaceData), array_values($replaceData), $url);
	    }
	    $method = strtolower($method);
	    $postData = array();
	    foreach ($data as $key => $value) {
	        if ($value === '') continue;
	        $postData[$key] = $value;
	    }

	    $curl->setCookie($cookie);
    	$curl->setHeader(array('X-Requested-With' => 'XMLHttpRequest'));

    	$response = $curl->$method(API_URL . $url, $postData);
    	if ($response->header->has('Set-Cookie'))
	        @setcookie(API_COOKIE_KEY, $response->header->get('Set-Cookie'));

	    return json_decode($response->body, true);
	}

	public static function getCaptcha($url, $cookie)
	{
	    $curl = new Curl;
	    $curl->setCookie($cookie);
	    $curl->setHeader(array('X-Requested-With' => 'XMLHttpRequest'));
	    $response = $curl->get(API_URL . $url);
	    if ($response->header->has('Set-Cookie'))
	        @setcookie(API_COOKIE_KEY, $response->header->get('Set-Cookie'));

		file_put_contents('temp/captcha.png', $response->body);
	    return 'temp/captcha.png?t=' . time();
	}
}