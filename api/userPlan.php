<?php 
//媒体主-发现活动相关接口
return array(
	array(
		'name'	=> '获取发现活动数据接口',
		'desc'	=> '用于获取发现活动列表页数据（广告主发布，并通过审核的活动）',
		'url'	=> 'user/plan/data',
		'type'	=> 'get',
		'params'=> array(
			'order'    => '排序类型 time-asc time-desc 按时间排序; cost-asc cost-desc 按预算排序; process-asc process-desc 按进度排序',
			'page'     => '页数 默认 1',
			'pageSize' => '每页n条 默认20',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'data'	=> array(
				'id'           => '活动id',
				'plan_name'    => '活动名称',
				'platform'     => '活动类型 转发｜直发 ；图文广告位',
				'plan_time'    => '活动开始时间',
				'condition'    => '活动条件[数组]',
				'cost'         => '总预算，单位元',
				'left_time'    => '剩余时间',
				'process'      => '完成进度，单位%',
				'user_company' => '广告主公司',
			)
		),
	),
	array(
		'name'	=> '投标记录接口',
		'desc'	=> '用于活动投标活动详细页的投标记录，我的投标记录；媒体主的我的投标；注意：返回数据请根据相应业务做相应的处理',
		'url'	=> 'user/plan/mediadata',
		'type'	=> 'get',
		'params'=> array(
			'm'        => '获取数据类型 all 所有（默认）; my 我的',
			'plan_id'  => '活动id 不需要请勿传',
			'status'   => '活动状态 -1 未中标; 1 已中标; 0 进行中，不需要请勿传',
			'page'     => '页数 默认 1',
			'pageSize' => '每页n条 默认20',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'data'	=> array(
				'plan_id'                    => '活动id',
				'plan_name'                  => '活动名称',
				'plan_type'                  => '推广方式 转发｜直发 ；图文广告位',
				'plan_time'                  => '活动开始时间',
				'media_id'                   => '媒体账号id',
				'media_name'                 => '媒体账号名称',
				'media_fans_num'             => '媒体账号粉丝数量',
				'media_is_verify_fans_num'   => '媒体账号是否视频认证（0 | 1）',
				'media_verify_fans_num_time' => '媒体账号获取认证时间',
				'price'                      => '价格 单位元',
				'status'                     => '状态 -1 可以再次投标 -2 不可再次投标（未中标状态下有用）',
				'create_time'                => '竞标时间'
			)
		),
	),
	array(
		'name'	=> '获取媒体主竞价的媒体账号数据',
		'desc'	=> '获取对应活动的媒体主的媒体账号列表，在活动详细页点击竞价之后调用',
		'url'	=> 'user/plan/usermedia',
		'type'	=> 'get',
		'params'=> array(
			'plan_id'  => '活动id',
			'page'     => '页数 默认 1',
			'pageSize' => '每页n条 默认10',
		),
		'response'=>array(
			'totalCount' => '数据总条数',
			'data'	=> array(
				'id'       => '媒体账号id',
				'name'     => '媒体账号名称',
				'fans_num' => '媒体账号粉丝数量',
				'status'   => '媒体账号状态描述',
				'is_valid' => '该账号是否可以竞标 1 可以竞标 0 不可竞标',
				'price1'   => '第一个广告位默认价格 单位元',
				'price2'   => '第二个广告位默认价格 单位元',
				'price3'   => '第三个广告位默认价格 单位元',
				'price4'   => '第四个广告位默认价格 单位元'
			)
		),
	),
	array(
		'name'	=> '媒体主投标接口',
		'desc'	=> '媒体主在选择完广告位和价格后，调用该接口投标',
		'url'	=> 'user/plan/bid',
		'type'	=> 'post',
		'params'=> array(
			'plan_id'  => '活动id',
			'media_id' => '媒体账号id',
			'pos'      => '广告位',
			'price'    => '价格，单位元',
		),
		'response'=>array(
			'error' => '错误代号 0表示没有错误，1表示有错误',
			'message' => '错误信息'
		),
	)
);