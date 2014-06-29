<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php"; ?>

<form id="form" action="./index.php" method="GET" role="form">
  <div class="page-header">
    <h2>用户登录</h2>
  </div>
  <input id="untxt" type="text" name="un" class="form-control"
         placeholder="用户名" required="required" autofocus="autofocus"
         pattern="[\w\u4e00-\u9fa5]{1,14}" title="请输入1~14位汉字、数字、字母或下划线。"/>
  <br />
  <input type="password" id="pwtxt" name="pw" class="form-control"
         placeholder="密码" required="required" pattern="[\x20-\x7e]{6,16}"
         title="密码应为6~16位。"/>
  <br />
  <input type="hidden" name="action" value="login" />
  <input type="submit" id="lgnbtn" value="登录" class="btn btn-lg btn-primary btn-block"/>
</form>

<?php include "./page/footer.php"; ?>