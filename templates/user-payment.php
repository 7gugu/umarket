<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-shopping-basket"></span> 支付系统
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-12">
						<button type='button'  onclick="javascript:window.location.href='index.php?mod=commoditylist'" class='am-btn'>继续购物</button>
                     <hr><br>
						<?php 
						global $mode;
						global $sql;
						global $alipay;                      
						$parse=array();
						loadalert(); 
						$connect=$sql->connect();
						$action = isset($_GET['action']) ? $_GET['action'] : '';
                        if($action=='queryorder'){
                        $outTradeNo = $_GET['outTradeNo'];
                        $result = $alipay->orderquery($outTradeNo);
						if($result['code']==0){
						$context="update umarket_order set `order_state`='1' where `order_id`='{$outTradeNo}'";
						$res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);						
						}
                        echo json_encode($result);die;
                        } 
						if(isset($_POST['balance'])){
$balance="0.00";
$res=$sql->fetch_array($sql->query($connect,"select * from umarket_wallet where `wallet_steamid`='{$_COOKIE['steamid']}'"));
if($res){$balance=$res['wallet_balance'];}
echo "<div id='ajax'>".$balance."</div>";
die;
}
						if(isset($_GET['cancel_order'])&&$_GET['cancel_order']!=""){
							$orderid=$_GET['cancel_order'];
							$context="update umarket_order set `order_state`='3' where `order_id`='{$orderid}'";
						    $res=$sql->query($connect,$context);
							setcookie("warning","订单号:{$orderid}已被取消",time()+5);
							redirect("index.php?mod=user:shoppingcart");
						}
						if(isset($_POST['order_id'])&&isset($_POST['pay_bill'])){
							$order_id=$_POST['order_id'];
							$context="select * from `umarket_order` where `order_id`='{$order_id}'";
							$res=$sql->fetch_array($sql->query($connect,$context));
							$bill_value=0;
							if($res){
							$order_context=unserialize($res['order_context']);
							foreach($order_context as $oc){
							$bill_value=$bill_value+$oc['item_price']*$oc['item_count'];
							}
							$res=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='uwallet_service_charge'"));
						$bill_value=$bill_value+(1+$res[1]);
							$context="select * from `umarket_wallet` where `wallet_steamid`='{$_COOKIE['steamid']}'";
							$res=$sql->fetch_array($sql->query($connect,$context));
							$wallet_balance=$res['wallet_balance'];
							$final_balance=$wallet_balance-$bill_value;
							if($final_balance>=0){
								
								$sql->query($connect,"update umarket_order set `order_state`='1' where `order_id`='{$order_id}'");//更新订单状态
								$sql->query($connect,"update umarket_wallet set `wallet_balance`='{$final_balance}' where `wallet_steamid`='{$_COOKIE['steamid']}'");//更新钱包状态
								echo "<div id='response'>1</div><div id='ajax'>订单支付成功</div>";
								die;
							}else{
								echo "<div id='response'>0</div><div id='ajax'>余额不足,请充值后再支付</div>";
								die;
							}
							}else{
								echo "<div id='response'>0</div><div id='ajax'>订单已支付/订单不存在/无法查询到该订单</div>";
							}
						}
						if(isset($_GET['orderid'])){
						//发起交易
						$orderid=$_GET['orderid'];
						$context="select * from umarket_order where `order_id`='{$orderid}' and `order_state`='0'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$bill_value=0;
						$order_context=unserialize($res['order_context']);
						foreach($order_context as $oc){
							$bill_value=$bill_value+$oc['item_price']*$oc['item_count'];
						}

						$res1=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='uwallet_service_charge'"));
						$bill_value=$bill_value+(1+$res1[1]);
						$order_id=$res['order_id'];
						if($res['payment_method']=='1'){
						//Uwallet支付
						?>
							<div class="am-u-sm-12 am-u-md-8 am-u-sm-centered">
	<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <span >订单号:<?php echo $res['order_id']; ?></span>
  </header>
  <div class="am-panel-bd">
  <?php
  $balance="0.00";
$res=$sql->fetch_array($sql->query($connect,"select * from umarket_wallet where `wallet_steamid`='{$_COOKIE['steamid']}'"));
if($res){$balance=$res['wallet_balance'];}
?>
	<strong><p style="white-space:nowrap;"><font class='am-text-xxxl'>钱包余额:</font><font  id='balance' class='am-text-xxl'>￥<?php echo $balance; ?></font></p></strong>
	<br>
	<div id='bill_value'>
	<strong><p style="white-space:nowrap;"><font class='am-text-xxxl'>订单金额:</font><font  class='am-text-xxl'>￥<?php echo $bill_value;?></font></p></strong>
	</div>
	<hr>
	<div id='alert_show'></div>
	<button type='button' id='pay_button' onclick="pay_bill()" class='am-btn am-btn-success'>支付订单</button>	
  </div>
  <script>
  function pay_bill(){
	  //编写js
	  $.ajax({
  type: 'POST',
  async: false, 
  url: 'index.php?mod=user:payment',
  data: {  pay_bill: true , order_id : <?php echo $order_id; ?>},
	   beforeSend:function(){
	$("#pay_button").attr({"disabled":"disabled"});
	$("#pay_button").html("<i class='am-icon-spinner am-icon-spin'></i>订单支付中...");
	  },
  success:function(data){ 
   if($("div#response",data).text()==1){
	update_balance();
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'>扣款成功,正在跳转....</div></section>";
	$("#alert_show").html(template);
	setTimeout(function (){window.location.href = 'index.php?mod=user:payment&order_result=<?php echo $order_id; ?>';},3000);
	}else{
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'>扣款失败,正在跳转....</div></section><br><section class='am-panel am-panel-default'><div class='am-panel-bd'>失败原因:"+$("div#ajax",data).text()+"</div></section>";
	$("#alert_show").html(template);
	setTimeout(function (){window.location.href = 'index.php?mod=user:payment&order_result=<?php echo $order_id; ?>';},4000);	
	}
  }
});
  }
 function update_balance(){
		//初始化金额
		$.ajax({
  type: 'POST',
  async: false, 
  url: 'index.php?mod=user:payment',
  data: {  balance: true },
	   beforeSend:function(){
	$('#balance').html("￥0.00");
	var data="<div class='am-alert am-alert-warning' id='query_result'><i class='am-icon-spinner am-icon-spin'></i> 余额数据加载中...<br></div>";
	$('#alert_show').html(data);	
    setTimeout(function (){$('#query_result').alert('close')},2000);
	  },
  success:function(data){
   $('#balance').html("￥"+$("div#ajax",data).text());
  }
});

 }
window.onload=function(){update_balance();}
  </script>
</section>
</div>
						<?php
						}elseif($res['payment_method']=='2'){
						//支付宝支付
						if($res['orderstate']==1){
						setcookie("warning","交易已完成",time()+5);
						redirect("index.php?mod=panel:index");	
						}
						$price=0;
						$array_context=unserialize($res['context']);
						foreach($array_context as $i){
						$price=$price+$i['item_price']*$i['item_count'];
						}
						$res=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='alipay_service_charge'"));
						$price=$price+(1+$res[1]);				
				$outTradeNo = $orderid;     //你自己的商品订单号，不能重复
				$payAmount = $price;          //付款金额，单位:元
				$orderName = SYSTEM_SNAME.'|交易单号:'.$orderid;    //订单标题
				$alipay->setAppid(APPID);
				//$alipay->setNotifyUrl(NOTIFYURL);//目测不用
				$alipay->setRsaPrivateKey(RSAPRIVATEKEY);
				$alipay->setTotalFee($payAmount);
				$alipay->setOutTradeNo($outTradeNo);
				$alipay->setOrderName($orderName);
				$result = $alipay->doPay(0);
				$result = $result['alipay_trade_precreate_response'];
				if($result['code'] && $result['code']=='10000'){
				//生成二维码
				$url = 'https://www.kuaizhan.com/common/encode-png?large=true&data='.$result['qr_code'];
				}else{
				echo $result['msg'].' : '.$result['sub_msg'];exit();
				}					
 //$url = 'https://www.kuaizhan.com/common/encode-png?large=true&data=https://www.7gugu.com';
						?>
          
		<div class="am-u-sm-12 am-u-md-8 am-u-sm-centered">
	<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
     <span >订单号:<?php echo $res['order_id']; ?></span>
                <span style="float:right;">总金额: <?php echo $price; ?>元</span>
  </header>
  <div class="am-panel-bd">
   <div style="text-align:center;"><img src="<?php echo $url; ?>" style="" /></div>
            <div style="text-align:center;">
			请使用<span class="label label-success">支付宝</span>扫描 二维码以完成支付
			</div>
<hr>
			<div class="am-progress am-progress-striped am-progress am-active " style="display: none;">
  <div class="am-progress-bar am-progress-bar-secondary"  style="width:1%"></div>
</div>
  </div>
</section>
</div>

   
    <script>
        $(function () {
            $(".am-progress").show();
            var i = 0;
            setInterval(function () {
                i++;
                $(".am-progress .am-progress-bar").css("width", (i * 10) + "%");
                if (i >= 10) {
                    i = 1;
                }
            }, 2000);

            setInterval(function () {
                var url = "index.php?mod=user:payment&action=queryorder&outTradeNo=<?php echo $orderid; ?>";
                $.getJSON(url, function (result) {
                    if (result.code == 0) {
                        window.location.href = 'index.php?mod=user:payment&order_result=<?php echo $orderid; ?>';
                    }
                });
            }, 3000);

        });
    </script>
	<?php 
	}
						}else{
						setcookie("fail","订单错误,请速于管理员联系/再尝试",time()+5);
						redirect("index.php?mod=user:shoppingcart");	
						}
                        						
						}elseif(isset($_GET['order_result'])){
						//说明订单支付成功
                  ?>
				  		<div class="am-u-sm-12 am-u-md-8 am-u-sm-centered">
	<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
     订单状态
  </header>
  <div class="am-panel-bd">
  <?php                 
	                    $res=$sql->fetch_array($sql->query($connect,"select * from umarket_order where `order_id`='{$_GET['order_result']}'"));
						if($res['order_state']==1){
						$_SESSION['cart']=array();
						$goods_array=unserialize($res['order_context']);
						foreach($goods_array as $i){
						$item_id=$i['goods_id'];
						$appid=$i['appid'];
						$order_icon_url=$i['img_path'];
						$order_item_name=$i['item_name'];
						
						$res=$sql->fetch_array($sql->query($connect,"select * from umarket_inventory where `goods_steam_id`='{$item_id}' "));
						//var_dump($i);
						$order_context="";
						if($res){
							$bot_accountid=$res['bot_accountid'];
							$order_market_id=$res['order_market_id'];
							$res=$sql->fetch_array($sql->query($connect,"select * from umarket_inventory_order where `order_market_id`='{$order_market_id}' "));
							$order_context=$res['order_context'];
							}else{
								$order_market_id=$bot_accountid=0;
								}
						$res=$sql->fetch_array($sql->query($connect,"select * from umarket_inventory_order order by order_market_id DESC limit 1 "));
						$order_market_id=0;if($res){$order_market_id=$res[0];}$order_market_id++;//计算位于库存订单中的ID
						$order_time=date("Y-m-d H:i:s");
						//导入买家的库存
						$context="insert into umarket_inventory_order(order_market_id,order_stat,order_context,order_steamid,order_outtime,order_icon_url,order_item_name,player_steam_id,bot_accountid,order_game_id,order_time)values('{$order_market_id}','3','{$order_context}','0','0','{$order_icon_url}','{$order_item_name}','{$_COOKIE['steamid']}','{$bot_accountid}','{$appid}','{$order_time}')";
						$sql->query($connect,$context);
						//更新订单记录的状态[饰品已卖出,并已被转让]
						$goods_submit_time=date("H:I:S");
						$sql->query($connect,"update umarket_inventory set `goods_state`='9',`goods_buyer_id`='{$_COOKIE['steamid']}',`goods_submit_time`='{$goods_submit_time}' where `order_market_id`='{$order_market_id}'");
						}
     ?>
	 <h4>支付成功</h4>
     <button type='button'  onclick="javascript:window.location.href='index.php?mod=panel:inventory'" class='am-btn am-btn-success'>前往库存</button>
						<?php 
						}else{ 
						$context="update umarket_order set `order_state`='4' where `order_id`='{$_GET['order_result']}'";//交易失败
						$res=$sql->query($connect,$context);
						?>
   <h4>支付失败</h4>
     <button type='button'  onclick="javascript:window.location.href='index.php?mod=user:payment&orderid=<?php echo $_GET['order_result']; ?>'" class='am-btn am-btn-warning'>返回重新支付</button>&nbsp;&nbsp;&nbsp;
	 <button type='button'  onclick="javascript:window.location.href='index.php?mod=user:payment&cancel_order=<?php echo $_GET['order_result']; ?>'" class='am-btn am-btn-danger'>取消订单</button>
		
						
						<?php } ?>
  </div>
</section>
</div>

                  <?php						
						}else{
						setcookie("fail","拒绝访问",time()+5);
						redirect("index.php?mod");
						}	
	?>

                        </div>
                    </div>
                </div>

            </div>
        </div>