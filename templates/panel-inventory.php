 <?php 
 global $mode;
 global $steambot;
 global $sql;
 $connect=$sql->connect();
 $parse = explode(':',$mode);
if(@isset($parse[2])){$m=$parse[2];}else{$m="0";}//获取细化模块的编号
 if(isset($_POST['gameid'])&&$_POST['gameid']!=""&&isset($_POST['steamid'])){
						 $result=array();
						 $mode="0";
						 if(isset($_POST['mode'])){$mode=$_POST['mode'];}
						 $gameid=$_POST['gameid'];
						 if($mode=='1'){
					    //获取商城库存
						$steamid=$_POST['steamid'];
						$gameid=$_POST['gameid'];
						$context="select count(*) from umarket_inventory_order where `player_steam_id`='{$steamid}' and `order_game_id`='{$gameid}' and `order_stat`='3' ";			
						$result=$sql->fetch_array($sql->query($connect,$context));
                        if($result[0]==0){echo "<div id='response'>0</div><div id='ajax'><section class='am-panel am-panel-default'><div class='am-panel-bd'><span class='am-badge am-badge-warning am-radius am-text-lg'>该游戏暂无可被提取到商城的饰品</span></div></section></div></div>";die;}
						$context="select * from umarket_inventory_order where `player_steam_id`='{$steamid}' and `order_game_id`='{$gameid}' and `order_stat`='3' ";			
						$resource=$sql->query($connect,$context);
						echo "<div id='response'>1</div><div id='ajax'>";
						echo "<div id='market_inventory_item'>";
						while($result=$sql->fetch_array($resource)){
						 $count=0;
						 foreach(json_decode($result['order_context'],true) as $order_context){
						$order_icon_url=json_decode($result['order_icon_url'],true);
						$order_item_name=json_decode($result['order_item_name'],true);
				echo "<li><div class='am-gallery-item am-u-sm-2'>";
				echo "<img style='width=96px;' src='".$order_icon_url[$count]."'/>";	 
                echo "<h3 class='am-gallery-title'>".$order_item_name[$count]."</h3>";
                echo "<input type='checkbox' id='am-check' name='param[]' value='".$order_context['assetid']."|".$order_item_name[$count]."|".$order_icon_url[$count]."'>";
				echo "</li>";
						  $count++;
						  }
						
				
						}
						echo "</div>";
				echo "<input type='hidden' id='gameid' value='".$gameid."'\>";
					echo "</div>";	
						 }else{
					    //获取Steam库存
						@$inventory=$steambot->getinventory($_POST['steamid'],$_POST['gameid']);
						 $json=json_decode($inventory,true);
						 foreach($json['assets'] as $j){
							  $assetid=$j['assetid'];
							   $classid=$j['classid'];
						foreach($json['descriptions'] as $d){
							if(array_search($classid,$d)!=false){
								$description=$d['descriptions'][0]['value'];	
								foreach($d['tags'] as $t){
									if(in_array("cosmetic_slot",$t)!=false){
									$type=$t['internal_name'];
									break;
									}
								}
                            break;	
							}
							
						}
						if(@isset($d['icon_url'])){$icon_url="https://steamcommunity-a.akamaihd.net/economy/image/".$d['icon_url']."/96fx96f";}else{$icon_url="";}
						if(@isset($d['name_color'])){$name_color=$d['name_color'];}else{$name_color="";}
						$res=array(
						    "gameid"=>$gameid,
							"assetid"=>$assetid,
							"classid"=>$classid,
							"icon_url"=>$icon_url,
							"item_name"=>$d['name'],
							"item_name_color"=>$name_color,
							"descriptions"=>$description,
							"type"=>@$type
							);	
                         array_push($result,$res);							
						  }
						  echo "<div id='ajax'>";
						  foreach($result as $r){
				 echo "<div id='inventory_item'><li><div class='am-gallery-item am-u-sm-2'>";
				 if(strlen($r['descriptions'])>1){
			     echo "<div class='show'><img style='width=96px;' src='".$r['icon_url']."'  ><div>".$r['descriptions']."</div></img></div>";
				 }else{
				echo "<img style='width=96px;' src='".$r['icon_url']."'/>";
				 }
                echo "<h3 class='am-gallery-title'>".$r['item_name']."</h3>";
                echo "<input type='checkbox' id='am-check' name='param[]' value='".$r['assetid']."|".$r['item_name']."|".$r['icon_url']."'>";
				echo "</div></li></div>";
						  }
				echo "<input type='hidden' id='gameid' value='".$gameid."'\>";
					echo "</div>";	
 }
						  die;
					 }
	//发起交易
    if(isset($_POST['gameid'])&&!isset($_POST['steamid'])&&!isset($_POST['process_item'])){
	$bot_num_0=mysqli_fetch_array($sql->query($connect,"select count(*) from umarket_bot_account where `accountstate`='0'"));
	$bot_num_1=mysqli_fetch_array($sql->query($connect,"select count(*) from umarket_bot_account where `accountstate`='1'"));
	$bot_num=$bot_num_0[0]+$bot_num_1[0];
	if($bot_num<=0){
		echo "<div id='response'>0</div><div id='alert'>无可用机器人,请稍后再发起请求或咨询站点管理员</div>";
		die;
	}
	$inventory_array=array();
	$icon_array=array();
	$name_array=array();
	if(!isset($_POST['param'])){
		echo "<div id='response'>0</div>";
		die;
	}
	if(isset($_POST['type'])){
		$order_state=$_POST['type'];//1代表发起发送请求
	}else{
		$order_state="0";//默认发起提取请求
	}
	$all_inventory=$_POST['param'];//获取来自checkbox的传值
	$appid=$_POST['gameid'];
	$context = "select * from umarket_inventory_order order by order_market_id DESC limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$order_market_id=0;if($res){$order_market_id=$res[0];}$order_market_id++;//计算位于库存订单中的ID
	foreach($all_inventory as $ai){
	$param=explode("|",$ai);
	$order_icon_url=$param[2];
	$order_item_name=$param[1];
	$them=array("appid"=>$appid,"contextid"=>"2","amount"=>1,"assetid"=>$param[0]);//拼接交易数据
    array_push($inventory_array,$them);//把数据推入数组中储存 0代表提取物品1代表发送物品
	array_push($icon_array,$order_icon_url);//把物品图片数据推入数组中储存
	array_push($name_array,$order_item_name);//把物品名称数据推入数组中储存
	//将饰品拆分存入数据库中,记录饰品动态,方便后期跟踪
	$context="select * from umarket_inventory order by inventory_id desc limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$inventory_id=0;
	if($res){
	$inventory_id=$res['inventory_id'];
	}
	$inventory_id++;
	$save_time=date("Y-m-d h:i:sa");//记录上架时间
	$context="INSERT INTO umarket_inventory (`inventory_id`,`goods_price`,`goods_count`,`goods_steam_id`,`goods_buyer_id` ,`goods_seller_id` ,`goods_submit_time` ,`goods_state` ,`goods_name`,`order_market_id`,`goods_game_id`,`goods_img`,`bot_accountid`)VALUES ('{$inventory_id}',  '10000',  '1',  '{$param[0]}',  '0',  '{$_COOKIE['steamid']}',  '{$save_time}',  '1',  '{$order_item_name}','{$order_market_id}','{$appid}','{$order_icon_url}','0')";
	$sql->query($connect,$context);//更新上架库存-历史记录,可操作状态:上架-不可操作,下架-可被操作
	}
	$order_context=json_encode($inventory_array);//序列化订单内容
	$order_icon_url=json_encode($icon_array);//序列化物品图片内容
	$order_item_name=json_encode($name_array);//序列化物品名称内容
	$context = "select * from umarket_query_list order by order_id DESC limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$order_id=0;if($res){$order_id=$res[0];}$order_id++;//计算位于排队序列中的ID
	$context = "select * from umarket_account where `username`='{$_COOKIE['username']}'";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$accountid=$res['accountid'];
	$trade_link=$res['tradelink'];
	$pattern = '/(\?|&)(.+?)=([^&?]*)/i';
    preg_match_all($pattern, $trade_link, $matches);
	$order_partner=$matches[3][0];
	$order_token=$matches[3][1];
	$order_time=time();//订单发起时间,用于查档使用
	$order_serect=MD5(time()."cnm");//计算交易暗号
	$context="insert into umarket_query_list(order_id,order_stat,order_token,order_context,order_serect,order_partner,order_market_id,order_steam_id,order_time)values('{$order_id}','{$order_state}','{$order_token}','{$order_context}','{$order_serect}','{$order_partner}','{$order_market_id}','0','{$order_time}')";	
    $sql->query($connect,$context);
	$count_query=$sql->affected_rows($connect);
	$order_outtime=0;
	$bot_accountid=0;
	$context="insert into umarket_inventory_order(order_market_id,order_stat,order_context,order_steamid,order_outtime,order_icon_url,order_item_name,player_steam_id,bot_accountid,order_game_id)values('{$order_market_id}','{$order_state}','{$order_context}','0','{$order_outtime}','{$order_icon_url}','{$order_item_name}','{$_COOKIE['steamid']}','{$bot_accountid}','{$appid}')";
    $sql->query($connect,$context);
	$count_inventory=$sql->affected_rows($connect);
	$count_sub=$count_inventory+$count_query;
	if($count_sub==2){
	echo "<div id='response'>".$order_market_id."</div>";
	}else{
	echo "<div id='response'>0</div>";
	}
	die;
}	
    //检测交易是否被机器人认领
	if(isset($_POST['checkoffer'])){
		$order_market_id=$_POST['checkoffer'];//商城id
		if($order_market_id==""){echo "<div id'response'>null</div>";die;}
		$context = "select * from umarket_inventory_order where `order_market_id`='{$order_market_id}' ";
	    $res=$sql->fetch_array($sql->query($connect,$context));
		$context = "select * from umarket_query_list where `order_market_id`='{$order_market_id}' ";
	    $result=$sql->fetch_array($sql->query($connect,$context));
		if($res['order_stat']=='3'){
		echo "<div id='response'>1</div><div id='order_market_id'>".$res['order_market_id']."</div><div id='order_steamid'>".$res['order_steamid']."</div><div id='order_outtime'>".$res['order_outtime']."</div><div id='order_serect'>".$result['order_serect']."</div>";	
		}else{
		echo "<div id='response'>0</div>";//交易未被认领
		}
		die;
	}	
//取消canceloffer传过来的ID对应的交易
if(isset($_POST['canceloffer'])){
	if($_POST['canceloffer']==""){
		echo "<div id='response'>0</div>";
		die;
	}
	$order_steamid=$_POST['canceloffer'];
	$url = 'https://api.steampowered.com/IEconService/CancelTradeOffer/v1/?key='.APIKEY;
$res=json_decode($steambot->curl($url,array('tradeofferid' => $_POST['canceloffer'])),true);
var_dump($res);
if(array_key_exists("response",$res)){
	echo "<div id='response'>1</div>";//执行成功
		$context="select * from  umarket_inventory_order where `order_steamid`='{$_POST['canceloffer']}'";
		$array=$sql->fetch_array($sql->query($connect,$context));
		$order_market_id=$array['order_market_id'];
		$context="update umarket_inventory set `goods_state`='8' where `order_market_id`='{$order_market_id}'";//更新订单历史状态,取消交易
		$sql->query($connect,$context);	
		$context="update umarket_inventory_order set `order_stat`='8' where `order_steamid`='{$_POST['canceloffer']}'";//更新订单历史状态,取消交易
		$sql->query($connect,$context);	
}else{
	echo "<div id='response'>0</div>";//执行失败
}
//由于返回的字段没有状态,所以只能返回前端,依靠状态监测来确定交易的实际状态
die;
}
//获取交易的状态
if(isset($_POST['check_offer_status'])){
$order_steamid=htmlspecialchars($_POST['check_offer_status']);
$language="zh";
$status_url="https://api.steampowered.com/IEconService/GetTradeOffer/v1/?key=".APIKEY."&tradeofferid=".$order_steamid."&language=".$language;
$status_json=file_get_contents($status_url);
$status_array=json_decode($status_json,true);
$status=$status_array['response']['offer']['trade_offer_state'];
if($status==3){
		$context="select * from  umarket_inventory_order where `order_steamid`='{$order_steamid}'";
		$array=$sql->fetch_array($sql->query($connect,$context));
		$order_market_id=$array['order_market_id'];
		$context="update umarket_inventory set `goods_state`='6' where `order_market_id`='{$order_market_id}'";//更新订单历史状态,交易成功
		$sql->query($connect,$context);	
}
		echo "<div id='response'>".$status."</div>";
die;
}
//处理上架请求
if(isset($_POST['process_item'])){
	$submit_price=$_POST['price'];
	//var_dump($submit_price);
	$submit_assetid=$_POST['assetid'];
	$submit_icon_url=$_POST['icon_url'];
	$submit_item_name=$_POST['item_name'];
	$gameid=$_POST['gameid'];
	$count=0;
		foreach($submit_price as $f){
	$price=$f;
	$assetid=$submit_assetid[$count];
	$icon_url=$submit_icon_url[$count];
	$item_name=$submit_item_name[$count];
	$count++;
	$context="SELECT *  FROM `umarket_inventory_order` WHERE `order_context` LIKE '%{$assetid}%' limit 1";//获取库存中可用的饰品
	$available_inventory_context=$sql->fetch_array($sql->query($connect,$context));
	//var_dump($available_inventory_context);
//echo $count."<br>";
	$available_inventory_array=json_decode($available_inventory_context['order_context'],true);
	$available_inventory_id=$available_inventory_context['order_market_id'];
	$goods_seller_id=$available_inventory_context['player_steam_id'];
	$del_count=0;
foreach($available_inventory_array as $a){
	if(array_search($assetid,$a)!=false){//对库存数组进行比对,并推出要上架的货品
		array_splice($available_inventory_array,$del_count,1);
		$array=json_encode($available_inventory_array);
		//若数据为空,则更新这部分的库存的状态为清空
		if($array==json_encode(array())){
		$context="update `umarket_inventory_order` set `order_stat`='7' where `order_market_id`='{$available_inventory_id}'";
		$sql->query($connect,$context);//更新库存数据
		}
		$context="update `umarket_inventory_order` set `order_context`='{$array}' where `order_market_id`='{$available_inventory_id}'";
		$sql->query($connect,$context);//更新库存数据
		$del_count++;
//=============================================
		//插入或更新该栏目的索引
$context="select count(*) from umarket_goods_map_".$gameid." where `goods_id`='{$assetid}'";
$res=$sql->fetch_array($sql->query($connect,$context));
if($res[0]==0){
	//插入[新建]
	//插入实际库存//umarket_goods_[goods_id]
	$context="select * from umarket_goods_map_".$gameid." order by map_id desc limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$map_id=0;
	if($res){
	$map_id=$res['map_id'];
	}
	$map_id++;
	$goods_count=1;
	$goods_min_price=$price;
	$context="insert into `umarket_goods_map_".$gameid."`(map_id,goods_id,goods_name,goods_img,goods_min_price,goods_count,goods_type)values('{$map_id}','{$assetid}','{$item_name}','{$icon_url}','{$goods_min_price}','{$goods_count}','0')";
}else{
	//更新
	//更新实际库存
	$context="select * from umarket_goods_map_".$gameid." where `goods_id`='{$assetid}' limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$map_id=$res['map_id'];
	$goods_count=$res['goods_count']+1;
	$goods_min_price=$res['goods_min_price'];
	//加入最高价的判断
	if($price>$goods_min_price){
		$min_price=$price;
	}else{
		$min_price=$goods_min_price;
	}
	$context="update `umarket_goods_map_".$gameid."` set `goods_count`='{$goods_count}' , `goods_min_price`='{$min_price}' where `map_id`='{$map_id}'";
}
$sql->query($connect,$context);//更新上架库存
//===========================================
//构造存储函数
$context="select * from umarket_goods_".$gameid." order by item_id desc limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$item_id=0;
	if($res){
	$item_id=$res['item_id'];
	}
	$item_id++;
	$goods_context=json_encode(array("goods_name"=>$item_name,"goods_addtion"=>""));
	$context="insert into `umarket_goods_".$gameid."` (item_id,goods_context,goods_price,goods_count,goods_seller_id,goods_id)values('{$item_id}','{$goods_context}','{$price}','1','{$goods_seller_id}','{$assetid}')";
	$sql->query($connect,$context);//更新上架库存
	$context="update umarket_inventory set `goods_state`='7',`goods_price`='{$price}' where `order_market_id`='{$available_inventory_id}' and `goods_steam_id`='{$assetid}' limit 1";//更新订单历史状态,饰品上架不可操作
	$sql->query($connect,$context);//更新上架库存-历史记录,可操作状态:上架-不可操作,下架-可被操作
//============================================
	}else{
		continue;
	}
}
	}
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'></div>";
	}
	
	die;
}
//获取已上架的饰品
if(isset($_POST['get_market_inventory'])){
	//检索umarket_inventory中order_state为7的库存,更新状态为6,并获取assetid和出售者的ID
	$user_steamid=$_COOKIE['steamid'];
	$context="select * from `umarket_inventory` where `goods_state`='7' and `goods_seller_id`='{$user_steamid}'";
	$response=$sql->query($connect,$context);
	if($response==false){echo "<div id='response'>0</div>";die;}else{echo "<div id='response'>1</div>";}
	echo "<div id='ajax'>";			
	while($res=$sql->fetch_array($response)){
		$gameid=$res['goods_game_id'];//饰品对应的游戏ID
		$assetid=$res['goods_steam_id'];//饰品的SteamID
		$item_name=$res['goods_name'];//饰品名称
		$img_path=$res['goods_img'];
		$goods_price=$res['goods_price'];
		$goods_market_id=$res['order_market_id'];
		$bot_accountid=$res['bot_accountid'];
		$inventory_id=$res['inventory_id'];
		echo "<li><div class='am-gallery-item am-u-sm-2'>";
		echo "<img style='width=96px;' src='".$img_path."'/>";
        echo "<h3 class='am-gallery-title'>".$item_name."<br>￥".$goods_price."</h3>";
        echo "<input type='checkbox' id='am-check' name='param[]' value='".$assetid."|".$item_name."|".$img_path."|".$gameid."|".$goods_market_id."|".$bot_accountid."|".$inventory_id."'>";
		echo "</div></li>";
	}
	echo "</div>";	
	die;
}
//执行下架操作
if(isset($_POST['submit_down_item'])){
	$param=$_POST['param'];
	if($param==""){
		echo "<div id='response'>0</div>";
		die;
	}
	foreach($param as $p){
		$array=explode("|",$p);
		$goods_market_id=$array[4];
		$goods_steam_id=$array[0];
		$inventory_id=$array[6];
		$img_path=json_encode(array($array[2]));
		$item_name=json_encode(array($array[1]));
		$gameid=$array[3];
		$bot_accountid=$array[5];
		$order_context=json_encode(array(array("appid"=>$gameid,"contextid"=>2,"amount"=>1,"assetid"=>$goods_steam_id)));
		$sql->query($connect,"update umarket_inventory set `goods_state`='6' where `order_market_id`='{$goods_market_id}' and `goods_steam_id`='{$goods_steam_id}'");	
		$context = "select * from umarket_inventory_order order by order_market_id DESC limit 1 ";
		$res=$sql->fetch_array($sql->query($connect,$context));
		$order_market_id=0;if($res){$order_market_id=$res[0];}$order_market_id++;//计算位于库存订单中的ID
		$sql->query($connect,"insert into umarket_inventory_order (order_market_id,order_stat,order_context,order_steamid,order_outtime,order_icon_url,order_item_name,player_steam_id,bot_accountid,order_game_id)values('{$order_market_id}','3','{$order_context}','0','0','{$img_path}','{$item_name}','{$_COOKIE['steamid']}','{$bot_accountid}','{$gameid}')");
		$sql->query($connect,"update umarket_inventory set `order_market_id`='{$goods_market_id}' where `inventory_id`='{$inventory_id}'");
		$sql->query($connect,"DELETE FROM `umarket_goods_{$gameid}` WHERE `goods_seller_id` = '{$_COOKIE['steamid']}' and `goods_id`={$goods_steam_id}  LIMIT 1;");//删除位于该游戏ID下的物品记录
		$res=$sql->fetch_array($sql->query($connect,"select * from umarket_goods_map_{$gameid} where `goods_id`='{$goods_steam_id}'"));
		$goods_count=$res['goods_count']-1;
		$sql->query($connect,"update umarket_goods_map_{$gameid} set `goods_count`='{$goods_count}' where  `goods_id`='{$goods_steam_id}'");//更改还游戏ID下的索引数据
		
	}
	echo "<div id='response'>1</div>";
}
//执行修改价格的操作
if(isset($_POST['update_item_price'])){
	if(!isset($_POST['price'])){echo "无数据";die;}
	$submit_price=json_decode($_POST['price'],true);
	var_dump($submit_price);
	$submit_assetid=json_decode($_POST['assetid'],true);
	$goods_market_id=json_decode($_POST['goods_market_id'],true);
	$count=0;
	foreach($submit_price as $p){
	if($p==""){$p=500;}
	$goods_price=$p;
	$assetid=$submit_assetid[$count];
	$goods_market_id=$goods_market_id[$count];
	$count++;
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_inventory  where `order_market_id`='{$goods_market_id}'"));
	$goods_game_id=$res['goods_game_id'];
	$sql->query($connect,"update umarket_inventory set `goods_price`='{$goods_price}' where `order_market_id`='{$goods_market_id}' and `goods_steam_id`='{$assetid}' limit 1");
	$sql->query($connect,"update umarket_goods_{$goods_game_id} set `goods_price`='{$goods_price}' where `goods_seller_id`='{$_COOKIE['steamid']}' and `goods_id`='{$assetid}' limit 1");
	}	
if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'></div>";
	}
	die;
}
 ?>
 <div class="tpl-content-wrapper">
 <style>
.show{
position:relative; /*这个是关键*/
z-index:2;
}
.show:hover{
z-index:3;
}
.show div{
display: none;
}
.show:hover div{ /*span 标签仅在 :hover 状态时显示*/
display:block;
position:fixed;
top:140px;
left:550px;
word-break:break-all;
word-wrap:break-word;
width:auto;
height:auto;
border:1px solid #56b9e7;
background-color:#282c34;
padding: 3px;
color:#abb2bf;
}
 </style>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 
<?php						
						switch($m){
						 case 1:
						echo "我的库存";
						 break;
						 case 2:
						echo "正在出售";
						 break;
                         default:
						 echo "Steam库存";
						 break;
						}
						 ?>
                    </div>
                </div>
                <div class="tpl-block ">
                     <div class="am-g tpl-amazeui-form">
               <div class="am-u-sm-12">
			   <?php loadalert();?>
<div class="am-u-sm-12 am-u-md-6">
<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title">库存</h3>
  </header>
  <div class="am-panel-bd">
   <button type="button" class="am-btn am-btn-danger" onclick="window.location.href='index.php?mod=panel:inventory:1'">我的库存</button>
&nbsp;
<button type="button" class="am-btn am-btn-secondary" onclick="window.location.href='index.php?mod=panel:inventory:0'">Steam库存</button>
&nbsp;
<button type="button" class="am-btn am-btn-success"  onclick="window.location.href='index.php?mod=panel:inventory:2'">正在出售</button>
 
 <br>
 </div>
</section>
</div>
<?php
						  
					 switch($m){
						 case 1:
                        ?>
<div class="am-u-sm-12 am-u-md-6">
<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title">游戏</h3>
  </header>
  <div class="am-form am-panel-bd">
     <select id="doc-select-1" onchange="update_show_market(this.options[this.selectedIndex].value)">
	  <option value='initial'>请选择想获取的游戏</option>
	<?php 
	$context="select * from umarket_option where `option_name`='column_index'";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$column_index=unserialize($res['option_context']);
					$column=json_decode($steambot->getgamelist($_COOKIE['steamid']),true);
					$count=0;
					var_dump($column_index);
					 foreach ($column[3] as $n){
						 $num=array_search($n,$column[3]);
						 if($column[1][$num]=="753"||!in_array($column[1][$num],$column_index)){$count++;continue;}	 
						 echo "<option value='".$column[1][$num]."' >".$n."</option>";   
					}
					if(count($column[3])==$count){
						echo "<option value='error'>无存入商城库存的饰品</option>";
					}
						  ?>				
      </select>
      <span class="am-form-caret"></span>
  </div>
</section>
</div>						
                        <?php
						 break;
						 case 2:
						?>
						<div class="am-u-sm-12 am-u-md-6">
<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title">注意事项</h3>
  </header>
  <div class="am-form am-panel-bd">
     请谨慎操作
  </div>
</section>
</div>
						<?php
						 break;
                         default:
                        ?>	
<div class="am-u-sm-12 am-u-md-6">
<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title">游戏</h3>
  </header>
  <div class="am-form am-panel-bd">
     <select id="doc-select-1" onchange="update_show(this.options[this.selectedIndex].value)">
	  <option value="initial">请选择想获取的游戏</option>
	<?php 
					$column=json_decode($steambot->getgamelist($_COOKIE['steamid']),true);
					 foreach ($column[3] as $n){
						 $num=array_search($n,$column[3]);
						 if($column[1][$num]=="753"){continue;}
						 echo "<option value='".$column[1][$num]."' >".$n."</option>";   
					}
						  ?>				
      </select>
      <span class="am-form-caret"></span>
  </div>
</section>
</div>
						<?php
					break;
					 }		
?>
<div class="am-u-sm-12">
<div id='alert_show'></div>
</div>
<div class="am-u-sm-12">
<div id='status_show'></div>
</div>
<?php					
/* if(isset($_COOKIE['order_context'])&&$_COOKIE['order_context']!=""){
							 $order_context=explode($_COOKIE['order_context'],"|");
							 $order_steamid=$order_context[0];
							 $order_outtime=$order_context[1];
							$sub_time=time()-$order_outtime;//计算过期时间与现在时间的差值
							if($sub_time>0){
								echo "<script>canceloffer(".$order_steamid.",1);</script>";
								setcookie("order_context","",time()+1);
							}else{
							 $context="select * from umarket_query_list where `order_steam_id`='{$order_steamid}'";
							 $res=$sql->fetch_array($sql->query($connect,$context));
							 $order_request_time=$res['order_time'];
							 $order_market_id=$res['order_market_id'];
							 $remain_time=301;
							 $remain_time=$order_outtime-$order_request_time;//计算剩余时间
								echo "<script>check_offer(".$order_market_id.",false,".$remain_time.");</script>";
							} 
						 } */
					 switch($m){
						 case 1:
						 //market inventory
                        ?>
	<div class="am-u-sm-12" id="takeback_inventory"></div>
	<form method="POST" id="market_inventory_item" name="market_inventory_item" action="index.php?mod=panel:inventory">				
  <ul data-am-widget="gallery" class="am-gallery am-u-sm-12 am-gallery-bordered"  >
  <div id="market_inventory_show"></div>
  </ul>
  </form>
                        <?php
						 break;
						 case 2:
						 //正在上架商品
						?>
						<div class="am-u-sm-12" id="market_inventory_button_show"></div>
	<form method="POST" id="market_inventory_item" name="market_inventory_item" action="index.php?mod=panel:inventory">				
  <ul data-am-widget="gallery" class="am-gallery am-u-sm-12 am-gallery-bordered"  >
  <div id="market_inventory_show"></div>
  </ul>
  </form>
  <script>
  window.onload=function (){get_market_inventory();}
  </script>
						<?php
						 break;
                         default:
						 //steam inventory
                        ?>	
						
<div class="am-u-sm-12" id="submit_inventory"></div>
<div class='am-u-sm-12' id='button_attention'><section class='am-panel am-panel-default'><div class='am-panel-bd'><span class='am-badge am-badge-warning am-radius am-text-lg'>您可点击上方的下拉选项,查询您的Steam背包内容</span></div></section></div>
<div class="am-u-sm-12" id="alert_show"></div>
<form method="POST" id="inventory_item" name="inventory_item" action="index.php?mod=panel:inventory">				
  <ul data-am-widget="gallery" class="am-gallery am-u-sm-12 am-gallery-bordered"  >
  <div id="inventory_show"></div>
  </ul>
  </form>
						<?php						
                         break;						  
					 }
					 
						  ?>		  
	<script>
	
function update_show(n){
		if(n=="initial"){
		$('#submit_inventory').remove();
		$('#inventory_item').remove();
		}
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { steamid: '<?php echo $_COOKIE['steamid']; ?>', gameid: n },
  beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 请求发起中...</div>";
			$('#alert_show').html(data);
			$('#button_attention').remove();
			//setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
  success:function(data){
	  console.log(data);
	 console.log($("div#ajax",data).html());
	$('#inventory_show').html($("div#ajax",data).html());
	$('#query_alert').alert('close');
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'><button type='button' class='am-btn am-btn-warning' onclick='submit_inventory()' >提取到商城</button><br></div></section>";
	$('#submit_inventory').html(template);
	  }
});
		
	}
	//更新用户在商城的库存
	function update_show_market(n){
		if(n=="initial"){
		$('#takeback_inventory').remove();
		$('#market_inventory_item').remove();
		}
	$.ajax({
  type: 'POST',
  async: false,
  url: 'index.php?mod=panel:inventory',
  data: { steamid: '<?php echo $_COOKIE['steamid']; ?>', gameid: n , mode: '1'},
  success:function(data){
	//console.log(data);
	//console.log($("div#ajax",data).html());
	$('#market_inventory_show').html($("div#ajax",data).html());
	var template="<section class='am-panel am-panel-default'><div class='am-panel-bd'><button type='button' class='am-btn am-btn-warning' onclick='takeback_inventory()' >取回背包</button>&nbsp;&nbsp;<button type='button' class='am-btn am-btn-success' onclick='submit_item()' >上架商品</button><br></div></section>";
	if($("div#response",data).text()=='0'){
	$('#takeback_inventory').html("");	
	}else {
	$('#takeback_inventory').html(template);	
	}
	  }
});
		
	}
	</script>
                        
                    
               </div>
</div>
            </div>
        </div>