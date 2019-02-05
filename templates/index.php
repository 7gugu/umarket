<div class="tpl-content-wrapper">
<?php 
global $sql;
$connect=$sql->connect();
 if(!isset($_COOKIE["access_count"])){
        $res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_account where `accountid`='0'"));
		$num=0;
		if($res[0]){
			$time_count=date("Y-m-d");
			$access_res=$sql->fetch_array($sql->query($connect,"select * from umarket_account where `accountid`=0"));
			$num=$access_res['token'];//使用token存人数
			$sql_time=$access_res['regtime'];//数据库时间
			if($sql_time!=$time_count){$time_count=date("Y-m-d");}else{$time_count=$sql_time;}
			$num++;
			$sql->query($connect,"update umarket_account set `token`='{$num}',`regtime`='{$time_count}' where `accountid`='0'");
         }else{ 
		 //初始化人数统计
		 $time_count=date("Y-m-d");
		 $context="insert into umarket_account(accountid,username,password,email,status,regtime,token,token_exptime,steamid,tradelink,phone,access,token_status,avatar)values('0','统计访问人数','#@$#$@sdsadaDQWD','0','0','0','1','{$time_count}','0','0','0','0','0','0')";//使用regtime存储日期,用token存人数		
		$sql->query($connect,$context);
        }
		setcookie("access_count",1, time()+3600*24);//访问过标记
    }
$index_slides=INDEX_SLIDES;
$index_slides=explode("|",$index_slides);
?>		
  <div data-am-widget="slider" style="height:458px; border:none; overflow:hidden;" class="am-hide-md-down am-slider am-slider-a5" data-am-slider='{"directionNav":false}' >
  <ul class="am-slides">
<?php 
if(count($index_slides)>0&&$index_slides[0]!=""){
foreach($index_slides as $is){ ?>
<li><img src="<?php echo $is; ?>"></li>
<?php 
}
}else{ 
?>
<li><img src="./assets/img/zhanweitu.png" /></li>
<?php } ?>
  </ul>
</div>
<br>
<?php loadalert(); ?>
 <div class="tpl-content-scope">
                <div class="note note-info">
                    <h3>Umarket | 开源市场
                        <span class="close" data-close="note"></span>
                    </h3>
                    <p> 简单快捷安装方便</p>
                    <p><span class="label label-danger">提示:</span> 自带机器人后端
                    </p>
                </div>
            </div>


            <div class="row">
         
            </div>

        </div>

