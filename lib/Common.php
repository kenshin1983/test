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

	function getCurl($url, $postData, $cookie)
	{
		$header = array(
	        'Accept:*/*',
	        'Accept-Charset:GBK,utf-8;q=0.7,*;q=0.3',
	        'Accept-Encoding:gzip,deflate,sdch',
	        'Accept-Language:zh-CN,zh;q=0.8',
	        'Connection:keep-alive',
	        // 'Host:'.$this->host,
	        // 'Origin:'.$this->origin,
	        // 'Referer:'.$this->referer,
	        'X-Requested-With:XMLHttpRequest',
	        'Expect:'
	    );
	    $cookieData = '';
		foreach ($cookie as $k => $v) {
		    $cookieData .= "$k=$v;";
		}
		$curl = curl_init(); //启动一个curl会话
	    curl_setopt($curl, CURLOPT_URL, $url); //要访问的地址
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置HTTP头字段的数组
	    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
	    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); //从证书中检查SSL加密算法是否存在
	    // curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent); //模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); //自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); //发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); //Post提交的数据包
	    curl_setopt($curl, CURLOPT_COOKIE, $cookieData); //读取储存的Cookie信息
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, GET_HEADER); //显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
	    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	    $result = curl_exec($curl); //执行一个curl会话
	    curl_close($curl); //关闭curl

	    $values = @explode("\r\n\r\n", $result);
	    $header = $values[0];
	    $body = '';
	    for ($i = 1, $len = count($values); $i < $len; $i++) {
	        $body .= $values[$i];
	    }
	    $header = explode("\n", $header);
	    foreach ($header as $line) {
	        if (preg_match('/^set-cookie:[\s]*([^=]+)=([^;]+)/i', $line,$match)) {//获取cookie
				$cookie[$match[1]] = $match[2];
			}
	    }
	    setcookie(API_COOKIE_KEY, serialize($cookie));

	    if (strpos($result, 'image/jpeg') !== false
	       || strpos($result, '<!DOCTYPE html>') !== false) {
	        return $body;
	    }

	    $result = json_decode($body, true);
	    if(!$result) return $body;

	    return $result;
	}

	public static function getCaptcha($url, $cookie)
	{
	    $curl = new Curl;
	    $curl->setCookie($cookie);
	    $curl->setHeader(array('X-Requested-With' => 'XMLHttpRequest'));
	    $response = $curl->get($url);
	    if ($response->header->has('Set-Cookie')) {
		    $line = $response->header->get('Set-Cookie');
		    if (preg_match('/^[\s]*([^=]+)=([^;]+)/i', $line,$match)) {//获取cookie
				$cookie[$match[1]] = $match[2];
			}
			@setcookie(API_COOKIE_KEY, serialize($cookie));
	    }

		file_put_contents('temp/captcha.png', $response->body);
	    return 'temp/captcha.png?t=' . time();
	}
}