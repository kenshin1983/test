<?php 
//用户相关接口
return array(
	array(
		'name'	=> '用户登录',
		'desc'	=> '用于首页ajax用户登录，可以用于登录广告主|媒体主|后台用户',
		'url'	=> 'index/login',
		'type'	=> 'post',
		'params'=> array(
			'email'		=> '登录账号',
			'password'	=> '登录密码',
			'remember '	=> ' 1 记住我 如果没有勾选就不要传这个参数',
			'captcha'	=> '验证码',
			'type'		=> '1广告主 0媒体主',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
		'captcha' => 'captcha'
	)
);