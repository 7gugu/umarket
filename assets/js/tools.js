function modal_init(id,title,body){
	//初始化模态窗口
	 if(!arguments[0]) id = "modal_event";
	 if(!arguments[1]) title = "提醒标题";
	 if(!arguments[2]) body = "提醒事项";
	 var template="<div class=\"am-popup\" id=\""+id+"\" style=\"top:430px;\"><div class=\"am-popup-inner\"><div class=\"am-popup-hd\"><h4 class=\"am-popup-title\">"+title+"</h4><span data-am-modal-close class=\"am-close\">&times;</span></div><div class=\"am-popup-bd\" style=\"height:100%;\">"+body+"</div></div></div>";
	 $('#modal_show').html(template);
	}
function modal_open(id){
	//启用模态窗口
	 $(id).modal('open');
	} 
function modal_close(id){
	//关闭模态窗口
	 $(id).modal('close');	
	}
	
	//类同与PHP的explode
function explode(inputstring, separators, includeEmpties) {
 
 inputstring = new String(inputstring);
  separators = new String(separators);
 
  if(separators == "undefined") {
    separators = " :;";
  }
 
  fixedExplode = new Array(1);
  currentElement = "";
  count = 0;
 
  for(x=0; x < inputstring.length; x++) {
    str = inputstring.charAt(x);
    if(separators.indexOf(str) != -1) {
        if ( ( (includeEmpties <= 0) || (includeEmpties == false)) && (currentElement == "")) {
        }
        else {
            fixedExplode[count] = currentElement;
            count++;
            currentElement = "";
        }
    }
    else {
        currentElement += str;
    }
  }
 
  if (( ! (includeEmpties <= 0) && (includeEmpties != false)) || (currentElement != "")) {
      fixedExplode[count] = currentElement;
  }
  return fixedExplode;
}
//设定cookies
function setcookie(name,value,time)
{ 
    var strsec = getsec(time); 
    var exp = new Date(); 
    exp.setTime(exp.getTime() + strsec*1); 
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
} 
function getsec(str)
{  
   var str1=str.substring(1,str.length)*1; 
   var str2=str.substring(0,1); 
   if (str2=="s")
   { 
        return str1*1000; 
   }
   else if (str2=="h")
   { 
       return str1*60*60*1000; 
   }
   else if (str2=="d")
   { 
       return str1*24*60*60*1000; 
   } 
} 
//这是有设定过期时间的使用示例： 
//s20是代表20秒 
//h是指小时，如12小时则是：h12 
//d是天数，30天则：d30 
	function process_item(){
	//获取模态窗口传过来的数值
	var price = [];
	var assetid = [];
	var icon_url = [];
	var item_name = [];
	var gameid = 0;
	gameid = $("input[type='hidden']#gameid").val();	
	$("input#price").each(function(i){
    price[i] =  $(this).val();
    });
	$("input#icon_url").each(function(i){
    icon_url[i] = $(this).val();
    });
	$("input#assetid").each(function(i){
    assetid[i] = $(this).val();
    });
	$("input#item_name").each(function(i){
    item_name[i] = $(this).val();
    });
	modal_close("#submit_item");
	var template="<div class='am-alert am-alert' id='query_item_suc'>上架请求提交中...,请等待确认通知.</div>";
	$('#alert_show').html(template);
$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { price: price , assetid: assetid , icon_url: icon_url , item_name: item_name , gameid: gameid , process_item: true },
  beforeSend:function(){
    template="<div class='am-alert am-alert-success' id='query_item_suc'>上架请求已被提交,请等待确认通知.</div>";
	$('#alert_show').html(template);
	  },
  success:function(data){
	  console.log("process_item返回码:"+$("div#response",data).text());
	 if($("div#response",data).text()==1){
    template="<div class='am-alert am-alert-success' id='query_response'>饰品已被上架,请前往[正在上架]查看交易状态.</div>";
	update_show_market(gameid);
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_response'>饰品上架失败,请重新尝试.</div>";
update_show_market(gameid);
	 }
	 $('#alert_show').html(template);
	 setTimeout(function(){$("#query_response").alert('close');},3000);
	  }
});	
	}
	function submit_item(){
	var gameid=0;
	gameid = $("input[type='hidden']#gameid").val();	
	var checkbox=[];
    $("input[type='checkbox']:checked").each(function(i){
    checkbox[i] = $(this).val();
    });	
	var body="";
	body = "<table class=\"am-table am-table-striped am-table-hover\"><thead><tr><th>饰品</th><th>数量</th><th>价格</th></tr></thead><tbody>";
	for(i = 0,len=checkbox.length; i < len; i++) {
	var arr = explode(checkbox,"|",true);
	body = body+"<tr><td>"+arr[1]+"<input type='hidden' id='item_name' value='"+arr[1]+"'></td><td>1</td><td><div class=\"am-form am-input-group\"><span class=\"am-input-group-label\">￥</span><input id='price' type=\"number\" class=\"am-form-field\" value='100'></div><input id='assetid' type='hidden' value='"+arr[0]+"'><input id='icon_url' type='hidden' value='"+arr[2]+"'></td></tr>";
   }
    var status="";
   if(checkbox.length==0){
	   status="disabled";
	   body=body+"<tr><td>无被勾选的商品</td><td></td><td></td></tr>";
	   }
	body = body+"</tbody></table><input type='hidden' id='gameid' value='"+gameid+"'>";
	body = body+"<button type=\"button\" class=\"am-btn am-btn-danger\" style=\"position: fixed;bottom: 10px;left:250px\" onclick='process_item()' "+status+">确认上架</button>";
	modal_init("submit_item","上架商品",body);
	modal_open("#submit_item");

	}
	//发送取回物品请求
	function takeback_inventory(assetid){
	var checkbox=[];
    $("input[type='checkbox']:checked").each(function(i){
    checkbox[i] = $(this).val();
	$(this).after("<div id='inventory_show'><span class='label label-info'>处理中...</span></div>")
	$(this).remove();
    });	
	//console.log(checkbox);
	var gid="";
	gid=$("input#gameid").val();
	//console.log(gid);
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: {param:checkbox , gameid:gid , type:1 },
  success:function(data){
	// console.log(data);
	//  console.log($("div#response",data).text());
	  if($("div#response",data).text()!='0'){
	var order_market_id=$("div#response",data).text();//获取订单位于商城的id,用于检测请求的发起
	var data="<div class='am-alert am-alert-success' id='query_list_suc'>取回请求已被放入序列,请耐心等待机器人的发起的交易.</div>";
	$('#alert_show').html(data);
	$('#submit_inventory').remove();
	var Interval_id=setInterval(function () {check_offer(order_market_id,Interval_id)},5000);	//每5秒发起一次检测
	  }else{
	var data="<div class='am-alert am-alert-danger' id='query_list_fail'>取回请求发起失败/未勾选要提取的商品.</div>";
	$('#alert_show').html(data);	
   setTimeout(function (){$('#uery_list_fail').alert('close')},5000);
setTimeout(function(){location.reload();},3000)   
	  }
	  
	  }
});
	}
	/*
	@function 检查交易位于steam的状态
	@param int offer_steamid steam交易订单ID
	*/
	function check_offer_state(offer_steamid){
		//传入offer的steamid
		var url='index.php?mod=panel:inventory';
		var Interval_id=setInterval(function () {
		$.post(url,{check_offer_status:offer_steamid},function (data) {
		if($("div#response",data).text()=='2'){
		//已经发送	
		var template ="<div class='am-alert am-alert-secondary' data-am-alert>交易正在等待接受...</div>";	
		}else if($("div#response",data).text()=='3'){
		var template ="<div class='am-alert am-alert-success' id='trade_suc' data-am-alert>交易成功</div>";
		setcookie("order_context","","s1");
        clearInterval(Interval_id);	
		$("div#order_accept").remove();	
		setTimeout(function (){$('#trade_suc').alert('close');location.reload();},5000);	
		}else if($("div#response",data).text()=='6'){
		var template ="<div class='am-alert am-alert-danger' id='trade_fail' data-am-alert>交易已取消</div>";
		setcookie("order_context","","s1");
        clearInterval(Interval_id);
        $("div#order_accept").remove();		
		setTimeout(function (){$('#trade_fail').alert('close');location.reload();},3000);	
		}else if($("div#response",data).text()=='9'){
		var template ="<div class='am-alert am-alert-warning' data-am-alert>交易需要二次验证</div>";
		setcookie("order_context","","s1");
        clearInterval(Interval_id);			
		}else{
			var response = "";
			if($("div#response",data).text()==""){response="ERROR";}else{response=$("div#response",data).text();}
		var template ="<div class='am-alert am-alert-secondary' data-am-alert>交易状态未知,交易状态码:"+$("div#response",data).text()+"</div>";	
		clearInterval(Interval_id);
		}
		$('#status_show').html(template);
			})
		},10000);
		
	}
	function canceloffer(order_steamid,type){
		 if(!arguments[1]) type = "0";//默认是人工模式,1是无返回字段
		 var url = "index.php?mod=panel:inventory";
		$.post(url,{canceloffer:order_steamid},function (data) {
			if(type=='0'){
		if($("div#response",data).text()=='1'){
		var template ="<div class='am-alert am-alert-success' id='cancel_suc'>取消交易成功.</div>";	
		$('#alert_show').html(template);
		}else{
		var template ="<div class='am-alert am-alert-danger' id='cancel_fail'>取消交易失败,请重新尝试.&nbsp;&nbsp;&nbsp;<button type='button' class='am-btn am-btn-warning' onclick='canceloffer("+order_steamid+")' >删除订单</button></div>";	
		$('#alert_show').html(template);
		}
	}
	});
	}
//单纯分钟和秒倒计时
function resetTime(time,order_steamid){
  var timer=null;
  var t=time;
  var m=0;
  var s=0;
  m=Math.floor(t/60%60);
  m<10&&(m='0'+m);
  s=Math.floor(t%60);
  function countDown(){
   s--;
   s<10&&(s='0'+s);
   if(s.length>=3){
    s=59;
    m="0"+(Number(m)-1);
   }
   if(m.length>=3){
    m='00';
    s='00';
    clearInterval(timer);//归零则移除本身并cancel交易
	var url="index.php?mod=panel:inventory";
	$("div#order_accept").remove();
	canceloffer(order_steamid,'1');
	var template="<div class='am-alert am-alert-danger' id='query_list_fail'>交易过期,请重新发起请求.</div>";
	$('#alert_show').html(template);	
   setTimeout(function (){$('#uery_list_fail').alert('close')},10000);
   $("div#status_show").remove();
   location.reload();
   }
   $('#outtime').html("剩余时间:"+m+"分钟"+s+"秒");
  }
  timer=setInterval(countDown,1000);
}
	function check_offer(checkoffer_id,Interval_id,resettime){
		//编写状态检测[交易是否被机器人认领与发起]
		if(!arguments[1]) Interval_id = false;//默认是不关闭上一个定时器
		if(!arguments[2]) resettime = "301";//默认是5min
		 var url = "index.php?mod=panel:inventory";
                $.post(url,{checkoffer:checkoffer_id},function (data) {
				//	console.log(data);
				//	console.log($("div#response",data).text());
					if($("div#response",data).text()=='1'){
					var order_market_id = $("div#order_market_id",data).text();
					var order_steamid = $("div#order_steamid",data).text();
					var order_outtime = $("div#order_outtime",data).text();
					if(Interval_id!=false){clearInterval(Interval_id);setcookie("order_context",order_steamid+"|"+order_outtime,"h24");}//清除上一个计时器,设定本次交易的steamID|过期时间
					var template = "<div class='am-alert am-alert-warning' id='order_accept'><h3>订单已被发起</h3><p>机器人已经发送了订单,请在剩余时间倒数到0分0秒前接受本次交易</p><ul><li>Steam订单号:"+order_steamid+"</li><li>操作订单:<button type='button' class='am-btn am-btn-danger' onclick='canceloffer("+order_steamid+")' >取消订单</button></li><li id='outtime'></li></ul></div>";
					resetTime(resettime);
					$("div#query_list_suc").remove();
					$('#alert_show').html(template);
					check_offer_state(order_steamid);
					}
                   //console.log(result);//检测到非0后,就开始更新alert为订单号秘钥,让玩家确认,倒计时,并且发起gettradeoffer的请求,检测offer状态
                });
	}
	//提取饰品到商城背包
	function submit_inventory(){
   var checkbox=[];
   $("input[type='checkbox']:checked").each(function(i){
    checkbox[i] = $(this).val();
	$(this).after("<div id='inventory_show'><span class='label label-info'>处理中...</span></div>")
	$(this).remove();
    });	
	console.log(checkbox);
	var gid="";
	gid=$("input#gameid").val();
	console.log(gid);
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: {param:checkbox , gameid:gid},
  success:function(data){
	// console.log(data);
	//  console.log($("div#response",data).text());
	  if($("div#response",data).text()!='0'){
	var order_market_id=$("div#response",data).text();//获取订单位于商城的id,用于检测请求的发起
	var data="<div class='am-alert am-alert-success' id='query_list_suc'>请求已被放入序列,请耐心等待机器人的发起的交易.</div>";
	$('#alert_show').html(data);
	$('#submit_inventory').remove();
	var Interval_id=setInterval(function () {check_offer(order_market_id,Interval_id)},5000);	//每5秒发起一次检测
	  }else{
		  var warning_data="请求发起失败/未勾选要提取的商品.";
		  if($("div#alert",data).text()!=''){warning_data=$("div#alert",data).text();}
	var data="<div class='am-alert am-alert-danger' id='query_list_fail'>"+warning_data+"</div>";
	$('#alert_show').html(data);	
   setTimeout(function (){$('#query_list_fail').alert('close');},5000);	
   setTimeout(function (){location.reload();},1000);	
	  }
	  
	  }
});
	}
	function update_item_price(step){
	if(!arguments[0]) step = "1";
	if(step==1){
	var checkbox=[];
    $("input[type='checkbox']:checked").each(function(i){
    checkbox[i] = $(this).val();
    });	
	var body="";
	body = "<table class=\"am-table am-table-striped am-table-hover\"><thead><tr><th>饰品</th><th>数量</th><th>价格</th></tr></thead><tbody>";
	for(i = 0,len=checkbox.length; i < len; i++) {
	var arr = explode(checkbox,"|",false);
	body = body+"<tr><td>"+arr[1]+"<input type='hidden' id='item_name' value='"+arr[1]+"'></td><td>1</td><td><div class=\"am-form am-input-group\"><span class=\"am-input-group-label\">￥</span><input id='price' type=\"number\" class=\"am-form-field\"></div><input id='assetid' type='hidden' value='"+arr[0]+"'><input id='goods_market_id' type='hidden' value='"+arr[4]+"'></td></tr>";
   }
   var status="";
   if(checkbox.length==0){
	   status="disabled";
	   body=body+"<tr><td>无被勾选的商品</td><td></td><td></td></tr>";
	   }
	body = body+"</tbody></table>";
	body = body+"<button type=\"button\" class=\"am-btn am-btn-danger\" style=\"position: fixed;bottom: 10px;left:250px\" onclick='update_item_price(2)' "+status+">确认修改</button>";
	modal_init("update_item_price","修改商品价格",body);
	modal_open("#update_item_price");
	}else if(step==2){
	modal_close("#update_item_price");
	$("input#price").each(function(i){
    price[i] = $(this).val();
    });
	$("input#assetid").each(function(i){
    assetid[i] = $(this).val();
    });
	$("input#goods_market_id").each(function(i){
    goods_market_id[i] = $(this).val();
    });
	price=JSON.stringify(price);
	assetid=JSON.stringify(assetid);
	goods_market_id=JSON.stringify(goods_market_id);
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { price:price,assetid:assetid , goods_market_id:goods_market_id , update_item_price:true },
  beforeSend:function(){
    template="<div class='am-alert am-alert-success' id='query_item_suc'>修改价格请求已提交,请不要离开本页面等待之后的通知...</div>";
	$('#alert_show').html(template);
	  },
  success:function(data){ 
	if($("div#response",data).text()=='1'){
		var template ="<div class='am-alert am-alert-success' id='cancel_suc'>物品修改价格成功,现已生效,正在重载货架数据.</div>";	
		$('#alert_show').html(template);
		 setTimeout(function(){location.reload();},3000);
		}else{
		var template ="<div class='am-alert am-alert-danger' id='cancel_fail'>物品修改价格失败,请重新尝试</div>";	
		$('#alert_show').html(template);
		 setTimeout(function(){location.reload();},1000);
		}
	
	  }
  });			
	}else{
	console.log("未知操作");
	}
	}
	function submit_down_item(){
	var checkbox=[];
   $("input[type='checkbox']:checked").each(function(i){
    checkbox[i] = $(this).val();
    });	
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { param: checkbox , submit_down_item:true },
  beforeSend:function(){
    template="<div class='am-alert am-alert-success' id='query_item_suc'>下架请求已提交,请不要离开本页面等待之后的通知...</div>";
	$('#alert_show').html(template);
	  },
  success:function(data){ 
	if($("div#response",data).text()=='1'){
		var template ="<div class='am-alert am-alert-success' id='cancel_suc'>物品下架成功,现已存入您的商城库存中.正在重载货架数据...</div>";	
		$('#alert_show').html(template);
		get_market_inventory();
		}else{
		var template ="<div class='am-alert am-alert-danger' id='cancel_fail'>物品下架失败,请重新尝试</div>";	
		$('#alert_show').html(template);
		}
	
	  }
  });		
	//提交下架的物品,交予后端处理,先更新umarket_inventory的状态为6,然后把商品逐个导入到inventory_order,并设置状态为3
	}
	function get_market_inventory(){
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { get_market_inventory: true },
  beforeSend:function(){
    template="<div class='am-alert am-alert-success' id='query_item_suc'>已上架的饰品正在查询中...</div>";
	$('#alert_show').html(template);
	  },
  success:function(data){ 
	$('#alert_show').html("");
	if($("div#ajax",data).text()==''){
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'><span class='am-badge am-badge-warning am-radius am-text-lg'>该游戏暂无可下架到商城库存的饰品</span></div></section>";
	$('#market_inventory_show').html(template);
	$('#market_inventory_button_show').html("");
	}else{
	$('#market_inventory_show').html($("div#ajax",data).html());
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'><button type='button' class='am-btn am-btn-danger' onclick='submit_down_item()' >下架商品</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='am-btn am-btn-success' onclick='update_item_price()' >修改商品价格</button><br></div></section>";
	$('#market_inventory_button_show').html(template);	
	}
	  }
  });		
	}
	//格式化数据
	function form_data(data){
		var reg = new RegExp("<td2>","gm");//g,表示全部替换。
				data=data.replace(reg,"<td>");
				var reg = new RegExp("<tr1>","gm");//g,表示全部替换。
				data=data.replace(reg,"<tr>");
				var reg = new RegExp("</td3>","gm");//g,表示全部替换。
				data=data.replace(reg,"<td>");
				var reg = new RegExp("</tr4>","gm");//g,表示全部替换。
				data=data.replace(reg,"<tr>");
				return data;
	}
		//编辑用户数据
		function edit_account(mode,accountid,option_value){
			if(!arguments[0])mode = 0;
			if(!arguments[1]&&mode!=1){
			var data="<div class='am-alert am-alert-danger' id='query_alert'>按钮初始化失败,请刷新重试</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			return;			
			};
			var template;
			if(mode==0){
			$("#account_list_show").hide();
			$("#edit_account_show").show();
			template="<button class='am-btn am-btn-danger'type='button' onclick=\"if(confirm('重置密码有风险,请谨慎操作.请先与客户沟通!!!同意请确定，不同意请取消')){window.onclick=function(){edit_account(2,"+accountid+");}}\"><span class=\"am-icon-refresh\"></span> 重置密码</button>";
			$("#resetting_button").html(template);
			template="<button type='button' class='am-btn am-btn-warning' onclick='edit_account(3,"+accountid+")'>保存修改</button>";
			$("#email_save").html(template);
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  query_account: true , accountid:accountid },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据读取中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				$("input#account_username").val($("div#account_username",data).text());
				$("#account_email").val($("div#account_email",data).text());
				$("#account_steamid").val($("div#account_steamid",data).text());
				$("#account_tradelink").val($("div#account_tradelink",data).text());
				template="<input type='text' class='am-form-field' id='wallet_balance' disabled></span>";
				$("#resetting_wallet_button").html(template);
				$("#wallet_balance").val($("div#wallet_balance",data).text());
				$("#wallet_alipay_realname").val($("div#wallet_alipay_realname",data).text());
				$("#wallet_alipay_account").val($("div#wallet_alipay_account",data).text());
				template="<select data-am-selected onchange=\"edit_account(5,"+accountid+",this.options[this.selectedIndex].value)\"><option selected value=\"-1\">选择新的权限分组</option><option value=\"0\">普通用户</option><option value=\"1\">管理员</option><option value=\"2\">超级管理员</option></select>";
				$("#access_select").html(template);
				if($("div#account_status",data).text()!=2){
				template="<button class='am-btn am-btn-danger'type='button' id='setting_state_button' onclick=\"if(confirm('封禁用户有风险,请三思后再操作.请先与客户沟通!!!确认封禁请确定，不同意请取消')){window.onclick=function(){edit_account(4,"+accountid+");}}\"><span class=\"am-icon-refresh\"></span> 封禁用户</button>"
				$("#state_button").html(template);	
				}else{
				template="<button class='am-btn am-btn-success'type='button' id='setting_state_button' onclick=\"edit_account(4,"+accountid+");\"><span class=\"am-icon-refresh\"></span> 解除封禁</button>"
				$("#state_button").html(template);	
				}
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(1);},3000);	
				}
			}
		});	
			}else if(mode==1){
			$("#account_list_show").show();
			$("#edit_account_show").hide();	
			}else if(mode==2){
			//重置用户密码
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  resetting_password: true , accountid:accountid },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据保存中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>密码重置成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(0,accountid);},3000);	
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>密码重置失败,请重新尝试...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload();},3000);	
				}
			}
		});	
			}else if(mode==3){
			//保存修改后的邮箱
			var account_email=$("input#account_email").val();
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  update_email: true , accountid:accountid , account_email:account_email },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据保存中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>数据修改成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(0,accountid);},3000);	
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload();},3000);	
				}
			}
		});	
			}else if(mode==4){
			//封禁/解封用户	
			$.ajax({
				type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:account',
				data: {  setting_user_state: true , accountid:accountid },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据保存中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			$("#setting_state_button").attr("disabled",true);
			},
			success:function(data){
				$("#setting_state_button").attr("disabled",false);
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>用户状态修改成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload();},3000);	
				return;
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>用户状态修改失败,错误原因["+$("div#alert",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(0,accountid);location.reload();},3000);	
				}
			}
		});	
			}else if(mode==5){
			//调节用户权限
            $.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  update_user_access: true , accountid:accountid , access:option_value },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据保存中...</div>";
			$('#alert_show_edit').html(data);
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>权限状态修改成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload();},5000);
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>权限状态修改失败,错误原因["+$("div#alert",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(0,accountid);},3000);	
				}
			}
		});			
			}else if(mode==6){
			//清零用户钱包
            $.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  resetting_user_wallet: true , accountid:accountid},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 请求发起中...</div>";
			$('#alert_show_edit').html(data);
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>钱包已清零,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload();},3000);
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>清零操作失败,错误原因["+$("div#alert",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_account(0,accountid);},3000);	
				}
			}
		});				
			}
		}
		