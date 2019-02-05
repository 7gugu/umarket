<?php 
 global $sql;
 $connect=$sql->connect();
if(isset($_POST['update_log_list'])){
if($_POST['mode']==1){
	$context="select count(*) from umarket_order where `order_state`='1' and `order_request_steamid`='{$_COOKIE['steamid']}'";
	$array=$sql->fetch_array($sql->query($connect,$context));
	if($array[0]==0){
 echo "<div id='response'>0</div>";
 die;
	}
	//买家订单数据
	$context="select* from umarket_order where `order_state`='1' and `order_request_steamid`='{$_COOKIE['steamid']}'";//遍历已交易成功的的订单
	echo "<div id='response'>1</div>";
	echo "<div id='ajax'>";
//	$context="select * from umarket_order where `order_state`='1' and `order_request_steamid`='100'";//测试数据
	$response=$sql->query($connect,$context);
	while($array=$sql->fetch_array($response)){
	$order_id=$array['order_id'];
	if($array['payment_method']==1){
		$order_payment="Uwallet支付";
	}elseif($array['payment_method']==2){
		$order_payment="支付宝支付";
	}else{
		$order_payment="未知支付方式";
		}
	$order_context=unserialize($array['order_context']);
	$total_price=0;
	foreach($order_context as $oc){
	$img_path=$oc['img_path'];
	$item_name=$oc['item_name'];
	$item_count=$oc['item_count'];
	$item_price=$oc['item_price'];
	echo "<tr1><td2><span class=\"label label-danger\">{$order_id}</span></td3><td2><img style='height:50px;border:none;overflow:hidden;' src='{$img_path}' alt='...' class='am-img-thumbnail'>{$item_name}</td3><td2><span class=\"label label-info\">{$item_count}</span></td3><td2><span class=\"label label-success\"><span class=\"am-icon-rmb\"></span>{$item_price}</span></td3><td2><span class=\"label label-warning\">{$order_payment}</span></td3><td2><button class='am-btn am-btn-danger'type='button' onclick=\"if(confirm('“删除订单记录有风险,请谨慎操作。\r\n删除该记录后即意味着您放弃申诉与该条数据的访问权利\r\n删除数据无法恢复!!!”\r\n\r\n同意请确定，不同意请取消'))window.onclick=function(){delete_order(\"{$order_id}\");} \"><span class=\"am-icon-close am-icon-md\"></span> 删除</button></td3></tr4>";
	}
	}
		echo "</div>";
}elseif($_POST['mode']==2){
	//卖家订单数据

	$context="select count(*) from umarket_inventorey where `goods_state`='9' and `goods_seller_id`='{$_COOKIE['steamid']}'";
	$array=$sql->fetch_array($sql->query($connect,$context));
	if($array[0]==0){
 echo "<div id='response'>0</div>";
 die;
	}

	$context="select * from umarket_inventory where `goods_state`='9' and `goods_seller_id`='{$_COOKIE['steamid']}'";
	$response=$sql->query($connect,$context);
	echo "<div id='response'>1</div>";
	echo "<div id='ajax'>";
	while($array=$sql->fetch_array($response)){
	$inventory_id=$array['inventory_id'];
	$goods_img=$array['goods_img'];
	$goods_name=$array['goods_name'];
	$goods_count=$array['goods_count'];
	$goods_submit_time=$array['goods_submit_time'];
	$goods_price=$array['goods_price'];
	echo "<tr1><td2><span class=\"label label-danger\">{$inventory_id}</span></td3><td2><img style='height:50px;border:none;overflow:hidden;' src='{$goods_img}' alt='...' class='am-img-thumbnail'>{$goods_name}</td3><td2><span class=\"label label-info\">{$goods_count}</span></td3><td2><span class=\"label label-success\"><span class=\"am-icon-rmb\"></span>{$goods_price}</span></td3><td2><span class=\"label label-warning\">{$goods_submit_time}</span></td3></tr4>";
	}
		echo "</div>";
}else{
	echo "<div id='ajax'>Invalid Request </div>";
	die;
}
 die;
 }
 if(isset($_POST['delete_order'])){
	 $mode=$_POST['mode'];
	 $order_id=$_POST['order_id'];
	 $steamid=$_COOKIE['steamid'];
	$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_order where `order_request_steamid`='{$steamid}' and `order_id`='{$order_id}'"));
	if($res[0]!=0){
	$context = "update umarket_order set `order_state`='4' where `order_request_steamid`='{$steamid}' and `order_id`='{$order_id}' limit 1";
	$sql->query($connect,$context);
	echo "<div id='response'>1</div>";
	}else{
	echo "<div id='reesponse'>0</div>";	
	}
 }
 ?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span>我的订单</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class=""><a href="#tab1" onclick="update_seller_list()">已售商品</a></li>
                                <li class="am-active"><a href="#tab2" onclick="update_buyer_list()">已购商品</a></li>
                            </ul>

                            <div class="am-tabs-bd" style="touch-action: pan-y; -moz-user-select: none;">
                                <div class="am-tab-panel am-fade" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="height:100%;">
                                       <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
<table class="am-table am-table-hover">
   <thead>
        <tr>
            <th>订单编号</th><th>饰品名称</th><th>个数</th><th>价格</th><th>交易时间</th>
        </tr>
    </thead>
    <tbody id='seller_log_show'>
       <tr><td>0</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
    </tbody>
</table>						
						</div>
                    </div>       	 
                                        
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar">
									</div></div>
                                </div>
                                <div class="am-tab-panel am-fade am-active am-in" id="tab2">
                                    <div id="wrapperB" class="wrapper" >
                                       
                                         <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
<table class="am-table am-table-hover">
   <thead>
        <tr>
            <th>订单编号</th><th>饰品名称</th><th>个数</th><th>价格</th><th>交易方式</th><th>操作</th>
        </tr>
    </thead>
    <tbody id='buyer_log_show'>
       <tr><td>0</td><td>-</span></td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
    </tbody>
</table>						
						</div>
                    </div>   												
                                        
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar">
									
</div></div>
                                </div>

                            </div>
                        </div>

                    </div>
        </div>
		<script>
		window.onload=function(){update_buyer_list();}
		function delete_order(id){
		$.ajax({
  type: 'POST',
  async: true, 
  url: 'index.php?mod=panel:order',
  data: {  delete_order: true , order_id:id},
 beforeSend:function(){
	$('#delete_button').attr("disabled",true);
	  },
  success:function(){
 if($("div#response",data).text()==1){
    template="<div class='am-alert am-alert-success' id='query_response'>订单已被删除,正在重载数据</div>";
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_response'>订单删除失败,请重试,系统正在重载数据</div>";
	 }
	 $('#alert_show').html(template);
	 setTimeout(function (){update_buyer_list()},2000);
  }
});

		}
		function update_buyer_list(){
		$.ajax({
  type: 'POST',
  async: true, 
  url: 'index.php?mod=panel:order',
  data: {  update_log_list: true , mode:'1'},
 beforeSend:function(){
	//$('#log_show').html("");
	document.getElementById("wrapperB").style.height= "400px";
	$('#buyer_log_show').html("<tr><td>0</td><td>数据加载中<span class=\"am-icon-spinner am-icon-spin\"></span></td><td>-</td><td>-</td><td>-</td><td>-</td></tr>");
	  },
  success:function(data){
	if($("div#response",data).text()==0){
		$('#buyer_log_show').html("<tr><td>0</td><td>暂无订单</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>");
	}else{
	document.getElementById("wrapperB").style.height= "100%";
	var body = $("div#ajax",data).html();
	//不要修改下面这坨屎
	var reg = new RegExp("<td2>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("<tr1>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	var reg = new RegExp("</td3>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("</tr4>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	console.log(body);
	$('#buyer_log_show').html(body);
	  }
  }
});
		}
		function update_seller_list(){
		$.ajax({
  type: 'POST',
  async: true, 
  url: 'index.php?mod=panel:order',
  data: {  update_log_list: true , mode:'2'},
 beforeSend:function(){
	//$('#log_show').html("");
	document.getElementById("wrapperA").style.height= "400px";
	$('#seller_log_show').html("<tr><td>0</td><td>数据加载中<span class=\"am-icon-spinner am-icon-spin\"></span></td><td>-</td><td>-</td><td>-</td></tr>");
	  },
  success:function(data){
	if($("div#response",data).text()==0){
		$('#seller_log_show').html("<tr><td>0</td><td>暂无订单</td><td>-</td><td>-</td><td>-</td></tr>");
	}else{
	document.getElementById("wrapperA").style.height= "100%";
	var body = $("div#ajax",data).html();
	//不要修改下面这坨屎
	var reg = new RegExp("<td2>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("<tr1>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	var reg = new RegExp("</td3>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	var reg = new RegExp("</tr4>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	console.log(body);
	$('#seller_log_show').html(body);
	  }
  }
});
		}
		</script>
		