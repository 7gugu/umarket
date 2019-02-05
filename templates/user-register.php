<?php 
if(!ENABLE_REG){setcookie("warning","站点已关闭注册!",time()+10);redirect("index.php?mod=login");}
?>
<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-key"></span> 注册
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-9">
						<?php 
						global $mode;
						global $steam;
						global $sql;
						global $e;
						$connect=$sql->connect();
						$parse = explode(':',$mode);
						if(isset($parse[1])){$step=$parse[1];}else{$step="0";}
						switch ($step) {
						case '4':
						if(isset($_GET['t'])){
						$context="select count(*) from umarket_account where `token`='{$_GET['t']}'";
						$res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
						if($res[0]==0){
						setcookie("warning","很抱歉,该链接已失效,请重新发起验证",time()+5);
						redirect("index.php?mod");
						}else{
						$context="select * from umarket_account where `token`='{$_GET['t']}'";
						$res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
						if(time()>$res['token_exptime']){
						setcookie("warning","很抱歉,该链接已失效,请重新发起验证",time()+5);
						redirect("index.php?mod");
						}else{
						$context="update umarket_account set `token_status`='1' where `token`='{$_GET['t']}'";//token_status 0未验证邮箱1已验证邮箱
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
						}else{
						setcookie("fail","拒绝访问",time()+5);
						redirect("index.php?mod");		
						}
						break;
						case '3':
						if(isset($_POST['captcha'])){
						$username=$_POST['username'];
						$password=$_POST['password'];
						$email=$_POST['email'];
						$phone=$_POST['phone'];
						$steamid=$_SESSION['steamid'];
						$avatar=$_SESSION['steamavatar'];
						//$steamid="0";//debug用
						$captcha=$_POST['captcha'];
						if($capcha==$_SESSION['capcha']){
                        $context="select count(*) from umarket_account where `username`='{$username}' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
                       
						if($res[0]!=0){
						setcookie("warning","很抱歉,该用户名已被注册,请更换一个",time()+5);
						redirect("index.php?mod=register:2");
						}
						$context="select count(*) from umarket_account where `email`='{$email}' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
						if($res[0]!=0){
						setcookie("warning","很抱歉,该邮箱已被注册,请更换一个",time()+5);
						redirect("index.php?mod=register:2");
						}
						$context="select count(*) from umarket_account where `phone`='{$phone}' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
						if($res[0]!=0){
						setcookie("warning","很抱歉,该电话已被注册,请更换一个",time()+5);
						redirect("index.php?mod=register:2");
						}
						$context = "select * from umarket_account order by accountid DESC limit 1 ";
	                    $res=$sql->query($connect,$context);
	                    $res=$sql->fetch_array($res);
						$accountid=0;if($res){$accountid=$res[0];}$accountid++;
					    $regtime=date("Y-m-d h:i:sa");
						if(EMAIL_AUTH){
						$token_exptime=time()+1800;//验证链接只有30min的有效期
						$token=md5(time());
						$e->send($username.' <'.$email.'>',SYSTEM_SNAME.' <'.EMAIL_USER.'>',SYSTEM_SNAME.'-邮箱验证','请点击以下链接进行认证操作<br>'.NODE_WEB.'index.php?mod=user:register:4&t='.$token);
						}
						$context="insert into umarket_account(accountid,username,password,email,status,regtime,token,token_exptime,steamid,tradelink,phone,access,token_status,avatar)values('{$accountid}','{$username}','{$password}','{$email}','1','{$regtime}','{$token}','{$token_exptime}','{$steamid}','','{$phone}','0','0','{$avatar}')";
						$sql->query($connect,$context);
						$context="select * from umarket_wallet order by wallet_id desc limit 1 ";
						$res=$sql->fetch_array($sql->query($connect,$context));
						$wallet_id=0;
						if($res){
						$wallet_id=$res['wallet_id'];
						}
						$wallet_id++;
						$context="INSERT INTO `umarket_wallet` (`wallet_id`, `wallet_steamid`, `wallet_balance`, `wallet_balance_alipay`, `wallet_alipay_realname`, `wallet_alipay_account`) VALUES ('{$wallet_id}','{$steamid}','0.00','0.00','','')";
						$sql->query($connect,$context);
						$res=$sql->affected_rows($connect);
						if($res>0){
						setcookie("suc","注册成功啦!现在你可以登录系统了owo",time()+35);
						//setcookie("username",$username,time()+3600);
	                    //setcookie("password",$password,time()+3600);
						redirect("index.php?mod=register:4");
						}else{
						setcookie("warning","注册失败,请联系管理员或再试试",time()+5);
						redirect("index.php?mod=register:2");		
						}
						$sql->close($connect);
						}else{
						setcookie("warning","验证码错误,请再试试",time()+5);
						redirect("index.php?mod=register:2");	
						}
						}else{
						setcookie("warning","注册失败,请联系管理员或再试试",time()+5);
						redirect("index.php?mod=register:2");	
						}
                        session_destroy();				
						break;
							?>
						<?php
                        case '2'://获取具体数据                        					
						?>
						<h2>第二步:</h2>
						<?php 
						loadalert();
						if(isset($_SESSION['steamid'])&&$_SESSION['steamid']!=""){ 
						?>
					<script type="text/javascript">
function check(form){
if(form.username.value==''){alert('用户名不能为空！');form.username.focus();return false;}
if(form.password.value==''){alert('密码不能为空！');form.password.focus();return false;}
if(form.confirmpassword.value==''){alert('重复密码不能为空！');form.confirmpassword.focus();return false;}
if(form.email.value==''){alert('邮箱不能为空！');form.email.focus();return false;}
if(form.phone.value==''){alert('手机号不能为空！');form.phone.focus();return false;}
if(form.captcha.value==''){alert('验证码不能为空！');form.captcha.focus();return false;}
if(form.password.value.length<6){alert('密码至少为6位，请重新输入！');form.password.focus();return false;}
if(form.password.value!=form.confirmpassword.value){alert('你两次输入的密码不一致，请重新输入！');form.confirmpassword.focus();return false;}
if(form.captcha.value.length>4){alert('你验证码输多了！');form.confirmpassword.focus();return false;}
return true;
}
    </script>
                            <form class="am-form am-form-horizontal" method="post" action="index.php?mod=register:3" onSubmit="return check(this)">
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">用户名 / Username</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" id="username" name="username" placeholder="用户名长度为6-15位">
                                        <small>输入你的名字,让我们记住你。</small>
                                    </div>
                                </div>
                                 <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">密码 / Password</label>
                                    <div class="am-u-sm-9">
                                        <input type="password" id="password" name="password" placeholder="">
                                        <small>输入你的密码,您的密码将会进行加密处理,密码至少为6位。</small>
                                    </div>
                                </div>
								 <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">确认密码 / Name</label>
                                    <div class="am-u-sm-9">
                                        <input type="password" name="confirmpassword" id="confirmpassword" placeholder="">
                                        <small>请再次输入你的密码。</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-3 am-form-label">电子邮件 / Email</label>
                                    <div class="am-u-sm-9">
                                        <input type="email" id="email" name="email" placeholder="输入你的电子邮件 / Email">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-3 am-form-label">手机号 / Phone</label>
                                    <div class="am-u-sm-9">
                                        <input type="tel" name="phone" id="phone" placeholder="">
										<small>输入你的手机号码</small>
                                    </div>
                                </div>
								
								<div class="am-form-group">
                                    <label for="user-steamid" class="am-u-sm-3 am-form-label">SteamID</label>
                                    <div class="am-u-sm-9">
                                        <input type="number" id="steamid" name="steamid" value="<?php if(isset($_SESSION['steamid']))echo $_SESSION['steamid']; ?>" disabled>
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="captcha" class="am-u-sm-3 am-form-label">验证码</label>
                                    <div class="am-u-sm-5">
									<input type="text"  id="captcha_input" name="captcha" placeholder="">
									<small>注意大小写:)</small>  
									</div>
									<div class="am-u-sm-4">
									<img src="./lib/image_captcha.php"  onclick="this.src='./lib/image_captcha.php?'+new Date().getTime();" style="cursor: pointer;  margin-left: 10px; margin-bottom: 10px;">
                                     <input type="hidden" name="register" id="register">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">下一步</button>
                                    </div>
                                </div>
                            </form>
						<?php 
						}else{
						setcookie("warning","请先完成上一步操作再进行该步骤",time()+10);
						redirect("index.php?mod=register");						
						}
						break;
						case '1'://获取返回的数据并重定向到下一步
                        if($steam->loggedIn()){
						$_SESSION['steamid']=$steam->steamid;
						$_SESSION['steamavatar']=$steam->avatar;
                        $context="select count(*) from umarket_account where `steamid`='{$steamid}' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
                        $sql->close($connect);
						if($res[0]==0){
						setcookie("suc","做的很好,我们已经获取了您的steamID,接下来,请填写以下表格完成注册",time()+35);
						redirect("index.php?mod=register:2");
						}else{
						setcookie("warning","很抱歉,该SteamID已被注册,请绑定其他Steam账户",time()+35);
						redirect("index.php?mod=register:2");	
						}
						}else{
						setcookie("fail","很抱歉,我们无法获取到您的steamID,请重试一遍/寻求管理员",time()+35);
						redirect("index.php?mod=register");	
						}
						break;					
						default: //step0
						?>
						<h2>第一步:</h2>
						<?php loadalert(); ?>
						<button type='button'  onclick="javascript:window.location.href='<?php echo $steam->loginUrl(); ?>'" class='am-btn am-btn-warning'><span class="am-icon-steam"></span>Steam登录</button>
                        <br>
						<small>我们需要获取您的steamID,请戳上面的按钮帮助我们完成这一步</small>
						<?php
						break;
						} 
						?>
                        </div>
                    </div>
                </div>

            </div>
