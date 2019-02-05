<?php 
session_start();
set_time_limit(0);
if(RUNTIME){$t1 = microtime(true);}
$steambot = new SteamBot;
$sql = new mysql;
$steam = new steamauthOOP;
$e=new SMTP(EMAIL_HOST,EMAIL_PORT,EMAIL_AUTH,EMAIL_USER,EMAIL_PASSW);
$alipay = new AlipayService();
$access="";
$_SESSION['status']=0;
$count=0;
$username=$password=$email=$phone=$steamid=$captcha="";
//验证账户的有效性
if(isset($_COOKIE['username'])){
$username=$_COOKIE['username'];
$password=$_COOKIE['password'];
$connect=$sql->connect();
$context="select count(*) from umarket_account where `username`='{$username}' and  `password` ='{$password}' ";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
 $sql->close($connect);
$count=$res[0];
if($count==1){
$_SESSION['status']=1;
}
}
//登录账户,初始化参数
if(isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['register'])){
$res=array();
$username=$_POST['username'];
$password=$_POST['password'];
$connect=$sql->connect();
$context="select count(*) from umarket_account where `username`='{$username}' and  `password` ='{$password}' ";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
if($res[0]==1){
	$context="select * from umarket_account where `username`='{$username}' and  `password` ='{$password}' ";
    $res=$sql->query($connect,$context);
    $res=$sql->fetch_array($res);
	setcookie("username",$username,time()+3600);
	setcookie("password",$password,time()+3600);
	setcookie("steamid",$res['steamid'],time()+3600);
	setcookie("email",$res['email'],time()+3600);
	setcookie("phone",$res['phone'],time()+3600);
	setcookie("suc","登陆成功",time()+30);
	$_SESSION['cart']=array();
	$_SESSION['access']=$res['access'];
	redirect('index.php');
}else{
setcookie("fail","登录失败",time()+30);
redirect("index.php?mod=login");
}
$sql->close($connect);
}
if(isset($_GET['gameid'])&&$_GET['mod']=="commoditylist"&&$_GET['gameid']!=""){$_SESSION['gameid']=$_GET['gameid'];}
if(isset($_GET['goods_id'])&&$_GET['mod']=="commoditydetail"&&$_GET['goods_id']!=""){$_SESSION['goods_id']=$_GET['goods_id'];}
?>