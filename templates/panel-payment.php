<?php 
global $sql;
$connect=$sql->connect();
if(isset($_POST['balance'])){
$balance="0.00";
$res=$sql->fetch_array($sql->query($connect,"select * from umarket_wallet where `wallet_steamid`='{$_COOKIE['steamid']}'"));
if($res){$balance=$res['wallet_balance'];}
echo "<div id='ajax'>".$balance."</div>";
die;
}
if(isset($_POST['recharge'])){
//充值逻辑
$key_name=$_POST['key_name'];
$key_password=$_POST['key_password'];
if($key_name==""||$key_password==""){
	echo "<div id='response'>0</div><div id='ajax'>缺失充值码/充值密码,请再次填写/寻求管理员帮助</div>";
}
$context="select * from umarket_wallet_key where `key_name`='{$key_name}' and `key_password`={$key_password} and `key_state`='0'";	
$res=$sql->fetch_array($sql->query($connect,$context));
if($res){
//存在即读取金额并充值
$key_balance=$res['key_balance'];
$result=$sql->fetch_array($sql->query($connect,"select * from `umarket_wallet` where `wallet_steamid`='{$_COOKIE['steamid']}'"));
if($result){
	$wallet_balance=$result['wallet_balance'];
	$wallet_balance=$wallet_balance+$key_balance;
	$sql->query($connect,"update `umarket_wallet` set `wallet_balance`='{$wallet_balance}'  where `wallet_steamid`='{$_COOKIE['steamid']}'");//update
	//更新激活码状态0可用1不可用
	$sql->query($connect,"update `umarket_wallet_key` set `key_state`='1'  where `key_name`='{$key_name}'");
	$res=$sql->affected_rows($connect);
	if($res>0){
		echo "<div id='response'>1</div><div id='ajax'>激活成功,金额已充值进您的Uwallet钱包中,系统正在重载余额数据</div>";
	}else{
		echo "<div id='response'>0</div><div id='ajax'>激活失败,请联系站点管理员寻找解决办法</div>";
	}
	die;
	
}else{echo "<div id='response'>0</div><div id='ajax'>数据库指令出错,请重试或联系网站管理员</div>";}
}else{
//回传不存在的信息
echo "<div id='response'>0</div><div id='ajax'>充值码或充值密码不存在/输入错误</div>";
}
die;
}
if(isset($_POST['query_data'])){
	if(isset($_POST['query_wallet_alipay'])){
		$res=$sql->fetch_array($sql->query($connect,"select * from umarket_wallet where `wallet_steamid`='{$_COOKIE['steamid']}'"));
		if($res){
			echo "<div id='response'>1</div>";
		echo "<div id='alipay_account'>".$res['wallet_alipay_account']."</div>";
		echo "<div id='alipay_realname'>".$res['wallet_alipay_realname']."</div>";
		echo "<div id='wallet_balance_alipay'><font class='am-text-xxl'><span class='am-icon-rmb'></span>".$res['wallet_balance_alipay']."</font></div>";
		die;
		}
		die;
	}
	echo "<div id='response'>0</div>";
	die;
}
if(isset($_POST['update_wallet_alipay'])){
	$alipay_account=htmlspecialchars($_POST['alipay_account']);
	$alipay_realname=htmlspecialchars($_POST['alipay_realname']);
	$sql->query($connect,"update umarket_wallet set `wallet_alipay_account`='{$alipay_account}' where `wallet_steamid`='{$wallet_steamid}'");
	$sql->query($connect,"update umarket_wallet set `wallet_alipay_realname`='{$alipay_realname}' where `wallet_steamid`='{$_COOKIE['steamid']}'");
	if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
}
if(isset($_POST['extract_alipay'])){
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_wallet where `wallet_steamid`='{$_COOKIE['steamid']}'"));
	$extract_number=htmlspecialchars($_POST['extract_number']);
	preg_match_all('/\d+/',$extract_number,$extract_number);
	$extract_number = join('',$extract_number[0]);
	if($res){
		if($res['wallet_balance_alipay']<$extract_number){echo "<div id='response'>0</div><div id='ajax'>提取金额超出可提取金额</div>";die;}
		if($extract_number<=0||$extract_nuumber==0){echo "<div id='response'>0</div><div id='ajax'>提取金额不可为空或小于等于0</div>";die;}
		//转账操作
		//新建转账单号
		//引入秘钥信息
		//生成请求,发送请求
		//等待回调
$context = "select * from umarket_order order by orderid DESC limit 1 ";
$order_res=$sql->fetch_array($sql->query($connect,$context));
$orderid=0;if($res!=false){$orderid=$order_res[0];}$orderid++;
$order_time=date("Y-m-d h:i:sa");
$context="insert into umarket_order(order_id,order_state,payment_method,order_context,order_request_steamid,order_email,order_phone,order_time)values('{$orderid}','5','2','{$extract_nuumber}','{$_COOKIE['steamid']},'{$COOKIE['email']}','{$COOKIE['phone']}','{$order_time}')";
$sql->query($connect,$context);
if($sql->affected_rows($connect)){
$appid = APPID;  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
$outTradeNo = uniqid();     //商户转账唯一订单号
$payAmount = $extract_number;       //转账金额，单位：元 （金额必须大于等于0.1元)
$remark = SYSTEM_SNAME.'|体现操作|'.$res['wallet_steamid'];    //转帐备注
$signType = SIGNTYPE;       //签名算法类型，支持RSA2和RSA，推荐使用RSA2
//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
$saPrivateKey=RSAPRIVATEKEY;
$account = $res['wallet_alipay_account'];      //收款方账户（支付宝登录号，支持邮箱和手机号格式。）
$realName = $res['wallet_alipay_realname'];     //收款方真实姓名
$aliPay = new AlipayService($appid,$saPrivateKey);
$alipay->setTotalFee($payAmount);
$alipay->setOutTradeNo($outTradeNo);
$alipay->setAccount($account);
$alipay->setRealName($account);
$alipay->setRemark($account);
$result = $aliPay->doPay(1);
$result = $result['alipay_fund_trans_toaccount_transfer_response'];
if($result['code'] && $result['code']=='10000'){
    echo '<div id=\'response\'>1</div>';
	$context="update umarket_order set `order_state`='6' where `order_id`='{$orderid}";
}else{
    echo "<div id='response'>0</div><div id='ajax'>".$result['msg'].' : '.$result['sub_msg']."</div>";
	$context="update umarket_option set `order_state`='7' where `order_id`='{$order_id}";
}
$sql->query($connect,$context);
}else{
	echo "<div id='response'>0</div><div id='ajax'>订单创建失败 </div>";
	$context="";
}
	}else{
		echo "<div id='response'>0</div><div id='ajax'>SQL查询失败</div>";
	}
	die;
}
?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span>我的钱包</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class=""><a href="#tab1" onclick="query_wallet_alipay()">支付宝</a></li>
                                <li class="am-active"><a href="#tab2" onclick="">Uwallet钱包</a></li>
                            </ul>

                            <div class="am-tabs-bd" >
                                <div class="am-tab-panel am-fade" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="height:100%;">
									      <div class="am-u-sm-12 am-u-md-9">
	<strong><p><font class='am-text-xxxl'>支付宝信息</font></p></strong>
	<div id='alert_show'></div>
						<div class="am-form">
     <strong>支付宝实名名称: </strong><input type="text" id='alipay_realname' class="am-form-field"   />
	 <br>
	<strong>支付宝账户[邮箱]: </strong><input type="text" id='alipay_account' class="am-form-field"  />
	<br>
	<button type='button'   class='am-btn am-btn-danger' onclick='update_wallet_alipay()'>保存</button></li>

  
						<hr>
						<strong><p><font class='am-text-xxl'>提取现金</font></strong><br>
						<strong><p><font class='am-text-xxl'>可取金额:</font><div id='alipay_balance'>0.00</div></strong><br>
						
						<div class="am-form">
     <strong>提取金额: </strong><input type="number" name='extract_number_alipay' class="am-form-field"  placeholder="提取金额" >
	 <br>
	<button type='button'  id='extract_alipay_button' class='am-btn am-btn-warning' onclick='extract_alipay()'>体现</button></li>

  </div>
  </div>
						
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>这里是支付宝</p>
 </div>
</section>
						</div> 
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
									<script>
		function extract_alipay(){
		var extract_number_alipay='';
		extract_number_alipay=$("input#extract_number_alipay").val();
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=panel:payment',
				data: {  extract_alipay: true , extract_number:extract_number},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>体现请求发送成功,请稍后留意支付宝消息,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>体现请求发送失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
		}	
		function update_wallet_alipay(){
		var alipay_account=alipay_realname='';
		alipay_account=$("input#alipay_account").val();
		alipay_realname=$("input#alipay_realname").val();
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=panel:payment',
				data: {  update_wallet_alipay: true , alipay_account:alipay_account,alipay_realname:alipay_realname},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
				function query_wallet_alipay(){
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=panel:payment',
				data: {  query_data: true , query_wallet_alipay:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#alipay_realname").val($("div#alipay_realname",data).text());
				$("input#alipay_account").val($("div#alipay_account",data).text());
				$("div#alipay_balance").html($("div#wallet_balance_alipay",data).html());
				console.info($("div#wallet_balance_alipay",data).html());
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	
									</script>
                                </div>
                                <div class="am-tab-panel am-fade am-active am-in" id="tab2">
                                    <div id="wrapperB" class="wrapper" style="height:100%;">
										    <div class="am-u-sm-12 am-u-md-9">
											
											<strong><p style="display:inline;"><font class='am-text-xxxl'>钱包余额:</font><div id='balance' class='am-text-xxl'></div></p></strong>
						<hr>
						<strong><p><font class='am-text-xxl'>充值钱包</font></strong><br>
						<div id='alert_show'></div>
						<div class="am-form">
     <strong>充值卡号: </strong><input type="text" name='key_name' id='key_name' class="am-form-field"  placeholder="我的激活码" >
	 <br>
	<strong>充值密码: </strong><input type="text" name='key_paw' id='key_password' class="am-form-field"  placeholder="我的激活密码" >
	<br>
	<button type='button'  id='recharge_button' class='am-btn am-btn-danger' onclick='recharge_money()'>充值</button></li>
<script>
function recharge_money(){
	var key_name=$("#key_name").val();
	var key_password=$("#key_password").val();
	$.ajax({
  type: 'POST',
  async: true, 
  url: 'index.php?mod=panel:payment',
  data: {  recharge: true , key_name:key_name , key_password:key_password},
	   beforeSend:function(){
	$("#recharge_button").attr({"disabled":"disabled"});
	$("#key_name").attr({"disabled":"disabled"});
	$("#key_password").attr({"disabled":"disabled"});
	$("#recharge_button").html("<i class='am-icon-spinner am-icon-spin'></i>充值请求提交中...");
	  },
  success:function(data){
	  $("#recharge_button").attr({"disabled":false});
	  $("#recharge_button").text("充值");
	  $("#key_name").attr({"disabled":false});
	  $("#key_password").attr({"disabled":false});
	  $("#key_name").val("");
	  $("#key_password").val("");
	if($("div#response",data).text()==0){
	var data="<div class='am-alert am-alert-danger' id='query_result'>"+$("div#ajax",data).text()+".</div>";
	$('#alert_show').html(data);	
   setTimeout(function (){$('#query_result').alert('close')},3000);
	}else{
	var data="<div class='am-alert am-alert-success' id='query_result'>"+$("div#ajax",data).text()+".</div>";
	$('#alert_show').html(data);	
    setTimeout(function (){$('#query_result').alert('close')},5000);
	update_balance();
	  }
  }
});
}
function update_balance(){
		$.ajax({
  type: 'POST',
  async: true, 
  url: 'index.php?mod=panel:payment',
  data: {  balance: true },
	   beforeSend:function(){
	$('#balance').html("￥0.00");
	var data="<div class='am-alert am-alert-success' id='query_result'><i class='am-icon-spinner am-icon-spin'></i> 数据加载中...</div>";
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
  </div>
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
   <p>这里是Uwallet</p>
 </div>
</section>
						</div> 
                                        
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div></div>
                                </div>

                            </div>
                        </div>

                    </div>
        </div>