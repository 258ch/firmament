<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php"; ?>

<form id="form" action="./index.php" method="GET" role="form">
  <div class="page-header">
    <h2>用户登录</h2>
  </div>
  <input id="untxt" type="text" name="un" class="form-control" placeholder="用户名" required="" autofocus="" />
  <br />
  <input type="password" id="pwtxt" name="pw" class="form-control" placeholder="密码" required="" />
  <br />
  <input type="hidden" name="action" value="login" />
  <input type="submit" id="lgnbtn" value="登录" class="btn btn-lg btn-primary btn-block"/>
</form>

<?php include "./page/footer.php"; ?>