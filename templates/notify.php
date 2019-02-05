<?php
//因为自带回调,所以要被弃用
require '../init.php';
global $alipay;
global $sql;
$result = $alipay->rsaCheck($_POST,$_POST['sign_type']);
if($result===true){
    //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
    //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
   //商户订单号
	$out_trade_no = $_POST['out_trade_no'];
	//支付宝交易号
	$trade_no = $_POST['trade_no'];
	//交易状态
	$trade_status = $_POST['trade_status'];
    $connect=$sql->connect();
	$context="select * from umarket_order where `orderid`='{$out_trade_no}'";
	$res=$sql->query($connect,$context);
	$res=$sql->fetch_array($res);
	$price=0;
	foreach(unserialize($res['context']) as $i){
	$price=$price+$i['item_price']*$i['item_count'];
	}
	if($res['trade_amount']==$price){
	if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
	$context="update umarket_order set orderstate='1' where `orderid`='{$out_trade_no}'";
	$res=$sql->query($connect,$context);
    }
	}else{
	echo "error";
    exit();	
	}
	echo 'success';
	$sql->close($connect);
	exit();
}
echo 'error';
exit();