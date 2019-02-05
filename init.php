<?php
/**
 * Umarket饰品市场
 *
 * 加载核心
 */
header("content-type:text/html; charset=utf-8");
require './config/config.php';
require './config/config-node.php';
require './config/config-basic.php';
require './config/config-reg.php';
require './config/config-appearance.php';
require './config/config-alipay.php';
require './lib/steambot_class.php';
require './lib/mysql_class.php';
require './lib/smtp_class.php';
require './lib/steamauthOOP_class.php';
require './lib/general.php';
require './lib/alipay_class.php';
require './lib/msg.php';
require './lib/reg.php';
if (!file_exists('./assets/install.lock') && file_exists('./setup/install.php')) {
	msg('<h2>检测到无 install.lock 文件</h2><ul><li><font size="4">如果您尚未安装本程序，请<a href="./setup/install.php">前往安装</a></font></li><li><font size="4">如果您已经安装本程序，请手动放置一个空的 install.lock 文件到 /assets 文件夹下，<b>为了您站点安全，在您完成它之前我们不会工作。</b></font></li></ul><br/><h4>为什么必须建立 install.lock 文件？</h4>它是系统的保护文件，如果系统检测不到它，就会认为站点还没安装，此时任何人都可以安装/重装Umarket。<br/><br/>',false,true);	
	exit();
	}
if(SYSTEM_NO_ERROR){error_reporting(0);}
?>