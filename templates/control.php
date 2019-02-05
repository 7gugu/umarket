<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $mode;
if(isset($_SESSION['access'])){$access=$_SESSION['access'];}else{$access="0";}//0游客1用户2管理员
if(!isset($_COOKIE['steamid'])){$_SESSION['access']=0;}
$parse=array();
if(strstr($mode , ':')) {$parse = explode(':',$mode);}else{$parse[0]=$mode;$parse[1]="";}

switch ($parse[0]) {
	case 'commoditylist':
        loadhead('商品列表');
		template('header');
		template('navi');
		template('commoditylist');
		break;
	case 'commoditydetail':
        loadhead('商品详细');
		template('header');
		template('navi');
		template('commoditydetail');
		break;
	case 'user':
		if ($access < 1) msg('请先登录后再操作！','index.php?mod=login');//使用session来储存权限
		switch($parse[1]) {
	case 'payment':
	    loadhead('商品结算');
		template('header');
		template('navi');
		template('user-payment');//使用session传入订单,支付跳转及结算完成可外加参数传入,支付完可带参数跳回改页面,然后跳到玩家背包页
	break;
	case 'shoppingcart':
	    loadhead('购物车');
		template('header');
		template('navi');
		template('user-shoppingcart');//使用cookie传入订单
	break;
	case 'logout':
        loadhead('登出系统');
		template('header');
		template('navi');
		template('user-logout');
		break;
			default:
        loadhead('我的信息');
		template('header');
		template('navi');
		template('panel-index');
		break;
		}
	break;
	case 'forgetpw':
        loadhead('找回密码');
		template('header');
		template('navi');
		template('user-forgetpw');
		break;
	case 'login':
        loadhead('登录商城');
		template('header');
		template('navi');
		template('user-login');
		break;
	case 'register':
        loadhead('注册用户');
		template('header');
		template('navi');
		template('user-register');
		break;
	case 'panel':
		if ($access < '1') msg('请先登录后再操作！','index.php?mod=login');//使用session来储存权限
		switch($parse[1]) {
	case 'option':
	loadhead("账号设置");
	template("header");
	template("navi");
	template("panel-option");
	break;
	case 'broadcast':
	    loadhead('系统广播');
		template('header');
		template('navi');
		template('panel-broadcast');//使用session传入订单,支付跳转及结算完成可外加参数传入,支付完可带参数跳回改页面,然后跳到玩家背包页
	break;
	case 'inventory':
	    loadhead('我的库存');
		template('header');
		template('navi');
		template('panel-inventory');//使用cookie传入订单
	break;
	case 'order':
        loadhead('我的订单');
		template('header');
		template('navi');
		template('panel-order');
		break;
	case 'payment':
        loadhead('我的支付');
		template('header');
		template('navi');
		template('panel-payment');
				break;
			default:
        loadhead('我的信息');
		template('header');
		template('navi');
		template('panel-index');
		break;
		}
	break;
	case 'admin':
		if ($access < '2') msg('权限不足！','index.php?mod');//使用session来储存权限
		switch($parse[1]) {
			case 'account':
                loadhead('管理用户');
				template('header');
		        template('navi');
				template('admin-account');
				break;
			case 'botmanage':
                loadhead('管理机器人');
				template('header');
		        template('navi');
				template('admin-botmanage');
				break;
			case 'broadcast':
                loadhead('系统广播');
				template('header');
		        template('navi');
				template('admin-broadcast');
				break;
			case 'option':
                loadhead('全局设置');
				template('header');
		        template('navi');
				template('admin-option');
				break;
			case 'payment':
				loadhead('支付系统');
				template('header');
		        template('navi');
				template('admin-payment');
				break;
			case 'upgrade':
                loadhead('检查更新');
				template('header');
		        template('navi');
				template('admin-upgrade');
				break;
			case 'stat':
                loadhead('统计信息');
				template('header');
		        template('navi');
				template('admin-stat');
				break;
				case 'column':
                loadhead('栏目管理');
				template('header');
		        template('navi');
				template('admin-column');
				break;
			case 'order':
                loadhead('商品信息');
				template('header');
		        template('navi');
				template('admin-order');
				break;
			case 'about':
            loadhead('关于系统');
			template('header');
		    template('navi');
			template('admin-about');
			break;
			default:
        loadhead('系统后台');
		template('header');
		template('navi');
		template('admin-index');
		break;
		}
		break;
	default:
        loadhead();
		template('header');
		template('navi');
		template('index');
		break;
}
?>