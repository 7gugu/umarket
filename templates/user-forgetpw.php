<?php 
if(!EMAIL_AUTH){msg('该站点已关闭找回密码功能');}
?>
<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-key"></span> 找回密码
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-9">
						<?php 
						global $mode;
						global $sql;
						global $e;
						$connect=$sql->connect();
						$parse = explode(':',$mode);
						if(isset($parse[2])){$step=$parse[2];}else{$step="0";}
						switch ($step) {
						case '3':
						if(isset($_POST['captcha'])){
						$password=$captcha=$token="";
						$captcha=$_POST['captcha'];
						if($capcha==$_SESSION['capcha'] && isset($_POST['token'])){
						$token=$_POST['token'];
						$password=$_POST['password'];
                        $context="select * from umarket_account where `token`='{$token}' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
						if($res['password']!=$password){
						//更新密码并跳转登录页面
						$context="update umarket_account set `password`='{$password}' where `token`='{$token}'";
						$res=$sql->query($connect,$context);
                        $res=$sql->affected_rows($connect);
						if($res==0){
						setcookie("warning","很抱歉,密码修改失败,请稍后再试/联系管理员",time()+5);
						redirect('http://localhost/umarket/dev/php/index.php?mod=user:forgetpw:2&f='.$token);
						}else{
						setcookie("suc","密码修改成功",time()+5);
						redirect("index.php?mod=user:login");	
						}
						}else{
						setcookie("warning","很抱歉,新密码不能与旧密码相同,请换一个",time()+5);
						redirect('http://localhost/umarket/dev/php/index.php?mod=user:forgetpw:2&f='.$token);	
						}					
						$sql->close($connect);
						}else{
						setcookie("warning","验证码错误或丢失部分数据,请再试试",time()+5);
						redirect('http://localhost/umarket/dev/php/index.php?mod=user:forgetpw:2&f='.$_POST['token']);	
						}
						session_destroy();	
						}else{
						setcookie("warning","验证码错误或丢失部分数据,请再试试",time()+5);
						redirect("index.php?mod=user:forgetpw:2");	
						}			
						break;
							?>
						<?php
                        case '2'://获取具体数据                        					
						?>
						<h2>修改我的密码:</h2>
						<?php 
						loadalert();
						if(!isset($_GET['f'])){setcookie("warning","无法获取验证信息,请重试或联系管理员",time()+10);redirect("index.php?mod=user:forgetpw");}
						$context="select * from umarket_account where `token`='{$_GET['f']}' ";  
                        $t=$res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);						
						if($t && time()<$res['token_exptime']){ 
						?>
					<script type="text/javascript">
function check(form){
if(form.password.value==''){alert('密码不能为空！');form.password.focus();return false;}
if(form.confirmpassword.value==''){alert('重复密码不能为空！');form.confirmpassword.focus();return false;}
if(form.captcha.value==''){alert('验证码不能为空！');form.captcha.focus();return false;}
if(form.password.value.length<6){alert('密码至少为6位，请重新输入！');form.password.focus();return false;}
if(form.password.value!=form.confirmpassword.value){alert('你两次输入的密码不一致，请重新输入！');form.confirmpassword.focus();return false;}
if(form.captcha.value.length>4){alert('你验证码输多了！');form.confirmpassword.focus();return false;}
return true;
}
    </script>
                            <form class="am-form am-form-horizontal" method="post" action="index.php?mod=user:forgetpw:3" onSubmit="return check(this)">
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
                                    <label for="captcha" class="am-u-sm-3 am-form-label">验证码</label>
                                    <div class="am-u-sm-5">
									<input type="text"  id="captcha_input" name="captcha" placeholder="">
									<small>注意大小写:)</small>  
									</div>
									<div class="am-u-sm-4">
									<img src="./lib/image_captcha.php"  onclick="this.src='./lib/image_captcha.php?'+new Date().getTime();" style="cursor: pointer;  margin-left: 10px; margin-bottom: 10px;">
                                     <input type="hidden"  id="token" name="token" placeholder="">
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
						redirect("index.php?mod=user:forgetpw");						
						}
						break;
						case '1'://获取返回的数据并重定向到下一步
                        if(isset($_POST['email'])){
						$email=$_POST['email'];
                        $context="select count(*) from umarket_account where `email`='{$email}' and `token_status`='1' ";
                        $res=$sql->query($connect,$context);
                        $res=$sql->fetch_array($res);
                        $sql->close($connect);
						if($res[0]==1){
						$token_exptime=time()+1800;//验证链接只有30min的有效期
						$token=md5(time());
						$context="update umarket_account set `token`='{$token}' and `token_exptime`='{$token_exptime}' where `email`='{$_POST['email']}'";
						$res=$sql->query($connect,$context);
						$e->send('可爱的你 <'.$email.'>','7gugu <gz7gugu@qq.com>',SYSTEM_SNAME.'-邮箱验证','请点击该链接进行认证操作'.'http://localhost/umarket/dev/php/index.php?mod=user:forgetpw:2&f='.$token);
						setcookie("suc","做的很好,我们已经核对了您的身份并发送一确认信到该邮箱,接下来,请前往邮箱认证",time()+5);
						redirect("index.php?mod");
						}else{
						setcookie("warning","很抱歉,我们无法核实您的身份,请检查您的邮箱拼写/联系管理员",time()+5);
						redirect("index.php?mod");	
						}
						}else{
						setcookie("fail","很抱歉,系统貌似出了毛病,请重试一遍/寻求管理员",time()+35);
						redirect("index.php?mod");	
						}
						break;					
						default: //step0
						?>
						<h2>第一步:</h2>
						<?php loadalert(); ?>
						<form class="am-form am-form-horizontal" method="post" action="index.php?mod=user:forgetpw:1">
                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-3 am-form-label">电子邮件 / Email</label>
                                    <div class="am-u-sm-9">
                                        <input type="email" id="email" name="email" placeholder="输入你的电子邮件 / Email">
                                        <small>我们需要核对您的邮箱以便确认您的身份,请填写上面的输入栏帮助我们完成这一步</small><br>
										<small>若您未验证邮箱,则请联系管理员</small>
									</div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">下一步</button>
                                    </div>
                                </div>
                            </form>				
						<?php
						break;
						} 
						?>
                        </div>
                    </div>
                </div>

            </div>
