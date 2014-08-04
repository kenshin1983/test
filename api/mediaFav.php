<?php 
//收藏接口
return array(
	array(
		'name'	=> '收藏数据接口',
		'desc'	=> '收藏数据接口-描述',
		'url'	=> 'media/fav/data',
		'type'	=> 'get',
		'params'=> array(
			'id'		=> '活动id,默认0,0表示未添加活动',
			'platform'	=> '平台          默认0            0表示微信公众账号 1表示朋友圈（当有活动id时，该参数无效）',
			'type'		=> '平台类型   默认weixin   weixin | weibo （当有活动id时，该参数无效）',
			'id'		=> '页码          默认1',
			'id'		=> '页数          默认10',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '添加收藏接口',
		'desc'	=> '将媒体账号添加到用户收藏的分组',
		'url'	=> 'media/fav/create',
		'type'	=> 'post',
		'params'=> array(
			'id'		=> '媒体账号ID，多个用，号隔开',
			'type'		=> '平台类型   默认weixin   weixin | weibo',
			'tag_ids'	=> '分组ID，多个用，号隔开',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '删除收藏接口',
		'desc'	=> '从用户的收藏列表移除收藏的媒体账号',
		'url'	=> 'media/fav/delete',
		'type'	=> 'delete',
		'params'=> array(
			'id'		=> '媒体账号ID，多个用，号隔开',
			'type'		=> '平台类型   默认weixin   weixin | weibo',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '获取用户的收藏分组',
		'desc'	=> '',
		'url'	=> 'media/fav/tags',
		'type'	=> 'get',
		'params'=> array(
			'id'	=> '媒体账号ID 默认0    用来判断分组是否已选中',
			't'		=> '平台            默认0    0表示微信公众账号 1表示朋友圈',
		),
		'response'=>array(
			'id' => '分组ID',
			'tag_name' => '分组名称',
			'num' => '分组下媒体账号数量',
			'isCheck' => '1 已选中 0 未选中'
		),
	),
	array(
		'name'	=> '添加一个收藏分组',
		'desc'	=> '',
		'url'	=> 'media/fav/addtag',
		'type'	=> 'post',
		'params'=> array(
			'name'	=> '分组名称',
			't'		=> '平台            默认0    0表示微信公众账号 1表示朋友圈',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '删除一个收藏分组',
		'desc'	=> '',
		'url'	=> 'media/fav/deltag/%id%',
		'type'	=> 'delete',
		'params'=> array(
			'id'	=> '需要删除的分组ID',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '分组重命名',
		'desc'	=> '',
		'url'	=> 'media/fav/rename/%id%',
		'type'	=> 'post',
		'params'=> array(
			'id'	=> '需要删除的分组ID',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	)
);