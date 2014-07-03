<?php
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
	require_once "./lib/common.php";

    include "./config.php";
    $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
    if($conn->connect_errno != 0)
    {
      $errmsg = "数据库连接错误：" . $conn->connect_error;
	  ShowMsg($errmsg, '');
      debug_log($errmsg);
	  return;
    }
	
	$sqlarr = array(
"ALTER TABLE signlog
DROP COLUMN sid",

"ALTER TABLE signlog
ADD PRIMARY KEY (uid, tbname, date)"
	);
	
	foreach($sqlarr as $sql)
	  $conn->query($sql);
	$errmsg = "修改数据库表成功";
	ShowMsg($errmsg, './index.php');
    debug_log($errmsg);
?>