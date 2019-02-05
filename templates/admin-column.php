<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-key"></span> 栏目管理
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-12">
						<?php 
						loadalert();
						global $mode;
						global $sql;
						$connect=$sql->connect();
						$parse = explode(':',$mode);
						if(isset($parse[2])){$function=$parse[2];}else{$function="";}
						if(isset($parse[3])){$column_id=$parse[3];}else{$column_id=0;}
						switch ($function) {
						case 'create'://创建栏目
						if(isset($_POST['column_name'])){
						if($_POST['column_name']!=""){$column_name=$_POST['column_name'];}else{$column_name="缺省";}	
						if($_POST['game_id']!=""){$game_id=$_POST['game_id'];}else{$game_id="0";}
						$context="select count(*) from umarket_option where option_name='column_{$game_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res[0]!=0){setcookie("warning","游戏ID不可重复啦!",time()+5);redirect("index.php?mod=admin:column");}
						$column_widgets_option=serialize(array('slider','quote','filter','list','iframe'));
						$option_context=serialize(array('column_name'=>$column_name,'column_game_id'=>$game_id,'column_widgets_option'=>$column_widgets_option));
						$context="insert into umarket_option(option_name,option_context,option_status)values('column_{$game_id}','{$option_context}','0')";
						$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						$context="select count(*) from umarket_option where `option_name`='column_index'";//检索栏目的索引
						$res=$sql->fetch_array($sql->query($connect,$context));
						$index_number=$res[0];
						if($index_number==0){
						//新建索引
						$index_array=array();
						array_push($index_array,$game_id);
						$index_array=serialize($index_array);
						$context="insert into umarket_option(option_name,option_context,option_status)values('column_index','{$index_array}','0')";
						$sql->query($connect,$context);
						}else{
						//插入索引
						$context="select count(*) from umarket_option where `option_name`='column_index'";
						$res=$sql->fetch_array($sql->query($connect,$context));
						if($res[0]==1){
						$context="select * from umarket_option where `option_name`='column_index'";
						$res=$sql->fetch_array($sql->query($connect,$context));
						$column_array=unserialize($res['option_context']);
						if(array_search($game_id,$column_array)!=false){
							setcookie("warning","索引已存在,系统已跳过",time()+5);
						}else{
							array_push($column_array,$game_id);
						}
						$column_array=serialize($column_array);
						$context="update umarket_option set `option_context`='{$column_array}' where `option_name`='column_index'";
						$sql->query($connect,$context);
						}
						}
						//创建栏目对应游戏ID的索引数据表
						$context="CREATE TABLE IF NOT EXISTS `umarket_goods_map_".$game_id."` (`map_id` int(11) NOT NULL,`goods_id` int(11) NOT NULL,`goods_name` text NOT NULL,`goods_img` text NOT NULL,`goods_min_price` int(11) NOT NULL,`goods_count` int(11) NOT NULL,`goods_type` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='存储ID游戏物品种类'";
						$sql->query($connect,$context);
						//创建栏目对应游戏ID的库存数据表
						$context="CREATE TABLE IF NOT EXISTS `umarket_goods_".$game_id."` (`item_id` int(11) NOT NULL DEFAULT '0' COMMENT '货品ID',`goods_context` text NOT NULL COMMENT '货品信息',`goods_price` float NOT NULL DEFAULT '99999' COMMENT '货品价格',`goods_count` int(11) NOT NULL DEFAULT '1' COMMENT '货品个数',`goods_seller_id` text NOT NULL COMMENT '商品出售者STEAM-ID',`goods_id` int(11) NOT NULL COMMENT '饰品的steamid') ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='上架货品'";
						$sql->query($connect,$context);
						setcookie("suc","创建成功啦!",time()+10);
						redirect("index.php?mod=admin:column");
						}else{
						setcookie("warning","创建失败,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");		
						}
						}
						?>
						<script type="text/javascript">function check(form){if(form.column_name.value==''){alert('栏目名称不能为空！');form.column_name.focus();return false;}if(form.gameid.value==''){alert('栏目游戏ID不能为空！');form.game_id.focus();return false;}return true;}</script>
                            <form class="am-form am-form-horizontal" method="post" action="index.php?mod=admin:column:create" onSubmit="return check(this)">
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">栏目名称 / Column-name</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" id="column_name" name="column_name" placeholder="英文游戏缩写|中文名">
                                        <small>建议少于15个字。</small>
                                    </div>
                                </div>
                                 <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">栏目游戏ID / Game-ID</label>
                                    <div class="am-u-sm-9">
                                        <input type="number" id="game_id" name="game_id" placeholder="">
                                        <small>输入该栏目承载的饰品对应的游戏ID。</small>
                                    </div>
                                </div>
								 <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">组件管理 / Name</label>
                                    <div class="am-u-sm-9">
                                       <ul class="am-list am-list-static am-list-border">
                                       <li>头图</li>
									   <li>文本描述</li>
									   <li>筛选器</li>
									   <li>商品列表</li>
									   <li>广告位</li>
                                       <ul>
                                        <small>创建后即可对其进行自定义管理。</small>
                                    </div>
                                </div>
                              
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">下一步</button>
										&nbsp;
										<button type="submit" class="am-btn am-btn-default" onclick="javascript:window.location.href='index.php?mod=admin:column'">放弃创建</button>
                                    </div>
                                </div>
                            </form>
						<?php
						break;
						case 'del'://删除栏目
						if($column_id==""||$column_id==0){setcookie("warning","传参错误,请稍后再试",time()+5);redirect("index.php?mod=admin:column");}
						$context="select count(*) from umarket_option where option_name='column_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						//var_dump($res);
						if($res[0]!=0){
						$context="delete from umarket_option where option_name='column_{$column_id}'";
						echo $context;
						$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","删除成功",time()+5);
						redirect("index.php?mod=admin:column");
						}else{
						setcookie("warning","删除失败,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");		
						}
						}else{
						setcookie("warning","传参错误,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");		
						}
						break;
						case 'manage'://管理栏目
						//获取栏目组件参数
						if(isset($_POST['old_column_name'])){
						$column_id=$_POST['old_column_name'];	
						}
						//获取头图参数
						$context="select * from umarket_option where option_name='slider_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$sql_slider=$res['option_context'];	
						}else{
						$sql_slider="";	
						}
						//获取文字描述
						$context="select * from umarket_option where option_name='quote_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$sql_quote=$res['option_context'];	
						}else{
						$sql_quote="";	
						}
						//获取列表范围
						$context="select * from umarket_option where option_name='list_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
                        $list_count=$res['option_context'];				
						}else{
						$list_count="1";
						}
						//获取广告位数据
						$context="select * from umarket_option where option_name='iframe_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$sql_iframe=$res['option_context'];	
						}else{
						$sql_iframe="";	
						}
						
						//获取栏目主参数
						$context="select * from umarket_option where option_name='column_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						$option_context=unserialize($res['option_context']);
						if(isset($_GET['save'])){//保存配置
						//获取组件参数
						//头图
						if(isset($_POST['w_slider'])){
						$context="select * from umarket_option where option_name='slider_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$context="update umarket_option set `option_context`='{$_POST['w_slider']}'  where `option_name`='slider_{$column_id}'";	
						}else{
						$context="insert into umarket_option(option_name,option_context,option_status)values('slider_{$column_id}','{$_POST['w_slider']}','0')";	
						}
							$sql->query($connect,$context);
						}
						//文字描述
						if(isset($_POST['w_quote'])){
						$context="select * from umarket_option where option_name='quote_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						$quote=htmlspecialchars($_POST['w_quote']);
						if($res!=false){
						$context="update umarket_option set `option_context`='{$quote}'  where `option_name`='quote_{$column_id}'";	
						}else{
						$context="insert into umarket_option(option_name,option_context,option_status)values('quote_{$column_id}','{$quote}','0')";	
						}
						
						$sql->query($connect,$context);
						}
						//列表范围
						if(isset($_POST['w_list'])){
						$context="select * from umarket_option where option_name='list_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						$w_list=$_POST['w_list'];
						if($w_list==""){$w_list=30;}
						if($res!=false){
						$context="update umarket_option set `option_context`='{$w_list}'  where `option_name`='list_{$column_id}'";	
						}else{
						$context="insert into umarket_option(option_name,option_context,option_status)values('list_{$column_id}','30','0')";	
						}
						$sql->query($connect,$context);
						}
						//广告位
						if(isset($_POST['iframe'])){
						$context="select * from umarket_option where option_name='iframe_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						if($res!=false){
						$context="update umarket_option set `option_context`='{$_POST['w_iframe']}'  where `option_name`='iframe_{$column_id}'";	
						}else{
						$context="insert into umarket_option(option_name,option_context,option_status)values('iframe_{$column_id}','{$_POST['w_iframe']}','0')";	
						}
						$sql->query($connect,$context);
						}
				
						//获取栏目参数的值,并按照组件1:组件2:组件3进行处理排序
						$column_name=$_POST['column_name'];
						$arr=$_POST;
						$game_id=$_POST['game_id'];
						//post过来的数据除了设置项的数据,还有排序信息,此处是使用排序的方法使无关排序的数据推出栈
						$arr['column_name']=$arr['game_id']=$arr['w_slider']=$arr['w_quote']=$arr['w_list']=$arr['w_iframe']=9999;
						asort($arr);
						array_pop($arr);
						array_pop($arr);
						array_pop($arr);
						array_pop($arr);
						array_pop($arr);
						array_pop($arr);
						array_pop($arr);
						arsort($arr);
						$count=array_count_values($arr);					
					if(array_key_exists('0',$count)){
					if($count['0']!=0){
						for($i=0;$i<$count['0'];$i++)
						{
						array_pop($arr);	
						}
						}
						}
						asort($arr);
						$arr=array_keys($arr);
						$option="hello";
						foreach($arr as $i){
						$option .=":".$i;	
						}
						$option=str_replace("hello:","",$option);
						//echo $option;
						$option_context=serialize(array('column_name'=>$column_name,'column_game_id'=>$game_id,'column_widgets_option'=>$option));
						$context="select * from umarket_option where option_name='column_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->fetch_array($res);
						//创建栏目对应游戏ID的索引数据表
						$context="CREATE TABLE IF NOT EXISTS `umarket_goods_map_".$game_id."` (`map_id` int(11) NOT NULL,`goods_id` int(11) NOT NULL,`goods_name` text NOT NULL,`goods_img` text NOT NULL,`goods_min_price` int(11) NOT NULL,`goods_count` int(11) NOT NULL,`goods_type` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='存储ID游戏物品种类'";
						$sql->query($connect,$context);
						//创建栏目对应游戏ID的库存数据表
						$context="CREATE TABLE IF NOT EXISTS `umarket_goods_".$game_id."` (`item_id` int(11) NOT NULL DEFAULT '0' COMMENT '货品ID',`goods_context` text NOT NULL COMMENT '货品信息',`goods_price` float NOT NULL DEFAULT '99999' COMMENT '货品价格',`goods_count` int(11) NOT NULL DEFAULT '1' COMMENT '货品个数',`goods_seller_id` text NOT NULL COMMENT '商品出售者STEAM-ID',`goods_id` int(11) NOT NULL COMMENT '饰品的steamid') ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='上架货品'";
						$sql->query($connect,$context);
						if($res!=false){
						//更新数据,删除旧数据	
						$context="update umarket_option set `option_context`='{$option_context}' , `option_name`='column_{$game_id}' where `option_name`='column_{$column_id}'";
						$res=$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","更新成功",time()+5);
						redirect("index.php?mod=admin:column");
						}else{
						setcookie("warning","更新失败/没数据发生更改OuO,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");		
						}
						}else{
						//插入新数据
						$context="insert into umarket_option(option_name,option_context,option_status)values('column_{$game_id}','{$option_context}','0')";
						$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","更新成功!",time()+5);
						redirect("index.php?mod=admin:column");
						}else{
						setcookie("warning","更新失败/没数据发生更改OuO,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");		
						}
						}
						die;
						}
						?>
						<style>
						.widget-area{list-style-type: none; margin: 0; padding: 0; float: left; margin-right: 10px; background: #eee; padding: 5px; width:100%;}
						</style>
						<form class="am-form am-form-horizontal" method="post" action="index.php?mod=admin:column:manage&save" onSubmit="update_list()">
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-md-2  am-u-sm-12 am-form-label">栏目名称</label>
                                    <div class="am-u-md-10">
                                        <input type="text" id="column_name" name="column_name" placeholder="英文游戏缩写|中文名" value="<?php echo $option_context['column_name']; ?>">
                                        <small>建议少于15个字。</small>
                                    </div>
                                </div>
                                 <div class="am-form-group">
                                    <label for="user-name" class="am-u-md-2 am-u-sm-12 am-form-label">栏目游戏ID</label>
                                    <div class="am-u-md-10  am-u-sm-12">
                                        <input type="number" id="game_id" name="game_id" value="<?php echo $option_context['column_game_id']; ?>">
                                        <small>输入该栏目承载的饰品对应的游戏ID。</small>
                                    </div>
                                </div>
								 <div class="am-form-group">
                                    <label for="user-name" class="am-u-md-2  am-u-sm-12 am-form-label">商店列表组件管理</label>
                                    <div class="am-u-md-5  am-u-sm-12">
									<div class="am-panel am-panel-success">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">激活组件</h3>
  </div>
  <div class="am-panel-bd">
  <span class="label label-danger">  把组件拖动到这时,便代表启用该组件</span>
  <span class="label label-info">排序则视为渲染的次序</span>
  </div>
  <ul id="column_widget" class=" am-list am-list-static am-list-border widget-area">
 <?php 
 $widget=explode(':',$option_context['column_widgets_option']);
 foreach($widget as $w){
	if($w=="slider"){echo "<li id='slider'>头图</li>";}
	if($w=="quote"){echo "<li id='quote'>文本描述</li>";}
	if($w=="filter"){
		echo " <li id='filter'>筛选器 ";
		if($column_id=="730"||$column_id=="570"){
		echo "<span class='label label-info'>增强版</span>";	
		}else{
		echo "<span class='label label-info'>通用版</span>";	
		}
		echo "</li>";
	}
	if($w=="list"){echo " <li id='list'>商品列表</li>";}
	if($w=="iframe"){echo " <li id='iframe'>广告位</li>";}
 }
 ?> 
                                       </ul>
                                    </div>
                                    </div>
									<div class="am-u-md-5  am-u-sm-12">
									<div class="am-panel am-panel-danger">
                  <div class="am-panel-hd">
                  <h3 class="am-panel-title">禁用组件</h3>
                  </div>
                  <div class="am-panel-bd">
                  <span class="label label-danger">  把组件拖动到这时,便代表禁用该组件</span>
                  <span class="label label-info">该区域的组件特性将被禁用</span>
                  </div>
           <ul id="column_widget_dis" class=" am-list am-list-static am-list-border widget-area" >
           <?php 
$arr=array('slider','quote','filter','list','iframe');
$result=array_diff($arr,$widget);
 foreach($result as $w){
	if($w=="slider"){echo "<li id='slider'>头图</li>";}
	if($w=="quote"){echo "<li id='quote'>文本描述</li>";}
	if($w=="filter"){echo " <li id='filter'>筛选器</li>";}
	if($w=="list"){echo " <li id='list'>商品列表</li>";}
	if($w=="iframe"){echo " <li id='iframe'>广告位</li>";}
 }
?>		   
           </ul>
		 
                                    </div>
                                    </div>
									<input type="hidden" id="slider" name="slider" value="">
									<input type="hidden" id="old_column_name" name="old_column_name" value="<?php echo $option_context['column_game_id']; ?>">
									<input type="hidden" id="quote" name="quote" value="">
									<input type="hidden" id="filter" name="filter" value="">
									<input type="hidden" id="list" name="list" value="">
									<input type="hidden" id="iframe" name="iframe" value="">
                                </div>
<script>
		var sortable_column_widget = new Sortable(document.getElementById('column_widget'), { group: ".column_move",animation: 150});
		var sortable_column_widget = new Sortable(document.getElementById('column_widget_dis'), { group: ".column_move",animation: 150});
		var sortable_detail_widget = new Sortable(document.getElementById('detail_widget'), { group: ".widget_move",animation: 150});
		var sortable_detail_widget = new Sortable(document.getElementById('detail_widget_dis'), { group: ".widget_move",animation: 150});
						function update_list(){
						$("input#slider").attr("value",$("ul#column_widget li#slider").index()+1);
                        $("input#quote").attr("value",$("ul#column_widget li#quote").index()+1);
                        $("input#filter").attr("value",$("ul#column_widget li#filter").index()+1);
                        $("input#list").attr("value",$("ul#column_widget li#list").index()+1);
                        $("input#iframe").attr("value",$("ul#column_widget li#iframe").index()+1);
                        $("input#goods_list").attr("value",$("ul#detail_widget li#goods_list").index()+1);
                        $("input#order_recent").attr("value",$("ul#detail_widget li#order_recent").index()+1);	
						}
						$(document).ready(function(){
						update_list(); 
						});
						$("#sub_button").click(function(){
                        update_list(); 
                        });
						</script>
						  <div class="am-form-group">
                                    <label for="slider" class="am-u-md-2 am-u-sm-12 am-form-label">头图链接</label>
                                    <div class="am-u-md-10  am-u-sm-12">
                                        <input type="text" id="w_slider" name="w_slider" placeholder="http://xxxx.com/xxx.jpg,http://xxxx.com/xxx.jpg" value="<?php echo $sql_slider; ?>">
                                        <small>仅支持链接[多张图可用","分隔开]。</small>
                                    </div>
                                </div>
								  <div class="am-form-group">
                                    <label for="quote" class="am-u-md-2 am-u-sm-12 am-form-label">文字描述</label>
                                    <div class="am-u-md-10  am-u-sm-12">
                                        <input type="text" id="w_quote" name="w_quote" value="<?php echo $sql_quote; ?>">
                                        <small>不支持HTML标签</small>
                                    </div>
                                </div>
								  <div class="am-form-group">
                                    <label for="quote" class="am-u-md-2 am-u-sm-12 am-form-label">列表展示范围</label>
									<div class="am-u-md-10 am-u-sm-12">
                                        <input type="number" id="w_list" name="w_list" value="<?php echo $list_count; ?>">
                                        <small>可显示最大值[留空则设置默认展示数:30条]</small>
                                    </div>
                                </div>
								  <div class="am-form-group">
                                    <label for="quote" class="am-u-md-2 am-u-sm-12 am-form-label">广告位设置</label>
                                    <div class="am-u-md-10  am-u-sm-12">
									<textarea id="w_iframe" name="w_iframe" rows="5" id="doc-ta-1"><?php echo $sql_iframe; ?></textarea>
                                        <small>不支持HTML标签</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" id="sub_button" class="am-btn am-btn-primary">保存</button>
										&nbsp;
										<button type="submit" class="am-btn am-btn-default" onclick="javascript:window.location.href='index.php?mod=admin:column'">返回栏目列表</button>
                                    </div>
                                </div>
                            </form>
						<?php 
						break;
						case 'status':
						if(isset($_GET['on'])){
						$column_id=$_GET['on'];
						$context="update umarket_option set `option_status`='1' where `option_name`='column_{$column_id}'";
						}elseif(isset($_GET['off'])){
						$column_id=$_GET['off'];					
						$context="update umarket_option set `option_status`='0' where `option_name`='column_{$column_id}'";		
						}else{
						setcookie("danger","传参错误,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");	
						}
						if($column_id==""){
						setcookie("danger","传参错误,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");	
						}
						$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","更新成功!",time()+5);
						redirect("index.php?mod=admin:column");
						}else{
						setcookie("warning","更新失败,请再试试或开启调试",time()+5);
						redirect("index.php?mod=admin:column");
						}
						break;
						default: //step0 
						?>
						<table class="am-table am-table am-table-hover">
    <thead>
        <tr>
            <th>栏目名称</th>
            <th>游戏ID</th>
			<th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
	<?php 
	$context="SELECT *  FROM `umarket_option` WHERE `option_name` LIKE '%column_%' and `option_name` NOT LIKE 'column_index'";
	$row=$sql->query($connect,$context);
//	var_dump($sql->fetch_array($row));
	 while($res=mysqli_fetch_row($row)){ 
	 $option_context=unserialize($res[1]);
//	 	 print_r($res[1]);
	if($option_context!=array()){
	?>
        <tr>
		    <td><h2><strong><?php echo $option_context['column_name']; ?></strong></h2></td>
            <td><span class="label label-info"><?php echo $option_context['column_game_id']; ?></span></td> 
	<td>
	<?php if($res[2]==1){ ?>
	<span class="label label-success">已启用</span>
	<?php }else{ ?>
	<span class="label label-danger">已禁用</span>
	<?php } ?>
	</td>
            <td>
		<?php	if($res[2]==0){		?>
<button type='button'  onclick="javascript:window.location.href='index.php?mod=admin:column:status&on=<?php echo $option_context['column_game_id']; ?>'" class='am-btn am-btn-success'>启用栏目</button>
<?php	}else{ ?>
<button type='button'  onclick="javascript:window.location.href='index.php?mod=admin:column:status&off=<?php echo $option_context['column_game_id']; ?>'" class='am-btn am-btn-danger'>禁用栏目</button>
<?php	} ?>
 <button type='button'  onclick="javascript:window.location.href='index.php?mod=admin:column:manage:<?php echo $option_context['column_game_id']; ?>'" class='am-btn am-btn-warning'>管理栏目</button>
 <button type='button'  onclick="javascript:window.location.href='index.php?mod=admin:column:del:<?php echo $option_context['column_game_id']; ?>'" class='am-btn am-btn-danger'><span class="am-icon-close"></span></button>


			</td>
        </tr>
	 <?php } ?>
	 
<?php 
}
	if($sql->fetch_array($sql->query($connect,"SELECT *  FROM `umarket_option` WHERE `option_name` LIKE '%column_%' and `option_name` NOT LIKE 'column_index'"))==null){
		?>
		<tr>
		    <td></td>
            <td><h2><strong>未创建可用的栏目</strong></h2></td><td></td><td></td>
        </tr>
		<?php
	}

?>
	 <tr>
     <td><button type='button'  onclick="javascript:window.location.href='index.php?mod=admin:column:create'" class='am-btn am-btn-secondary'>创建栏目</button></td> 
	<td></td>
	<td></td>
    <td></td>
        </tr>
    </tbody>
</table>
						<?php break; }?>
                        </div>
                    </div>
                </div>

            </div>
