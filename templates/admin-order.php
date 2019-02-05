<?php 
global $sql;
$connect=$sql->connect();
if(isset($_POST['query_data'])){
$perNumber=5;
@$page=$_POST['page'];
$count=$sql->query($connect,"select count(*) from umarket_order");
$rs=$sql->fetch_array($count); 
$totalNumber=$rs[0];
$totalPage=ceil($totalNumber/$perNumber);
if (!isset($page)) {
 $page=1;
}
$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
$result=$sql->query($connect,"select * from umarket_order  limit $startCount,$perNumber");
//$c=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_order  limit $startCount,$perNumber")); 
echo "<div id='page'>".$page."</div>";
echo "<div id='totalnumber'>".$totalNumber."</div>";
echo "<div id='totalpage'>".$totalPage."</div>";
echo "<div id='order_log'>";
//if($c[0]==0){
//	echo "without-log";
//}else{
while($array=$sql->fetch_array($result)){
echo "<tr1><td2>{$array['order_id']}</td3>";
if($array['order_state']==0){
	$order_state="<span class=\"label label-info\">未支付</span>";
}elseif($array['order_state']==1){
	$order_state="<span class=\"label label-success\">支付成功</span>";//支付宝支付成功
}elseif($array['order_state']==2){
	$order_state="<span class=\"label label-success\">支付成功</span>";//微信支付成功
}elseif($array['order_state']==3){
	$order_state="<span class=\"label label-danger\">取消支付</span>";
}elseif($array['order_state']==4){
	$order_state="<span class=\"label label-danger\">支付失败</span>";
}elseif($array['order_state']==5){
	$order_state="<span class=\"label label-danger\">支付关闭</span>";
}else{
	$order_state="<span class=\"label label-danger\">支付状态未知</span>";
}
echo "<td2>{$order_state}</span></td3>";
echo "<td2><span class=\"label label-success\">{$array['order_request_steamid']}</span></td3>";
if($array['payment_method']==1){
	$status="<span class=\"label label-info\">Uwallet支付</span>";
}elseif($array['payment_method']==2){
	$status="<span class=\"label label-warning\">支付宝支付</span>";
}else{
	$status="<span class=\"label label-danger\">未知支付方式</span>";
}
echo "<td2>{$status}</td3>";
echo "<td2><button class='am-btn am-btn-default' type='button' onclick=\"edit_order(0,{$array['order_id']})\">查看详情</button></td3></tr4>";
}
//}
echo "</div>";
die;
}

if(isset($_POST['query_order_data'])){
	$orderid=$_POST['orderid'];
	$context="select * from umarket_order where `order_id`='{$orderid}'";
	$result=$sql->fetch_array($sql->query($connect,$context));
	if($result){
		echo "<div id='response'>1</div>";
		echo "<div id='order_time'>".$result['order_time']."</div>";
		echo "<div id='order_detail'>";
		$order_context=unserialize($result['order_context']);
		foreach($order_context as $oc){
			echo "<tr1>";
			echo "<td2><img style=\"width:90px;height:60px;border:none;overflow:hidden;\" src=\"".$oc['img_path']."\" class=\"am-img-thumbnail\"><td3>";
			echo "<td2>".$oc['item_name']."<td3>";
			echo "<td2>".$oc['item_count']."<td3>";
			echo "<td2>￥".$oc['item_price']."<td3>";
			$res=$sql->fetch_array($sql->query($connect,"select * from umarket_inventory where `order_market_id`='{$result['order_id']}'"));
			if($res['goods_seller_id']!=""){$goods_seller_id=$res['goods_seller_id'];}else{$goods_seller_id="无";}
			if($res['goods_buyer_id']!=""){$goods_buyer_id=$res['goods_buyer_id'];}else{$goods_buyer_id="无";}
			echo "<td2>".$goods_seller_id."<td3>";
			echo "<td2>".$goods_buyer_id."<td3>";
			echo "<td2>".$oc['appid']."<td3>";
			echo "<tr4>";
		}
		echo "</div>";
	}else{
		echo "<div id='response'>0</div>";
	}
	die;
}

if(isset($_POST['close_order'])){
	$orderid=$_POST['orderid'];
	if($orderid==""){echo "<div id='response'>0</div><div id='ajax'>传值错误</div>";die;}
	$context="update umarket_order set `order_state`='5' where `order_id`='{$orderid}'";//交易关闭
	$res=$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
	}

if(isset($_POST['delete_order'])){
	$orderid=$_POST['orderid'];
	if($orderid==""){echo "<div id='response'>0</div><div id='ajax'>传值错误</div>";die;}
	$context="DELETE FROM `umarket_order` WHERE `order_id` = {$orderid} LIMIT 1;";//删除订单
	$res=$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
}

?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span>管理订单</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class="am-active"><a href="#tab2" onclick="">订单管理</a></li>
                            </ul>
                                </div>
                                <div class="am-tab-panel am-fade am-active am-in" id="tab2">
                                    <div id="wrapperB" class="wrapper" style="height:100%;"> 
									   <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12" id='order_list_show'>
						<?php loadalert(); ?>
						<div id='alert_show_list'></div>
<table class="am-table am-table-hover">
   <thead> 
        <tr>
            <th>订单编号</th><th>订单状态</th><th>发起人SteamID</th><th>支付方式</th><th>操作</th>
        </tr>
    </thead> 
    <tbody id='order_log_show'>
       <tr><td>0</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
    </tbody>
</table>						
						
						<div class="am-cf" id='am-cf' >
	 <div id='totalnumber'></div>
                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination" id='am-pagination'>
                                        </ul>
                             </div>
                                </div>
								</div>
								
	<div id="edit_order_show" class="am-u-sm-12 am-form" style="display:none;">
<div id="alert_show_edit"></div>
<strong><h2>订单详情<div id='order_time'></div></h2></strong>
<hr>
<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
           <th>商品</th>
            <th>商品名字</th>
           <th>数量</th>
		   <th>单价</th>
		   <th>卖家ID</th>
		   <th>买家ID</th>
		   <th>游戏ID</th>
        </tr>
    </thead>
    <tbody id='order_detail_show'>
	<tr>
	<td>-</td>
	<td>0</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	</tr>
    </tbody>
  </table>
  <hr>
  <div class="am-u-sm-9 am-form">
  <section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title">订单操作</h3>
  </header>
  <div class="am-panel-bd" id="order_option">

  </div>
</section>
  </div> 
<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>用户拥有知情权,请在更改此数据前与用户先取得联系,获取书面授权再修改</p>
 </div>
</section>
						</div> 
<br>
<br>
<br>
<br>
<button type="button" class="am-btn am-btn-default" onclick="edit_order(1);">返回列表</button>
</div>   
								
								
								
								
								
								
								
								
								
								<div>
								
								</div>
								
								
								
								
								
                                </div>
								
		<script>
		function edit_order(mode,orderid){
			if(!arguments[0])mode = 0;
			if(!arguments[1]&&mode!=1){
			var data="<div class='am-alert am-alert-danger' id='query_alert'>按钮初始化失败,请刷新重试</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			return;			
			};
			var template,order_time;
			if(mode==0){
			$("#order_list_show").hide();
			$("#edit_order_show").show();
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:order',
				data: {  query_order_data: true , orderid:orderid },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据读取中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var body = $("div#order_detail",data).html();
				console.log(body);
	//不要修改下面这坨屎
	var reg = new RegExp("<td2>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("<tr1>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	var reg = new RegExp("<td3>","gm");//g,表示全部替换。
	body=body.replace(reg,"</td>");
	var reg = new RegExp("<tr4>","gm");//g,表示全部替换。
	body=body.replace(reg,"</tr>");
	console.log(body);
	$('tbody#order_detail_show').html(body);
	console.log($("div#order_time",data).text());
	if($("div#order_time",data).text()==""){
	order_time="缺省订单时间";
	}else{
	order_time=$("div#order_time",data).text();
	}
	$("#order_time").text("["+order_time+"]");
	var data = "<button type=\"button\" class=\"am-btn am-btn-danger\" onclick=\"if(confirm('“删除订单记录有风险,请谨慎操作。删除该记录后即意味着您只能通过数据恢复技术才可以获取该记录!!!”同意请确定，不同意请取消'))window.onclick=function(){edit_order(2,"+orderid+");}\">删除订单</button>	&nbsp; <button type=\"button\" class=\"am-btn am-btn-secondary\" onclick=\"edit_order(3,"+orderid+");\">关闭订单</button>";
	$('#order_option').html(data);
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){edit_order(1);},3000);	
				}
			}
		});	
			}else if(mode==1){
			$("#order_list_show").show();
			$("#edit_order_show").hide();
			$("tbody#order_detail_show").html("");
			}else if(mode==2){
			//删除订单
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:order',
				data: {  delete_order: true , orderid:orderid},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>订单删除成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){$('#query_alert').alert('close');},3000);		
				setTimeout(function (){location.reload();},3000);		
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>订单删除失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){$('#query_alert').alert('close');},3000);		
				setTimeout(function (){location.reload();},3000);		
				}
		}
		});
			}else if(mode==3){
			//关闭订单
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:order',
				data: {  close_order: true , orderid:orderid},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>订单关闭成功,页面正在重载中...</div>";
				$('#alert_show_insert').html(data);
				setTimeout(function (){$('#query_alert').alert('close');location.reload();},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>订单关闭失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_insert').html(data);
				setTimeout(function (){$('#query_alert').alert('close');location.reload();},3000);	
				}
		}
		});
			}
		}
		function update_order_log(page){
			if(!arguments[0]) page = 1;
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:order',
				data: {  query_data: true , page:page},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#order_log",data).text()=="without-log"){
		$('#order_log_show').html("<tr><td>0</td><td>暂无订单</td><td>-</td><td>-</td><td>-</td></tr>");
	}else{
	var template="";
	var i;
	var body = $("div#order_log",data).html();
	//不要修改下面这坨屎
	var reg = new RegExp("<td2>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("<tr1>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	var reg = new RegExp("</td3>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("</tr4>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	//console.log(body);
	$('#order_log_show').html(body);
	$('#totalnumber').text("共"+$("div#totalnumber",data).text()+"条记录");
	var page = parseInt($("div#page",data).text());
	var totalpage = parseInt($("div#totalpage",data).html());
	if(totalpage<5){
	for (i=1; i <= totalpage; i++){

    if (page == i) {
        template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_order_log("+i+")'>"+i+"</a></li>";
    }else{
        template =  template+"<li><a href='javascript:void(0);' onclick='update_order_log("+i+")' >"+i+"</a></li>";
		}
	}		
	}else{
	for (i=1; i <= page+5; i++){
    if (page == i) {
         template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_order_log("+i+")'>"+i+"</a></li>";
    }else{
         template = template+"<li><a href='javascript:void(0);' onclick='update_order_log("+i+")'>"+i+"</a></li>";
    }
		}
	}
	
	$('#am-pagination').html(template);
	}
		}
		});
		}
		window.onload=function(){update_order_log();}
		</script>
                    </div>   
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
                                </div>

                            </div>
                        </div>

                    </div>
        </div>