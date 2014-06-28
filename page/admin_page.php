<?php if(!defined("IN_USE")) exit(); ?>

<?php include "./page/header.php" ?>

<div class="row row-offcanvas row-offcanvas-right">

  <div class="col-md-2 sidebar-offcanvas" id="sidebar" role="navigation">
    <div class="list-group">
      <a href="#" id="user-item" class="list-group-item active">用户管理</a>
      <a href="#" id="reg-item" class="list-group-item">注册设置</a>
    </div>
  </div><!--. sidebar-offcanvas-->

  <div class="col-md-10 container">

<!------------------------------------------------------------------------------------------------------------->
  
    <div id="user-part" class="admin-part">
  
      <div id="user-list" class="panel panel-primary">
	    <div class="panel-heading">用户列表</div>
		<table id="user-table" class="table table-striped">
		  <tr>
		    <th>uid</th>
			<th>用户名</th>
		    <th>操作</th>
		  </tr>
		</table>
	  </div><!--#slog-list-->
  
    </div><!--#user-part-->
  
<!------------------------------------------------------------------------------------------------------------->
  
    <div id="reg-part" class="admin-part hidden" >
	
	  <div id="reg-setting" class="panel panel-default">
	    <div class="panel-heading">注册设置</div>
		<div class="panel-body">
		
		  <div class="row">
		    <div class="col-md-2">是否开放注册</div>
			<div class="col-md-1">
			  <input type="checkbox" id="reg-allowreg" class="form-control" />
			</div>
		  </div>
		  <div class="row">
		    <div class="col-md-2">邀请码（为空即不使用）</div>
			<div class="col-md-4">
			  <input type="text" id="reg-keytxt" class="form-control" />
			</div>
		  </div>
		  <div class="row">
		    <div class="col-md-2"></div>
		    <div class="col-md-4">
			  <input type="button" value="提交" id="reg-smbtn" class="btn btn-default btn-block" />
			</div>
		  </div>
		
	    </div><!--#reg-setting-->
	  </div><!--#reg-part-->
	
	</div><!--#reg-setting-->
  
  </div><!--.container-->

</div><!--.row-->

<script src="./js/admin.js"></script>

<?php include "./page/footer.php" ?>