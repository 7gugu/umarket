<?php
//网页不可调的参数
date_default_timezone_set('Asia/Shanghai');
define("DBUSERNAME","root");//数据库用户名
define("DBPASSWORD","root");//数据库密码
define("DBNAME","umarket");//数据库名
define("APIKEY","");//Web API秘钥,访问https://steamcommunity.com/dev/apikey获取
define('SYSTEM_VER','1.3.1');
define('MODE_DEBUG',false);//是否开启调试模式
define('MODE_MANUAL',false);//是否开启手动模式[机器人二步验证,手动输入]
//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
define('SYSTEM_ROOT','C:\umarket');
define('SUPPORT_URL', 'https://www.7gugu.com');
define('SYSTEM_NO_ERROR', false );
?>