<?php 
ob_start();
require 'install_fuc.php';
if(file_exists("../assets/install.lock")&&!isset($_GET['s'])){
	header("location:install.php?step&s=8");
	exit();
}
if(isset($_POST['dbname'])){
	$dbname=htmlspecialchars($_POST['dbname']);
	$dbhost=htmlspecialchars($_POST['dbhost']);
	$dbusername=htmlspecialchars($_POST['dbusername']);
	$dbpassword=htmlspecialchars($_POST['dbpassword']);
	if($dbhost!="localhost"){
		$dbhost_array=explode(":",$dbhost);
		$dbhost=$dbhost_array[0];
		if(count($dbhost_array)<=1){$dbport=3306;}else{$dbport=$dbhost_array[1];}
	}
	$connect=mysqli_connect($dbhost,$dbusername,$dbpassword,$dbname,$dbport);
	if($connect){
		setcookie("suc",'1',time()+30);
		setcookie("dbhost",$dbhost,time()+3600);
		setcookie("dbport",$dbport,time()+3600);
		setcookie("dbusername",$dbusername,time()+3600);
		setcookie("dbpassword",$dbpassword,time()+3600);
		setcookie("dbname",$dbname,time()+3600);
	}else{
		setcookie("suc",'0',time()+30);
	}
	header("location:install.php?step&s=4");
	exit();
}

if(isset($_GET['step'])&&$_GET['s']==5){
	$dbport=3306;
	$dbname=htmlspecialchars($_COOKIE['dbname']);
	$dbhost=htmlspecialchars($_COOKIE['dbhost']);
	$dbusername=htmlspecialchars($_COOKIE['dbusername']);
	$dbpassword=htmlspecialchars($_COOKIE['dbpassword']);
	if($dbhost!="localhost"){
		$dbhost_array=explode(":",$dbhost);
		$dbhost=$dbhost_array[0];
		if(count($dbhost_array)<=1){$dbport=3306;}else{$dbport=$dbhost_array[1];}
	}
	$connect=mysqli_connect($dbhost,$dbusername,$dbpassword,$dbname,$dbport);
	//导入数据表[若存在则会自动跳过]
	loadsql($connect,"./umarket.sql");
	mysqli_close($connect);
}

if(isset($_GET['step'])&&$_GET['s']==6){
//获取POST过来的数据
	$dbport=3306;
	$dbname=htmlspecialchars($_COOKIE['dbname']);
	$dbhost=htmlspecialchars($_COOKIE['dbhost']);
	$dbusername=htmlspecialchars($_COOKIE['dbusername']);
	$dbpassword=htmlspecialchars($_COOKIE['dbpassword']);
	if($dbhost!="localhost"){
		$dbhost_array=explode(":",$dbhost);
		$dbhost=$dbhost_array[0];
		if(count($dbhost_array)<=1){$dbport=3306;}else{$dbport=$dbhost_array[1];}
	}
	$admin_username=htmlspecialchars($_POST['admin_username']);
	if($admin_username==""){$admin_username="admin";}
	$admin_password=htmlspecialchars($_POST['admin_password']);
	if($admin_password==""){$admin_password="admin";}
	$admin_steamid=htmlspecialchars($_POST['admin_steamid']);
	if($admin_steamid==""){setcookie('suc','4',time()+30);}
	$admin_email=htmlspecialchars($_POST['admin_eamil']);
	if($admin_email==""){setcookie('suc','4',time()+30);}
	$webapi_key=htmlspecialchars($_POST['webapi_key']);
	$system_root=htmlspecialchars($_POST['system_root']);
	if($system_root==""){$system_root=__FILE__;}
	$system_name=htmlspecialchars($_POST['system_name']);
	$system_sort_name=htmlspecialchars($_POST['system_sort_name']);
	$weblink=htmlspecialchars($_POST['weblink']);
//初始化配置文件
$new_contents="
<?php
//网页不可调的参数
date_default_timezone_set('Asia/Shanghai');
define('DBUSERNAME','".$dbusername."');//数据库用户名
define('DBPASSWORD','".$dbpassword."');//数据库密码
define('DBNAME','".$dbname."');//数据库名
define('APIKEY','".$webapi_key."');//Web API秘钥
define('SYSTEM_VER','1.00');//版本号
define('MODE_DEBUG',false);//是否开启调试模式
define('MODE_MANUAL',false);//是否开启手动模式[机器人二步验证,手动输入]
//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
define('SYSTEM_ROOT','".$system_root."');//系统根目录
define('SUPPORT_URL', 'https://www.7gugu.com');//作者博客地址
define('SYSTEM_NO_ERROR', FALSE );//关闭系统错误提示
define('SYSTEM_REV','1.0');//版本修订号
?>
	";
$config_main=file_put_contents($system_root."/config/config.php",trim($new_contents),LOCK_EX);//判断是否初始化正确
$new_contents="
<?php
define(\"DBIP\",\"".$dbhost."\");//数据库IP(一般不需修改)
define(\"DBPORT\",\"".$dbport."\");//数据库端口
define(\"BOTIP\",\"127.0.0.1\");//机器人服务器IP
define(\"BOTPORT\",\"7979\");//机器人后端端口
define(\"WEBIP\",\"".$weblink."\");//Web服务器IP
?>
	";
$config_node=file_put_contents($system_root."/config/config-node.php",trim($new_contents),LOCK_EX);
$new_contents="
<?php
define('APPID','');//https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
define('SIGNTYPE','RSA2');//签名算法类型，支持RSA2和RSA，推荐使用RSA2
define('RSAPRIVATEKEY','');//商户私钥
define('RSAPUBLICKEY','');//商户公钥
?>
";
$config_alipay=file_put_contents($system_root."/config/config-alipay.php",trim($new_contents),LOCK_EX);
$new_contents="
<?php
define('ENABLE_REG',true);
define('EMAIL_HOST','smtp.qq.com');
define('EMAIL_PORT',25);
define('EMAIL_AUTH',false);
define('EMAIL_USER','');
define('EMAIL_PASSW','');
?>
";
$config_reg=file_put_contents($system_root."/config/config-reg.php",trim($new_contents),LOCK_EX);
$new_contents="
<?php
define('SYSTEM_NAME','".$system_name."');
define('SYSTEM_SNAME','".$system_sort_name."');
define('SYSTEM_FOOTER_RECORD_NUMBER','');
define('SYSTEM_FOOTER_SIGN','7gugu');
define('USER_AVATAR',true);
define('RUNTIME',true);
?>
";
$config_basic=file_put_contents($system_root."/config/config-basic.php",trim($new_contents),LOCK_EX);
$new_contents="
<?php
define('SYSTEM_HEADER_ICON','./assets/img/logo.png');
define('SYSTEM_ICON','./assets/i/favicon.png');
define('INDEX_SLIDES','');
?>
";
$config_appearance=file_put_contents($system_root."/config/config-appearance.php",trim($new_contents),LOCK_EX);
$regtime=date("Y-m-d h:i:sa");
$context="insert into umarket_account(accountid,username,password,email,status,regtime,token,token_exptime,steamid,tradelink,phone,access,token_status,avatar)values('1','{$admin_username}','{$amin_password}','{$admin_email}','1','{$regtime}','0','0','{$admin_steamid}','','0','0','2','0')";
$connect=mysqli_connect($dbhost,$dbusername,$dbpassword,$dbname,$dbport);
mysqli_query($connect,$context);
mysqli_close($connect);
	if($config_main&&$config_reg&&$config_alipay&&$config_appearance&&$config_basic&&$config_node){
		setcookie('suc','3',time()+30);//配置写入成功
	}else{
		setcookie('suc','4',time()+30);//配置写入失败
	}
}
if(isset($_GET['step'])&&$_GET['s']==7){
	file_put_contents("../assets/install.lock",'');
	header("location:/index.php");
	exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Umarket|安装引导</title>
<meta name="generator" content="Umarket Ver-1.00" />
<meta name="author" content="7gugu (https://www.7gugu.com)" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="renderer" content="webkit"><meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="stylesheet" href="../assets/css/amazeui.min.css">
<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/app.css">
<script src="../assets/js/jquery.min.js"></script>
<script>
function display_step(display_step,step){
	$("div#step"+display_step).attr("style",'display:none');
	$("div#step"+step).attr("style",'');
}
</script>
</head>
<body>
<header class="am-topbar am-topbar-inverse admin-header">
        <div class="am-topbar-brand">
            <a href="javascript:;" class="tpl-logo">
                <img src="../assets/img/logo.png" alt="">
            </a>
        </div>
    </header>
	<div class="tpl-page-container tpl-page-header-fixed">
<div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
              Umarket | 安装流程            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
				                    <li class="tpl-left-nav-item">
					<a href="" class="nav-link">
                            <i class="am-icon-file"></i>
                            <span>确认协议</span>
                    </a>
                    </li>
					  <li class="tpl-left-nav-item">
					<a href="" class="nav-link">
                            <i class="am-icon-server"></i>
                            <span>环境检测</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="" class="nav-link">
                            <i class="am-icon-terminal"></i>
                            <span>配置参数</span>
                    </a>
                    </li>
					                    <li class="tpl-left-nav-item">
                        <a href="" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-plug"></i>
                            <span>导入数据结构</span>

                        </a>
                    </li>
					    <li class="tpl-left-nav-item">
                        <a href="" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-check"></i>
                            <span>安装完成</span>
                        </a>
                    </li>					
                </ul>
            </div>
        </div>
<div class="tpl-content-wrapper">

<div id='step1'>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 阅读安装协议
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<pre class="am-pre-scrollable">
 Umarket《最终用户使用许可协议书》 V1.0
        【首部及导言】
        架设或使用Umarket开源商城项目（包括此程序本身及相关所有文档，以下简称“Umarket”）及其衍生品（包括在Umarket基础上二次开发的项目及依赖Umarket程序运行的插件等），您应当阅读并遵守Umarket《最终用户使用许可协议书》（以下简称“协议”） 。请您务必审慎阅读、充分理解各条款内容，协议中的重点内容可能以加粗或加下划线的形式提示您重点注意。除非您已阅读并接受本协议所有条款，否则您无权架设或使用Umarket。您架设或使用Umarket即视为您已阅读并同意本协议的约束。 
一、【协议的范围】
        本协议是Umarket用户与7gugu之间关于用户架设或使用Umarket及其衍生品所订立的协议。“7gugu” 主要指http://www.7gugu.com/网站及其运营和管理人员。“用户”是指架设或使用Umarket及其衍生品的架设者、使用人，以下也成为“您”。
二、【许可使用】
        2.1 通过任何途径下载的Umarket程序，在不违反本协议的前提下可自行架设、使用。
        2.2 其它参考Umarket及其衍生品的源代码的程序，须征得至少两位Umarket开发者同意后，在标示原有版权信息的情况下发行、架设、使用。
三、【禁止使用】
        3.1 不得以任何理由、任何手段（包括但不限于删减、遮挡、修改字号、添加nofollow属性等）修改Umarket及其衍生品原有的版权信息、版权链接的指向及友情链接，并保证原有版权能够在显眼处展示。
        3.2 禁止以任何形式向他人兜售Umarket的复制品或延伸产品。
四、【许可终止】
        4.1 无论何时，如果您主动放弃或被收回了许可，您必须立即销毁Umarket的所有复制品、衍生品，并关闭任何由Umarket搭建的服务。
        4.2 如您违反了本协议的任何一项条款和条件，则视为一切许可被收回。
        4.3 爱用不用，不用就滚，一旦您滚蛋，则视为一切许可被收回。
五、【权利保留】
        未明确声明的一切其它权利均为7gugu所保留，对本协议的一切解释权归7gugu所有。
六、【责任限度】
        在适用法律所允许的最大范围内，7gugu在任何情况下绝不就因使用或不能使用Umarket或就未提供支持服务所发生的任何特殊、意外、非直接或间接的损害负赔偿责任，即使事先被告知该损害发生的可能性。
七、【协议的生效与变更】
        7.1 7gugu有权在必要时修改本协议条款。您可以在相关页面查阅最新版本的协议条款。
        7.2 本协议条款变更后，如果您继续架设或使用Umarket及其衍生品，即视为您已接受修改后的协议。如果您不接受修改后的协议，那么视为您放弃一切许可（参见4.1）。
八、【适用和管辖法律】
        《中华人民共和国著作权法》、《中华人民共和国计算机软件保护条例》、《中华人民共和国商标法》、《中华人民共和国专利法》等中华人民共和国法律。
        【结语】
        本协议和上述有限保证及责任限制受中华人民共和国法律管辖。
        此外，在安装过程中，我们将会收集一些信息以便于统计安装量及改进体验。我们绝不会收集您的隐私信息，也不会向任何人泄露这些信息。
        至此，您肯定已经详细阅读并已理解本协议，并同意严格遵守全部条款和条件。
7gugu
</pre>
<button class='am-btn am-btn-success'type='button' onclick="if(confirm('“我尊重原作者为Umarket付出的心血，在使用该系统的同时将保护原作者的版权。\r\n保证原作者的名称、链接等版权信息不被删改、淡化或遮挡，如果我没有做到，自愿承担由此引发的所有不良后果”\r\n\r\n同意请确定，不同意请取消')){$('div#step1').attr('style','display:none;');$('div#step2').attr('style','');} else {alert('请立即删除所有与本程序相关的文件及其延伸产品');window.opener=null;window.open('','_self');window.close();}">我接受</button>
             <button class='am-btn am-btn-danger'type='button' onclick="alert('请立即删除所有与本程序相关的文件及其延伸产品');window.opener=null;window.open('','_self');window.close();">我拒绝</button>
             <br><br>
						</div>
                    </div>
                </div>
            </div>
            </div>
      
	  <div id='step2' style="display:none;">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 环境检测
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
				<?php $disabled=false; ?>
				 <table class="am-table  am-table-hover table-main">
              <thead>
              <tr>
               <th class="table-id">模块|权限</th><th class="table-title">最低要求</th><th class="table-type">当前</th><th class="table-author am-hide-sm-only">注释</th>
              </tr>
              </thead>
              <tbody>       
        <tr>
            <td>file_get_contents()</td>
            <td>必须</td>
            <td><?php echo checkfunc('file_get_contents',true);if(checkfunc('file_get_contents',false)=="不可用"){$disabled=true;} ?></td>
            <td>读取文件</td>
        </tr>
        <tr>
            <td>Zip</td>
            <td>必须</td>
            <td><?php echo checkfunc('zip_open',true);if(checkfunc('zip_open',false)=="不可用"){$disabled=true;} ?></td>
            <td>Zip 解包和压缩</td>
        </tr>
        <tr>
            <td>写入权限</td>
            <td>必须</td>
            <td><?php if (is_writable("install.php")) { echo "<a class='am-badge am-badge-success am-radius'>可用</a>"; } else { echo "<a class='am-badge am-badge-danger am-radius'>不可用</a>";$disabled=true; } ?></td>
            <td>写入文件(1/2)</td>
        </tr>
        <tr>
            <td>file_put_contents()</td>
            <td>必须</td>
            <td><?php echo checkfunc('file_put_contents',true);if(checkfunc('file_put_contents',false)=="不可用"){$disabled=true;} ?></td>
            <td>写入文件(2/2)</td>
        </tr>
            <tr>
            <td>Socket模块</td>
            <td>必须</td>
            <td><?php echo checkfunc('socket_connect',true);if(checkfunc('socket_connect',false)=="不可用"){$disabled=true;} ?></td>
            <td>核心模块,用以与后端链接</td>
        </tr>
        <tr>
            <td>数据库操作: mysqli</td>
            <td>必须</td>
            <td><?php echo checkclass('mysqli',true);if(checkclass('mysqli',false)=="不可用"){$disabled=true;} ?></td>
            <td>本系统强制使用mysqli函数连接数据库</td>
        </tr>
        <tr>
            <td>PHP 5+</td>
            <td>必须</td>
            <td><?php echo "<span class=\"am-badge am-badge-secondary am-radius\">".phpversion()."</span>"; if(phpversion()<5.4){$disabled=true;}?></td>
            <td>运行环境,必须高于5.4</td>
        </tr>
        <tr>
            <td>OS version</td>
            <td>必须</td>
            <td><?php echo "<span class=\"am-badge am-badge-secondary am-radius\">".checkos()."</span>";if(checkos()=="Linux"){$disabled=true;} ?></td>
            <td>目前仅支持windows内核的系统</td>
        </tr>
              </tbody>  
            </table>
             <button class='am-btn am-btn-default'type='button' <?php if($disabled){echo "disabled";}else{ echo "onclick=\"javascript:$('div#step2').attr('style','display:none;');$('div#step3').attr('style','')\""; } ?>>继续安装</button>
             <br><br> 
				
				

						</div>
                    </div>
                </div>
            </div>
            </div>
	  
	  <div id='step3' style='display:none'>
	  <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 配置数据(1/3)
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<form name='db-config-submit' method='post' action='install.php'>
				 <table class="am-table  am-table-hover table-main am-form">
              <tbody>       
        <tr>
            <td>数据库名</td>
            <td><input type="text" class="am-form-field"  placeholder="数据库名" name="dbname"></td>
            <td>把Umarket装到哪个数据库?</td>
        </tr>
        <tr>
            <td>用户名</td>
            <td><input type="text" class="am-form-field"  placeholder="数据库用户名" name="dbusername"></td>
            <td>您的数据库用户名.</td>
            
            
        </tr>
        <tr>
            <td>密码</td>
            <td><input type="text" class="am-form-field"  placeholder="数据库密码" name="dbpassword"></td>
            <td>你的数据库密码.</td>
        </tr>
        <tr>
            <td>数据库主机</td>
            <td><input type="text" class="am-form-field"  placeholder="数据库主机地址" name="dbhost" value="localhost"></td>
            <td>如果您的数据库不在本主机上,请修改为数据库服务器的IP地址与端口,使用:分隔</td>
        </tr>
              </tbody>  
            </table>
			<button class='am-btn am-btn-default' type='submit' >连接数据库</button>
             	 </form>
						</div>
                    </div>
                </div>
            </div>
	  </div>
	  
	    <div id='step4' style='display:none'>
	  <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 配置数据(2/3)
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
			<?php
			if(isset($_COOKIE['suc'])){if($_COOKIE['suc']==0){
			?>
			<h2><strong>Oops!</strong></h2><p>连接数据库出现了一些问题.请检查您的数据库服务器的状态以及数据库参数的配置,您可以点击下方的按钮返回,重新尝试连接数据库</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:display_step("4","3");'>重新配置</button>
			<?php
			}else{
			?>
			<h2><strong>不错!</strong></h2><p>我们已成功与数据库连接,您可以点击下方的按钮继续我们的安装</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:window.location.href="install.php?step&s=5";'>现在安装</button>
			<?php
			}}else{
			?>
			<h2><strong>Oops!</strong></h2><p>安装流程出现了一些问题,请您点击下方按钮返回上一步重试安装</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:history.back(-1);'>返回上一步</button>
			<?php
			}
			?>			
             	 
						</div>
                    </div>
                </div>
            </div>
	  </div>
	  
	   <div id='step5' style='display:none'>
	  <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 配置数据(3/3)
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
			<h2><strong>不错!</strong></h2><p>我们已成功将数据表导入至您指定的数据库中.接下来,您需要填写与核验一些资料,无需担心填错,这些信息以后也可以更改.</p>
			
			<form name='db-config-submit' method='post' action='install.php?step&s=6'>
				 <table class="am-table  am-table-hover table-main am-form">
              <tbody>       
        <tr>
            <td>商城全称</td>
            <td><input type="text" class="am-form-field"  placeholder="商城全称" name="system_name"></td>
            <td>例如:Umarket|开源饰品商城</td>
        </tr>
        <tr>
            <td>商城缩写</td>
            <td><input type="text" class="am-form-field"  placeholder="商城缩写" name="system_sort_name"></td>
            <td>例如:Umarket</td>
            
            
        </tr>
        <tr>
            <td>商城系统根目录</td>
            <td><input type="text" class="am-form-field"  placeholder="商城系统根目录" name='system_root' value="<?php echo str_ireplace("\\","/",str_ireplace("\setup\install.php","",__FILE__)); ?>"></td>
            <td>本程序位于主机上的绝对路径.</td>
        </tr>
        <tr>
            <td>商城域名</td>
            <td><input type="text" class="am-form-field"  placeholder="商城域名" name="weblink" value=""></td>
            <td>例如:www.7gugu.com</td>
        </tr>
		<tr>
            <td>WebAPI秘钥</td>
            <td><input type="text" class="am-form-field"  placeholder="WebAPI秘钥" name="webapi_key" value=""></td>
            <td>用于查询饰品数据,访问https://steamcommunity.com/dev/apikey获取,未知可不填</td>
        </tr>
		<tr>
            <td>管理员用户名</td>
            <td><input type="text" class="am-form-field"  placeholder="管理员用户名" name="admin_username" value=""></td>
            <td>初始化管理员账户,必填,不填则为admin</td>
        </tr>
		<tr>
            <td>管理员密码</td>
            <td><input type="text" class="am-form-field"  placeholder="管理员密码" name="admin_password" value=""></td>
            <td>初始化管理员账户,必填,不填则为admin</td>
        </tr>
		<tr>
            <td>管理员邮箱</td>
            <td><input type="email" class="am-form-field"  placeholder="管理员邮箱" name="admin_email" value=""></td>
            <td>用于找回密码,必填</td>
        </tr>
		<tr>
            <td>管理员SteamID</td>
            <td><input type="text" class="am-form-field"  placeholder="管理员SteamID" name="admin_steamid" value=""></td>
            <td>初始化管理员账户</td>
        </tr>
              </tbody>  
            </table>
			<button class='am-btn am-btn-default' type='submit' >提交</button>
             	 </form>
			
			
             	 
						</div>
                    </div>
                </div>
            </div>
	  </div>
	   <div id='step6' style='display:none'>
	  <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 
				<?php
				if(isset($_COOKIE['suc'])&&$_COOKIE['suc']==3){
				echo "安装完成!";
				}elseif(isset($_COOKIE['suc'])&&$_COOKIE['suc']==4){
				echo "安装失败!";
				}else{
				echo "安装错误!";
				}
				?>
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php
			if(isset($_COOKIE['suc'])){if($_COOKIE['suc']==3){
			?>
			<h2><strong>欢迎!</strong></h2><p>Umarket已安装完成,您可以点击下方的按钮开始使用了!</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:window.location.href="install.php?step&s=7";'>开始使用</button>
			<?php
			}else{
			?>
			<h2><strong>Oops!</strong></h2><p>初始化配置文件出现了一些问题.请检查您的文件夹可操作权限是否配置正确,您可以点击下方的按钮返回,重新尝试初始化配置</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:display_step("6","5");'>重新配置</button>
			<?php
			}}else{
			?>
			<h2><strong>Oops!</strong></h2><p>安装流程出现了一些问题,请您点击下方按钮返回上一步重试安装</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:history.back(-1);'>返回上一步</button>
			<?php
			}
			?>	
             	 
						</div>
                    </div>
                </div>
            </div>
	  </div>
	  
	   <div id='step8' style='display:none'>
	  <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 检测到安装锁
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
			
			<h2><strong>Oops!</strong></h2><p>检测到您已或曾经安装过本程序,若您仍要执行安装程序,您可以前往assets文件夹下,删除install.lock后,再次访问本页面方可继续执行.</p>
			<button class='am-btn am-btn-default' type='button' onclick='javascript:window.location.href="/index.php";'>返回首页</button>
			
             	 
						</div>
                    </div>
                </div>
            </div>
	  </div>
        </div>
		<?php if(isset($_GET['step'])){$s=$_GET['s'];if($s==""||$s==0){$s=1;}echo "<script>display_step('1','".$s."')</script>";} ?>
<script type="text/javascript" src="../assets/js/amazeui.min.js"></script><script type="text/javascript" src="../assets/js/app.js"></script>
<br/>Umarket|饰品市场 V1.00 // 作者: <a href="https://www.7gugu.com" target="_blank">7gugu</a>
</body>
</html>