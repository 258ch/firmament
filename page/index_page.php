<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php" ?>

<div id="banner" class="jumbotron">
  <h1><span class="glyphicon glyphicon-edit"></span> 苍穹 - 贴吧签到助手</h1>
  <p>简捷高效的贴吧自动签到解决方案。</p>
</div>

<?php if(defined("IS_LOGIN")) { ?>
<div class="alert alert-info" role="alert">
  <p>欢迎回来，<?php echo $param['un']; ?>。</p>
</div>
<?php } ?>

<div class="row">
  <div class="col-md-4">
    <h2>全天候</h2>
    <p>我们将为您提供全天候的签到服务，再也不用每天手动签到了。 </p>
  </div>
  <div class="col-md-4">
    <h2>经验更多</h2>
    <p>模拟客户端签到，首签+6，连续+8。 </p>
  </div>
  <div class="col-md-4">
    <h2>使用方便</h2>
    <p>全自动无人值守，一劳永逸，永久免费，智能监控，拒绝漏签。</p>
  </div>
</div>

<?php include "./page/footer.php" ?>