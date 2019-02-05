<?php 
global $sql;
$connect=$sql->connect();
if(isset($_POST['query_data'])){
	if(isset($_POST['key_log'])){
$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_wallet_key"));
$totalNumber=$res[0];
$key_number=$actived_key=$unactived_key="0";
$key_number=$totalNumber;
$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_wallet_key where `key_state`='0'"));
if($res){$unactived_key=$res[0];}
$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_wallet_key where `key_state`='1'"));
if($res){$actived_key=$res[0];}
echo "<div id='actived_key'>".$actived_key."</div>";
echo "<div id='unactived_key'>".$unactived_key."</div>";
echo "<div id='key_number'>".$key_number."</div>";
$perNumber=5;
@$page=$_POST['page'];
$totalPage=ceil($unactived_key/$perNumber);
if (!isset($page)) {
 $page=1;
}
$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
$result=$sql->query($connect,"select * from umarket_wallet_key where `key_state`='0' limit $startCount,$perNumber");
echo "<div id='page'>".$page."</div>";
echo "<div id='totalnumber'>".$totalNumber."</div>";
echo "<div id='totalpage'>".$totalPage."</div>";
echo "<div id='key_log'>";
while($array=$sql->fetch_array($result)){
echo "<tr1>";
echo "<td2><span class=\"label label-success\">".$array['key_name']."</span></td3>";
echo "<td2><span class=\"label label-info\">".$array['key_password']."</span></td3>";
echo "<td2><span class=\"label label-warning\">".$array['key_balance']."</span></td3>";
echo "<td2><button type='button' class='am-btn am-btn-danger am-btn-sm' onclick='edit_key(1,".$array['key_id'].")'>删除</button></td3>";
echo "</tr4>";
}
echo "</div>";
die;
}
if(isset($_POST['query_service_charge'])){
	echo "<div id='response'>1</div>";
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='alipay_service_charge'"));
	if($res[1]){$service_charge=$res[1]*100;}else{$service_charge="0";}
	echo "<div id='alipay_service_charge'>".$service_charge."</div>";
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='uwallet_service_charge'"));
	if($res[1]){$service_charge=$res[1]*100;}else{$service_charge="0";}
	echo "<div id='uwallet_service_charge'>".$service_charge."</div>";
	die;
}
if(isset($_POST['query_alipay_config'])){
	echo "<div id='response'>1</div>";
	echo "<div id='alipay_appid'>".APPID."</div>";
	echo "<div id='alipay_rsaprivatekey'>".RSAPRIVATEKEY."</div>";
	echo "<div id='alipay_rsapublickey'>".RSAPUBLICKEY."</div>";
	if(SIGNTYPE=="RSA"){
		$signtype=" <option value='RSA'>RSA</option><option value='RSA2'>RSA2</option>";
	}else{
		$signtype="<option value='RSA2'>RSA2</option><option value='RSA'>RSA</option>";
	}
	echo "<div id='alipay_signtype'>".$signtype."</div>";
	die;
}
echo "<div id='response'>0</div>";
die;
}

if(isset($_POST['delete_key'])){
	@$key_id=$_POST['key_id'];
	if($key_id==""){echo "<div id='response'>0</div><div id='ajax'>传值错误</div>";die;}
	if($key_id=="actived-all"){
		//批量删除已激活的激活码
		$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_wallet_key where `key_state`='1'"));
		if($res[0]==0){echo "<div id='response'>0</div><div id='ajax'>无可删除的失效激活码</div>";die;}
		$context="delete from umarket_wallet_key where `key_state`='1'";
		$sql->query($connect,$context);
	}elseif($key_id=="unactived-all"){
		//批量删除可用的激活码
		$res=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_wallet_key where `key_state`='0'"));
		if($res[0]==0){echo "<div id='response'>0</div><div id='ajax'>无可删除的激活码</div>";die;}
		$context="delete from umarket_wallet_key where `key_state`='0'";
		$sql->query($connect,$context);
	}else{
	$context="delete from umarket_wallet_key where `key_id`='{$key_id}'";
	$res=$sql->query($connect,$context);
	}
	if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
}

if(isset($_POST['generate_key'])){
@$key_salt=$_POST['key_salt'];
@$key_number=$_POST['key_number'];
@$key_balance=$_POST['key_balance'];
if($key_number==""||$key_number==0){$key_number=1;}
if($key_number==""||$key_number<=0){$key_balnce=10;}
if($key_salt==""){$key_salt=time();}
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	for($n=0;$n<$key_number;$n++){
	$randomString = ''; 
    for ($i = 0; $i < 5; $i++) { 
    $randomString .= $characters[rand(0, strlen($characters) - 1)]; 
    } 
	$new_key_name=md5(time().$randomString);
	$randomString = ''; 
	for ($i = 0; $i < 5; $i++) { 
    $randomString .= $characters[rand(0, strlen($characters) - 1)]; 
    } 
	$new_key_password=md5($key_salt.$randomString);
	$context = "select * from umarket_wallet_key order by key_id DESC limit 1 ";
	$res=$sql->fetch_array($sql->query($connect,$context));
	$key_id=0;if($res){$key_id=$res[0];}$key_id++;
	$context="INSERT INTO `umarket_wallet_key` (`key_id`, `key_name`, `key_password`, `key_state` , `key_balance`) VALUES ('{$key_id}', '{$new_key_name}', '{$new_key_password}', '0', '{$key_balance}')";
	$sql->query($connect,$context);
}
if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div><div id='ajax'>".$sql->affected_rows($connect)."</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
}

if(isset($_POST['export_key'])){
	@$key_number=$_POST['key_number'];
	if($key_number==""||$key_number<=0){echo "<div id='response'>0</div><div id='ajax'>导出数值不可小于1</div>";die;}
	$key_number=ceil($key_number);
	$context="select * from umarket_wallet_key limit 0,{$key_number}";
	$res=$sql->query($connect,$context);
	echo "<div id='response'>1</div>";
	echo "<div id='key_data'>";
	while($array=$sql->fetch_array($res)){
		echo $array['key_name']."|".$array['key_password']."|".$array['key_balance']."\r\n";
	}
	echo "</div>";
	die;
}
if(isset($_POST['update_service_charge'])){
	$alipay_service_charge=htmlspecialchars($_POST['alipay_service_charge'])/100;
	$uwallet_service_charge=htmlspecialchars($_POST['uwallet_service_charge'])/100;
	$sql->query($connect,"update umarket_option set `option_context`='{$alipay_service_charge}' where `option_name`='alipay_service_charge'");
	$sql->query($connect,"update umarket_option set `option_context`='{$uwallet_service_charge}' where `option_name`='uwallet_service_charge'");
	if($sql->affected_rows($connect)>0){
			echo "<div id='response'>1</div>";
		}else{
			echo "<div id='response'>0</div><div id='ajax'>SQL执行错误</div>";
		}
	die;
}
if(isset($_POST['update_alipay_config'])){
	$alipay_appid=htmlspecialchars($_POST['alipay_appid']);
	$alipay_rsaprivatekey=htmlspecialchars($_POST['alipay_rsaprivatekey']);
	$alipay_rsapublickey=htmlspecialchars($_POST['alipay_rsapublickey']);
	$alipay_signtype=htmlspecialchars($_POST['alipay_signtype']);
	$backup_contents=file_get_contents(SYSTEM_ROOT."/config/config-alipay.php");
	if(!file_put_contents(SYSTEM_ROOT."/backup/config-alipay-".time().".backup",$backup_contents,LOCK_EX)){echo "<div id='response'>0</div><div id='alert'>备份失败,更新程序中断...</div>";die;}
	$new_contents="
<?php
define('APPID','{$alipay_appid}');//https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
define('SIGNTYPE','{$alipay_signtype}');//签名算法类型，支持RSA2和RSA，推荐使用RSA2
define('RSAPRIVATEKEY','{$alipay_rsaprivatekey}');//商户私钥
define('RSAPUBLICKEY','{$alipay_rsapublickey}');//商户公钥
?>
	";
	if(file_put_contents(SYSTEM_ROOT."/config/config-alipay.php",trim($new_contents),LOCK_EX)){
		echo "<div id='response'>1</div>";
	}else{
		echo "<div id='response'>0</div><div id='alert'>写入失败,更新程序中断...</div>";
	}
	die;
}
?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span>钱包系统</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class=""><a href="#tab1" onclick="query_service_charge()">钱包全局设置</a></li>
                                <li class=""><a href="#tab2" onclick="query_alipay_config()">管理支付宝</a></li>
                                <li class="am-active"><a href="#tab3" onclick="">管理Uwallet</a></li>
                            </ul>

                            <div class="am-tabs-bd" >
                                <div class="am-tab-panel am-fade" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="height:100%;">
									      <div class="am-u-sm-12 am-u-md-9">
	<strong><p><font class='am-text-xxxl'>全局设置</font></p></strong>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
						<?php loadalert(); ?>
						<div id='alert_show'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>支付宝手续费:</strong></td>
            <td>
			<div class="am-input-group">
			<input type="number" class="am-form-field" id="alipay_service_charge" placeholder="支付宝手续费">
			<span class="am-input-group-label">%</span>
			</div>
			</td>
        </tr>
		<tr>
            <td><strong>Uwallet手续费:</strong></td>
            <td>
			<div class="am-input-group">
			<input type="number" class="am-form-field" id="uwallet_service_charge" placeholder="Uwallet手续费">
			<span class="am-input-group-label">%</span>
			</div>
			</td>
        </tr>
		<tr>
            <td></td>
			<td>
			<button class='am-btn am-btn-danger'type='button' onclick="if(confirm('修改设置有风险,请谨慎操作.请先在此确认配置操作!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_service_charge();}}"><span class="am-icon-refresh"></span> 更新配置</button>
			</td>
        </tr>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改</small>  
						
            </div>
        </div>
				<script>
				function update_service_charge(){
		var alipay_service_charge=uwallet_service_charge='20';
		alipay_service_charge=$("input#alipay_service_charge").val();
		uwallet_service_charge=$("input#uwallet_service_charge").val();
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  update_service_charge: true , alipay_service_charge:alipay_service_charge , uwallet_service_charge:uwallet_service_charge},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功,页面正在重载中...</div>";
				$('#alert_show').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
				function query_service_charge(){
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  query_data: true , query_service_charge:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据获取中...</div>";
			$('#alert_show').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#alipay_service_charge").val($("div#alipay_service_charge",data).text());
				$("input#uwallet_service_charge").val($("div#uwallet_service_charge",data).text());
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show').html(data);
				}
			}
		});
	}
	
				</script>		
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>目前仅可设置手续费,更多的营销策略设定待开发...</p>
 </div>
</section>
						</div> 
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
                                </div>
								
								<div class="am-tab-panel am-fade" id="tab2">
                                    <div id="wrapperA" class="wrapper" style="height:100%;">
									      <div class="am-u-sm-12 am-u-md-9">
	<strong><p><font class='am-text-xxxl'>支付宝设置</font></p></strong>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form am-form">
						<?php loadalert(); ?>
						<div id='alert_show_alipay'></div>
						<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>支付宝APPID:</strong></td>
            <td>
			<input type="number" class="am-form-field" id="alipay_appid" placeholder="支付宝APPID">
			</td>
        </tr>
		<tr>
            <td><strong>支付宝商户私钥:</strong></td>
            <td>
			<input type="text" class="am-form-field" id="alipay_rsaprivatekey" placeholder="支付宝商户私钥">
			</td>
        </tr>
		<tr>
            <td><strong>支付宝商户公钥:</strong></td>
            <td>
			<input type="text" class="am-form-field" id="alipay_rsapublickey" placeholder="支付宝商户公钥">
			</td>
        </tr>
		<tr>
            <td><strong>签名算法类型:</strong></td>
            <td>
			<div class="am-form-group">
      <select id="alipay_signtype">
      </select>
      <span class="am-form-caret"></span>
    </div>
			</td>
        </tr>
		<tr>
            <td></td>
			<td>
			<button class='am-btn am-btn-danger'type='button' onclick="if(confirm('修改设置有风险,请谨慎操作.请先在此确认配置操作!!!确认配置请确定，不确定请取消')){window.onclick=function(){update_alipay_config();}}"><span class="am-icon-refresh"></span> 更新配置</button>
			</td>
        </tr>
    </tbody>
  </table>
<small>由于该区域的设置较为敏感,故需谨慎考虑后再修改</small>  
						
            </div>
        </div>
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
    <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
  <p>管理支付宝</p>
 </div>
</section>
						</div> 
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
									<script>
		function update_alipay_config(){
		var alipay_appid=alipay_rsaprivatekey=alipay_signtype='';
		alipay_appid=$("input#alipay_appid").val();
		alipay_rsaprivatekey=$("input#alipay_rsaprivatekey").val();
		alipay_rsapublickey=$("input#alipay_rsapublickey").val();
		alipay_signtype=$("#alipay_signtype").val();
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  update_alipay_config: true , alipay_appid:alipay_appid , alipay_rsaprivatekey:alipay_rsaprivatekey , alipay_rsapublickey:alipay_rsapublickey , alipay_signtype:alipay_signtype},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_alipay').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#response",data).text()==1){
				var data="<div class='am-alert am-alert-success' id='query_alert'>支付宝数据更新成功,页面正在重载中...</div>";
				$('#alert_show_alipay').html(data);
				setTimeout(function (){location.reload()},3000);				
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>支付宝数据更新失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show_alipay').html(data);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				}
			}
		});
	}
		function query_alipay_config(){
		$.ajax({
		type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  query_data: true , query_alipay_config:true },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_alipay').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				console.log(data);
				if($("div#response",data).text()==1){
				var alert="<div class='am-alert am-alert-success' id='query_alert'>数据更新成功...</div>";
				$('#alert_show_alipay').html(alert);
				setTimeout(function (){$('#query_alert').alert('close')},3000);
				$("input#alipay_appid").val($("div#alipay_appid",data).text());
				$("input#alipay_rsaprivatekey").val($("div#alipay_rsaprivatekey",data).text());
				$("input#alipay_rsapublickey").val($("div#alipay_rsapublickey",data).text());
				$("select#alipay_signtype").html($("div#alipay_signtype",data).html());
				console.log($("div#alipay_signtype",data).text());
				}else{
				var data="<div class='am-alert am-alert-danger' id='query_alert'>数据获取失败失败,[错误原因]-["+$("div#alert",data).text()+"]</div>";
				$('#alert_show_alipay').html(data);
				}
			}
		});
	}
		</script>
                                </div>
								
								
                                <div class="am-tab-panel am-fade am-active am-in" id="tab3">
                                    <div id="wrapperB" class="wrapper" style="height:100%;">
										    <div class="am-u-sm-12 am-u-md-9">
											
						<strong><p><font class='am-text-xxl'>Uwallet系统</font></strong><br>
						<div id='alert_show'></div>
						<div class="am-form">
     <strong><div id='totalnumber'></div> </strong><div class='am-progress' id='progress-bar'>
 
</div>
	 <hr>
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 激活码列表</h3>
  </header>
  <div class="am-panel-bd">
<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>激活码</th>
            <th>激活密码</th>
            <th>金额</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id='wallet_log_show'>
        
    </tbody>
  </table>
  <div class="am-cf" id='am-cf' >
                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination" id='am-pagination'>
                                        </ul>
                             </div>
                                </div>
 </div>
</section>
<section class="am-panel am-panel-default" id='export_key'>
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 导出激活码</h3>
  </header>
  <div class="am-panel-bd">
  <div id='alert_export_show'></div>
<div class="am-input-group">
  <span class="am-input-group-label">导出个数</span>
  <input type="number" class="am-form-field" placeholder="导出个数" id='key_number'>
  <span class="am-input-group-btn">
        <button class="am-btn am-btn-default" type="button" onclick='export_key()'>导出</button>
      </span>
</div>

<hr>
<textarea rows="5" id='key_show' style='display:none'>
</textarea>
 </div>
</section>	
<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 生成激活码</h3>
  </header>
  <div class="am-panel-bd">
<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>激活码个数</td><td><input type="number" min='1' max='50' id='key_number'></td></tr>
        <tr><td>单个激活码的金额</td><td><input type="number" min='1' id='key_balance'></td></tr>
        <tr><td>盐[不填则自动生成]</td><td><input type="text" id='key_salt'></td></tr>
        <tr><td></td><td><button type='button' class='am-btn am-btn-danger am-btn-sm' onclick='edit_key(0)'>生成激活码</button></td></tr>
        <tr><td>清空不可用的激活码</td><td><button type='button' class='am-btn am-btn-default am-btn-sm' onclick='edit_key(2)'>确定</button></td></tr>
        <tr><td>清空可用的激活码</td><td><button type='button' class='am-btn am-btn-secondary am-btn-sm' onclick='edit_key(3)'>确定</button></td></tr>
    </tbody>
  </table>
 </div>
</section>				
	<br>
<script>
function export_key(){
	var key_number=$("input#key_number").val();
	if(key_number==""||key_number<=0)key_number = 10;
	$.ajax({
				type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  export_key: true , key_number:key_number},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_export_show').html(data);	
			},
			success:function(data){
	if($("div#response",data).text()==1){
	template="<div class='am-alert am-alert-success' id='query_alert'>激活码已导出</div>";
	var key_data=$("div#key_data",data).text();
	$("textarea#key_show").text(key_data);
	$("textarea#key_show").show();
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_alert'>激活码导出失败,请重试或联系站点管理员</div>";
	 }
	 $('#alert_export_show').html(template);
	 setTimeout(function (){$('#query_alert').alert('close')},3000);
		}
		});
}
function edit_key(mode,param){
	if(!arguments[0]) mode = 0;
	if(mode==0){
	//生成激活码
	var key_salt=$("input#key_salt").val();
	var key_number=$("input#key_number").val();
	var key_balance=$("input#key_balance").val();
	if(key_number=="")key_number=1;
	$.ajax({
				type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  generate_key: true , key_salt:key_salt , key_number:key_number , key_balance:key_balance},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			},
			success:function(data){
	if($("div#response",data).text()==1){
	template="<div class='am-alert am-alert-success' id='query_alert'>激活码生成成功,生成了["+$("div#ajax",data).text()+"]个,正在重载数据</div>";
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_alert'>激活码生成失败,请重试,系统正在重载数据</div>";
	 }
	 $('#alert_show').html(template);
	 setTimeout(function (){$('#query_alert').alert('close')},3000);
	 update_key_log();
		}
		});
	}else if(mode==1){
	//删除激活码
	var key_id=param;
	$.ajax({
				type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  delete_key: true , key_id:key_id},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			},
			success:function(data){
	if($("div#response",data).text()==1){
	template="<div class='am-alert am-alert-success' id='query_alert'>激活码删除成功,正在重载数据</div>";
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_alert'>激活码删除失败,请重试,系统正在重载数据</div>";
	 }
	 $('#alert_show').html(template);
	 setTimeout(function (){$('#query_alert').alert('close')},3000);
	 update_key_log();
		}
		});
	}else if(mode==2){
		//批量删除不可用的激活码
		$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:payment',
				data: {  delete_key: true , key_id:'actived-all'},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			},
			success:function(data){
	if($("div#response",data).text()==1){
	template="<div class='am-alert am-alert-success' id='query_alert'>激活码清空成功,正在重载数据</div>";
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_alert'>激活码清空失败,请重试,系统正在重载数据</div>";
	 }
	 $('#alert_show').html(template);
	 setTimeout(function (){$('#query_alert').alert('close')},3000);
	 update_key_log();
		}
		});
	}else if(mode==3){
		//批量删除可用的激活码
		$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:payment',
				data: {  delete_key: true , key_id:'unactived-all'},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show').html(data);	
			},
			success:function(data){
	if($("div#response",data).text()==1){
	template="<div class='am-alert am-alert-success' id='query_response'>可用激活码清空成功,正在重载数据</div>";
	 }else{
	template="<div class='am-alert am-alert-danger' id='query_response'>可用激活码清空失败,请重试,系统正在重载数据</div>";
	 }
	 $('#alert_show').html(template);
	 setTimeout(function (){$('#query_alert').alert('close')},3000);
	 update_key_log();
		}
		});
	}
}
function update_key_log(page){
			if(!arguments[0]) page = 1;
			$.ajax({
				type: 'POST',
				async: true, 
				url: 'index.php?mod=admin:payment',
				data: {  query_data: true , key_log:true, page:page},
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
	var i,body;
	$('#totalnumber').text("数据统计-[共"+$("div#totalnumber",data).text()+"条]:");
	var page = parseInt($("div#page",data).text());
	var totalpage = parseInt($("div#totalpage",data).html());
	var actived_key=$("div#actived_key",data).text();
	var unactived_key=$("div#unactived_key",data).text();
	if($("div#totalnumber",data).text()!=0){
	var progress_actived=actived_key/$("div#totalnumber",data).text()*100;
	var progress_unactived=unactived_key/$("div#totalnumber",data).text()*100;
	var template=" <div class='am-progress-bar am-progress-bar-success'  style='width: "+progress_unactived+"%'>未激活</div><div class='am-progress-bar am-progress-bar-danger'  style='width: "+progress_actived+"%'>已激活</div>";
	$("div#progress-bar").html(template);
	//console.log(template);
	if($("div#key_log",data).text()==""){
		$('#wallet_log_show').html("<tr><td>0</td><td>暂无激活码</td><td>-</td></tr>");
				}else{
	body = $("div#key_log",data).html();
	//不要修改下面这坨屎
	var reg = new RegExp("<td2>","gm");//g,表示全部替换。
	body=body.replace(reg,"<td>");
	reg = new RegExp("<tr1>","gm");//g,表示全部替换。
	body=body.replace(reg,"<tr>");
	reg = new RegExp("</td3>","gm");//g,表示全部替换。
	body=body.replace(reg,"</td>");
	reg = new RegExp("</tr4>","gm");//g,表示全部替换。
	body=body.replace(reg,"</tr>");
	//console.log(body);
	$('#wallet_log_show').html(body);
	var template="";
	if(totalpage<5){
	for (i=1; i <= totalpage; i++){

    if (page == i) {
        template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_key_log("+i+")'>"+i+"</a></li>";
    }else{
        template =  template+"<li><a href='javascript:void(0);' onclick='update_key_log("+i+")' >"+i+"</a></li>";
		}
	}		
	}else{
	for (i=1; i <= page+5; i++){
    if (page == i) {
         template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_key_log("+i+")'>"+i+"</a></li>";
    }else{
         template = template+"<li><a href='javascript:void(0);' onclick='update_key_log("+i+")'>"+i+"</a></li>";
    }
		}
	}
	
	$('#am-pagination').html(template);
	
				}
	}else{
	var template=" <div class='am-progress-bar'  style='width: 100%'>无激活码</div>";
	$("div#progress-bar").html(template);
	body="<td>-</td><td><span class=\"label label-danger\">无可用的激活码</span></td><td>-</td>";
	$('#wallet_log_show').html(body);
	}
	
		}
		});
		}
		window.onload=function(){update_key_log();}
</script>
  </div>
							</div>
							<div class="am-u-md-3 am-show-lg-only">
						<section class="am-panel am-panel-default">
  <header class="am-panel-hd">
   <h3 class="am-panel-title"><span class="am-icon-info-circle"></span> 提示</h3>
  </header>
  <div class="am-panel-bd">
   <p>这里是Uwallet</p>
 </div>
</section>
						</div> 
                                        
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div></div>
                                </div>

                            </div>
                        </div>

                    </div>
        </div>