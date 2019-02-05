<?php
global $mode;
$parse = explode(':',$mode);
if(isset($parse[2])){$step=$parse[2];}else{$step="basic";}
if(isset($_POST['query_data'])){
	if(isset($_POST['query_node'])){
		echo "<div id='response'>1</div>";
		echo "<div id='node-data'>";
		echo "<tr1>";
		echo "<td2><strong>商城服务器地址:</strong></td3><td2><label class='am-checkbox'>";
		if(WEBIP=="localhost"){
		echo "<input type='checkbox' id='shop_server_checkbox' onclick=\"if(this.checked==false){\$('input#shop_server_input').attr('disabled',true);\$('input#shop_server_input').val(''); }else{\$('input#shop_server_input').attr('disabled',false);}	\"> ";
		}else{
		echo "<input type='checkbox' id='shop_server_checkbox' onclick=\"if(this.checked==false){\$('input#shop_server_input').attr('disabled',true);\$('input#shop_server_input').val(''); }else{\$('input#shop_server_input').attr('disabled',false);}	\" checked> ";
		}
			echo "</label></td3><td2>";
			if(WEBIP=="localhost"){
			echo "<input type='text' class='am-form-field' id='shop_server_input' placeholder='不输入则默认商城位于主服务器上' disabled>";
			}else{
			echo "<input type='text' class='am-form-field' id='shop_server_input' placeholder='不输入则默认商城位于主服务器上' value='".WEBIP."'>";
			}
			echo "</td3>";
			echo "</tr4>";
			echo "<tr1>";
	echo "<td2><strong>机器人服务器地址:</strong></td3><td2><label class='am-checkbox'>";
		if(BOTIP=="localhost"){
		echo "<input type='checkbox' id='bot_server_checkbox'  onclick=\"if(this.checked==false){\$('input#bot_server_input').attr('disabled',true);\$('input#bot_server_input').val(''); }else{\$('input#bot_server_input').attr('disabled',false);}	\"> ";
		}else{
		echo "<input type='checkbox' id='bot_server_checkbox'  onclick=\"if(this.checked==false){\$('input#bot_server_input').attr('disabled',true);\$('input#bot_server_input').val(''); }else{\$('input#bot_server_input').attr('disabled',false);}	\" checked> ";
		}
			echo "</label></td3><td2>";
			if(BOTIP=="localhost"){
			echo "<input type='text' class='am-form-field' id='bot_server_input' placeholder='不输入则默认机器人位于主服务器上' disabled>";
			}else{
			echo "<input type='text' class='am-form-field' id='bot_server_input' placeholder='不输入则默认机器人位于主服务器上' value='".BOTIP.":".BOTPORT."'>";
			}
			echo "</td3>";
			echo "</tr4>";
			echo "<tr1>";
	echo "<td2><strong>数据库服务器地址:</strong></td3><td2><label class='am-checkbox'>";
		if(DBIP=="localhost"){
			echo "<input type='checkbox' id='sql_server_checkbox'  onclick=\"if(this.checked==false){\$('input#sql_server_input').attr('disabled',true);\$('input#sql_server_input').val(''); }else{\$('input#sql_server_input').attr('disabled',false);}	\"> ";
		}else{
			echo "<input type='checkbox' id='sql_server_checkbox'  onclick=\"if(this.checked==false){\$('input#sql_server_input').attr('disabled',true);\$('input#sql_server_input').val(''); }else{\$('input#sql_server_input').attr('disabled',false);}	\" checked> "	;
		}
			echo "</label></td3><td2>";
			if(DBIP=="localhost"){
			echo "<input type='text' class='am-form-field' id='sql_server_input' placeholder='不输入则默认数据库位于主服务器上' disabled>";
			}else{
			echo "<input type='text' class='am-form-field' id='sql_server_input' placeholder='不输入则默认数据库位于主服务器上' value='".DBIP.":".DBPORT."'>";
			}
			echo "</td3>";
			echo "</tr4>";
echo "<tr1><td2></td3><td2></td3><td2><button class='am-btn am-btn-danger'type='button' onclick=\"if(confirm('配置节点有风险,请谨慎操作.请先在此确认服务器配置!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_node_option();}}\"><span class=\"am-icon-refresh\"></span> 更新节点配置</button></td3></tr4>";
	echo "</div>";
	die;
	}
	if(isset($_POST['query_basic'])){
		echo "<div id='response'>1</div>";
		echo "<div id='system_name'>".SYSTEM_NAME."</div>";
		echo "<div id='system_sname'>".SYSTEM_SNAME."</div>";
		echo "<div id='system_footer_record_number'>".SYSTEM_FOOTER_RECORD_NUMBER."</div>";
		echo "<div id='system_footer_sign'>".SYSTEM_FOOTER_SIGN."</div>";
		echo "<div id='webapi_key'>".APIKEY."</div>";
		if(USER_AVATAR){$user_avatar='1';}else{$user_avatar='0';}
		echo "<div id='user_avatar'>".$user_avatar."</div>";
		if(RUNTIME){$runtime='1';}else{$runtime='0';}
		echo "<div id='runtime'>".$runtime."</div>";
	die;
	}
	if(isset($_POST['query_reg'])){
		$enable_reg=$email_auth="0";
		echo "<div id='response'>1</div>";
		if(EMAIL_AUTH){$email_auth="1";}
		echo "<div id='email_auth'>".$email_auth."</div>";
		echo "<div id='email_host'>".EMAIL_HOST."</div>";
		echo "<div id='email_user'>".EMAIL_USER."</div>";
		echo "<div id='email_passw'>".EMAIL_PASSW."</div>";
		echo "<div id='email_port'>".EMAIL_PORT."</div>";
		if(ENABLE_REG){$enable_reg="1";}
		echo "<div id='enable_reg'>".$enable_reg."</div>";
		die;
	}
	if(isset($_POST['query_appearance'])){
		echo "<div id='response'>1</div>";
		echo "<div id='system_header_icon'>".SYSTEM_HEADER_ICON."</div>";
		echo "<div id='system_icon'>".SYSTEM_ICON."</div>";
		echo "<div id='index_slides'>".INDEX_SLIDES."</div>";
		die;
	}
	echo "<div id='response'>0</div>";
	die;
}

if(isset($_POST['update_node_option'])){
	@$node_web=htmlspecialchars($_POST['node_web']);
	if($node_web==""){$node_web="localhost";}
	@$node_sql=htmlspecialchars($_POST['node_sql']);
	if($node_sql==""){$node_sql="localhost:3306";}
	@$node_bot=htmlspecialchars($_POST['node_bot']);
	if($node_bot==""){$node_bot="localhost:2270";}
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config-node.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-node-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>备份失败,更新程序中断...</div>";die;}
	$divide=@explode(":",$node_bot);
	$bot_ip=$divide[0];
	$bot_port=$divide[1];
	$divide=@explode(":",$node_sql);
	$sql_ip=$divide[0];
	$sql_port=$divide[1];
	$new_contents="
	<?php
define(\"DBIP\",\"".$sql_ip."\");//数据库IP(一般不需修改)
define(\"DBPORT\",\"".$sql_port."\");//数据库端口
define(\"BOTIP\",\"".$bot_ip."\");//机器人服务器IP
define(\"BOTPORT\",\"".$bot_port."\");//机器人后端端口
define(\"WEBIP\",\"".$node_web."\");//Web服务器IP
?>
	";
	if(file_put_contents(SYSTEM_ROOT."/config/config-node.php",trim($new_contents),LOCK_EX)){
		echo "<div id='response'>1</div>";
	}else{
		echo "<div id='response'>0</div><div id='alert'>写入失败,更新程序中断...</div>";
	}
	die;
	}

	if(isset($_POST['update_basic_option'])){
	@$system_name=htmlspecialchars($_POST['system_name']);
	if($system_name==""){$node_web="Umarket|开源饰品市场";}
	@$system_sname=htmlspecialchars($_POST['system_sname']);
	if($system_sname==""){$node_web="Umarket";}
	@$system_footer_record_number=htmlspecialchars($_POST['system_footer_record_number']);
	if($system_footer_record_number==""){$system_footer_record_number="";}
	@$system_footer_sign=htmlspecialchars($_POST['system_footer_sign']);
	if($system_footer_sign==""){$system_footer_sign="Power by 7gugu";}
	@$webapi_key=htmlspecialchars($_POST['webapi_key']);
	@$webapi_key_old=htmlspecialchars($_POST['webapi_key_old']);
	@$user_avatar=htmlspecialchars($_POST['user_avatar']);
	if($user_avatar==""){$user_avatar=false;}
	@$runtime=htmlspecialchars($_POST['runtime']);
	if($runtime==""){$runtime=false;}
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config-basic.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-basic-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>Basic配置文件备份失败,更新程序中断...</div>";die;}
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>主配置文件备份失败,更新程序中断...</div>";die;}
	$new_contents="
<?php
define('SYSTEM_NAME','{$system_name}');
define('SYSTEM_SNAME','{$system_sname}');
define('SYSTEM_FOOTER_RECORD_NUMBER','{$system_footer_record_number}');
define('SYSTEM_FOOTER_SIGN','{$system_footer_sign}');
define('USER_AVATAR',{$user_avatar});
define('RUNTIME',{$runtime});
?>
	";
	//引入文件锁,防止重复修改的请求
	if(file_put_contents(SYSTEM_ROOT."/config/config-basic.php",trim($new_contents),LOCK_EX)){
		echo "<div id='response'>1</div>";
	}else{
		echo "<div id='response'>0</div><div id='alert'>写入失败,更新程序中断...</div>";
	}
	//更替apikey数据
	$config_content=file_get_contents(SYSTEM_ROOT."/config/config.php");
	$config_content=str_ireplace($webapi_key_old,$webapi_key,$config_content);
	file_put_contents(SYSTEM_ROOT."/config/config.php",trim($config_content),LOCK_EX);
	die;	
	}
	if(isset($_POST['update_reg_option'])){
	$email_auth=$enable_reg='false';
	@$email_auth=htmlspecialchars($_POST['email_auth']);
	if($email_auth==""){$email_auth='false';}
	@$email_host=htmlspecialchars($_POST['email_host']);
	if($email_host==""){$email_auth='false';}
	@$email_port=htmlspecialchars($_POST['email_port']);
	if($email_port==""){$email_port=25;}
	@$email_user=htmlspecialchars($_POST['email_user']);
	if($email_user==""){$email_user="";}
	@$email_passw=htmlspecialchars($_POST['email_passw']);
	if($email_passw==""){$email_passw="";}
	@$enable_reg=htmlspecialchars($_POST['enable_reg']);
	if($enable_reg==""){$enable_reg=false;}
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config-reg.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-reg-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>备份失败,更新程序中断...</div>";die;}
	$new_contents="
<?php
define('ENABLE_REG',{$enable_reg});
define('EMAIL_HOST','{$email_host}');
define('EMAIL_PORT',{$email_port});
define('EMAIL_AUTH',{$email_auth});
define('EMAIL_USER','{$email_user}');
define('EMAIL_PASSW','{$email_passw}');
?>
	";
	if(file_put_contents(SYSTEM_ROOT."/config/config-reg.php",trim($new_contents),LOCK_EX)){
		echo "<div id='response'>1</div>";
	}else{
		echo "<div id='response'>0</div><div id='alert'>写入失败,更新程序中断...</div>";
	}
	die;	
	}
	if(isset($_POST['update_appearance_option'])){
	@$system_icon=htmlspecialchars($_POST['system_icon']);
	@$system_header_icon=htmlspecialchars($_POST['system_header_icon']);
	@$index_slides=htmlspecialchars($_POST['index_slides']);
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config-appearance.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-appearance-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>备份失败,更新程序中断...</div>";die;}
	$new_contents="
<?php
define('SYSTEM_HEADER_ICON','{$system_header_icon}');
define('SYSTEM_ICON','{$system_icon}');
define('INDEX_SLIDES','{$index_slides}');
?>
	";
	if(file_put_contents(SYSTEM_ROOT."/config/config-appearance.php",trim($new_contents),LOCK_EX)){
		echo "<div id='response'>1</div>";
	}else{
		echo "<div id='response'>0</div><div id='alert'>写入失败,更新程序中断...</div>";
	}
	die;	
	}
switch($step){
	case 'basic':
?>
<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 基础设置
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
                        <div class="am-u-sm-9">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>站点名称:</strong></td>
            <td><input type="text" class="am-form-field" id="system_name" placeholder="站点名称"></td>
        </tr>
		<tr>
            <td><strong>站点名称缩写:</strong></td>
            <td><input type="text" class="am-form-field" id="system_sname" placeholder="站点名称缩写" ></td>
        </tr>
		<tr>
            <td><strong>页脚备案号:</strong></td>
			<td><input type="text" class="am-form-field" id="system_footer_record_number" placeholder="页脚备案号"></td>
        </tr>
		<tr>
            <td><strong>页脚签名:</strong></td>
			<td><input type="text" class="am-form-field" id="system_footer_sign" placeholder="页脚签名"></td>
        </tr>
		<tr>
            <td><strong>WebAPI秘钥:</strong></td>
			<td><input type="text" class="am-form-field" id="webapi_key" placeholder="WebAPI秘钥"><input type="hidden" id="webapi_key_old" value=''></td>
        </tr>
		<tr>
            <td><strong>加载用户Steam头像:</strong></td>
			<td><label class="am-checkbox">
      <input type="checkbox" id='user_avatar' value="" data-am-ucheck>
    </label></td>
        </tr>
		<tr>
            <td><strong>启用运行时间:</strong></td>
			<td><label class="am-checkbox">
      <input type="checkbox" id='runtime' value="" data-am-ucheck>
    </label></td>
        </tr>
		<tr>
            <td></td>
			<td>
			<button class='am-btn am-btn-danger'type='button' onclick="if(confirm('修改基础设置有风险,请谨慎操作.请先在此确认配置操作!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_basic_option();}}"><span class="am-icon-refresh"></span> 更新配置</button>
			</td>
        </tr>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改</small>  
						</div>
						<div class="am-u-sm-3">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>设置站点的全局设置</p>
 </div>
</section>	
						</div>
                    </div>
                </div>

            </div>
        </div>
		<script>
		function update_basic_option(){
		var system_name=system_sname=system_footer_record_number=system_footer_sign='Uamarket|7gugu';
		var user_avatar=runtime=false;
		system_name=$("input#system_name").val();
		system_sname=$("input#system_sname").val();
		system_footer_record_number=$("input#system_footer_record_number").val();
		system_footer_sign=$("input#system_footer_sign").val();
		webapi_key=$("input#webapi_key").val();
		webapi_key_old=$("input#webapi_key_old").val();
		if($("input#user_avatar").prop("checked")){user_avatar=true;}
		if($("input#runtime").prop("checked")){runtime=true;}
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  update_basic_option: true , system_name: system_name , system_sname: system_sname , system_footer_record_number: system_footer_record_number , system_footer_sign: system_footer_sign , webapi_key:webapi_key , webapi_key_old:webapi_key_old , user_avatar: user_avatar , runtime: runtime},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>基础数据更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>基础数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
		function query_basic(){
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  query_data: true , query_basic:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#system_name").val($("div#system_name",data).text());
				$("input#system_sname").val($("div#system_sname",data).text());
				$("input#system_footer_record_number").val($("div#system_footer_record_number",data).text());
				$("input#system_footer_sign").val($("div#system_footer_sign",data).text());
				$("input#webapi_key").val($("div#webapi_key",data).text());
				$("input#webapi_key_old").val($("div#webapi_key",data).text());
				if($("div#user_avatar",data).text()=='1'){$("input#user_avatar").uCheck('toggle');}
				if($("div#runtime",data).text()=='1'){$("input#runtime").uCheck('toggle');}
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	window.onload=function(){query_basic();}
		</script>
<?php	
	break;
	case 'node':
	?>
	<div class="tpl-content-wrapper">
	 <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 节点设置
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
                        <div class="am-u-sm-9">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>选项</th>
			<th>输入</th>
        </tr>
    </thead>
	<tbody id='node-option'>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改,不勾选即被认为该功能模块位于该服务器上</small>  
						</div>
						<div class="am-u-sm-3">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>站点允许分布式部署,端口请用:进行分隔,如127.0.0.1:80</p>
 </div>
</section>	
						</div>
                    </div>
                </div>

            </div>
	</div>
	<script>
	function update_node_option(){
		var node_web=node_bot=node_sql='localhost';
		if($("input#shop_server_checkbox").val()){node_web=$("input#shop_server_input").val();}
		if($("input#bot_server_checkbox").val()){node_bot=$("input#bot_server_input").val();}
		if($("input#sql_server_checkbox").val()){node_sql=$("input#sql_server_input").val();}
		console.log(node_web);
		//return;
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  update_node_option: true , node_bot:node_bot , node_sql:node_sql , node_web:node_web },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>节点更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>节点数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
	function query_node(){
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  query_data: true , query_node:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				data=$("div#node-data",data).html();
				data=form_data(data);
				console.log(data);
				$("tbody#node-option").html(data);
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	window.onload=function(){query_node();}
	</script>
	<?php
	break;
	case 'appearance':
	?>
	<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 外观设置
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
                        <div class="am-u-sm-9">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
	<tr>
            <td><strong>导航条图标地址:</strong></td>
			<td><input type="text" class="am-form-field" id="system_header_icon" placeholder="头栏图标地址">
			<small>图标尺寸推荐为:142px x 30 px</small>
			</td>
        </tr>
		<tr>
            <td><strong>ICON图标地址:</strong></td>
			<td><input type="text" class="am-form-field" id="system_icon" placeholder="ICON地址"></td>
        </tr>
        <tr>
            <td><strong>首页滚动图地址:</strong></td>
            <td><input type="text" class="am-form-field" id="index_slides" placeholder="首页滚动图地址">
			<small>多个图片地址请使用"|"分隔,支持相对路径与外链混合使用</small>
			</td>
        </tr>
		<tr>
            <td></td>
			<td>
			<button class='am-btn am-btn-danger'type='button' onclick="if(confirm('配置有风险,请谨慎操作.请先在此确认服务器配置!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_appearance_option();}}"><span class="am-icon-refresh"></span> 更新配置</button>
			</td>
        </tr>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改</small>  
						</div>
						<div class="am-u-sm-3">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>该处的输入支持,相对路径/外链</p>
  <p>即:./img/1.jpg|http://www.xxx.com/1.jpg</p>
 </div>
</section>	
						</div>
                    </div>
                </div>

            </div>
        </div>
		<script>
	function update_appearance_option(){
		var system_header_icon=system_icon=index_slides='';
		system_header_icon=$("input#system_header_icon").val();
		system_icon=$("input#system_icon").val();
		index_slides=$("input#index_slides").val();
		//return;
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  update_appearance_option: true , system_header_icon:system_header_icon , system_icon:system_icon , index_slides:index_slides },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>外观参数更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>外观参数更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
	function query_appearance(){
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  query_data: true , query_appearance:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#system_header_icon").val($("div#system_header_icon",data).text());
				$("input#system_icon").val($("div#system_icon",data).text());
				$("input#index_slides").val($("div#index_slides",data).text());
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	window.onload=function(){query_appearance();}
	</script>
	<?php
	break;
	case 'signup':
	?>
	<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 注册设置
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
                        <div class="am-u-sm-9">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
	<tr>
            <td><strong>启用注册:</strong></td>
			<td><label class="am-checkbox">
      <input type="checkbox" id='enable_reg' value="" data-am-ucheck>
    </label>
	<small>若不启用该选项,则下列选项将不会生效</small>
	</td>
        </tr>
		<tr>
            <td><strong>启用邮箱验证:</strong></td>
			<td><label class="am-checkbox">
      <input type="checkbox" id='email_auth' value="" data-am-ucheck>
    </label></td>
        </tr>
        <tr>
            <td><strong>Smtp服务器地址:</strong></td>
            <td><input type="text" class="am-form-field" id="email_host" placeholder="Smtp服务器地址"></td>
        </tr>
		<tr>
            <td><strong>Smtp服务器端口:</strong></td>
            <td><input type="text" class="am-form-field" id="email_port" placeholder="Smtp服务器端口" ></td>
        </tr>
		<tr>
            <td><strong>邮箱用户名:</strong></td>
			<td><input type="text" class="am-form-field" id="email_user" placeholder="邮箱用户名"></td>
        </tr>
		<tr>
            <td><strong>邮箱密码:</strong></td>
			<td><input type="text" class="am-form-field" id="email_passw" placeholder="邮箱密码"></td>
        </tr>
		<tr>
            <td></td>
			<td>
			<button class='am-btn am-btn-danger'type='button' onclick="if(confirm('配置设置有风险,请谨慎操作.请先在此确认配置!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_reg_option();}}"><span class="am-icon-refresh"></span> 更新配置</button>
			</td>
        </tr>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改</small>  
						</div>
						<div class="am-u-sm-3">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>设置站点的全局设置</p>
 </div>
</section>	
						</div>
                    </div>
                </div>

            </div>
        </div>
		<script>
		function update_reg_option(){
		var email_host=email_port=email_user=email_passw='';
		var enable_reg=email_auth=false;
		email_host=$("input#email_host").val();
		email_port=$("input#email_port").val();
		email_user=$("input#email_user").val();
		email_passw=$("input#email_passw").val();
		if($("input#email_auth").prop("checked")){email_auth=true;}
		if($("input#enable_reg").prop("checked")){enable_reg=true;}
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  update_reg_option: true , email_auth:email_auth , enable_reg:enable_reg , email_host:email_host , email_port:email_port , email_user:email_user , email_passw:email_passw },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>注册配置数据更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>注册配置数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
		function query_reg(){
		$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:option',
				data: {  query_data: true , query_reg:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#email_host").val($("div#email_host",data).text());
				$("input#email_port").val($("div#email_port",data).text());
				$("input#email_user").val($("div#email_user",data).text());
				$("input#email_passw").val($("div#email_passw",data).text()); 
				if($("div#email_auth",data).text()=='1'){$("input#email_auth").uCheck('toggle');}
				if($("div#enable_reg",data).text()=='1'){$("input#enable_reg").uCheck('toggle');}
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	window.onload=function(){query_reg();}
		</script>
	<?php
	break;
} 
?>