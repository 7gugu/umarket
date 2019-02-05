<?php
/**
 * 页面路由系统
 * @param string mod 请求页面名
 */
	require dirname(__FILE__).'/init.php';
	if(isset($_GET['mod'])){
	$mode=htmlspecialchars($_GET['mod']);
	template('control');
	}else{
	reDirect('index.php?mod');
	}
    loadfoot();
	die;
	?>