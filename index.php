<?php 
error_reporting(E_ALL);

define('ROOT_DIR', __DIR__);
define('API_DIR', ROOT_DIR . '/api');
define('GET_HEADER', 1);
define('API_COOKIE_KEY', 'api-cookie');
define('API_URL', 'http://wtb.local/');

$list = getAll(API_DIR);

if (isset($_GET['r'])) {
	list($m, $i) = explode('-', $_GET['r']);
	$api = $list[$m][$i];
} else {
	$api = current(current($list));
}

$cookie = isset($_COOKIE[API_COOKIE_KEY]) ? unserialize($_COOKIE[API_COOKIE_KEY]) : array();

$response = '';

$currentUser = getCurrentUser(API_URL . 'status', $cookie);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//curl发送请求
	$url = API_URL . $api['url'];
	$postData = $_POST;

	$response = getCurl($url, $postData, $cookie);
}

//获取api
function getAll($dir)
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

function getCaptcha($url, $cookie)
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
    // curl_setopt($curl, CURLOPT_POST, 1); //发送一个常规的Post请求
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); //Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIE, $cookieData); //读取储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, GET_HEADER); //显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
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

    file_put_contents('temp/captcha.png', $body);
    return 'temp/captcha.png?t=' . time();
}

function getCurrentUser($url, $cookie)
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
    // curl_setopt($curl, CURLOPT_POST, 1); //发送一个常规的Post请求
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $postData); //Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIE, $cookieData); //读取储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); //显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
    $result = curl_exec($curl); //执行一个curl会话
    curl_close($curl); //关闭curl

    return json_decode($result, true);
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>api-test</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="row">
    	<div class="col-md-4">
    		<ul>
    			<?php foreach($list as $name => $file) :?>
    			<li>
    				<?php echo $name; ?>
    				<ol>
    					<?php foreach($file as $key => $item) :?>
    					<li><a href="?r=<?php echo $name . '-' . $key; ?>"><?php echo $item['name']; ?></a></li>
	    				<?php endforeach;?>
    				</ol>
    			</li>
	    		<?php endforeach;?>
    		</ul>
    	</div>
    	<div class="col-md-4">
			<form role="form-horizontal" method="post">
				<div class="form-group">
					<label>接口</label>
					<p class="form-control-static"><?php echo $api['name']?></p>
					<p class="form-control-static"><?php echo $api['desc']?></p>
				</div>
				<div class="form-group">
					<label>接口地址</label>
					<p class="form-control-static"><?php echo API_URL . $api['url']?></p>
					<p class="form-control-static"><?php echo $api['type']?></p>
				</div>
				<div class="form-group">
					<label>接口参数</label>
					<?php foreach($api['params'] as $key => $param) :?>
						<p class="form-control-static">
							<?php echo $key?> : <textarea class="form-control" rows="3" name="<?php echo $key?>"><?php if(isset($_POST[$key])) echo $_POST[$key]; ?></textarea>
							<span class="help-block"><?php echo $param; ?></span>
						</p>
					<?php endforeach;?>
				</div>
				<?php if (!empty($api['captcha'])) : ?>
					<div class="form-group">
						<label for="exampleInputPassword1">验证码</label>
						<p class="form-control-static"><img src="<?php echo getCaptcha(API_URL . $api['captcha'], $cookie) ?>"></p>
					</div>
				<?php endif;?>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
    	</div>
    	<div class="col-md-4">
  			<div class="panel panel-default">
				<div class="panel-heading">当前登录用户：</div>
				<div class="panel-body">
                    <?php if (is_array($currentUser)) : ?>
                        <?php foreach ($currentUser as $key => $value) :?>
                            <p><?php echo $key . ':' . $value?></p>
                        <?php endforeach;?>
                    <?php else :?>
                        <?php echo $currentUser; ?>
                    <?php endif;?>            
                </div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">返回：</div>
				<div class="panel-body">
					<?php if (is_array($response)) : ?>
						<?php foreach ($response as $key => $value) :?>
							<p><?php echo $key . ':' . $value?></p>
						<?php endforeach;?>
					<?php else :?>
						<?php echo $response; ?>
					<?php endif;?>
				</div>
			</div>
    	</div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>