<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php"; ?>

<form id="form" action="./index.php" method="GET">
  <div class="page-header">
    <h2>用户注册</h2>
  </div>
  <input id="untxt" type="text" name="un" class="form-control" placeholder="用户名" required="" autofocus="" />
  <br />
  <input type="password" id="pwtxt" name="pw" class="form-control" placeholder="密码" required="" />
  <br />
  <input type="text" id="mailtxt" name="mail" class="form-control" placeholder="邮箱" required="" />
  <br />
<?php if($param['key'] != "") { ?>
  <input type="text" id="keytxt" name="key" class="form-control" placeholder="邀请码" required="" />
  <br />
<?php } ?>
  <input type="hidden" name="action" value="reg" />
  <input type="submit" id="regbtn" value="注册" class="btn btn-lg btn-primary btn-block"/>
</form>

<?php include "./page/footer.php"; ?>