<?php 
//数据库操作类 by 7gugu
class mysql{
	//返回mysql的操作句柄
	function connect(){
		$res = new mysqli(DBIP,DBUSERNAME,DBPASSWORD,DBNAME,DBPORT) ;
		if (mysqli_connect_error()&&MODE_DEBUG==true) {
			switch (mysqli_connect_errno()) {
				case 1045:
					throw new Exception("连接数据库失败，数据库用户名或密码错误",10000);
					break;

                case 1049:
					throw new Exception("连接数据库失败，未找到您填写的数据库",10000);
					break;

				case 2003:
					throw new Exception("连接数据库失败，数据库端口错误",10000);
					break;

				case 2005:
					throw new Exception("连接数据库失败，数据库地址错误或者数据库服务器不可用",10000);
					break;

				case 2006:
					throw new Exception("连接数据库失败，数据库服务器不可用",10000);
					break;

				default :
					throw new Exception("连接数据库失败，请检查数据库信息。错误编号：" . mysqli_connect_errno(),10000);
					break;
			}
		}
		return $res;
	}
	//数据库操作
	function query($connect,$text,$noerror=false){
	$connect->query("set names 'utf8'");
	$res=$connect->query("{$text}");
	if (!$res) {
			if ($noerror == true) {
				return false;
			} else {
				throw new Exception("MySQL 语句执行错误：<br/><b>语句：</b>$text<br/><b>错误：</b>" . mysqli_connect_error(),10000);
			}	
		} else {
			return $res;
		}
	return $res;
}
   // 遍历数据库到数组
   function fetch_array($query,$m="both"){
	   return $query->fetch_array(MYSQLI_BOTH);
   }
   //判断影响行数
   function affected_rows($connect){
	  return $connect->affected_rows;
   }
   //断开数据库连接
   public function close($connect){
   return $connect->close();
   }
}
?>