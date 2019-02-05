<?php 
function checkfunc($function,$state = false) {
    if (function_exists($function)) {
		if(!$state){
		return '可用';	
		}else{
        return "<a class='am-badge am-badge-success am-radius'>可用</a>";
		}
	} else {
        if (!$state) {
            return "不可用";
        } else {
            return "<a class='am-badge am-badge-danger am-radius'>不可用</a>";
        }
    }
}
function checkclass($class,$state = false) {
    if (class_exists($class)) {
		if(!$state){
        return "可用";
		}else{
		return "<a class='am-badge am-badge-success am-radius'>可用</a>";	
		}
    } else {
		if(!$state){
		return '不可用';
		}else{
        return "<a class='am-badge am-badge-danger am-radius'>不可用</a>";
		}
	}
}
function checkos(){
 $os=''; 
 $Agent=$_SERVER['HTTP_USER_AGENT']; 
if(stristr($Agent,'linux')){ 
  $os='Linux'; 
 }elseif(stristr($Agent,'Win')){
  $os='Windows'; 
 }else{
  $os="Linux"; //神仙操作
 }
 return $os;
}
function loadsql($connect,$file){
$_sql = file_get_contents($file);
$_arr = explode(';', $_sql);
foreach ($_arr as $_value) {
    mysqli_query($connect,$_value.';');
}
}
?>