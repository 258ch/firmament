<?php 
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
date_default_timezone_set('PRC');

require_once "./lib/common.php";
require_once "./lib/TBOps_Sign.php";
require_once "./lib/WizardHTTP.php";

$usr = GetUserInfo();
if(!$usr)
{
  echo app_error(2, '用户未登录');
  return;
}

$action = $_GET["action"];
if($action == "getid")
  do_getid($usr);
else if($action == "setid")
  do_setid($usr);
else if($action == "rmid")
  do_rmid($usr);
else if($action == "getlist")
  do_getlist($usr);
else if($action == "refreshlist")
  do_refreshlist($usr);
else if($action == "setign")
  do_setign($usr);
else if($action == "getlog")
  do_getlog($usr);
else if($action == "resetlog")
  do_resetlog($usr);
else if($action == "chpw")
  do_chpw($usr);
else if($action == "setbind")
  do_setbind($usr);
else if($action == "getbind")
  do_getbind($usr);
else if($action == "rmbind")
  do_rmbind($usr);
else if($action == "getuser")
  do_getuser($usr);
else if($action == "rmuser")
  do_rmuser($usr);
else if($action == "getregset")
  do_getregset($usr);
else if($action == "setregset")
  do_setregset($usr);
else
  echo app_error(5, '未指定操作'); 

function do_getid($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT * FROM tbid WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $row = $res->fetch_row();
  
  if(!$row)
    echo json_encode(array('errno' => 0, 'exist' => 0));
  else
    echo json_encode(array('errno' => 0, 'exist' => 1,
	                       'un' => $row[1], 'cookie' => $row[2]));
}

function do_setid($usr)
{
  $cookie = $_GET["cookie"];
  if(preg_match('/^BDUSS=.{192}$/', $cookie) == 0)
  {
	echo app_error(1, 'Cookie格式错误');
	return;
  }

  $wc = new WizardHTTP();
  $wc->SetDefHdr();
  $wc->SetHdr("Cookie", $cookie);
  $un = GetUN($wc);
  if($un == '')
  {
    echo app_error(1, 'Cookie已失效');
	return;
  }
  $r = json_encode(array('errno' => 0, 'un' => $un));
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "INSERT INTO tbid VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $usr['id'], $un, $cookie);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $res = GetTBList($wc);
  if($res['errno'] != 0)
  {
    echo app_error(1, $res['errmsg']);
	return;
  }
  $list = $res['list'];
  if(count($list) == 0)
  {
    echo $r;
	return;
  }
  
  $sql = "INSERT INTO tblist VALUES";
  foreach($list as $elem)
    $sql .= sprintf(" (%d,'%s','F'),", $usr['id'], $elem);
  $sql = substr($sql, 0, strlen($sql) - 1);
  if(!$conn->query($sql))
  {
    echo exec_error($conn);
	return;
  }
  
  $dt = (int)Date('Ymd');
  $sql = "INSERT INTO signlog (uid,tbname,status,date) VALUES";
  foreach($list as $elem)
    $sql .= sprintf(" (%d,'%s','U',%d),", $usr['id'], $elem, $dt);
  $sql = substr($sql, 0, strlen($sql) - 1);
  if(!$conn->query($sql))
  {
    echo exec_error($conn);
	return;
  }
  
  echo $r;
}

function do_rmid($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "DELETE FROM tbid WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $sql = "DELETE FROM tblist WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $sql = "DELETE FROM signlog WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_refreshlist($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT * FROM tbid WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $row = $res->fetch_row();
  if(!$row)
  {
    echo app_error(3, 'Cookie未设置');
    return;
  }
  $cookie = $row[2];
  
  $sql = "SELECT tbname FROM tblist WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $old_list = array();
  while($row = $res->fetch_row())
    $old_list[] = $row[0];
  
  require_once './lib/TBOps_Sign.php';
  require_once './lib/WizardHTTP.php';
  $wc = new WizardHTTP();
  $wc->SetDefHdr();
  $wc->SetHdr('Cookie', $cookie);
  $res = GetTBList($wc);
  if($res['errno'] != 0)
  {
    echo app_error(1, $res['errmsg']);
	return;
  }
  $new_list = $res['list'];
  
  $to_add = array_diff($new_list, $old_list);
  $to_rm = array_diff($old_list, $new_list);
  
  if(count($to_add) != 0)
  {
    $sql = "INSERT INTO tblist VALUES";
    foreach($to_add as $tb)
      $sql .= sprintf(" ('%d','%s','F'),", $usr['id'], $tb);
    $sql = substr($sql, 0, strlen($sql) - 1);
    if(!$conn->query($sql))
    {
      echo exec_error($conn);
	  return;
    }
  }
  
  if(count($to_rm) != 0)
  {
    $tmp = "";
	foreach($to_rm as $tb)
	  $tmp .= "'" . $tb . "',";
	$tmp = substr($tmp, 0, strlen($tmp - 1));
    $sql = "DELETE FROM tblist WHERE uid=" . $usr['id'] .
	       " AND tbname IN (" . $tmp . ")";
    if(!$conn->query($sql))
    {
      echo exec_error($conn);
	  return;
    }
  }
  
  $sql = "SELECT tbname, ign FROM tblist WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $list = array();
  while($row = $res->fetch_row())
    $list[] = $row;
  
  echo json_encode(array('errno' => 0, 'list' => $list,
                         'add' => count($to_add),
						 'rm' => count($to_rm)));
}

function do_getlist($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT tbname, ign FROM tblist WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $list = array();
  while($row = $res->fetch_row())
    $list[] = $row;

  echo json_encode(array('errno' => 0, 'list' => $list));
}

function do_setign($usr)
{
  $tbname = $_GET['tbname'];
  if($tbname == "")
  {
    echo app_error(3, '参数tbname格式错误');
	return;
  }
  $ign = $_GET['ign'];
  if($ign != 'true' && $ign != 'false')
  {
    echo app_error(4, '参数ign格式错误');
	return;
  }
  if($ign == 'true')
    $ign = 'T';
  else $ign = 'F';
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "UPDATE tblist SET ign=? WHERE uid=? AND tbname=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sis", $ign, $usr['id'], $tbname);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_getlog($usr)
{
  $dt = $_GET['date'];
  if($dt == "")
    $dt = date("Ymd");
  if(!preg_match('/^\d+$/', $dt))
  {
    echo app_error(6, '日期格式错误');
	return;
  }
  $dt = (integer)$dt;
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT tbname, status FROM signlog WHERE uid=? AND date=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $usr['id'], $dt);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  $list = array();
  while($arr = $res->fetch_row())
    $list[] = $arr;

  echo json_encode(array('errno' => 0, 'list' => $list));
}

function do_resetlog($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $dt = (int)Date('Ymd');
  $sql = "UPDATE signlog SET status='R' WHERE status='F' AND uid=? AND date=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $usr['id'], $dt);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_chpw($usr)
{
  $oldpw = $_GET['oldpw'];
  $newpw = $_GET['newpw'];
  $newpw2 = $_GET['newpw2'];
  if(preg_match('/^[\x20-\x7e]{6,14}$/', $oldpw) == 0)
  {
    echo app_error(7, "原密码格式有误");
	return;
  }
  if(preg_match('/^[\x20-\x7e]{6,14}$/', $newpw) == 0)
  {
    echo app_error(7, "新密码格式有误");
	return;
  }
  if($newpw2 != $newpw)
  {
    echo app_error(11, "两次数据的密码不一致");
	return;
  }
  $oldpw = md5($oldpw);
  $newpw = md5($newpw);
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "UPDATE user SET upwd=? WHERE upwd=? AND uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $newpw, $oldpw, $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  if($stmt->affected_rows == 0)
  {
    echo app_error(10, "密码错误");
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_getbind($usr)
{
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT user1.uname, user2.uname FROM bind " .
         "JOIN user AS user1 ON uid1=user1.uid " .
         "JOIN user AS user2 ON uid2=user2.uid WHERE uid1=? OR uid2=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $usr['id'], $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $res = $stmt->get_result();
  $list = array();
  while($arr = $res->fetch_row())
  {
    if($arr[0] != $usr['un'])
	  $list[] = $arr[0];
	else if($arr[1] != $usr['un'])
	  $list[] = $arr[1];
  }
  
  echo json_encode(array('errno' => 0, 'list' => $list));
}

function do_setbind($usr)
{
  $bdname = $_GET['bdname'];
  if($bdname == "")
  {
    echo app_error(8, '需要填写目标账号');
	return;
  }
  if($bdname == $usr['un'])
  {
    echo app_error(8, '不可以绑定自己');
	return;
  }
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT * FROM user WHERE uname=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $bdname);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  if($res->num_rows == 0)
  {
    echo app_error(9, "被绑定用户不存在");
	return;
  }
  $row = $res->fetch_array();
  $tar_uid = $row[0];
  
  $sql = "INSERT INTO bind VALUES (?,?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $usr['id'], $tar_uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_rmbind($usr)
{
  $bdname = $_GET['bdname'];
  if($bdname == "")
  {
    echo app_error(8, '需要填写目标账号');
	return;
  }
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT * FROM user WHERE uname=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $bdname);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  $res = $stmt->get_result();
  if($res->num_rows == 0)
  {
    echo app_error(9, "被绑定用户不存在");
	return;
  }
  $row = $res->fetch_array();
  $tar_uid = $row[0];
  
  $sql = "DELETE FROM bind WHERE (uid1=? AND uid2=?) OR (uid1=? AND uid2=?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iiii", $usr['id'], $tar_uid, $tar_uid, $usr['id']);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_getuser($usr)
{
  if($usr['id'] != 1)
  {
    echo app_error(2, '用户无管理权限');
    return;
  }
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT uid, uname FROM user";
  $res = $conn->query($sql);
  if(!$res)
  {
    echo exec_error($conn);
	return;
  }
  $list = array();
  while($row = $res->fetch_array())
    $list[] = $row;

  echo json_encode(array('errno' => 0, 'list' => $list));
}

function do_rmuser($usr)
{
  if($usr['id'] != 1)
  {
    echo app_error(2, '用户无管理权限');
    return;
  }
  
  $uid = $_GET['uid'];
  if($uid == "")
  {
    echo app_error(12, '请填写目标用户');
    return;
  }
  if($uid == 1)
  {
    echo app_error(12, '不可删除管理员');
    return;
  }

  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "DELETE FROM tbid WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $sql = "DELETE FROM tblist WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $sql = "DELETE FROM signlog WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  $sql = "DELETE FROM bind WHERE uid1=? OR uid2=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $uid, $uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }

  $sql = "DELETE FROM user WHERE uid=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $uid);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  
  echo json_encode(array('errno' => 0));
}

function do_getregset($usr)
{
  if($usr['id'] != 1)
  {
    echo app_error(2, '用户无管理权限');
    return;
  }
  
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "SELECT v FROM setting WHERE k='allowreg'";
  $res = $conn->query($sql);
  if(!$res)
  {
    echo exec_error($conn);
	return;
  }
  if($res->num_rows == 0)
    $allow = true;
  else
  {
    $row = $res->fetch_array();
    $allow = $row[0];
	$allow = ($allow == "true");
  }
  
  $sql = "SELECT v FROM setting WHERE k='regkey'";
  $res = $conn->query($sql);
  if(!$res)
  {
    echo exec_error($conn);
	return;
  }
  if($res->num_rows == 0)
    $key = "";
  else
  {
    $row = $res->fetch_array();
    $key = $row[0];
  }

  echo json_encode(array('errno' => 0, 'allow' => $allow, 'key' => $key));
}

function do_setregset($usr)
{
  if($usr['id'] != 1)
  {
    echo app_error(2, '用户无管理权限');
    return;
  }
  
  $key = $_GET['key'];
  $allow = $_GET['allow'];
  if($allow != "true")
    $allow = "false";
	
  include "./config.php";
  $conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
  if($conn->connect_errno != 0)
  {
    echo conn_error($conn);
	return;
  }
  
  $sql = "INSERT IGNORE INTO setting VALUES ('allowreg',?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $allow);
  if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  if($stmt->affected_rows == 0)
  { 
	$sql = "UPDATE setting SET v=? WHERE k='allowreg'";
	$stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $allow);
    if(!$stmt->execute())
    {
      echo exec_error($stmt);
	  return;
    }
  }
  
  $sql = "INSERT IGNORE INTO setting VALUES ('regkey',?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $key);
   if(!$stmt->execute())
  {
    echo exec_error($stmt);
	return;
  }
  if($stmt->affected_rows == 0)
  {
    $sql = "UPDATE setting SET v=? WHERE k='regkey'";
	$stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
    if(!$stmt->execute())
    {
      echo exec_error($stmt);
	  return;
    }
  }
  
  echo json_encode(array('errno' => 0));
}
?>