<?php 
//活动相关接口
return array(
	array(
		'name'	=> '活动列表页数据接口',
		'desc'	=> '用于获取活动列表页数据，包括竞标活动和普通活动，进行中、已完成、草稿活动',
		'url'	=> 'plan/data',
		'type'	=> 'get',
		'params'=> array(
			'type'		=> '活动类型，0:草稿(默认)；1:进行中；2:已完成',
			'keyword'	=> '搜索活动关键字，匹配活动名称',
			'm'	=> '活动模块，normal：普通活动（默认）；bid：竞价活动',
			'page'	=> '页数 默认 1',
			'pageSize'		=> '每页n条 默认20',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'data'	=> array(
				'id'                => '活动id',
                'plan_name'         => '活动名称',
                'plan_time'         => '活动开始时间',
                'plan_type'         => '活动类型 转发｜直发 ；图文广告位',
                'operate_edit'      => '是否现实<查看>按钮',
                'operate_delete'    => '是否现实<删除>按钮',
                'operate_addmedia'  => '是否现实<添加媒体账号>按钮',
                'operate_copy'      => '是否现实<复制>按钮',
                //以下普通活动独有
                'accounts_num'		=> '总账号数量',
                'coverage'			=> '总粉丝数量',
                'highest_spending'	=> '最高消费，单位元',
                //以下竞标活动独有
                'cost'				=> '总预算，单位元',
                'process'			=> '完成进度，单位%',
                'win_num'			=> '已中标',
                'lose_num'			=> '未中标',
                'other_num'			=> '进行中',
			)
		),
	),
	array(
		'name'	=> '修改活动状态接口',
		'desc'	=> '用于修改活动状态，包括保存草稿和发布活动',
		'url'	=> 'plan/pub/%id%',
		'type'	=> 'post',
		'params'=> array(
			'id'		=> '活动ID',
			'status'	=> '修改状态，0:保存草稿(默认)；1:发布活动'
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '删除活动接口',
		'desc'	=> '删除一个活动',
		'url'	=> 'plan/delete/%id%',
		'type'	=> 'delete',
		'params'=> array(
			'id'		=> '活动ID',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '查看活动推广名单',
		'desc'	=> '查看活动推广名单数据',
		'url'	=> 'plan/datamedia',
		'type'	=> 'get',
		'params'=> array(
			'id'       => '活动ID， 默认0 ，表示还未添加活动',
			'page'     => '页数 默认 1',
			'pageSize' => '每页n条 默认10',
			'platform' => '平台          默认0            0表示微信公众账号 1表示朋友圈（当有活动id时，该参数无效）',
			'type'     => '平台类型   默认weixin   weixin | weibo （当有活动id时，该参数无效）',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'fans'       => '总粉丝数量',
			'cost'       => '总花费',
			'data'       => array(
				'id'            => '媒体账号ID',
				'wx_name'       => '媒体账号名称',
				'wx_id'         => '媒体账号',
				'fans_num'      => '粉丝数',
				'single_price'  => '第一个价格',
				'multi_1_price' => '第二个价格',
				'multi_2_price' => '第三个价格',
				'multi_3_price' => '第四个价格',
				'auth'          => '是否粉丝视频认证',
				'auth_time'     => '视频认证时间',
				//以下微信公众号独有
				'avatar'        => '头像',
				'wx_auth'       => '是否微信认证',
				//以下朋友圈独有
				'profession'    => '职业',
			)
		),
	),
	array(
		'name'	=> '添加媒体账号',
		'desc'	=> '添加推广账号，支持单个核批量添加',
		'url'	=> 'plan/addmedia',
		'type'	=> 'post',
		'params'=> array( 
			'id'       => '活动ID， 默认0 ，表示还未添加活动',
			'mode'     => '添加类型， id 通过id添加（默认） query 通过条件添加（暂不用）',
			'data'     => '添加数据，多个id以,号分隔',
			'platform' => '平台          默认0            0表示微信公众账号 1表示朋友圈（当有活动id时，该参数无效）',
			'type'     => '平台类型   默认weixin   weixin | weibo （当有活动id时，该参数无效）',
		),
		'response'=>array(
			'error'   => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '移除媒体账号',
		'desc'	=> '移除推广账号，支持单个核批量添加',
		'url'	=> 'plan/removemedia',
		'type'	=> 'post',
		'params'=> array( 
			'id'       => '活动ID， 默认0 ，表示还未添加活动',
			'mode'     => '移除类型， id 通过id添加（默认） query 通过条件添加（暂不用）',
			'data'     => '移除数据，多个id以,号分隔',
			'platform' => '平台          默认0            0表示微信公众账号 1表示朋友圈（当有活动id时，该参数无效）',
			'type'     => '平台类型   默认weixin   weixin | weibo （当有活动id时，该参数无效）',
		),
		'response'=>array(
			'error'   => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '获取竞价媒体主数据接口',
		'desc'	=> '获取竞价媒体主数据接口',
		'url'	=> 'plan/databid',
		'type'	=> 'get',
		'params'=> array( 
			'id'       => '活动ID',
			'status'   => '活动状态 -1 未中标; 1 已中标; 0 进行中（默认）',
			'page'     => '页数 默认 1',
			'pageSize' => '每页n条 默认10',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'data'       => array(
				'id'                  => '媒体账号ID',
				'media_name'          => '媒体账号名称',
				'fans_num'            => '粉丝数',
				'is_verify_fans_num'  => '是否粉丝视频认证',
				'price'               => '报价',
				//以下微信公众号独有
				'sex_male'            => '性别比例 男',
				'sex_female'          => '性别比例 女',
				'is_verify_sex'       => '是否性别比例认证',
				'open_rate'           => '图文打开率',
				'is_verify_open_rate' => '是否打开率认证',
				'pos'                 => '广告位',
				//以下朋友圈独有
				'sex'                 => '性别',
				'area_id'             => '地区id',
			)
		),
	),
	array(
		'name'	=> '拒绝竞价',
		'desc'	=> '广告主拒绝媒体主竞标，包括永久拒绝和本次拒绝',
		'url'	=> 'plan/refusemedia',
		'type'	=> 'post',
		'params'=> array( 
			'plan_id'  => '活动ID',
			'media_id' => '媒体账号ID',
			'text'     => '拒绝理由',
			'never'    => '是否永久拒绝，如果不是，请勿传',
		),
		'response'=>array(
			'error'   => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	),
	array(
		'name'	=> '竞价付款',
		'desc'	=> '广告主统一媒体账号竞标成功，支付费用到平台，支持批量支付',
		'url'	=> 'plan/paymedia',
		'type'	=> 'post',
		'params'=> array( 
			'plan_id'   => '活动ID',
			'media_ids' => '媒体账号IDs，多个用,号隔开',
			'cost'      => '修改后的预算 单位元',
			't'         => '类型 pay：支付(默认) check：检测', 
		),
		'response'=>array(
			'error'     => '错误代号 0表示没有错误，1表示有错误 2 表示余额不足 3 表示超出预算',
			'message'   => '错误信息',
			// 以下只会在t   == check的时候才会返回
			'cost'      => '最终总消费',
			'media_ids' => '最终媒体账号ID',
			'count'     => '最终媒体账号总数',
		),
	)
);