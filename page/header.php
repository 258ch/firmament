<?php if(!defined("IN_USE")) exit(); ?>
<html>
<head>
  <title>苍穹 - 贴吧签到助手</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/bootstrap.css" />
  <link rel="stylesheet" href="./css/bootstrap-theme.min.css" />
  <link rel="stylesheet" href="./css/style.css" />
  <script src="./js/jquery.min.js"></script>
  <link rel="shortcut icon" href="/favicon.ico">
</head>
<body><div id="main" class="container">

  <div id="maindir" class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="./index.php">苍穹</a>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
<?php if(!defined("IS_LOGIN")){ ?>
        <li><a href="./index.php?action=login">登录</a></li>
        <li><a href="./index.php?action=reg">注册</a></li>
<?php } else { ?>
        <li><a href="./index.php?action=sign">签到中心</a></li>
<?php if(defined("IS_ADMIN")){ ?>
        <li><a href="./index.php?action=admin">管理面板</a></li>
<?php } ?>
		<li><a href="./index.php?action=logout">退出</a></li>
<?php } ?>
        <li><a href="./index.php?action=about">关于</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
  