<?php
  error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
  define("IN_USE", true);
  
  require_once "./lib/common.php";
  
  $usr = GetUserInfo();
  if($usr)
  {
    define("IS_LOGIN", true);
	if($usr["id"] == 1)
	  define("IS_ADMIN", true);
  }

  $action = $_GET["action"];
  if($action == "login")
    do_login($usr);
  else if($action == "reg")
    do_reg($usr);
  else if($action == "logout")
    do_logout($usr);
  else if($action == "about")
    include "./page/about_page.php";
  else if($action == "sign")
    do_sign($usr);
  else if($action == "admin")
    do_admin($usr);
  else if($action == "chaccount")
    do_chac($usr);
  else //index
  {
    $param = array();
    if(defined("IS_LOGIN"))
	  $param['un'] = $usr['un'];
    include "./page/index_page.php";
  }
  
  function do_login($usr)
  {
    if($usr)
	{
      echo "<script>location.href=\"./index.php\";</script>";
	  return;
	}
	
	$un = $_GET["un"];
	if($un == "")
	{
	  include "./page/login_page.php";
	  return;
	}
	if(preg_match('/^[\w\x{4e00}-\x{9fa5}]{1,14}$/u', $un) == 0)
	{
	  ShowMsg("用户名格式错误！", "./index.php?action=login");
	  return;
	}
	
	$pw = $_GET["pw"];
	if(preg_match('/^[\x20-\x7e]{6,16}$/', $pw) == 0)
	{
	  ShowMsg("密码格式错误！", "./index.php?action=login");
	  return;
	}
	$pw = md5($pw);
	
	include "./config.php";
	$conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
	if($conn->connect_errno != 0)
	{
	  ShowMsg("数据库错误：" . $conn->connect_error, "./index.php?action=login");
	  return;
	}
	
    $sql = "SELECT * FROM user WHERE uname=? AND upwd=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ss", $un, $pw);
	if(!$stmt->execute())
	{
	  ShowMsg("数据库错误：" . $stmt->error, "./index.php?action=login");
	  return;
	}
    $stmt->store_result();
    if($stmt->num_rows == 0)
    {
	  ShowMsg("用户不存在或密码错误！", "./index.php?action=login");
	  return;
	}
    $stmt->bind_result($row_uid, $row_un, $row_pw, $row_mail);
    $stmt->fetch();
    
	SetUserInfo($row_uid, $un, $pw);
	ShowMsg("登录成功！", "./index.php");
  }
  
  function do_reg($usr)
  {
    if($usr)
	{
      echo "<script>location.href=\"./index.php\";</script>";
	  return;
	}
	
	include "./config.php";
	$conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
	if($conn->connect_errno != 0)
	{
	  ShowMsg("数据库错误：" . $conn->connect_error, "./index.php");
	  return;
	}
	
	//是否允许注册
	$sql = "SELECT v FROM setting WHERE k='allowreg'";
	$res = $conn->query($sql);
	if(!$res)
	{
	  ShowMsg("数据库错误：" . $conn->error, "./index.php");
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
	if(!$allow)
	{
	  ShowMsg("当前不允许新用户注册", "./index.php");
	  return;
	}
	
	//邀请码
	$sql = "SELECT v FROM setting WHERE k='regkey'";
	$res = $conn->query($sql);
	if(!$res)
	{
	  ShowMsg("数据库错误：" . $conn->error, "./index.php");
	  return;
	}
	if($res->num_rows == 0)
	  $key = "";
	else
    {
      $row = $res->fetch_array();
	  $key = $row[0];
    }
	
	$un = $_GET["un"];
	if($un == "")
	{
	  $param = array('key' => $key);
	  include "./page/reg_page.php";
	  return;
	}
	if(preg_match('/^[\w\x{4e00}-\x{9fa5}]{1,14}$/u', $un) == 0)
	{
	  ShowMsg("用户名格式错误！", "./index.php?action=reg");
	  return;
	}
	
	$pw = $_GET["pw"];
	$pw2 = $_GET['pw2'];
	if(preg_match('/^[\x20-\x7e]{6,16}$/', $pw) == 0)
	{
	  ShowMsg("密码格式错误！", "./index.php?action=reg");
	  return;
	}
	if($pw2 != $pw)
	{
	  ShowMsg("两次输入密码不一致！", "./index.php?action=reg");
	  return;
	}
	$pw = md5($pw);
	
	$mail = $_GET["mail"];
	if(preg_match('/^[\w\-\.]+?@(\w+?\.)+?\w{2,4}$/', $mail) == 0)
	{
	  ShowMsg("邮箱格式错误！", "./index.php?action=reg");
	  return;
	}
	
	$input_key = $_GET['key'];
	if($key != "" && $key != $input_key)
	{
	  ShowMsg("邀请码错误！", "./index.php?action=reg");
	  return;
	}
	
	$sql = "INSERT IGNORE INTO user (uname, upwd, umail) VALUES (?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("sss", $un, $pw, $mail);
	if(!$stmt->execute())
	{
	  ShowMsg("数据库错误：" . $stmt->error, "./index.php?action=reg");
	  return;
	}
	if($stmt->affected_rows == 0)
	{
	  ShowMsg("用户名或邮箱已存在！", "./index.php?action=reg");
	  return;
	}
	$uid = $stmt->insert_id;
	
	SetUserInfo($uid, $un, $pw);
	ShowMsg("注册成功！", "./index.php");
  }
  
  function do_sign($usr)
  {
    if(!$usr)
	{
	  echo "<script>location.href=\"./index.php?action=login\";</script>";
	  return;
	}
    include "./page/sign_page.php";
  }
  
  function do_admin($usr)
  {
    if(!$usr || $usr['id'] != 1)
	{
	  echo "<script>location.href=\"./index.php?action=login\";</script>";
	  return;
	}
    include "./page/admin_page.php";
  }
  
  function do_logout($usr)
  {
    if($usr)
	  setcookie("token", "");
	echo "<script>location.href=\"./index.php\";</script>";
  }
  
  function do_chac($usr)
  {
    if(!$usr)
	{
	  echo "<script>location.href=\"./index.php?action=login\";</script>";
	  return;
	}
  
    $bdname = $_GET['bdname'];
	if($bdname == "")
	{
	  ShowMsg("未指定用户", "./index.php?action=sign");
	  return;
	}
	
	include "./config.php";
	$conn = new mysqli($db_server, $db_username, $db_password, $db_name, $db_port);
	if($conn->connect_errno != 0)
	{
	  ShowMsg("数据库错误：" . $conn->connect_error, "./index.php?action=sign");
	  return;
	}
	
	$sql = "SELECT * FROM user WHERE uname=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bdname);
    if(!$stmt->execute())
    {
	  ShowMsg("数据库错误：" . $stmt->error, "./index.php?action=sign");
	  return;
    }
    $stmt->store_result();
    if($stmt->num_rows == 0)
    {
	  ShowMsg("被绑定用户不存在", "./index.php?action=sign");
	  return;
    }
    $stmt->bind_result($row_uid, $row_un, $row_pw, $row_mail);
    $stmt->fetch();
    $tar_usr = array('id' => $row_uid, 'un' => $row_un, 'pw' => $row_pw);
	
	$sql = "SELECT * FROM bind WHERE (uid1=? AND uid2=?) OR (uid1=? AND uid2=?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("iiii", $usr['id'], $tar_usr['id'], $tar_usr['id'], $usr['id']);
	if(!$stmt->execute())
	{
	  ShowMsg("数据库错误：" . $conn->error, "./index.php?action=sign");
	  return;
	}
	$stmt->store_result();
	if($stmt->num_rows == 0)
	{
	  ShowMsg("未绑定该用户", "./index.php?action=sign");
	  return;
	}
	
	SetUserInfo($tar_usr['id'], $tar_usr['un'], $tar_usr['pw']);
	ShowMsg("您已切换到用户：" . $tar_usr['un'], "./index.php?action=sign");
  }
?>