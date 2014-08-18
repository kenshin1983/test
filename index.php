<?php 
error_reporting(E_ALL);

define('ROOT_DIR', __DIR__);
define('API_DIR', ROOT_DIR . '/api');

require ROOT_DIR . '/config.php';
require ROOT_DIR . '/lib/Curl.php';
require ROOT_DIR . '/lib/Common.php';

$list = Common::getAll(API_DIR);

if (isset($_GET['r'])) {
	list($m, $i) = explode('-', $_GET['r']);
	$api = $list[$m][$i];
} else {
	$api = current(current($list));
}

$cookie = isset($_COOKIE[API_COOKIE_KEY]) ? $_COOKIE[API_COOKIE_KEY] : '';

$curl = new Curl();
$curl->setCookie($cookie);
$curl->setHeader(array('X-Requested-With' => 'XMLHttpRequest'));
$currentUser = $curl->get(API_URL . 'status');
$currentUser = json_decode($currentUser->body, true);

$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//curl发送请求
    $response = Common::doRequest($api['url'], $api['type'], $_POST, $cookie);
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
						<p class="form-control-static"><img src="<?php echo Common::getCaptcha($api['captcha'], $cookie) ?>"></p>
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
                    <pre><?php if(is_string($response)) echo $response; 
                               else var_dump($response); ?></pre>
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
