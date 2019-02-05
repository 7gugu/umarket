 <?php 
  global $sql;
 $connect=$sql->connect();
 if(isset($_POST['query_data'])){
 if(isset($_POST['get_stat'])){
 $order_time=date("Y-m-d");
 $context="select count(*) from umarket_order where `order_time` like '{$order_time}%'";
 $order_res=$sql->fetch_array($sql->query($connect,$context));
 echo "<div id='order_count'>".$order_res[0]."</div>";
 $context="select count(*) from umarket_account where `regtime` like '{$order_time}%'";
 $reg_res=$sql->fetch_array($sql->query($connect,$context));
 echo "<div id='reg_count'>".$reg_res[0]."</div>";
 $context="select count(*) from umarket_inventory where `goods_submit_time` like '{$order_time}%'";
 $inventory_res=$sql->fetch_array($sql->query($connect,$context));
 echo "<div id='inventory_count'>".$inventory_res[0]."</div>";
 $context="select * from umarket_account where `accountid` = '0'";
 $access_res=$sql->fetch_array($sql->query($connect,$context));
 echo "<div id='access_count'>".$access_res['token']."</div>";
 die;
 }
 }
 ?>
 <div class="tpl-content-wrapper">

			
			  <div class="tpl-content-page-title">
                数据统计
            </div>
			<br>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <h3>欢迎回来,<?php if(isset($_COOKIE['username'])){echo $_COOKIE['username'];}else{echo "管理员";}?>
                        <span class="close" data-close="note"></span>
                    </h3>
                    <p><span class="label label-danger">提示:</span> 您可通过检阅以下的数据反馈,对当日的交易状态与运作状态作出下一步的经营决策</p>
                </div>
            </div>
			<div id='alert_show'></div>
            <div class="row">
                <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
                    <div class="dashboard-stat blue">
                        <div class="visual">
                            <i class="am-icon-exchange"></i>
                        </div>
                        <div class="details">
                            <div class="number" id='order_count'> 0 </div>
                            <div class="desc"> 今日订单 </div>
                        </div>  
                    </div>
                </div>
                <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
                    <div class="dashboard-stat red">
                        <div class="visual">
                            <i class="am-icon-bar-chart-o"></i>
                        </div>
                        <div class="details">
                            <div class="number" id='access_count'> 0 </div>
                            <div class="desc"> 今日访客 </div>
                        </div>
                    </div>
                </div>
                <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
                    <div class="dashboard-stat green">
                        <div class="visual">
                            <i class="am-icon-arrow-circle-o-up"></i>
                        </div>
                        <div class="details">
                            <div class="number" id='inventory_count'> 0 </div>
                            <div class="desc"> 上架饰品 </div>
                        </div>
                    </div>
                </div>
                <div class="am-u-lg-3 am-u-md-6 am-u-sm-12">
                    <div class="dashboard-stat purple">
                        <div class="visual">
                            <i class="am-icon-paper-plane-o"></i>
                        </div>
                        <div class="details">
                            <div class="number" id='reg_count'> 0 </div>
                            <div class="desc"> 今日注册 </div>
                        </div>
                        
                    </div>
                </div>
<script>
function query_state(){
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:stat',
				data: {  query_data: true , get_stat:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("div#order_count").html($("div#order_count",data).text());
				$("div#inventory_count").html($("div#inventory_count",data).text());
				$("div#access_count").html($("div#access_count",data).text());
				$("div#reg_count").html($("div#reg_count",data).text());
			}
		});
	}
	window.onload=function(){query_state();}
</script>


            </div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
        </div>