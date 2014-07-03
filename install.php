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
"CREATE TABLE user
(
  uid integer AUTO_INCREMENT PRIMARY KEY,
  uname varchar(16) UNIQUE NOT NULL,
  upwd varchar(32) NOT NULL,
  umail varchar(64) UNIQUE NOT NULL
)",

"CREATE TABLE tbid
(
  uid integer PRIMARY KEY,
  tbun varchar(16),
  tbcookie varchar(256),
  FOREIGN KEY (uid) REFERENCES user(uid)
)",

"CREATE TABLE tblist
(
  uid integer,
  tbname varchar(128),
  ign varchar(1),
  PRIMARY KEY (uid, tbname),
  FOREIGN KEY (uid) REFERENCES user(uid)
)",

"CREATE TABLE signlog
(
  uid integer,
  tbname varchar(128),
  status varchar(1),
  date integer,
  PRIMARY KEY (uid, tbname, date),
  FOREIGN KEY (uid) REFERENCES user(uid)
)",

"CREATE TABLE setting
(
  k varchar(16) PRIMARY KEY,
  v varchar(32)
)",

"CREATE TABLE bind
(
  uid1 integer,
  uid2 integer,
  PRIMARY KEY (uid1, uid2),
  FOREIGN KEY (uid1) REFERENCES user(uid),
  FOREIGN KEY (uid2) REFERENCES user(uid)
)"
	);
	
	foreach($sqlarr as $sql)
	{
	  if(!$conn->query($sql))
      {
        $errmsg = "创建数据库表失败：" . $conn->error;
		ShowMsg($errmsg, '');
        debug_log($errmsg);
		return;
      }
	}
	$errmsg = "创建数据库表成功";
	ShowMsg($errmsg, './index.php');
    debug_log($errmsg);
?>