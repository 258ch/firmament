$(function()
{
  //tbid part
  var get_id = function()
  {
    $.get('./ajax.php?action=getid', function(data)
    {
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
	    return;
	  }
	  if(json.exist == 1)
	  {
	    $('#tbid-un').text(json.un);
		$('#tbid-cotxt').val(json.cookie);
	    $('#tbid-log').removeClass('hidden');
		$('#tbid-unlog').addClass('hidden');
	  }
	  else
	  {
	    $('#tbid-unlog').removeClass('hidden');
		$('#tbid-log').addClass('hidden');
	  }
    });
  };
  get_id();
  
  var set_id = function()
  {
    var cookie = $("#tbid-cotxt").val();
	if(!cookie.match(/^BDUSS=.{192}$/))
	{
	  alert("Cookie格式错误！");
	  event.preventdefault();
	  return;
	}
	$.get('./ajax.php?action=setid&cookie=' + cookie,
	      function(data)
    {
	  var json = eval("(" + data + ")");
	  if(json.errno != 0)
	    alert(json.errmsg);
	  else
	  {
	    $('#tbid-un').text(json.un);
		$('#tbid-unlog').addClass('hidden');
	    $('#tbid-log').removeClass('hidden');
	  }
	});
  };
  $("#tbid-setbtn").click(set_id);
  
  var rm_id = function()
  {
    if(!confirm("真的要退出吗？"))
	  return;
    $.get('./ajax.php?action=rmid', function(data)
	{
	  var json = eval("(" + data + ")");
	  if(json.errno != 0)
	    alert(json.errmsg);
	  else
	  {
		$('#tbid-unlog').removeClass('hidden');
	    $('#tbid-log').addClass('hidden');
	  }
	});
  };
  $("#tbid-exitbtn").click(rm_id);


  //tbli part
  var setign_tblist = function()
  {
    var row = $(this).parent().parent();
    var tbname = $(row.children()[0]).text();
	$.get('./ajax.php?action=setign&tbname=' +
	      tbname + "&ign=" + $(this).is(':checked'),
		  function(data)
	{
	  var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
		if($(this).is(':checked'))
		  $(this).removeAttr('checked');
		else
		  $(this).attr('checked', 'checked');
	    return;
	  }
	});
  };

  var get_tblist = function()
  {
    $.get('./ajax.php?action=getlist', function(data)
    {
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
	    return;
	  }
	  
	  $('.tbli-row').remove();
	  for(var i in json.list)
	  {
	    var tbname = json.list[i][0];
	    var ign = "";
	    if(json.list[i][1] == "T")
	      ign = "checked=\"checked\"";
		var tr = $("<tr class=\"tbli-row\"></tr>");
	    var td1 = $("<td>" + tbname + "</td>");
		var td2 = $("<td></td>");
	    var chk = $("<input type=\"checkbox\" class=\"tbli-ign\"" + ign + "/>");
	    td2.append(chk);
	    tr.append(td1);
	    tr.append(td2);
	    $("#tbli-table").append(tr);
	  }
	  $(".tbli-ign").click(setign_tblist);
    });
  };
  
  var refresh_tblist = function()
  {
    $.get('./ajax.php?action=refreshlist', function(data)
	{
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
		return;
	  }
      
	  $('.tbli-row').remove();
	  for(var i in json.list)
	  {
	    var tbname = json.list[i][0];
		var ign = "";
	    if(json.list[i][1] == "T")
	      ign = "checked=\"checked\"";
	    var tr = $("<tr class=\"tbli-row\"></tr>");
	    var td1 = $("<td>" + tbname + "</td>");
		var td2 = $("<td></td>");
	    var chk = $("<input type=\"checkbox\" class=\"tblist-ign\"" + ign + " />");
	    td2.append(chk);
	    tr.append(td1);
	    tr.append(td2);
	    $("#tbli-table").append(tr);
	  }
      $(".tblist-ign").click(setign_tblist); 

      var msg = "刷新成功！新增" + json.add.toString() +
	            "个，移除" + json.rm.toString() + "个。"
	  alert(msg);
	});
  };
  $("#tbli-refresh").click(refresh_tblist);
  
  //signlog part  
  var get_signlog = function()
  {
    $.get('./ajax.php?action=getlog', function(data)
    {
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
	    return;
	  }
	  
	  $('.slog-row').remove();
	  for(var i in json.list)
	  {
	    var tbname = json.list[i][0];
	    var status = json.list[i][1];
		if(status == 'O')
		  status = '签到成功';
		else if(status == 'R')
		  status = '等待重试';
		else if(status == 'U')
		  status = '等待签到';
		else status = '签到失败';
	    var td1 = $("<td>" + tbname + "</td>");
	    var td2 = $("<td>" + status + "</td>");
	    var tr = $("<tr class=\"slog-row\"></tr>");
	    tr.append(td1);
	    tr.append(td2);
	    $("#slog-table").append(tr);
	  }
    });
  };

  var reset_signlog = function()
  {
    $.get('./ajax.php?action=resetlog', function(data)
	{
      var json = eval('(' + data + ')');
	  if(json.errno != '0')
	    alert(json.errmsg);
	  else
	  {
	    alert('重置成功！');
		get_signlog();
	  }
    });
  };
  $('#slog-reset').click(reset_signlog);
  
  //chpw part
  var change_pw = function()
  {
    var oldpw = $('#chpw-oldpwtxt').val();
	var newpw = $('#chpw-newpwtxt').val();
    var newpw2 = $('#chpw-chkpwtxt').val();
	$.get('./ajax.php?action=chpw&oldpw=' + oldpw +
	      '&newpw=' + newpw + '&newpw2=' + newpw2,
		  function(data)
	{
	  var json = eval('(' + data + ')');
	  if(json.errno != '0')
	    alert(json.errmsg);
	  else
	    alert('修改密码成功！');
	});
  };
  $("#chpw-smbtn").click(change_pw);
  
  //bind part
  var change_bind = function(a)
  {
    var row = $(this).parent().parent();
    var name = $(row.children()[0]).text();
	location.href = "./index.php?action=chaccount&bdname=" + name;
  };
  
  var remove_bind = function()
  {
    if(!confirm("真的要删除吗？"))
	  return;
  
    var row = $(this).parent().parent();
    var name = $(row.children()[0]).text();
	$.get("./ajax.php?action=rmbind&bdname=" + name, function(data)
	{
	  var json = eval('(' + data + ')');
	  if(json.errno != 0)
	    alert(json.errmsg);
	  else
	    row.remove();
	});
  };
  
  var get_bind = function()
  {
    $.get('./ajax.php?action=getbind', function(data)
    {
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
	    return;
	  }
	  
	  $('.bind-row').remove();
	  for(var i in json.list)
	  {
	    var tr = $("<tr class=\"bind-row\"></tr>");
	    var td1 = $("<td>" + json.list[i] + "</td>");
	    var td2 = $("<td></td>");
	    var a1 = $("<a class=\"bind-chbtn\">切换</a>");
	    var a2 = $("<a class=\"bind-rmbtn\">删除</a>");
	    td2.append(a1);
	    td2.append(' | ');
	    td2.append(a2);
	    tr.append(td1);
	    tr.append(td2);
	    $("#bind-table").append(tr);
	  }
	  $('.bind-chbtn').click(change_bind);
	  $('.bind-rmbtn').click(remove_bind);
    });
  };

  var set_bind = function()
  {
    var bdname = $("#bind-idtxt").val();
	if(!(/[\w\u4e00-\u9fa5]{1,14}/.test(bdname)))
	{
	  alert('用户名格式有误！');
	  return;
	}
	var bdpw = $("#bind-pwtxt").val();
	if(!(/[\x20-\x7e]{6,16}/.test(bdpw)))
	{
	  alert('密码格式有误！');
	  return;
	}
    $.get('./ajax.php?action=setbind&bdname=' + bdname +
	      "&bdpw=" + bdpw, function(data)
	{
	  var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
		return;
	  }
	  var tr = $("<tr class=\"bind-row\"></tr>");
	  var td1 = $("<td>" + bdname + "</td>");
	  var td2 = $("<td></td>");
	  var a1 = $("<a class=\"bind-chbtn\">切换</a>");
	  var a2 = $("<a class=\"bind-rmbtn\">删除</a>");
	  td2.append(a1);
	  td2.append(' | ');
	  td2.append(a2);
	  tr.append(td1);
	  tr.append(td2);
	  $("#bind-table").append(tr);
	  $('.bind-chbtn').click(change_bind);
	  $('.bind-rmbtn').click(remove_bind);
	});
  };
  $("#bind-addbtn").click(set_bind);
  
  
  //navbar item callbacks
  $("#tbid-item").click(function()
  {
    if($("#tbid-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#tbid-item").addClass("active");
    $(".sign-part").addClass("hidden");
	$("#tbid-part").removeClass("hidden");
	get_id();
  });

  $("#tbli-item").click(function()
  {
    if($("#tbli-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#tbli-item").addClass("active");
    $(".sign-part").addClass("hidden");
	$("#tbli-part").removeClass("hidden");
	get_tblist();
  });
  
  $("#slog-item").click(function()
  {
    if($("#slog-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#slog-item").addClass("active");
    $(".sign-part").addClass("hidden");
	$("#slog-part").removeClass("hidden");
	get_signlog();
  });
  
  $("#chpw-item").click(function()
  {
    if($("#chpw-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#chpw-item").addClass("active");
    $(".sign-part").addClass("hidden");
	$("#chpw-part").removeClass("hidden");
  });
  
  $("#bind-item").click(function()
  {
    if($("#bind-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#bind-item").addClass("active");
    $(".sign-part").addClass("hidden");
	$("#bind-part").removeClass("hidden");
	get_bind();
  });
});