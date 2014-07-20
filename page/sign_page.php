<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php" ?>

<div class="row row-offcanvas row-offcanvas-right">

  <div class="col-md-2 sidebar-offcanvas" id="sidebar" role="navigation">
    <div class="list-group">
      <a href="#" id="tbid-item" class="list-group-item active">贴吧登录</a>
      <a href="#" id="tbli-item" class="list-group-item">贴吧列表</a>
      <a href="#" id="slog-item" class="list-group-item">签到记录</a>
      <a href="#" id="chpw-item" class="list-group-item">修改密码</a>
      <a href="#" id="bind-item" class="list-group-item">账号绑定</a>
    </div>
  </div><!--. sidebar-offcanvas-->
  
  <div class="col-md-10 container">
	
<!------------------------------------------------------------------------------------------------------------->
	
	<div id="tbid-part" class="sign-part">  
	
	  <div id="tbid-unlog" class="panel panel-danger">
	    <div class="panel-heading">Cookie设置</div>
	    <div class="panel-body">
	      <p>您还没有设置贴吧的Cookie，请设置。格式为“BDUSS=”以及后面的192个字母。</p>
		  <div class="row">
		    <div class="col-md-5">
	          <input type="text" id="tbid-cotxt" class="form-control" />
			</div>
			<div class="col-md-2">
		      <input type="button" value="设置" id="tbid-setbtn" class="btn btn-primary btn-block" />
			</div>
	      </div>
	    </div>
	  </div><!--#tbid-unlog-->
	  
	  <div id="tbid-log" class="panel panel-success hidden">
	    <div class="panel-heading">贴吧账号信息</div>
	    <div class="panel-body">
		  <div class="row">
		    <div class="col-md-3">
		      <p>用户名：<span id="tbid-un"></span></p>
			</div>
			<div class="col-md-1">
		      <input type="button" value="退出" id="tbid-exitbtn" class="btn btn-primary btn-block" />
			</div>
		  </div>
		</div>
	  </div><!--#tbid-log-->
	  
	</div><!--#tbid-part-->

<!------------------------------------------------------------------------------------------------------------->	

	<div id="tbli-part" class="sign-part hidden">
	  
	  <div id="tbli-list" class="panel panel-info">
	    <div class="panel-heading">贴吧列表</div>
		<table id="tbli-table" class="table table-striped">
		  <tr>
		    <th>贴吧名称</th>
		    <th>是否忽略</th>
		  </tr>
		</table>
		<hr />
		<div class="panel-body">
		  <div class="row">
		    <div class="col-md-4">
		      <input type="button" class="btn btn-block btn-info" id="tbli-refresh" value="重新获取" />
			</div>
		  </div>
	    </div>
	  </div><!--#tbli-list-->
	  
	</div><!--#tbli-part-->
	
<!------------------------------------------------------------------------------------------------------------->
	
	<div id="slog-part" class="sign-part hidden">
	
	  <div id="slog-list" class="panel panel-primary">
	    <div class="panel-heading">签到记录</div>
		<table id="slog-table" class="table table-striped">
		  <tr>
		    <th>贴吧名称</th>
		    <th>状态</th>
		  </tr>
		</table>
		<hr />
		<div class="panel-body">
		  <div class="col-md-4">
		      <input type="button" class="btn btn-block btn-primary" id="slog-reset" value="重置签到失败的贴吧" />
			</div>
	    </div>
	  </div><!--#slog-list-->
	  
	</div><!--#slog-part-->
	
<!------------------------------------------------------------------------------------------------------------->
	
	<div id="chpw-part" class="sign-part hidden">
	
	  <div id="chpw-ch" class="panel panel-danger">
	    <div class="panel-heading">修改密码</div>
		<div class="panel-body">
		  
		  <div class="row">
		    <div class="col-md-2">旧密码</div>
			<div class="col-md-4">
			  <input type="password" id="chpw-oldpwtxt" class="form-control" />
			</div>
		  </div>
		  <div class="row">
		    <div class="col-md-2">新密码</div>
			<div class="col-md-4">
			  <input type="password" id="chpw-newpwtxt" class="form-control" />
			</div>
		  </div>
		  <div class="row">
		    <div class="col-md-2">确认新密码</div>
			<div class="col-md-4">
			  <input type="password" id="chpw-chkpwtxt" class="form-control" />
			</div>
		  </div>
		  <div class="row">
		    <div class="col-md-2"></div>
		    <div class="col-md-4">
			  <input type="button" value="提交" id="chpw-smbtn" class="btn btn-danger btn-block" />
			</div>
		  </div>
		  
	    </div>
	  </div><!--#chpw-ch-->
	  
	</div><!--#chpw-part-->
	
<!------------------------------------------------------------------------------------------------------------->
	
	<div id="bind-part" class="sign-part hidden">
	
	  <div id="bind-list" class="panel panel-default">
	    <div class="panel-heading">账号绑定</div>
		<table id="bind-table" class="table table-striped">
		  <tr>
		    <th>账号</th>
		    <th>操作</th>
		  </tr>
		</table>
		<hr />
		<div class="panel-body">
		  <div class="row">
		    <div class="col-md-4">
		      <input type="text" class="form-control" id="bind-idtxt" placeholder="用户名"/>
			</div>
			<div class="col-md-4">
		      <input type="text" class="form-control" id="bind-pwtxt" placeholder="密码"/>
			</div>
		    <div class="col-md-2">
		      <input type="button" class="btn btn-block btn-primary" id="bind-addbtn" value="添加绑定" />
			</div>
		  </div>
	    </div>
	  </div><!--#bind-list-->
	  
	</div><!--#bind-part-->
	
  </div><!--.container-->
  
</div><!--.row-->

<script src="./js/sign.js"></script>

<?php include "./page/footer.php" ?>