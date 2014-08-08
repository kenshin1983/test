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
	)
);