<?php 
global $sql;
$connect=$sql->connect();
if(isset($_POST['create_bot'])){
	$bot_username=$_POST['bot_username'];
	$bot_password=$_POST['bot_password'];
	$bot_twofa_state=$_POST['twofa_state'];
	if($bot_twofa_state==1){
	$bot_twofa_serect=$_POST['twofa_serect'];	
	}else{
	$bot_twofa_serect="";	
	}
	$context="select count(*) from umarket_bot_account where `username`='{$bot_username}' ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	if($res[0]>0){
		echo "<div id='response'>0</div><div id='ajax'>机器人账户已存在</div>";
		die;
	}
	$context = "select * from umarket_bot_account order by accountid DESC limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$accountid=0;if($res){$accountid=$res[0];}$accountid++;
	$bot_port=0;
	$context = "select * from umarket_option where `option_name` ='bot_port_min'  ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$min_port=$res['option_context'];
	$context = "select * from umarket_option where `option_name` ='bot_port_max'  ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$max_port=$res['option_context'];
	$arr=range($min_port,$max_port);
	shuffle($arr);
	foreach($arr as $values)
	{
    $context="SELECT count(*) FROM `umarket_bot_account` WHERE `accountport`='{$values}'";
    $res=$sql->fetch_array($sql->query($connect,$context));
    if($res[0]==0){
	  $bot_port=$values;
	  break;
	}
	}
	$context="INSERT INTO `umarket_bot_account` (`accountid`, `username`, `password`, `twofastate`, `shared_serect`,`accountstate`,`accountport`) VALUES ('{$accountid}', '{$bot_username}', '{$bot_password}', '{$bot_twofa_state}', '{$bot_twofa_serect}','0','{$bot_port}')";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='ajax'></div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
	die;
}

if(isset($_POST['save_option'])){
	$bot_accountid=$_POST['bot_accountid'];
	$bot_username=$_POST['bot_username'];
	$bot_password=$_POST['bot_password'];
	$bot_twofa_state=$_POST['twofa_state'];
	if($bot_twofa_state==1){
	$bot_twofa_serect=$_POST['twofa_serect'];	
	}else{
	$bot_twofa_serect="";	
	}
	$bot_accountid=str_replace("bot-","",$bot_accountid);
	$context="select count(*) from umarket_bot_account where `username`='{$bot_username}' and `accountid`='{$bot_accountid}'";
	$res=$sql->fetch_array($sql->query($connect,$context));
	if($res[0]==0){
		echo "<div id='response'>0</div><div id='ajax'>机器人账户已存在</div>";
		die;
	}
	$context="update umarket_bot_account set `username`='{$bot_username}',`password`='{$bot_password}',`twofastate`='{$bot_twofa_state}',`shared_serect`='{$bot_twofa_serect}' where `accountid`='{$bot_accountid}' limit 1";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='ajax'></div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误/设置无改动</div>";
	}
	die;
}

if(isset($_POST['delete_bot'])){
	$bot_accountid=$_POST['bot_accountid'];
	$context="DELETE FROM `umarket_bot_account` WHERE `accountid` = '{$bot_accountid}' LIMIT 1;";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='ajax'></div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
	die;
}

if(isset($_POST['control_bot'])){
	 $bot_accountstate=$_POST['bot_accountstate'];
	 $bot_accountid=$_POST['bot_accountid'];
	 if($bot_accountstate==0){
		 //上线机器人
		 $bot_accountstate=0;
	 }else{
		 //下线机器人[挂起机器人]
		 $bot_accountstate=2;
	 }
	 $context="update umarket_bot_account set `accountstate`='{$bot_accountstate}' where `accountid`='{$bot_accountid}' limit 1 ";
	 $sql->query($connect,$context);
	 if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='ajax'></div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
	die;
}
if(isset($_POST['global_option'])){
	$min_port=$_POST['min_port'];
	$max_port=$_POST['max_port'];
	$bot_ip=$_POST['bot_ip'];
	if($min_port==""){$min_port=MIN_PORT;}
	if($max_port==""){$max_port=MAX_PORT;}
	if($bot_ip==""){$bot_ip="127.0.0.1";}
	if($max_port<$min_port){
		$min_port = substr($max_port,0,(strlen($max_port)-strlen($min_port)));
		$max_port = substr($max_port, strlen($min_port));
	}elseif($max_port==$min_port){
		$min_port=MIN_PORT;
		$max_port=MAX_PORT;
	}
	 $context="update umarket_option set `option_context`='{$bot_ip}' where `option_name`='bot_ip' limit 1 ";
	 $sql->query($connect,$context);
	 $context="update umarket_option set `option_context`='{$min_port}' where `option_name`='bot_port_min' limit 1 ";
	 $sql->query($connect,$context);
	 $context="update umarket_option set `option_context`='{$max_port}' where `option_name`='bot_port_max' limit 1 ";
	 $sql->query($connect,$context);
	 if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='ajax'></div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
	die;
}
?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span> Bot管理</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class=""><a href="#tab1" onclick="">Bot全局设置</a></li>
                                <li class="am-active"><a href="#tab2" onclick="">管理交易Bot</a></li>
                            </ul>

                            <div class="am-tabs-bd" >
                                <div class="am-tab-panel am-fade" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="height:100%;">
									      <div class="am-u-sm-12 am-u-md-9">
<div class="am-form">
<?php 
$max_port=$min_port=$bot_ip="";
$context="select * from umarket_option where `option_name`='bot_ip' limit 1 ";
$res=$sql->fetch_array($sql->query($connect,$context));
$bot_ip=$res['option_context'];
$context="select * from umarket_option where `option_name`='bot_port_min' limit 1 ";
$res=$sql->fetch_array($sql->query($connect,$context));
$min_port=$res['option_context'];
$context="select * from umarket_option where `option_name`='bot_port_max' limit 1 ";
$res=$sql->fetch_array($sql->query($connect,$context));
$max_port=$res['option_context'];
?>
						<div id="alert_show_global"></div>
<strong><h2>全局设置</h2></strong>
<hr>
 <table class="am-table">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Bot端口范围:</strong></td>
            <td>
			<div class="am-input-group">
			<input type="text" class="am-form-field" placeholder="最大端口" id='bot_port_max' value="<?php echo $max_port; ?>">
  <span class="am-input-group-label">-</span>
  <input type="text" class="am-form-field" placeholder="最小端口" id="bot_port_min" value="<?php echo $min_port; ?>">
</div>

			</td>
        </tr>
		<tr>
            <td><strong>Bot服务器地址:</strong></td>
            <td><input type="text" class="am-form-field" id="bot_ip"  placeholder="机器人服务器地址" value="<?php echo $bot_ip; ?>"></td>
        </tr>
    </tbody>
  </table>	
  <small>端口将用于后台分配机器人的端口,服务器地址不写则为127.0.0.1</small>
<hr>
<button type="button" class="am-btn am-btn-secondary" onclick="global_option()">保存设置</button>
</div>						
						
	<script>
	function global_option(){
	var min_port=$("#bot_port_min").val();	
	var max_port=$("#bot_port_max").val();	
	var bot_ip=$("#bot_ip").val();
	var data="";
	if(bot_ip==""){
		data="<div class='am-alert am-alert-warning' id='query_alert'>请填写机器人服务器IP.</div>";
	}else if(min_port==""){
		data="<div class='am-alert am-alert-warning' id='query_alert'>请填写机器人端口的最小值.</div>";
	}else if(max_port==""){
		data="<div class='am-alert am-alert-warning' id='query_alert'>请填写机器人端口的最大值.</div>";
	}else if(min_port>=max_port){
		data="<div class='am-alert am-alert-warning' id='query_alert'>请调换最大值与最小值的数据位置,并且最大最小值不可以一样</div>";
	}
	if(data!=""){
	$('#alert_show_global').html(data);
	setTimeout(function (){$('#query_alert').alert('close')},3000);
	return;
	}
		$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  global_option: true , min_port:min_port , max_port:max_port , bot_ip:bot_ip  },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_global').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				var data;
				if($("div#response",data).text()==1){
				data="<div class='am-alert am-alert-success' id='query_alert'>全局设置保存成功,页面正在重载中...</div>";			
				$('#alert_show_global').html(data);
	setTimeout(function (){location.reload()},3000);
				}else{
				data="<div class='am-alert am-alert-danger' id='query_alert'>全局设置保存失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				$('#alert_show_global').html(data);
	setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
	</script>					
						
						
						
						
						
						
						
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>全局设置意味着在此的所有设置,在设置后将被全部机器人所应用</p>
 </div>
</section>
						</div> 
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
                                </div>
								
								
								
                                <div class="am-tab-panel am-fade am-active am-in" id="tab2">
                                    <div id="wrapperB" class="wrapper" style="height:100%;">
									<?php loadalert();?>
					   <div class="am-panel am-panel-default" id="create_button">
    <div class="am-panel-bd"> 
	<button type="button" class="am-btn am-btn-warning" onclick="create_bot(0)">创建机器人</button>
	</div>
</div>
										    <div class="am-u-sm-12 am-u-md-9" id="bot_manage_list">
											<div id='alert_show'></div>
<div class="am-panel-group" id="parent">
						<?php
//编写遍历机器人的逻辑代码
$count=$sql->fetch_array($sql->query($connect,"select count(*) from `umarket_bot_account` order by `accountid` desc"));
$context="select * from `umarket_bot_account` order by `accountid` desc";
$res=$sql->query($connect,$context);
if($count[0]>0){
$count=0;
while($bm=$sql->fetch_array($res)){
?>
   <div class="am-panel am-panel-default am-form">
    <div class="am-panel-hd">
      <h4 class="am-panel-title" data-am-collapse="{parent: '#parent', target: '#<?php echo "bot-".$bm['accountid']; ?>'}">
        <?php echo "机器人序号-".$bm['accountid']; ?>
      </h4>
    </div>
    <div id="<?php echo "bot-".$bm['accountid']; ?>" class="am-panel-collapse am-collapse <?php if($count==0){echo "am-in";}?>">
      <div class="am-panel-bd">
        <table class="am-table">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>机器人账户名:</strong></td>
            <td><input type="text" class="am-form-field" id="bot_username" value="<?php echo $bm['username']; ?>" placeholder="机器人账户名用户名"></td>
        </tr>
		<tr>
            <td><strong>机器人账户密码:</strong></td>
            <td><input type="text" class="am-form-field" id="bot_password" value="<?php echo $bm['password']; ?>" placeholder="机器人账户密码"></td>
        </tr>
		<tr>
            <td><strong>二步验证状态:</strong></td>
            <td>
			<label class="am-checkbox">
      <input type="checkbox" id="twofa_checkbox_list" data-am-ucheck <?php if($bm['twofastate']){echo "checked";} ?> onclick="	if(this.checked==false){$('input#twofa_serect').attr('disabled',true);$('input#twofa_serect').val(''); }else{$('input#twofa_serect').attr('disabled',false);}	"> 
    </label>
											</td>
        </tr>
		<tr>
            <td><strong>二步验证私钥:</strong></td>
            <td><input type="text" class="am-form-field" id="twofa_serect" value="<?php echo $bm['shared_serect']; ?>" placeholder="二步验证私钥" <?php if(!$bm['twofastate']){echo "disabled";} ?>></td>
        </tr>
		<tr>
            <td><strong>机器人端口:</strong></td>
            <td><input type="text" class="am-form-field" value="<?php echo $bm['accountport']; ?>" placeholder="机器人端口" disabled></td>
        </tr>
		<tr>
            <td><strong>机器人状态:</strong></td>
            <td id="bot_state">
			<?php 
			if($bm['accountstate']==0){
				echo "<span class=\"label label-sm label-danger\">未登录</span>";
			}elseif($bm['accountstate']==1){
				echo "<span class=\"label label-sm label-success\">已登录</span>";
			}elseif($bm['accountstate']==2){
				echo "<span class=\"label label-sm label-danger\">已挂起</span>";
			}elseif($bm['accountstate']==3){
				echo "<span class=\"label label-sm label-info\">机器人正忙...</span>";
			}elseif($bm['accountstate']==4){
				echo "<span class=\"label label-sm label-info\">机器人下线中...</span>";
			}elseif($bm['accountstate']==5){
				echo "<span class=\"label label-sm label-warning\">机器人等待人工输入二步验证码</span>";	
			}
			?></td>
        </tr>
    </tbody>
  </table>	
      </div>
	  <footer class="am-panel-footer">
	<button type="button" class="am-btn am-btn-secondary am-btn-sm"  onclick="save_option('bot-<?php echo $bm['accountid']; ?>')">保存设置</button>
&nbsp;
<button type="button" class="am-btn am-btn-danger am-btn-sm" onclick="delete_bot(0,<?php echo $bm['accountid']; ?>)">删除机器人</button>
&nbsp;
<?php 
if($bm['accountstate']==2){
?>
<button type="button" class="am-btn am-btn-success am-btn-sm" onclick="control_bot(0,<?php echo $bm['accountid']; ?>)" >上线机器人</button>
<?php
}elseif($bm['accountstate']==0||$bm['accountstate']==1||$bm['accountstate']==3){
?>
<button type="button" class="am-btn am-btn-warning am-btn-sm" onclick="control_bot(1,<?php echo $bm['accountid']; ?>)" >下线机器人</button>
<?php 
}else{
	?>
	<button type="button" class="am-btn am-btn-default am-btn-sm" disabled="disabled">机器人状态不可操作</button>
<?php 
} 
?>
	  </footer>
    </div>
  </div>  
  <?php 
  $count++;
}
}else{
	?>
	<div class="am-panel am-panel-default">
    <div class="am-panel-bd">暂无可管理的机器人</div>
</div>
	<?php
}
  ?>					
  </div>
  </div>
   <div id="create_bot_panel" class="am-u-sm-9 am-form" style="display:none;">
<div id="alert_show_create"></div>
<strong><h2>创建机器人</h2></strong>
<hr>
 <table class="am-table">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>机器人账户名:</strong></td>
            <td><input type="text" class="am-form-field" id="bot_username_create" placeholder="机器人账户名用户名"></td>
        </tr>
		<tr>
            <td><strong>机器人账户密码:</strong></td>
            <td><input type="text" class="am-form-field" id="bot_password_create"  placeholder="机器人账户密码"></td>
        </tr>
		<tr>
            <td><strong>是否启用二步验证:</strong></td>
            <td>
			<label class="am-checkbox">
      <input type="checkbox" id="twofa_checkbox" onclick="if(this.checked==false){$('input#twofa_serect_private').attr('disabled',true);$('input#twofa_serect_private').val(''); }else{$('input#twofa_serect_private').attr('disabled',false);}" data-am-ucheck> 
    </label>
											</td>
        </tr>
		<tr>
            <td><strong>二步验证私钥:</strong></td>
            <td><input type="text" class="am-form-field" id="twofa_serect_private" placeholder="二步验证私钥" disabled></td>
        </tr>
    </tbody>
  </table>	
  <small>端口将由后台根据全局设置中设定的端口范围,自动分配端口</small>
<hr>
<button type="button" class="am-btn am-btn-secondary" onclick="create_bot(2)">确认创建</button>&nbsp;&nbsp;<button type="button" class="am-btn am-btn-default" onclick="create_bot(1)">放弃创建</button>
</div>   
  
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
   <p>可通过操作按钮来管理机器人</p>
 </div>
</section>
						</div> 
                                        
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div></div>
                                <script>
								function enabled_twofa(checked){
									if(this.checked==false){$("input#twofa_serect_private").attr("disabled",true);$("input#twofa_serect_private").val(""); }else{$("input#twofa_serect_private").attr("disabled",false);}
								}
								function create_bot(mode){
									if(!arguments[0]) mode = 0;
								if(mode==0){
									$("div#create_button").hide();
									$("div#bot_manage_list").hide();
									$("div#create_bot_panel").show();
								}else if(mode==1){
									$("div#create_button").show();
									$("div#bot_manage_list").show();
									$("div#create_bot_panel").hide();
									$('input#bot_username_create').val("");				
									$('input#bot_password_create').val("");				
									$('checkbox#twofa_checkbox').attr("disabled",true);
									$('input#twofa_serect_private').val("");
								}else if(mode==2){
				var bot_username=$("input#bot_username_create").val();
				var bot_password=$("input#bot_password_create").val();
				var twofa_state=$("input#twofa_checkbox").prop('checked');
				var twofa_serect;
				if(twofa_state){
					twofa_serect=$("input#twofa_serect_private").val();
					twofa_state="1";
					}else{
					twofa_serect="";
					twofa_state="0";
					}
					if(bot_username==""){
				var data="<div class='am-alert am-alert-warning' id='query_alert'>请填写机器人账户名.</div>";
				$('#alert_show_create').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
						return;
						}else if(bot_password==""){
				var data="<div class='am-alert am-alert-warning' id='query_alert'>请填写机器人账户密码.</div>";
				$('#alert_show_create').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
						return;
						}
									$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  create_bot: true , bot_username:bot_username , bot_password:bot_password , twofa_state:twofa_state , twofa_serect:twofa_serect },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_create').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>机器人创建成功,页面正在重载中...</div>";
				$('#alert_show_create').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>机器人创建失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				$('#alert_show_create').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
								}
								}
								
								
								function save_option(id){
									if(!arguments[0]){
				var data="<div class='am-alert am-alert-danger' id='query_alert'>按钮初始化失败,请刷新页面再试.</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000); 
						return;
						}
				var bot_accountid=id;
				var bot_username=$("#"+id).find("input#bot_username").val();
				var bot_password=$("#"+id).find("input#bot_password").val();
				var twofa_state=$("#"+id).find("input#twofa_checkbox_list").prop('checked');
				console.log(twofa_state);
				var twofa_serect;
				if(twofa_state){
					twofa_serect=$("#"+id).find("input#twofa_serect").val();
					twofa_state="1";
					}else{
					twofa_serect="";
					twofa_state="0";
					}
									$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  save_option: true , bot_accountid:bot_accountid , bot_username:bot_username , bot_password:bot_password , twofa_state:twofa_state , twofa_serect:twofa_serect },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>设置保存成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>设置保存失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});	
								}
								function delete_bot(mode,id){
										if(!arguments[0]){mode=0;}
										if(!arguments[1]){
							var data="<div class='am-alert am-alert-warning' id='query_alert'>按钮初始化失败,请刷新页面再试.</div>";
							$('#alert_show').html(data);	
							setTimeout(function (){$('#query_alert').alert('close')},3000);
											return;
											}
										if(mode==0){
				var data="<div class='am-alert am-alert-danger' id='query_alert'>确定要删除这个机器人吗?&nbsp;<button type=\"button\" class=\"am-btn am-btn-secondary\" onclick=\"$('#query_alert').alert('close')\">放弃删除</button>&nbsp;&nbsp;<button type=\"button\" class=\"am-btn am-btn-default\" onclick=\"delete_bot('1',"+id+")\">删除机器人</button></div>";
				$('#alert_show').html(data);						
										}else if(mode==1){
				var bot_accountid=id;	
										$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  delete_bot: true , bot_accountid:bot_accountid },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>机器人删除成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>机器人删除失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});	
										}
								}
								function control_bot(mode,id){
								if(!arguments[1]){
				var data="<div class='am-alert am-alert-danger' id='query_alert'>按钮初始化失败,请刷新页面再试.</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000); 
						return;
						}
						if(!arguments[0]){mode=0;}
						var bot_accountid=id;
						if(mode==0){
						$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  control_bot: true , bot_accountid:bot_accountid , bot_accountstate:0},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>已上线机器人.</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>上线机器人失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});		
						}else if(mode==1){
						$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:botmanage',
				data: {  control_bot: true , bot_accountid:bot_accountid ,bot_accountstate:1},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>已下线机器人.</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>下线机器人失败,[错误原因]-["+$("div#ajax",data).text()+"],页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);	
				}
			}
		});		
						}
								}
								</script>
								</div>

                            </div>
                        </div>

                    </div>
        </div>