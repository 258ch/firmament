<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php"; ?>

<form id="form" action="./index.php" method="GET">
  <div class="page-header">
    <h2>用户注册</h2>
  </div>
  <input id="untxt" type="text" name="un" class="form-control"
         placeholder="用户名" required="required" autofocus="autofocus"
         pattern="[\w\u4e00-\u9fa5]{1,14}" title="请输入1~14位汉字、数字、字母或下划线。"/>
  <br />
  <input type="password" id="pwtxt" name="pw" class="form-control"
         placeholder="密码" required="required" pattern="[\x20-\x7e]{6,16}"
         title="密码应为6~16位。"/>
  <br />
  <input type="email" id="mailtxt" name="mail" class="form-control"
         placeholder="邮箱" required="required" />
  <br />
<?php if($param['key'] != "") { ?>
  <input type="text" id="keytxt" name="key" class="form-control"
         placeholder="邀请码" required="required" />
  <br />
<?php } ?>
  <input type="hidden" name="action" value="reg" />
  <input type="submit" id="regbtn" value="注册" class="btn btn-lg btn-primary btn-block"/>
</form>

<?php include "./page/footer.php"; ?>