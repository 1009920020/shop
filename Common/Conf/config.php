<?php
return array(
	//'配置项'=>'配置值'
    'SHOW_PAGE_TRACE'=> FALSE,//配置跟踪信息
    //设置默认分组
    'DEFAULT_MODULE'=> 'Home',
    //允许访问的分组
    'MODULE_ALLOW_LIST'=> array('Home','Admin'),
    //设置smarty模版引擎
    'TMPL_ENGINE_TYPE'=> 'Smarty',
    //为Smarty配置相关配置
    'TMPL_ENGINE_CONFIG' => array(
        //'left_delimiter' => '<@@@',
        //'right_delimiter' => '@@@>',
    ),
    
    //配置session有效期
    'SESSION_OPTIONS' => array(
        'name' => 'session_name',
        'prefix' => 'think',
        'expire' =>10800,  //三小时过期
    ),
    
    //配置跳转文件目录
    'TMPL_ACTION_SUCCESS' => 'Public/jump',
    'TMPL_ACTION_ERROR' => 'Public/jump',
    
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'lyy_d',          // 数据库名
    'DB_USER'               =>  'lyy_u',      // 用户名
    'DB_PWD'                =>  'lyy_mm',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数    
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
);