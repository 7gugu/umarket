<?php
//交易机器人 Power by 7gugu
//注意!!!机器人务必使用已经解锁交易功能的机器人,
//不然用户无法取回自己的物品
//解锁交易功能需要机器人账户在steam商城上累计消费到20元才行
     include 'bot_header.php';
     $connect=$sql->connect();
	 $context="select * from umarket_bot_account where `accountstate`='0' limit 1 ";//抽取可用的交易机器人
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);
	 if($res==null){echo "Unavailable Bot ".PHP_EOL;exit();}
	//初始化机器人,检测机器人的登录状态
	echo "Bot is logging in now".PHP_EOL;
	$accountid=$res['accountid'];
	$username=$res['username'];
	$password=$res['password'];
	$port=$res['accountport'];
	if($res['twofastate']=='0'){
	$requires_twofa=false;
	$shared_serect="";
	}else{
	$requires_twofa=true;
	$shared_serect=$res['shared_serect'];
	}
	echo "Username:".$username.PHP_EOL;
	if(MODE_MANUAL){
	//手动模式
	$sql->query($connect,"update umarket_bot_account set `accountstate`='5' where `username`='{$username}'");//等待人工输入二步验证码
	echo "Please input TwoFa key:".PHP_EOL;
	$stdin=fopen('php://stdin','r');//获取用户输入
	$twofa=trim(fgets($stdin));
	echo "Your input:".$twofa.PHP_EOL;
    $res=$steambot->login($username,$password,$requires_twofa,'',$twofa);
	}elseif($requires_twofa==true&&$shared_serect==""){
	//手动模式
	$sql->query($connect,"update umarket_bot_account set `accountstate`='5' where `username`='{$username}'");//等待人工输入二步验证码
	echo "[Init] "."Please input TwoFa key:".PHP_EOL;
	$stdin=fopen('php://stdin','r');//获取用户输入
	$twofa=trim(fgets($stdin));
	echo "[Init] "."Your input:".$twofa.PHP_EOL;
    $res=$steambot->login($username,$password,false,'',$twofa);
	 }else{
	$res=$steambot->login($username,$password,$requires_twofa,$shared_serect);
	 }
	 var_dump($res);
	if(in_array("emailauth_needed",$res)==true&&@$res['emailauth_needed']==1){
		//如果接收到需要邮箱验证,就等待用户输入邮箱验证码
		while(true){
		echo "Please input EmailAuth key:".PHP_EOL;
	$stdin=fopen('php://stdin','r');//获取用户输入
	$email_auth=trim(fgets($stdin));
	echo "Your input:".$email_auth.PHP_EOL;
	$res=$steambot->login($username,$password,$requires_twofa,"","",$email_auth);
	if(@$res['emailauth_needed']!=1)break;
	}
	}
	 if($res['success']){
	echo "Login success".PHP_EOL;	
	}else{
	echo "Login fail".PHP_EOL;	
	exit();
	}	
	echo "SteamId:".$steambot->getSteamid("1").PHP_EOL;
     $context="update umarket_bot_account set `accountstate`='1' where `username`='{$username}'";//更新机器人状态
	 $res=$sql->query($connect,$context);
	
	
	//尝试获取来自分发器的订单
do{   
    //建立监听系统
    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
    if(socket_bind($socket,BOTIP,$port) == false){
        echo 'server bind fail:'.socket_strerror(socket_last_error());
    }else{
	echo "[". date("Y H:i:s")."] "."Bot is binding".PHP_EOL;	
	}
    if(socket_listen($socket,4)==false){
        echo 'server listen fail:'.socket_strerror(socket_last_error());
    }else{
	echo "[". date("Y H:i:s")."] "."Bot is listening".PHP_EOL;	
	}
    $accept_resource = socket_accept($socket);
    if($accept_resource !== false){
     $string = socket_read($accept_resource,1024);//获取订单号
	 echo "Have request the OrderID:".$string.PHP_EOL;
	         socket_close($accept_resource);
    }else{
	continue;	
	}
	
	$context="update umarket_bot_account set `accountstate`='3' where `username`='{$username}'";//机器人正忙无法通讯
    $sql->query($connect,$context);
	 if($steambot->getSteamid()==0){
		 //二次检测机器人的登录状态
	if($res['twofastate']==0){
		$requires_twofa=false;$shared_serect="";
	}else{
	$requires_twofa=true;$shared_serect=$res['shared_serect'];
	}
	if(MODE_MANUAL){
	//手动模式
	$sql->query($connect,"update umarket_bot_account set `accountstate`='5' where `username`='{$username}'");//等待人工输入二步验证码
	echo "[Init] "."Please input TwoFa key:".PHP_EOL;
	$stdin=fopen('php://stdin','r');//获取用户输入
	$twofa=trim(fgets($stdin));
	echo "[Init] "."Your input:".$twofa.PHP_EOL;
    $res=$steambot->login($username,$password,false,'',$twofa);
	 }else{
	$res=$steambot->login($username,$password,$requires_twofa,$shared_serect);
	 }
	  if($res['success']){
	echo "[". date("Y H:i:s")."] "."Login success".PHP_EOL;	
	}else{
	echo "[". date("Y H:i:s")."] "."Login fail".PHP_EOL;
	$context="update umarket_bot_account set `accountstate`='3' where `username`='{$username}'";//机器人正忙无法通讯
    $sql->query($connect,$context);
	break;
	}
		
	 }else{
	echo "[". date("Y H:i:s")."] "."Bot has logged in".PHP_EOL;	 
	 }
	 $context="select * from umarket_query_list where `order_id`='{$string}' ";
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);
	 $order_time=$res['order_time']+300;
	 if($order_time>=time()){
	 if($res!=false&&$res['order_stat']==0){
	    $json=json_encode(array(
		'newversion' => true,
		'version' => count(json_decode($res['order_context']))+1, 
		'me' => array("assets"=> [],"currency"=> [],"ready"=> false), 
		'them' =>  array("assets"=>json_decode($res['order_context']),"currency"=>[],"ready"=>false)
		),true);//构造提取操作参数 接受来自卖家的饰品
      }elseif($res!=false&&$res['order_stat']==1){
		$json=json_encode(array(
		'newversion' => true,
		'version' => count(json_decode($res['order_context']))+1,  
		'me' => array("assets"=>json_decode($res['order_context']),"currency"=>[],"ready"=>true), 
		'them' => array("assets"=> [],"currency"=> [],"ready"=> false)
		),true);//构造发出操作参数
}else{
	echo "[". date("Y H:i:s")."] "."Trade Invalid".PHP_EOL;
}
var_dump($json);
$context="select * from umarket_inventory_order where `order_market_id`={$res['order_market_id']}";
$result=$sql->fetch_array($sql->query($connect,$context));	
if($res!=false&&$res['order_stat']==1){
		$order_steam_id=$steambot->send($res['order_token'],$json,$res['order_partner'],$res['order_serect'],false);
}else{
		$order_steam_id=$steambot->send($res['order_token'],$json,$res['order_partner'],$res['order_serect'],true);	
}
		if(is_numeric($order_steam_id)){
			//更新被认领的订单状态,steam订单ID,订单状态,执行机器人的accountID
		$context="update umarket_query_list set `order_steam_id`='{$order_steam_id}',`order_stat`='2',`bot_accountid`='{$accountid}' where `order_id`='{$res['order_id']}'";//更新订单状态,成功发送了报价
		$sql->query($connect,$context);	
		$order_outtime=time()+300;
		if($res['order_stat']==1){
			$order_state="6";//发送取回请求交易成功
			$goods_state='3';
		}else{
			$order_state="3";//发送提取请求交易成功
			$goods_state="0";
		}
		$context="update umarket_inventory_order set `order_steamid`='{$order_steam_id}',`order_stat`='{$order_state}',`order_outtime`='{$order_outtime}',`bot_accountid`='{$accountid}' where `order_market_id`='{$res['order_market_id']}'";//更新订单状态,成功发送了报价
		$sql->query($connect,$context);
		$context="update umarket_inventory set `goods_state`='{$goods_state}' where `order_market_id`='{$res['order_market_id']}'";//更新订单历史状态,入库或出库成功
		$sql->query($connect,$context);	
		}elseif(strpos($order_steam_id,'\u60a8\u53d1\u9001\u7684\u4ea4\u6613\u62a5\u4ef7\u8fc7\u591a') == true){
		echo "[". date("Y H:i:s")."] "."Bot has too much trades with this buyer".PHP_EOL;//机器人与该玩家有太多的交易
		$context="update umarket_query_list set `order_stat`='5' where `order_id`='{$res['order_id']}'";
	    $res=$sql->query($connect,$context);
		$context="update umarket_inventory_order set `order_stat`='5' where `order_market_id`='{$res['order_market_id']}'";//更新订单状态,发送失败
		$sql->query($connect,$context);	
		if($res['order_stat']==1){
			$goods_state='5';
		}else{
			$goods_state="2";
		}
		$context="update umarket_inventory set `goods_state`='{$goods_state}',`bot_accountid`='{$accountid}' where `order_market_id`='{$res['order_market_id']}'";//更新订单历史状态,入库失败
		$sql->query($connect,$context);	
		}else{
		echo "[". date("Y H:i:s")."] "."Trade fail".PHP_EOL;//机器人交易发起失败
		$context="update umarket_query_list set `order_stat`='4' where `order_id`='{$res['order_id']}'";
	    $res=$sql->query($connect,$context);
		$context="update umarket_inventory_order set `order_stat`='4' where `order_market_id`='{$res['order_market_id']}'";//更新订单状态,
		$sql->query($connect,$context);	
		if($res['order_stat']==1){
			$goods_state='5';
		}else{
			$goods_state="2";
		}
		$context="update umarket_inventory set `goods_state`='{$goods_state}',`bot_accountid`='{$accountid}' where `order_market_id`='{$res['order_market_id']}'";//更新订单历史状态,入库失败
		$sql->query($connect,$context);	
		}
		if($sql->affected_rows($connect)>0){
		echo "[". date("Y H:i:s")."] "."SQL run success".PHP_EOL;
		}else{
		echo "[". date("Y H:i:s")."] "."SQL run fail".PHP_EOL;
		}
}else{
$context="update umarket_inventory set `goods_state`='2' where `order_market_id`='{$res['order_market_id']}'";//更新订单历史状态,入库失败	
$sql->query($connect,$context);	
$context="update umarket_query_list set `order_stat`='8' where `order_id`='{$res['order_id']}'";//交易已过期
$res=$sql->query($connect,$context);
}
	//机器人人工下线检测
	 $context="select * from umarket_bot_account where username='{$username}' ";
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);
	if($res['accountstate']=='4'){
	$context="update umarket_bot_account set `accountstate`='0' where `username`='{$username}'";//机器人下线
    $sql->query($connect,$context);
	break;	
	}
    $context="update umarket_bot_account set `accountstate`='1' where `username`='{$username}'";//机器人恢复可用状态
    $sql->query($connect,$context);
	socket_close($socket);
}while(true);
$sql->close($connect);
     ?>
		

