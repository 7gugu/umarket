 <?php 
  global $sql;
 $connect=$sql->connect();
 if(isset($_POST['insert_broadcast'])){
	 $broadcast_title=$_POST['broadcast_title'];
	 $broadcast_context=$_POST['broadcast_context'];
	 $broadcast_author=$_POST['broadcast_author'];
	 if($broadcast_title==""||$broadcast_context==""||$broadcast_author==""){
		echo "<div id='response'>0</div><div id='ajax'>输入栏不可为空</div>";
		}
	$broadcast_time=date("Y-m-d H:i:s");
	$broadcast_context=str_replace("\"","'",$broadcast_context);
	$context = "select * from umarket_broadcast order by broadcast_id DESC limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$broadcast_id=0;if($res){$broadcast_id=$res[0];}$broadcast_id++;
	$context="INSERT INTO `umarket_broadcast` (`broadcast_id`, `broadcast_title`, `broadcast_body`, `broadcast_time`, `broadcast_author`) VALUES ('{$broadcast_id}', '{$broadcast_title}', '{$broadcast_context}', '{$broadcast_time}', '{$broadcast_author}')";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'>SQL执行错误</div>";
	}
	die;
 }
 if(isset($_POST['delete_broadcast'])){
 $broadcast_id=$_POST['broadcast_id'];
 if($broadcast_id==""){
	echo "<div id='response'>0</div><div id='ajax'>传参错误</div>"; 
	die;
 }
 $context="DELETE FROM `umarket_broadcast` WHERE `broadcast_id` = '{$broadcast_id}' LIMIT 1";
 $sql->query($connect,$context);
 if($sql->affected_rows($connect)){
 echo "<div id='response'>1</div>";
 }else{
 echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
 }
 die;
 }
 if(isset($_POST['query_broadcast'])){
	 $broadcast_id=$_POST['broadcast_id'];
	 $context="select * from `umarket_broadcast` where `broadcast_id`='{$broadcast_id}'";
	 $result=$sql->fetch_array($sql->query($connect,$context));
	 echo "<div id='response'>1</div><div id='broadcast_id'>{$result['broadcast_id']}</div><div id='broadcast_title'>{$result['broadcast_title']}</div><div id='broadcast_context'>{$result['broadcast_body']}</div><div id='broadcast_time'>{$result['broadcast_time']}</div><div id='broadcast_author'>{$result['broadcast_author']}</div>";
 die;
 }
 if(isset($_POST['edit_broadcast'])){
	 $broadcast_id=$_POST['broadcast_id'];
	 if($broadcast_id==""){
	echo "<div id='response'>0</div><div id='ajax'>ID传参错误</div>"; 
	die;
 }
	 $broadcast_title=$_POST['broadcast_title'];
	 $broadcast_context=$_POST['broadcast_context'];
	 $broadcast_author=$_POST['broadcast_author'];
	 $broadcast_time=date("Y-m-d H:i:s");
	 if($broadcast_title==""||$broadcast_context==""||$broadcast_author==""){
		echo "<div id='response'>0</div><div id='ajax'>输入栏不可为空</div>";
		}
		$broadcast_context=str_replace("\"","'",$broadcast_context);
		$context="update umarket_broadcast set `broadcast_title`='{$broadcast_title}',`broadcast_author`='{$broadcast_author}',`broadcast_body`='{$broadcast_context}',`broadcast_time`='{$broadcast_time}' where `broadcast_id`='{$broadcast_id}' limit 1";
		$sql->query($connect,$context);
		if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误/公告无改动</div>";
		}
	die;
 }
 ?>
 <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 公告管理
                    </div>
                </div>
                <div class="tpl-block ">
                     <div class="am-g tpl-amazeui-form">
					  
               <div class="am-u-sm-12">
			   <?php loadalert();?>
					   <div class="am-panel am-panel-default" id="insert_button">
    <div class="am-panel-bd"> 
	<button type="button" class="am-btn am-btn-warning" onclick="insert_broadcast(0)">发布公告</button>
	</div>
</div>
<div class="am-u-sm-12 am-u-md-9" id='broadcast_show'>
<div id='alert_show'></div>
<div class="am-panel-group" id="parent">
<?php
//编写遍历公告的逻辑代码
$count=$sql->fetch_array($sql->query($connect,"select count(*) from `umarket_broadcast` order by `broadcast_id` desc"));
$context="select * from `umarket_broadcast` order by `broadcast_id` desc";
$res=$sql->query($connect,$context);
if($count[0]>0){
$count=0;
while($bc=$sql->fetch_array($res)){
?>
  <div class="am-panel am-panel-default">
    <div class="am-panel-hd">
      <h4 class="am-panel-title" data-am-collapse="{parent: '#parent', target: '#<?php echo "broadcast-".$count; ?>'}">
        <?php echo $bc['broadcast_title']; ?> <span class="am-badge am-badge-secondary"><?php echo $bc['broadcast_time']; ?></span>&nbsp;<span class="am-badge am-badge-primary"><?php echo $bc['broadcast_author']; ?></span>
      </h4>
    </div>
    <div id="<?php echo "broadcast-".$count; ?>" class="am-panel-collapse am-collapse am-in">
      <div class="am-panel-bd">
        <?php  echo  $bc['broadcast_body']; ?>
      </div>
	  <footer class="am-panel-footer">
	<button type="button" class="am-btn am-btn-success am-btn-sm" onclick="edit_broadcast(0,<?php echo $bc['broadcast_id']; ?>)">编辑公告</button>
&nbsp;
<button type="button" class="am-btn am-btn-danger am-btn-sm" onclick="delete_broadcast(<?php echo $bc['broadcast_id']; ?>)">删除公告</button>

	  </footer>
    </div>
  </div>
  <?php 
  $count++;
}
}else{
	?>
	<div class="am-panel am-panel-default">
    <div class="am-panel-bd">暂无公告</div>
</div>
	<?php
}
  ?>
</div>
</div>
<?php //style="display:none;" ?>
<div id="broadcast_insert" class="am-u-sm-9 am-form" style="display:none;">
<div id="alert_show_insert"></div>
<strong><h2>发布公告</h2></strong>
<hr>
<div class="am-input-group">
  <span class="am-input-group-label">公告标题</span>
  <input type="text" class="am-form-field" id="broadcast_title" placeholder="公告标题">
</div>
<br>
<div class="am-input-group">
   <span class="am-input-group-label">作者署名</i></span>
  <input type="text" class="am-form-field" id="broadcast_author" placeholder="作者署名">
</div>
<br>
<div class="am-input-group">
   <span class="am-input-group-label">公告内容</i></span>
  <textarea class="" rows="5" id="broadcast_context" placeholder="输入公告内容"></textarea>
</div>
<small>插入图片,请使用<code>img</code>标签插入</small>
<hr>
<button type="button" class="am-btn am-btn-secondary" onclick="insert_broadcast(2)">确认发布</button>&nbsp;&nbsp;<button type="button" class="am-btn am-btn-default" onclick="insert_broadcast(1)">放弃发布</button>
</div>





 <div id="edit_broadcast_show" class="am-u-sm-9 am-form" style="display:none;">
<div id="alert_show_edit"></div>
<strong><h2>编辑公告</h2></strong>
<hr>
<div class="am-input-group">
  <span class="am-input-group-label">公告标题</span>
  <input type="text" class="am-form-field" id="broadcast_title" placeholder="公告标题" value="">
</div>
<br>
<div class="am-input-group">
   <span class="am-input-group-label">作者署名</i></span>
  <input type="text" class="am-form-field" id="broadcast_author" placeholder="作者署名" value="">
</div>
<br>
<div class="am-input-group">
   <span class="am-input-group-label">公告内容</i></span>
  <input type='text' id="broadcast_context" placeholder="输入公告内容">
</div>
<input type="hidden" id="broadcast_id">
<small>插入图片,请使用<code>img</code>标签插入</small>
<hr>
<button type="button" class="am-btn am-btn-secondary" onclick="edit_broadcast(2)">确认更新</button>&nbsp;&nbsp;<button type="button" class="am-btn am-btn-default" onclick="edit_broadcast(1)">放弃编辑</button>
</div>             













<div class="am-u-sm-12 am-u-md-3" id='broadcast_alert'>
				<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd" id='broadcast_alert_body'>
   <p>点击公告页脚的按钮,即可对公告进行操作</p>
 </div>
</section>
				</div> 

			  </div>
			   
			   
			   <script>
			   function insert_broadcast(mode){
				   if(!arguments[0]) mode = 0;
				  if(mode==0){
				//显示插入公告的编辑器
				 $("#broadcast_show").hide();
				 $("#insert_button").hide();
				 $("#broadcast_insert").show();
				  }else if(mode==1){
				//隐藏公告编辑器
				$("#broadcast_show").show();
				$("#insert_button").show();
				 $("#broadcast_insert").hide();
				 $('input#broadcast_title').val("");				
				$('input#broadcast_author').val("");				
				$('textarea#broadcast_context').val("");
				  }else if(mode==2){
				//提交数据
				var broadcast_title=$("#broadcast_title").val();
				var broadcast_context=$("#broadcast_context").val();
				var broadcast_author=$("#broadcast_author").val();
				if(broadcast_title==""||broadcast_context==""||broadcast_author==""){
				alert("输入栏不可为空");
				return;
				}
				$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:broadcast',
				data: {  insert_broadcast: true , broadcast_title:broadcast_title , broadcast_context:broadcast_context , broadcast_author:broadcast_author  },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_insert').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>公告发布成功,页面正在重载中...</div>";
				$('#alert_show_insert').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>公告发布失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_insert').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});
				  }else{
				//未知操作	  
				setcookie("warning","未知操作","s10");
				location.reload();//重载页面
				}
			   }
			   function delete_broadcast(id){
				    if(!arguments[0]){
				var data="<div class='am-alert am-alert-danger' id='query_alert'>按钮初始化失败,请刷新页面再试.</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
						return;
						}
				$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:broadcast',
				data: {  delete_broadcast: true , broadcast_id:id },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>公告删除成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>公告删除失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});	
			   }
			   function edit_broadcast(mode,id){
				if(!arguments[0]){mode=0;}
					if(mode==0){
			if(!arguments[1]){alert("按钮初始化错误");return;}  
			$("#broadcast_show").hide();
			$("#insert_button").hide();
			$("#edit_broadcast_show").show();
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:broadcast',
				data: {  query_broadcast: true , broadcast_id:id },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据加载中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				$('input#broadcast_title').attr("value",$("div#broadcast_title",data).text());				
				$('input#broadcast_author').attr("value",$("div#broadcast_author",data).text());				
				$('input#broadcast_context').attr("value",$("div#broadcast_context",data).text());
				$('input#broadcast_id').attr("value",$("div#broadcast_id",data).text());				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据加载失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload()},3000);
				}
			}
		});					
			   }else if(mode==1){
				$('input#broadcast_title').val("");				
				$('input#broadcast_author').val("");				
				$('input#broadcast_context').val("");
				$('input#broadcast_id').val("");
			$("#broadcast_show").show();
			$("#insert_button").show();
			$("#edit_broadcast_show").hide();				
			   }else if(mode==2){
				var broadcast_title=$("input#broadcast_title").val();
				var broadcast_context=$("input#broadcast_context").val();
				var broadcast_author=$("input#broadcast_author").val();
				var broadcast_id=$("input#broadcast_id").val();
				if(broadcast_title==""||broadcast_context==""||broadcast_author==""){
				var data="<div class='am-alert am-alert-warning' id='query_alert'>输入框不可为空.</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				return;
				}
				if(broadcast_id==""){
				alert("ID数据获取错误,页面开始重载...");
				location.reload();
				}
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:broadcast',
				data: {  edit_broadcast: true , broadcast_id:broadcast_id , broadcast_title:broadcast_title , broadcast_context:broadcast_context , broadcast_author},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_edit').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},2000);
			},
			success:function(data){
				if($("div#response",data).text()==0){
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据提交失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-success' id='query_alert'>公告更新成功,页面正在重载中...</div>";
				$('#alert_show_edit').html(data);
				setTimeout(function (){location.reload()},2000);	
				}	
				}
			});		
			   }		
			   }
			   </script>
</div>
            </div>
        </div>