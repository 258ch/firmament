<?php

$auth_key = '';			//建议安装后尽快修改密钥

if(defined('SAE_MYSQL_DB')) {
// ------------------ SAE 数据库设定 ------------------
	$db_server = SAE_MYSQL_HOST_M;						// 已自动设置好，无需干预
	$db_port = SAE_MYSQL_PORT;
	$db_username = SAE_MYSQL_USER;
	$db_password = SAE_MYSQL_PASS;
	$db_name = SAE_MYSQL_DB;
// -------------- END SAE 数据库设定 ------------------
} elseif(getenv('HTTP_BAE_ENV_ADDR_SQL_IP')) {
// ------------------ BAE 数据库设定 ------------------
    $db_server = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
    $db_port = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
    $db_username = getenv('HTTP_BAE_ENV_AK');
    $db_password = getenv('HTTP_BAE_ENV_SK');
    $db_name = 'GXQLIvEDHyaq';	// 修改成你的数据库名
// -------------- END BAE 数据库设定 ------------------
} else {
// ------------------ 非BAE、SAE 数据库设定 ------------------
	$db_server = 'localhost';			// 数据库服务器地址
	$db_port = '3306';				// 数据库端口
	$db_username = 'root';			// 数据库用户名
	$db_password = '123456';			// 数据库密码
	$db_name = 'iceimpr';				// 数据库名
// -------------- END 非BAE、SAE 数据库设定 ------------------
}
?>