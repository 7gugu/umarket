  <?php  
global $sql;
$connect=$sql->connect();
$column_name="Untitled";
if(isset($_SESSION['gameid'])){ 
	//记录搜索的类型
$context="select * from umarket_option where `option_name`='column_{$_SESSION['gameid']}'";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
$option_context=unserialize($res['option_context']);
$column_name=$option_context['column_name'];
$column_widgets_option=explode(":",$option_context['column_widgets_option']);
$page_status=$res['option_status'];
if($page_status==0){
	echo " <div class='tpl-portlet-components'>
                <div class='portlet-title'>
                    <div class='caption font-green bold'>
                        <span class='am-icon-pause'></span>&nbsp;警告&nbsp;
                    </div>
                </div>
                <div class='tpl-block '>
				<div class='am-panel am-panel-danger'>
    <div class='am-panel-bd'> &nbsp;<span class='am-badge am-badge-warning am-text-xl am-radius'><?php echo $column_name; ?></span>&nbsp;<span class='am-badge am-badge-secondary am-text-xl am-radius'>很抱歉的通知您,该游戏已停止交易功能</span></div></div>             
                </div>
            </div>";
}
}else{
setcookie("fail","传参错误",time()+5);
redirect("index.php?mod");	
}
  ?>
  <div class="tpl-content-wrapper">
  <?php 
  loadalert();  
  foreach($column_widgets_option as $widget){
	switch($widget){
		case 'slider':
		echo "<div data-am-widget='slider' style='height:300px; border:none; overflow:hidden;' class='am-hide-md-down am-slider am-slider-a5' data-am-slider=\"{'directionNav':false}\" ><ul class='am-slides'>";
		$context="select * from umarket_option where `option_name`='slider_{$_SESSION['gameid']}'";
		$res=$sql->query($connect,$context);
        $res=$sql->fetch_array($res);
		$imgpath=explode(",",$res['option_context']);
		if($imgpath[0]!=""){
		foreach($imgpath as $i){
		echo "<li>";	
		echo "<img src='{$i}'>";
		echo "</li>";	
		}
		}else{
		echo "<li><img src='http://s.amazeui.org/media/i/demos/bing-1.jpg'></li>";
		}
		echo "</ul></div></br>";
		break;
		case 'quote':
		$context="select * from umarket_option where `option_name`='quote_{$_SESSION['gameid']}'";
		$res=$sql->query($connect,$context);
        $res=$sql->fetch_array($res);
		if($res["option_context"]==""){
			$context="服务商很懒呢~,啥都没写";
		}else{
			$context=$res["option_context"];
		}
		echo "<div class='am-panel am-panel-default'>
    <div class='am-panel-bd'>{$context}</div>
</div>
        ";
		break;
		case 'filter':
		?>
		<div class="tpl-portlet-components">
		<form class="am-form am-form-horizontal" method="POST" action="index.php?mod=commoditylist" >
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-filter"></span>&nbsp;筛选器
                    </div>
                </div>
                <div class="tpl-block ">
				<input type='hidden' name='filter' id='filter'>
                    <div class="am-form  am-g tpl-amazeui-form">
    <div class="am-u-sm-12 am-u-md-4">
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
	<div class="am-u-sm-12 am-u-md-4">
	<div class='am-panel am-panel-default'>
	<div class="am-panel-hd">装备类型</div>
    <div class='am-panel-bd'>
	<a class="am-badge am-badge-primary am-text-lg am-radius" href="index.php?mod=commoditylist&type=0">所有</a>
	<?php
	if($_SESSION['gameid']=="730"||$_SESSION['gameid']=="570"){	
	?>
<a class="am-badge am-badge-secondary am-text-lg am-radius" href="index.php?mod=commoditylist&type=1" >枪械</a>
<a class="am-badge am-badge-success am-text-lg am-radius" href="index.php?mod=commoditylist&type=2">帽子</a>
<a class="am-badge am-badge-warning am-text-lg am-radius" href="index.php?mod=commoditylist&type=3">衬衫</a>
<a class="am-badge am-badge-danger am-text-lg am-radius" href="index.php?mod=commoditylist&type=4">裤子</a>
	<?php 
	}
	?>
	<hr>
	 <input type="text" id="item_name" name="item_name" placeholder="饰品名称" value="">
	</div>
	</div>   
	</div>   
	<div class="am-u-sm-12 am-u-md-4">
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
		<?php 
		break;
		case 'list':
		?>
		 <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-list"></span>&nbsp;商品列表
                    </div>
                </div>
         <div class="am-form am-g">   
		<?php
		if(isset($_GET['type'])){$_SESSION['filter_type']=$_GET['type'];}elseif(!isset($_SESSION['filter_type'])){$_SESSION['filter_type']="0";}
		$context ="SELECT * FROM  `umarket_goods_map_{$_SESSION['gameid']}` where ";
		if($_SESSION['filter_type']!=0){$context .="`goods_type`='{$_SESSION['filter_type']}'  and";}
	    $context .=" `goods_min_price`";
		@$max_price=htmlspecialchars($_POST['max_price']);	
		@$min_price=htmlspecialchars($_POST['min_price']);
		if(isset($_POST['max_price'])&&$max_price!=""){$_SESSION["max"]=$max_price;}
		if(!isset($_SESSION['max'])){$_SESSION["max"]="99999";}
		if(isset($_POST['min_price'])&&$min_price!=""){$_SESSION["min"]=$min_price;}
		if(!isset($_SESSION['min'])){$_SESSION["min"]="0";}
		$context .= " BETWEEN {$_SESSION['min']} AND {$_SESSION['max']}";
		if(isset($_POST['item_name'])){$goods_name=htmlspecialchars($_POST['item_name']);$context .=" and `goods_name` like '%{$goods_name}%' ";}
		if(@isset($_POST['select'])&&@$_POST['select']=="price_down"){
		$context .= " ORDER BY `goods_max_price` DESC";//价格降序
		}elseif(@isset($_POST['select'])&&@$_POST['select']=="price_up"){
		$context .= " ORDER BY `goods_max_price` ASC";//价格升序
		}
		$_SESSION["query"]=$context;
		$context="SELECT count(*) FROM `umarket_goods_map_{$_SESSION['gameid']}`";
		$result=$sql->query($connect,$context);
		$res=$sql->fetch_array($result);
		$perNumber=20;
        @$page=$_GET['page'];
		$totalNumber=$res[0];
        $totalPage=ceil($totalNumber/$perNumber);//四舍五入取一个亿作为总页数
        if (!isset($page)) {
        $page=1;
        }
        $startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
        $context= $_SESSION["query"]." limit $startCount,$perNumber";//合成查询语句
		$result=$sql->query($connect,$context); //根据前面的计算出开始的记录和记录数
        if($totalNumber==0){//无商品
			?>
	<div class="am-u-sm-4 am-u-md-3">
	<div class='am-panel am-panel-default'>
	<div class="am-panel-hd">通告</div>
    <div class='am-panel-bd'>
	 <div class="am-form-group">
     <div class="am-u-sm-centered">
	 <img style="width:190px;height:120px;border:none;overflow:hidden;" src="https://steamcommunity-a.akamaihd.net/economy/image/-9a81dlWLwJ2UUGcVs_nsVtzdOEdtWwKGZZLQHTxDZ7I56KW1Zwwo4NUX4oFJZEHLbXK9QlSPcUivB9aSQPAUuCq0vDAWFh4IBBYuIWtJAhr7PHHdSQMu93iwIbbxqWnNejQw2gB6ZEnjO-UoNrx0AHgqkZkN2HzJ4_DI1M3ZEaQpAYWJ6NyKA" alt="..." class="am-img-thumbnail">
	 <p>该栏目还没人上架物品呢~,你可前往"我的库存"上架物品</p>
	 <a class="am-badge am-badge-success am-radius am-text-lg" href="index.php?mod=panel:inventory">前往我的库存</a>
    </div>
    </div>
	</div>
	</div> 	
	</div>
			<?php
		}elseif($result==null){
	echo " <div class='tpl-portlet-components'>
                <div class='portlet-title'>
                    <div class='caption font-green bold'>
                        <span class='am-icon-pause'></span>&nbsp;抱歉&nbsp;
                    </div>
                </div>
                <div class='tpl-block '>
				<div class='am-panel am-panel-danger'>
    <div class='am-panel-bd'> &nbsp;<span class='am-badge am-badge-secondary am-text-xl am-radius'>貌似没有符合你的要求的商品</span></div></div>             
                </div>
            </div>";
		}else{
		while($r=$sql->fetch_array($result)){
			?>	
		<div class="am-u-sm-4 am-u-md-3 am-u-end">
	<div class='am-panel am-panel-default'>
	<div class="am-panel-hd"><?php echo $r['goods_name'];?></div>
    <div class='am-panel-bd'>
	 <div class="am-form-group">
     <div class="am-u-sm-centered">
	 <img style="width:190px;height:120px;border:none;overflow:hidden;" src="<?php echo $r['goods_img'];?>" alt="..." class="am-img-thumbnail">
	 <a href="index.php?mod=commoditydetail&goods_id=<?php echo $r['goods_id'] ?>" class="am-badge am-badge-warning am-radius am-text-lg">查看详情</a>
    </div>
    </div>
	</div>
	</div> 	
	</div> 	
		<?php
		}
		}
		?>
    </div>	
	<div class="am-cf">
	  共<?php echo $totalNumber; ?>条记录
                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">

											<?php
if ($page != 1) { 
?>
                  <li><a href="index.php?mod=commoditylist&page=<?php echo $page-1;?>">«</a></li>
<?php }else{?>
 <li class="am-disabled"><a href="index.php?mod=commoditylist&page=<?php echo $page-1;?>">«</a></li>
<?php }
if($totalPage<5){
for ($i=$page; $i <= $totalPage; $i++){
    if ($page == $i) {
        echo "<li class='am-active'><a href='index.php?mod=commoditylist&page=$i'>$i</a></li>";
    }else{
        echo "<li><a href='index.php?mod=commoditylist&page=$i'>$i</a></li>";
    }
}	
}else{
for ($i=$page; $i < $page+5; $i++){
    if ($page == $i) {
        echo "<li class='am-active'><a href='index.php?mod=commoditylist&page=$i'>$i</a></li>";
    }else{
        echo "<li><a href='index.php?mod=commoditylist&page=$i'>$i</a></li>";
    }
}
}
if ($page<$totalPage) {
?>
<li><a href="index.php?mod=commoditylist&page=<?php echo $page+1;?>">»</a></li>
<?php }else{?>
<li class="am-disabled"><a href="index.php?mod=commoditylist&page=<?php echo $page+1;?>">»</a></li>
<?php }?>         
                                        </ul>
                                    </div>
                                </div>	
	</div>			
                       
					
		<?php
		break;
		case 'iframe':
		$context="select * from umarket_option where `option_name`='iframe_{$_SESSION['gameid']}'";
		$res=$sql->query($connect,$context);
        $res=$sql->fetch_array($res);
		if($res["option_context"]==""){
			$context="服务商很乖呢~,啥广告都没上传";
		}else{
			$context=$res["option_context"];
		}
		echo "<div class='am-panel am-panel-default'>
    <div class='am-panel-bd'>{$context}</div>
</div>
        ";
		break;
		
	}
  }
  ?>
			

                </div>


