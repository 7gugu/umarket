 <?php global $count; ?>
 <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-shopping-basket"></span> 购物车
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-12">
						<?php 
						loadalert(); 
						global $sql;
						$connect=$sql->connect();
						if(isset($_GET['item_id'])&&@$_GET['item_id']!=""){
						$session_cart=$_SESSION['cart'];
						$item_id=$_GET['item_id'];
						$context = "select * from umarket_goods_{$_SESSION['gameid']} where `item_id`='{$_GET['item_id']}'";
	                    $res=$sql->query($connect,$context);
	                    $res=$sql->fetch_array($res);
						$goods_id=$res['goods_id'];
						$goods_context=json_decode($res['goods_context'],true);
						$goods_price=$res['goods_price'];
						$context = "select * from umarket_goods_map_{$_SESSION['gameid']} where `goods_id`='{$goods_id}'";
	                    $res=$sql->query($connect,$context);
	                    $res=$sql->fetch_array($res);
						$img_path=$res['goods_img'];
						$cart=array('appid'=>$_SESSION['gameid'],'item_id'=>$item_id,'goods_id'=>$goods_id,'item_name'=>$goods_context['goods_name'],'item_count'=>'1','item_price'=>$goods_price,'addtion'=>'','img_path'=>$img_path);
						array_push($session_cart,$cart);
						$_SESSION['cart']=$session_cart;
						}
						if(isset($_GET['del'])){
						if($_GET['del']==""){setcookie("warning","出现技术问题,请联系管理员解决",time()+5);redirect("index.php?mod");}
						$element="";
						$element=$_GET['del'];
						$count=count($_SESSION['cart']);
						for($i=0;$i<$count;$i++){
						$key=array_search($element,$_SESSION['cart'][$i]);
						if($key!=false){unset($_SESSION['cart'][$i]);setcookie("suc","操作成功",time()+5); }else {setcookie("warning","操作失败",time()+5);}	
						}
						redirect("index.php?mod=user:shoppingcart");	
						}
						if($_SESSION['status']=="0" && $count==0){
						setcookie("warning","请登录后再访问",time()+5);
						redirect("index.php?mod");	
						} 
						if(isset($_POST['steamid'])){				
						$cart="";
						$steamid=$_POST['steamid'];	
						$phone=$_POST['phone'];	
						$email=$_POST['email'];
						$payment=$_POST['payment_method'];
						$cart=serialize($_SESSION['cart']);//购物车内的数组序列化
						$context = "select * from umarket_order order by order_id DESC limit 1 ";
	                    $res=$sql->query($connect,$context);
	                    $res=$sql->fetch_array($res);
						$orderid=0;if($res!=false){$orderid=$res[0];}$orderid++;
						$context = "select * from umarket_account where `username`='{$_COOKIE['username']}' ";
	                    $res=$sql->query($connect,$context);
	                    $res=$sql->fetch_array($res);
						$outtime=time()+1800;
						$orderserect=base64_encode(md5("rxsg".$outtime));
						$partner="";
                        preg_match('/partner=([\d]+)/', $res['tradelink'], $partner);
						$partner=str_ireplace("partner=","",$partner[0]);
						$order_time=date("Y-m-d h:i:sa");
						$context="insert into umarket_order(order_id,order_state,payment_method,order_context,order_request_steamid,order_email,order_phone,order_time)values('{$orderid}','0',{$payment},'{$cart}','','{$email}','{$phone}','{$order_time}')";
						$res=$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","订单创建成功!",time()+35);					
						redirect("index.php?mod=user:payment:1&orderid={$orderid}");
						}else{
						setcookie("warning","订单创建失败,请联再试试",time()+5);
						redirect("index.php?mod=user:shoppingcart");		
						}
						$sql->close($connect);
						}
						?>
                <table class="am-table am-table am-table-hover">
    <thead>
        <tr>
            <th>商品</th>
            <th>商品名字</th>
			<th>备注</th>
           <th>数量</th><th>单价</th><th>总价</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
	<?php 
	$total=0;
	if($_SESSION['cart']!=array()){
	foreach($_SESSION['cart'] as $i){
	$total=$total+$i['item_price']*$i['item_count'];
	?>
        <tr>
		    <td><img style="width:90px;height:60px;border:none;overflow:hidden;" src="https://steamcommunity-a.akamaihd.net/economy/image/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KW1Zwwo4NUX4oFJZEHLbXK9QlSPcUivB9aSQPAUuCq0vDAWFh4IBBYuIWtJAhr7PHHdSQMu93iwIbbxqWnNejQw2gB6ZEnjO-UoNrx0AHgqkZkN2HzJ4_DI1M3ZEaQpAYWJ6NyKA" alt="..." class="am-img-thumbnail"></td>
            <td><h2><strong><?php echo $i['item_name']; ?></strong></h2></td> 
	<td><span class="label label-warning"><?php if($i['addtion']==''||$i['addtion']==null){echo '无';}else{echo $i['addtion'];}?></span></a></td>
            <td><span class="label label-info"><?php echo $i['item_count']; ?>个</span></td>
            <td><span class="label label-success"><span class="am-icon-rmb"></span> <?php echo $i['item_price']; ?></span></td>
            <td><span class="label label-success"><span class="am-icon-rmb"></span> <?php echo $i['item_price']*$i['item_count']; ?></span></td>
            <td><a href="index.php?mod=user:shoppingcart&del=<?php echo $i['item_id']; ?>"><span class="label label-danger"><span class="am-icon-times"></span> 删除</span></a></td>
        </tr>
	<?php }}else{ ?>
	 <tr>
		    <td></td>
            <td><h2><strong>购物车空空如也呢!</strong></h2></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
	<?php } ?>
    </tbody>
</table>
<button type='button'  onclick="javascript:window.location.href='index.php?mod=commoditylist'" class='am-btn'>继续购物</button>
<hr><br>
<form class="am-form am-form-horizontal" name="login" method="post" <?php if($_SESSION['cart']!=array()){echo " action='index.php?mod=user:shoppingcart'";}?>>
	<div class="am-u-sm-12 am-u-md-6">
	 <div class="tpl-content-page-title">订单信息</div><br>
                             <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">邮箱信息</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" name="email" placeholder="请输入购买人的邮箱地址" value="<?php if(isset($_COOKIE['email']))echo $_COOKIE['email'];?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label  class="am-u-sm-3 am-form-label">SteamID</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" name="steamid" placeholder="请输入购买人的SteamID" value="<?php if(isset($_COOKIE['steamid']))echo $_COOKIE['steamid'];?>">
                                    </div>
                                </div>
								<div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label">手机号</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" name="phone" placeholder="请输入购买人的手机号" value="<?php if(isset($_COOKIE['phone']))echo $_COOKIE['phone'];?>">
                                    </div>
                                </div>
							</div>
							<div class="am-u-sm-12 am-u-md-6">
	 <div class="tpl-content-page-title">支付方式</div><br>
	 <script>
							function updatefee(){
							var original_fee=parseFloat($('#original').html());
                            var fee=parseFloat(0.02);
							var bank_fee=parseFloat(0.00);
							if($("input[name='payment_method']:checked").val()=='2'){bank_fee=eval(original_fee*fee);}
                            var final_fee;
                            final_fee=eval(original_fee+bank_fee);
                            $('#final_fee').html(final_fee);	
							$('#bank_fee').html(bank_fee);						
							}		
							</script>
                            <ul>  
                                     <li>
                                    <label class="com-checkbox">
                                        <input type="radio" name="payment_method" id="payment_method" value="1" onclick="updatefee()" checked>
                                            Uwallet钱包余额支付。
                                    </label>
                                </li> 							
                                <li>
                                    <label class="com-checkbox">
                                        <input type="radio" name="payment_method" id="payment_method"  value="2" onclick="updatefee()" >
                                        <img class="pics" src="./assets/img/alipay.png" alt="">支付宝&nbsp;中国最大的第三方支付。
                                    </label>
                                </li> 
                            </ul>
							<br><br>
							</div>
							<div class="am-u-sm-12 am-u-md-6">
	 <div class="tpl-content-page-title">结算金额</div>
	 <br>
                     					
                             <div class="am-panel am-panel-default">
							 <div class="am-panel-bd">
                                  <span class="label label-info">原始金额:</span> <span class="label label-success"><span class="am-icon-rmb"></span> <span id="original"><?php echo $total;?></span></span>
  
                                  <span class="label label-info">手续费:</span> <span class="label label-success"><span class="am-icon-rmb"></span> <span id="bank_fee">0.00</span></span>
  
                                  <span class="label label-info">实际金额:</span> <span class="label label-success"><span class="am-icon-rmb"></span> <span id="final_fee"><?php echo $total; ?></span></span>
</div></div>
								
							</div>
							
							 <button type="submit" style="padding-left:5px" class="am-btn am-btn-success am-btn-block <?php if($_SESSION['cart']==array()){echo "am-disabled";}?>">支付</button>
							</form>
                        </div>
                    </div>
                </div>

            </div>
        </div>