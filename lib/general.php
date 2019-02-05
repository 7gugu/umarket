<?php 
/**
 * 重定向链接
 * @param string $url 目标URL
 */
function redirect($url) {
	header("Location: ".$url);
	msg('<meta http-equiv="refresh" content="0; url='.htmlspecialchars($url).'" />请稍候......<br/><br/>如果您的浏览器没有自动跳转，请点击下面的链接',htmlspecialchars($url));
}

/**
 * 加载头部
 * @param string $title 页面标题
 */
function loadhead($title = '') {
	ob_start();
    if(php_sapi_name()=="cli") return;
	$title = empty($title) ? strip_tags(SYSTEM_NAME) : $title . ' - ' . strip_tags(SYSTEM_NAME);
	echo '<!DOCTYPE html><html><head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<title>'.$title.'</title>';
	echo '<meta name="generator" content="Umarket Ver.'.SYSTEM_VER.'" />';
	echo '<link rel="icon" type="image/png" href='.SYSTEM_ICON.'>';
	echo '<meta name="author" content="7gugu (https://www.7gugu.com)" />';
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
	echo '<meta name="renderer" content="webkit">';
    echo '<meta http-equiv="Cache-Control" content="no-siteapp" />';
	echo '<link rel="stylesheet" href="assets/css/amazeui.min.css">';
	echo '<link rel="stylesheet" href="assets/css/admin.css">';
	echo '<link rel="stylesheet" href="assets/css/app.css">';
	echo '<script src="assets/js/jquery.min.js"></script>';
	echo "<script type='text/javascript' src='assets/js/Sortable.js'></script>";
	echo '<script type="text/javascript" src="assets/js/amazeui.min.js"></script>';
	echo '<script type="text/javascript" src="assets/js/tools.js"></script>';//自己封装的需要用到的js函数
	echo '</head><body>';
}

/**
 * 加载底部
 */
function loadfoot() {
	    echo '<script type="text/javascript" src="assets/js/amazeui.min.js"></script>';
	    echo '<script type="text/javascript" src="assets/js/app.js"></script>';
        echo '<br/>'.SYSTEM_NAME.' V'.SYSTEM_VER.' // 作者: <a href="https://www.7gugu.com" target="_blank">7gugu</a>';
	    if(RUNTIME){ global $t1;$t2 = microtime(true);echo ' 运行耗时'.round($t2-$t1,3).'秒';}
		if(SYSTEM_FOOTER_RECORD_NUMBER!=false){
		echo "&nbsp;备案号:".SYSTEM_FOOTER_RECORD_NUMBER;
		}
		if(SYSTEM_FOOTER_SIGN!=false){
		echo "&nbsp;".SYSTEM_FOOTER_SIGN;
		}
		echo '</div></body></html>';
		ob_end_flush();
}

/**
 * 加载系统或插件的模板（或文件）
 * @param string $file 文件，pannel:index 表示panel层index页面
 * @return mixed
 */
function template($file) {
	if(strstr($file , ':')) {
		$parse = explode(':',$file);
		return include SYSTEM_ROOT . '/plugins/' . $parse[0] .'/' . $parse[1] . '.php';
	} else {
		return include SYSTEM_ROOT . '/templates/' . $file . '.php';
	}
}

/**
 * 加载提示框
 * @return 返回alert的模板数据
 */
function loadalert() {
		return include SYSTEM_ROOT . '/lib/alert.php';
}

/**
 * curl封装
 * @param string $url 链接地址
 * @param string $post 需要post的数据
 * @param string $refer
 * @param string $header 发起链接的头部
 */
function curl($url, $post=null,$refer=null,$header=null) { 
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, $header); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
	if($post!=null){
   @curl_setopt($curl, CURLOPT_POST, 1);
   @curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
}
   if(isset($refer)){
            curl_setopt($curl, CURLOPT_REFERER, $refer);
        }  
   $rs= curl_exec($curl);
    curl_close($curl);
return $rs;	
} 



?>
