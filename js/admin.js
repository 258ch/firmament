$(function()
{
  //user part
  var rm_user = function()
  {
    if(!confirm("真的要删除吗？"))
	  return;
	  
	var row = $(this).parent().parent();
	var uid = $(row.children()[0]).text();
	$.get("./ajax.php?action=rmuser&uid=" + uid, function(data)
	{
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	    alert(json.errmsg);
	  else
	    row.remove();
    });
  };

  var get_user = function()
  {
    $.get("./ajax.php?action=getuser", function(data)
	{
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
		return;
	  }
	  
	  $('.user-row').remove();
	  for(var i in json.list)
	  {
	    var uid = json.list[i][0];
        var name = json.list[i][1];
		var tr = $("<tr class=\"user-row\"></tr>");
        var td1 = $("<td>" + uid.toString() + "</td>");		
		var td2 = $("<td>" + name + "</td>");
		var td3 = $("<td></td>");
		var rmbtn = $("<a class=\"user-rmbtn\">删除</a>");
		td3.append(rmbtn);
		tr.append(td1);
		tr.append(td2);
		tr.append(td3);
		$("#user-table").append(tr);
      }
	  $(".user-rmbtn").click(rm_user);
    });
  };
  get_user();
  
  //regsetting part
  var get_regset = function()
  {
    $.get("./ajax.php?action=getregset", function(data)
	{
      var json = eval('(' + data + ')');
	  if(json.errno != 0)
	  {
	    alert(json.errmsg);
		return;
	  }
	  
	  if(json.allow)
	    $('#reg-allowreg').attr('checked', 'checked');
	  else
	    $('#reg-allowreg').removeAttr('checked');
	  $('#reg-keytxt').val(json.key);
    });
  };
  
  var set_regset = function()
  {
    var allow = $('#reg-allowreg').is(':checked');
	var key = $('#reg-keytxt').val();
	
	$.get('./ajax.php?action=setregset&allow=' + allow + "&key=" + key,
	      function(data)
	{
	  var json = eval('(' + data + ')');
	  if(json.errno != 0)
	    alert(json.errmsg);
	  else
	    alert('设置成功！');
	});
  };
  $('#reg-smbtn').click(set_regset);
  
  //navbar item callbacks
  $("#user-item").click(function()
  {
    if($("#user-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#user-item").addClass("active");
    $(".admin-part").addClass("hidden");
	$("#user-part").removeClass("hidden");
	get_user();
  });

  $("#reg-item").click(function()
  {
    if($("#reg-item").hasClass("active"))
	  return;
	$(".list-group-item").removeClass("active");
	$("#reg-item").addClass("active");
    $(".admin-part").addClass("hidden");
	$("#reg-part").removeClass("hidden");
	get_regset();
  });
  
});