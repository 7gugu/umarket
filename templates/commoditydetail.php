 <?php 
 /* @$max_price=$_POST['max_price'];	
		@$min_price=$_POST['min_price'];
		if(isset($_POST['max_price'])&&$max_price!=""){setcookie("max",$max_price,time()+3600);}
		if(isset($_POST['min_price'])&&$min_price!=""){setcookie("min",$min_price,time()+3600);}
		$context .= " BETWEEN {$_COOKIE['min']} AND {$_COOKIE['max']}";
		if(@isset($_POST['select'])&&@$_POST['select']=="price_down"){
		$context .= " ORDER BY `goods_price` DESC";//价格降序
		}else{
		$context .= " ORDER BY `goods_price` ASC";//价格升序
		}
		setcookie("query",$context,time()+3600);
		//排序系统 */ 
			if(!isset($_SESSION['goods_id'])||@$_SESSION['goods_id']==""){
setcookie("fail","传参错误",time()+5);
redirect("index.php?mod");	
}
	
 ?>
 
 
 <?php 
 global $sql;
 @$page=$_GET['page'];
 if (!isset($page)) {
        $page=1;
        }
$connect=$sql->connect();
$context="select * from umarket_goods_map_{$_SESSION['gameid']} where `goods_id`='{$_SESSION['goods_id']}'";
$result=$sql->query($connect,$context);
$res=$sql->fetch_array($result);
if($res['goods_img']!=""){
$img_path=$res['goods_img'];
}else{
$img_path="http://s.amazeui.org/media/i/demos/bing-1.jpg";
//https://steamcommunity-a.akamaihd.net/economy/image/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KW1Zwwo4NUX4oFJZEHLbXK9QlSPcUivB9aSQPAUuCq0vDAWFh4IBBYuIWtJAhr7PHHdSQMu93iwIbbxqWnNejQw2gB6ZEnjO-UoNrx0AHgqkZkN2HzJ4_DI1M3ZEaQpAYWJ6NyKA
}
 ?>
 <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-shopping-basket"></span> 商品详情
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g am-form tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php loadalert(); ?>
	<div class="am-u-sm-12 am-u-md-6">
	<div class="am-u-sm-centered">
	<img style="width:60%;height:50%;border:none;overflow:hidden;postion:center;" src="<?php echo $img_path; ?>" alt="..." class="am-img-thumbnail">
	</div>
	</div>
	<div class="am-u-sm-12 am-u-md-6">
	<span class="am-badge am-badge-secondary am-radius am-text-xl"><?php echo $res['goods_name']; ?></span>
                         <hr>	
							<button type='button'  onclick="javascript:history.back(-1)" class='am-btn' style="float:left;">继续购物</button>
							</div>

							
							
                        </div>
                    </div>
                </div>

            </div>
			<div class="tpl-portlet-components">
		<form class="am-form am-form-horizontal" method="POST" action="index.php?mod=commoditydetail" >
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-filter"></span>&nbsp;筛选器
                    </div>
                </div>
                <div class="tpl-block ">
				<input type='hidden' name='filter' id='filter'>
                    <div class="am-form  am-g tpl-amazeui-form">
    <div class="am-u-sm-12 am-u-md-6">
    <div class='am-panel am-panel-default'>
	<div class="am-panel-hd">价格梯度</div>
    <div class='am-panel-bd'>
	  <div class="am-form-group">
      <label for="max_price" class="am-u-sm-12 am-form-label">最高价</label>
      <input type="number" id="max_price" name="max_price" placeholder="最高价格" min="1" max="99999" value="<?php if(isset($_SESSION['max'])){echo $_SESSION['max'];} ?>">
	  <label for="min_price" class="am-u-sm-12 am-form-label">最低价</label>
      <input type="number" id="min_price" name="min_price" placeholder="最低价格" min="0" max="99998" value="<?php if(isset($_SESSION['min'])){echo $_SESSION['min'];} ?>">
       </div>
	</div>
	</div>   
	</div>     
	<div class="am-u-sm-12 am-u-md-6">
	<div class='am-panel am-panel-default'>
	<div class="am-panel-hd">排序方式</div>
    <div class='am-panel-bd'>
	 <div class="am-form-group">
      <select id="select" name="select">
	    <option value="price_default">默认降序</option>
        <option value="price_down">价格降序</option>
        <option value="price_up">价格升序</option>
      </select>
      <span class="am-form-caret"></span>
    </div>
	</div>
	</div> 
    <br>	
 <button type="submit" class="am-btn am-btn-default">提交</button>	
	</div>    
				</div> 
                        </div>
						</form>
                    </div>
		<div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-filter"></span>&nbsp;卖家列表
                    </div>
                </div>
                <div class="tpl-block ">
				  <div class="am-form  am-g tpl-amazeui-form">
   <table class="am-table  am-table-hover">
    <thead>
        <tr>
            <th>饰品样式</th>
            <th>卖家名称</th>
            <th>价格</th>
			<th>操作</th>
        </tr>
    </thead>
	<tbody>
				<?php
		$context="SELECT * FROM `umarket_goods_{$_SESSION['gameid']}` where `goods_id`={$_SESSION['goods_id']}";
		@$max_price=$_POST['max_price'];	
		@$min_price=$_POST['min_price'];
		@$select=$_POST['select'];
		if(isset($_POST['select'])&&$select!=""){$_SESSION["select"]=$select;}
		if(isset($_POST['max_price'])&&$max_price!=""){$_SESSION["max"]=$max_price;}
		if(!isset($_SESSION['max'])){$_SESSION["max"]="99999";}
		if(isset($_POST['min_price'])&&$min_price!=""){$_SESSION["min"]=$min_price;}
		if(!isset($_SESSION['min'])){$_SESSION["min"]="0";}
		$context .= " and `goods_price` BETWEEN {$_SESSION['min']} AND {$_SESSION['max']}";
		if(@isset($_SESSION["select"])&&@$_SESSION["select"]=="price_down"){
		$context .= " ORDER BY `goods_price` DESC";//价格降序
		}elseif(@isset($_SESSION["select"])&&@$_SESSION["select"]=="price_up"){
		$context .= " ORDER BY `goods_price` ASC";//价格升序
		}
		$_SESSION["query"]=$context;
		$context=str_replace("*","count(*)",$context);//合成分页查询的sql
		$result=$sql->query($connect,$context);
		$res=$sql->fetch_array($result);
		$perNumber=20;
		$totalNumber=$res[0];
        $totalPage=ceil($totalNumber/$perNumber);//四舍五入取一个亿作为总页数
        $startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
        $context= $_SESSION["query"]." limit $startCount,$perNumber";//合成查询语句
		$result=$sql->query($connect,$context); //根据前面的计算出开始的记录和记录数
        if($totalNumber==0){//无商品
			?>
		 <tr>
            <td></td>
            <td>商城暂未有人出售该饰品/没有符合您的条件的饰品呢~</td>
            <td>你可以前往库存上架该饰品</td>
			 <td> <a href="index.php?mod=panel:inventory" class="am-badge am-badge-success am-radius am-text-lg">前往库存</a></td>
        </tr>	
		<?php 
		}else{
		while($r=$sql->fetch_array($result)){
			$goods_context=json_decode($r['goods_context'],true);
			$goods_name=$goods_context['goods_name'];
			if(array_search($r['item_id'],$_SESSION['cart'])!=FALSE){continue;}
			?>	
		<tr>
            <td>
			<img style="width:90px;height:60px;border:none;overflow:hidden;" src="<?php echo $img_path;?>" alt="..." class="am-img-thumbnail">
			<span class="am-badge am-badge-danger am-radius am-text-xl"><?php echo $goods_name; ?></span>
			</td>
            <td><span class="am-badge am-radius am-text-md"><?php echo $r['goods_seller_id']; ?></span></td>
            <td><span class="am-badge am-badge-warning am-radius am-text-xl"><span class="am-icon-rmb"></span> <?php echo $r['goods_price']; ?></span></td>
			 <td><a href="index.php?mod=user:shoppingcart&item_id=<?php echo $r['item_id']; ?>" class="am-badge am-badge-success am-radius am-text-xl">加入购物车</a></td>
        </tr>
		<?php
		}
		}
		?>
	 </tbody>
</table>	
	</div>    
	<div class="am-cf">
	  共<?php echo $totalNumber; ?>条记录
                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">

											<?php
if ($page != 1) { 
?>
                  <li><a href="index.php?mod=commoditydetail&page=<?php echo $page-1;?>">«</a></li>
<?php }else{?>
 <li class="am-disabled"><a href="index.php?mod=commoditydetail&page=<?php echo $page-1;?>">«</a></li>
<?php }
if($totalPage<5){
for ($i=$page; $i <= $totalPage; $i++){
    if ($page == $i) {
        echo "<li class='am-active'><a href='index.php?mod=commoditydetail&page=$i'>$i</a></li>";
    }else{
        echo "<li><a href='index.php?mod=commoditydetail&page=$i'>$i</a></li>";
    }
}	
}else{
for ($i=$page; $i < $page+5; $i++){
    if ($page == $i) {
        echo "<li class='am-active'><a href='index.php?mod=commoditydetail&page=$i'>$i</a></li>";
    }else{
        echo "<li><a href='index.php?mod=commoditydetail&page=$i'>$i</a></li>";
    }
}
}
if ($page<$totalPage) {
?>
<li><a href="index.php?mod=commoditydetail&page=<?php echo $page+1;?>">»</a></li>
<?php }else{?>
<li class="am-disabled"><a href="index.php?mod=commoditydetail&page=<?php echo $page+1;?>">»</a></li>
<?php }?>         
                                        </ul>
                             </div>
                                </div>	
				</div> 
                        </div>
                    </div>
		