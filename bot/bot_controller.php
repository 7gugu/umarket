<?php 
//订单分发器 Power by 7gugu
include 'bot_header.php';
     $connect=$sql->connect();
	 echo "Order-Controller is working now !".PHP_EOL;
	 while(true){
	 $context="select * from umarket_bot_account where accountstate='1' order by rand() limit 1 ";
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);//随机获取一个可用机器人的端口,无则跳过
	 sleep(5);
	 if($res==null){
		echo "[". date("Y H:i:s")."] "."Unavailable Bot ".PHP_EOL;
		sleep(5);
		continue;
	 }
	 $port=$res['accountport'];//获取在线机器人的端口
	 $username=$res['username'];//获取该机器人的账号名
	 echo "[". date("Y H:i:s")."] "."Bot-AccountID:".$username.PHP_EOL;
	 $context="select * from umarket_query_list where `order_stat`='0' or `order_stat`='1' order by order_time asc limit 1 ";
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);
	 if($res==null){
	 echo "[". date("Y H:i:s")."] "."Unavailable trade".PHP_EOL;
	 continue;
	 }
	 $orderid=$res['order_id'];//获取一个未处理的订单
	 $context="select * from umarket_option where `option_name`='bot_ip' limit 1 ";
	 $res=$sql->query($connect,$context);
	 $res=$sql->fetch_array($res);
	 $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	 if(socket_connect($socket,$res['option_context'],$port) == false){
    echo "BOT connect fail".PHP_EOL;
	$context="update umarket_bot_account set accountstate='0' where `username`='{$username}'";//更新机器人状态
	$res=$sql->query($connect,$context);
    }else{
        if(socket_write($socket,$orderid,strlen($orderid)) == false){
            echo 'fail to write'.socket_strerror(socket_last_error());
        }else{
			echo "[". date("Y H:i:s")."] "."OrderID:".$orderid.PHP_EOL;
            echo "[". date("Y H:i:s")."] ".'client write success'.PHP_EOL;
			$context="update umarket_order set order_state='3' where order_id='{$orderid}' ";
			$res=$sql->query($connect,$context);//更新该订单的状态为处理中
        }
    }
    socket_close($socket);

	 }
	 $sql->close($connect);
?>