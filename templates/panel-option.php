<?php 
global $sql;
global $e;
$connect=$sql->connect();
if(isset($_POST['update_data'])){
if(isset($_POST['update_password'])){
@$new_password=$_POST['new_password'];
@$confirm_password=$_POST['confirm_password'];
if($new_password==""||$confirm_password==""){echo "<div id='response'>0</div><div id='ajax'>传值为空</div>";die;}
if($new_password!=$confirm_password){echo "<div id='response'>0</div><div id='ajax'>重复输入密码错误</div>";die;}
$context="select * from umarket_account where `username`='{$_COOKIE['username']}' and `password`='{$_POST['original_password']}'";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
if($res){
$sql->query($connect,"update umarket_account set `password`='{$confirm_password}' where `password`='{$_POST['original_password']}' and `username`='{$_COOKIE['username']}'");
if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div>";
	setcookie("password",$confirm_password,time()+3600);
	if(!EMAIL_AUTH){@$e->send($_COOKIE['username'].' <'.$_COOKIE['email'].'>',SYSTEM_SNAME.' <'.EMAIL_USER.'>',SYSTEM_SNAME.'-数据变更通知','刚刚账户['.$_COOKIE['email'].']的账号信息发生了变化!');}
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
}else{
echo "<div id='response'>0</div><div id='ajax'>原密码错误</div>";
}
die;
}
echo "<div id='response'>0</div><div id='ajax'>传参错误</div>";
die;
}
if(isset($_POST['query_data'])){
$context="select * from umarket_account where `username`='{$_COOKIE['username']}' and `password`='{$_COOKIE['password']}'";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
if($res){
	echo "<div id='response'>1</div>";
	echo "<div id='username'>".$res['username']."</div>";
	echo "<div id='email'>".$res['email']."</div>";
	echo "<div id='steamid'>".$res['steamid']."</div>";
	echo "<div id='phone'>".$res['phone']."</div>";
	echo "<div id='tradelink'>".$res['tradelink']."</div>";
	echo "<div id='email_button'><button class='am-btn am-btn-default' type='button' onclick='auth_email()'>验证邮箱</button></div>";
}else{
	echo "<div id='response'>0</div><div id='ajax'>查询失败</div>";
}
die;
}

if(isset($_POST['update_trade_link'])){
	@$tradelink=htmlspecialchars($_POST['tradelink']);
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_account where `username`='{$_COOKIE['username']}'"));
	if($res['tradelink']==$tradelink){echo "<div id='response'>1</div>";die;}
	$context="update umarket_account set `tradelink`='{$tradelink}' where `username`='{$_COOKIE['username']}'";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
	die;
}
if(isset($_POST['auth_email'])){

$token_exptime=time()+1800;//验证链接只有30min的有效期
$token=md5(time());
$email=htmlspecialchars($_POST['email']);
$e->send($username.' <'.$email.'>',SYSTEM_SNAME.' <'.EMAIL_USER.'>',SYSTEM_SNAME.'-邮箱验证','请点击以下链接进行认证操作<br>'.NODE_WEB.'index.php?mod=panel:emailauth&auth='.$token);
$sql->query($connect,"update umarket_account `token`='{$token}',`token_status`='0',`email`='{$email}' where `username`='{$_COOKIE['username']}' and `password`='{$_COOKIE['password']}'");
if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div>";
	}else{
	echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
	}
}

if(isset($_GET['auth'])){
	$auth=htmlspecialchars($_GET['auth']);
	$context="select count(*) from umarket_account where `token`='{$auth}'";
	$res=$sql->query($connect,$context);
    $res=$sql->fetch_array($res);
	if($res[0]==0){
		setcookie("warning","很抱歉,该链接已失效,请重新发起验证",time()+5);
		redirect("index.php?mod");
	}else{
		$context="select * from umarket_account where `token`='{$auth}'";
		$res=$sql->query($connect,$context);
        $res=$sql->fetch_array($res);
			if(time()>$res['token_exptime']){
				setcookie("warning","很抱歉,该链接已失效,请重新发起验证",time()+5);
				redirect("index.php?mod");
			}else{
				$context="update umarket_account set `token_status`='1' where `token`='{$auth}'";//token_status 0未验证邮箱1已验证邮箱
				$res=$sql->query($connect,$context);
                $res=$sql->affected_rows($connect);
					if($res==0){
						setcookie("warning","很抱歉,验证系统繁忙,请稍后再试/联系管理员",time()+5);
						redirect("index.php?mod");
						}else{
						setcookie("suc","邮箱验证成功",time()+5);
						redirect("index.php?mod");	
						}
				}
		}
	}

?>
<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 账号设置
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
                        <div class="am-u-sm-12 am-u-md-9">
						<div class="am-panel am-panel-default">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">个人信息</h3>
  </div>
  <ul class="am-form am-list am-list-static">
    <li><div class="am-input-group">
  <span class="am-input-group-label">用户名</span>
  <input type="text" class="am-form-field" placeholder="用户名" id="username" disabled />
</div></li>
	<li><div class="am-input-group">
  <span class="am-input-group-label">Steam ID</span>
  <input type="text" class="am-form-field" placeholder="SteamID" id="steamid" disabled />
</div></li>
	<li><div class="am-input-group">
  <span class="am-input-group-label">手机号码</span>
  <input type="text" class="am-form-field" placeholder="手机号码" id="phone" disabled />
</div></li>
	<li><div class="am-input-group">
  <span class="am-input-group-label">邮箱地址</span>
  <input type="text" class="am-form-field" placeholder="邮箱地址" id="email" />
<span class="am-input-group-btn" id="email_button">
        <button class="am-btn am-btn-default" type="button">修改邮箱</button>
      </span>
</div></li>
	<li>
	<div class="am-input-group"><span class="am-input-group-label">交易链接:</span><input type="text" class="am-form-field"  placeholder="我的交易链接" id="tradelink" />
	<span class="am-input-group-btn" id="trade_link_button">
        <button class="am-btn am-btn-default" type="button" onclick="update_trade_link()">保存修改</button>
      </span>
	</div>
	</li>
	
  </ul>
  <div class="am-panel-footer"><span class="label label-info">若交易链接为空,则账号的交易功能将被禁用</span></div>
</div>
							</div>
							<div class="am-u-sm-12 am-u-md-3">
						<div class="am-panel am-panel-default">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">提醒:</h3>
  </div>
  <div class="am-panel-bd">
   截图前,记得进行打码处理
  </div>
</div>
							</div>
							<div class="am-u-sm-12 am-u-md-9 am-u-end">
						<div class="am-panel am-panel-default">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">修改密码</h3>
  </div>
  <ul class="am-form am-list am-list-static">
    <li><div class="am-input-group">
  <span class="am-input-group-label">原密码</span>
  <input type="text" class="am-form-field" placeholder="Original Password" id="original_password">
</div></li>
	<li><div class="am-input-group">
  <span class="am-input-group-label">新密码</span>
  <input type="text" class="am-form-field" placeholder="New Password" id="new_password">
</div></li>
	<li><div class="am-input-group">
  <span class="am-input-group-label">重复新密码</span>
  <input type="text" class="am-form-field" placeholder="Confirm New Password" id="confirm_password">
</div></li>
  </ul>
  <div class="am-panel-footer"> <button type="button" class="am-btn am-btn-danger" onclick="update_password();">修改密码</button></div>
</div>
<script>
function update_password(){
	var original_password=$("input#original_password").val();
	var new_password=$("input#new_password").val();
	var confirm_password=$("input#confirm_password").val();
	if(original_password==""){alert("原密码不可为空!");return;}
	if(new_password==""){alert("新密码不可为空!");return;}
	if(confirm_password==""){alert("重复密码不可为空!");return;}
	if(confirm_password!=new_password){alert("重复密码输入错误!");return;}
	$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=panel:option',
				data: {  update_data:true , update_password:true , original_password:original_password , new_password:new_password , confirm_password:confirm_password },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>密码修改成功,请重新登陆一下...</div>";
				}else{
				var alert="<div class='am-alert am-alert-danger' id='query_alert'>密码修改失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				}
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close');},3000);
			}
		});
}



function query_option(){
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=panel:option',
				data: {  query_data: true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				//console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#email").val($("div#email",data).text());
				$("input#phone").val($("div#phone",data).text());
				$("input#username").val($("div#username",data).text());
				$("input#steamid").val($("div#steamid",data).text()); 
				$("input#tradelink").val($("div#tradelink",data).text());
				$("#email_button").html($("div#email_button",data).html());
				console.log($("div#tradelink",data).text());
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	
function update_trade_link(){
	var tradelink=$("input#tradelink").val();
	$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=panel:option',
				data: {  update_trade_link:true , tradelink:tradelink },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>交易链接修改成功...</div>";
				query_option();
				}else{
				var alert="<div class='am-alert am-alert-danger' id='query_alert'>交易链接修改失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				}
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close');},3000);
			}
		});
}

function auth_email(){
	var email=$("input#email").val();
	$.ajax({
		type: 'POST',
				async: false, 
				url: 'index.php?mod=panel:option',
				data: {  auth_email:true , email:email },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				//console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>修改邮箱请求成功,请到邮箱查收邮件进行修改/验证操作</div>";
				query_option();
				}else{
				var alert="<div class='am-alert am-alert-danger' id='query_alert'>修改邮箱请求失败,[错误原因]-["+$("div#ajax",data).text()+"]</div>";
				}
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close');},3000);
			}
		});
}
	window.onload=function(){query_option();}
</script>
							</div>
						</div>
                    </div>
                </div>

            </div>
        </div>
