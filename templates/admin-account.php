<?php 
	global $sql;
	$connect=$sql->connect();
	//查询用户列表
	if(isset($_POST['query_data'])){
	$perNumber=10;
	@$page=$_POST['page'];
	$count=$sql->query($connect,"select count(*) from umarket_account");
	$rs=mysqli_fetch_array($count); 
	$totalNumber=$rs[0];
	$totalPage=ceil($totalNumber/$perNumber);
	if (!isset($page)) {
	$page=1;
	}
	$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
	$result=$sql->query($connect,"select * from umarket_account  limit $startCount,$perNumber");
	$c=$sql->fetch_array($sql->query($connect,"select count(*) from umarket_account  limit $startCount,$perNumber")); 
	echo "<div id='page'>".$page."</div>";
	echo "<div id='totalnumber'>".($totalNumber-1)."</div>";
	echo "<div id='totalpage'>".$totalPage."</div>";
	echo "<div id='account_log'>";
	if($c[0]==0){
	echo "winout-log";
	}else{
	while($array=$sql->fetch_array($result)){
	if($array['accountid']==0){continue;}
	echo "<tr1><td2>{$array['accountid']}</td3>";
	echo "<td2><span class=\"label label-info\">{$array['username']}</span></td3>";
	echo "<td2><span class=\"label label-success\">{$array['email']}</span></td3>";
	if($array['status']==0){
	$status="<span class=\"label label-info\">未验证</span>";
	}elseif($array['status']==1){
	$status="<span class=\"label label-warning\">已验证</span>";
	}elseif($array['status']==2){
	$status="<span class=\"label label-danger\">被封禁</span>";
	}
	echo "<td2>{$status}</td3>";
	echo "<td2><button class='am-btn am-btn-default' type='button' onclick=\"edit_account(0,{$array['accountid']})\">编辑</button></td3></tr4>";
	}
	}
	echo "</div>";
	die;
	}
	//查询用户数据
	if(isset($_POST['query_account'])){
	$accountid=$_POST['accountid'];
	$context="select * from umarket_account where `accountid`='{$accountid}'";
	$result=$sql->fetch_array($sql->query($connect,$context));
	if($result){
	echo "<div id='response'>1</div>";
	echo "<div id='account_username'>".$result['username']."</div>";
	echo "<div id='account_email'>".$result['email']."</div>";
	echo "<div id='account_steamid'>".$result['steamid']."</div>";
	echo "<div id='account_tradelink'>".$result['tradelink']."</div>";
	echo "<div id='account_status'>".$result['status']."</div>";
	echo "<div id='account_access'>".$result['access']."</div>";
	$context="select * from umarket_wallet where `wallet_steamid`='{$result['steamid']}'";
	$result=$sql->fetch_array($sql->query($connect,$context));
	if($result['wallet_alipay_realname']==""){$wallet_alipay_realname="未启用";}else{$wallet_alipay_realname=$result['wallet_alipay_realname'];}
	if($result['wallet_alipay_account']==""){$wallet_alipay_account="未启用";}else{$wallet_alipay_account=$result['wallet_alipay_account'];}
	if($result['wallet_balance']==""){$wallet_balance=0;}else{$wallet_balance=$result['wallet_balance'];}
	echo "<div id='wallet_alipay_realname'>".$wallet_alipay_realname."</div>"; 
	echo "<div id='wallet_alipay_account'>".$wallet_alipay_account."</div>"; 
	echo "<div id='wallet_balance'>".$wallet_balance."</div>"; 
	}else{
	echo "<div id='response'>0</div>";
	}
	die;
	}
	//重置用户密码
	if(isset($_POST['resetting_password'])){
	$accountid=$_POST['accountid'];
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	$randomString = ''; 
	$length=5;
	for ($i = 0; $i < $length; $i++) { 
    $randomString .= $characters[rand(0, strlen($characters) - 1)]; 
	}	 
	$new_password=md5(time().$randomString);
	$context="update umarket_account set `password`='{$new_password}' where `accountid`='{$accountid}' limit 1";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'>SQL执行错误</div>";
	}
	die;
	}
	//更新用户邮箱
	if(isset($_POST['update_email'])){
	$accountid=$_POST['accountid'];
	$account_email=htmlspecialchars($_POST['account_email']);
	$context="update umarket_account set `email`='{$account_email}' where `accountid`='{$accountid}' limit 1";
	$sql->query($connect,$context);
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'>SQL执行错误</div>";
	}
	die;
	}
	//更新用户状态[封禁|解禁]
	if(isset($_POST['setting_user_state'])){
	$accountid=$_POST['accountid'];
	if($accountid==""){echo "<div id='response'>0</div><div id='ajax'>accountid-传值错误</div>"; die;}
	$res=$sql->fetch_array($sql->query($connect,"select * from umarket_account where `accountid`='{$accountid}'"));
	$user_state=$res['status'];
	if($user_state==2){
	$user_state=1;
	}else{
	$user_state=2;
	}
	$sql->query($connect,"update umarket_account set `status`='{$user_state}' where `accountid`= '{$accountid}'");
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'>SQL执行错误</div>";
	}
	die;
	}
	//更新用户权限
	if(isset($_POST['update_user_access'])){
	@$accountid=$_POST['accoountid'];
	$access=$_POST['access'];
	if($access=='-1'){die;}
	$context="select * from umarket_account where `accountid`='{$accountid}'";
	$res=$sql->fetch_array($sql->query($connect,$context));
	if($access==$res['access']){
	echo "<div id='response'>1</div>";
	die;
	}
	$sql->query($connect,"update umarket_account set `access`='{$access}' where `accountid`= '{$accountid}'");
	if($sql->affected_rows($connect)>0){
	echo "<div id='response'>1</div><div id='alert'></div>";
	}else{
	echo "<div id='response'>0</div><div id='alert'>SQL执行错误</div>";
	}
	die;
	}
?>
<div class="tpl-content-wrapper">
			<div class="tpl-portlet">                 
<div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><strong>  <span class="am-icon-code"></span>管理用户</strong></span>
                            </div></div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs="">
                            <ul class="am-tabs-nav am-nav am-nav-tabs">
                                <li class="am-active"><a href="#tab2" onclick="">账户管理</a></li>
                            </ul>
                                </div>
                                <div class="am-tab-panel am-fade am-active am-in" id="tab2">
                                    <div id="wrapperB" class="wrapper" style="height:100%;"> 
									   <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12" id='account_list_show'>
						<?php loadalert(); ?>
						<div id='alert_show_list'></div>
<table class="am-table am-table-hover">
   <thead> 
        <tr>
            <th>账户编号</th><th>账户名</th><th>账户邮箱</th><th>账户状态</th><th>操作</th>
        </tr>
    </thead> 
    <tbody id='account_log_show'>
       <tr><td>0</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
    </tbody>
</table>						
						
						<div class="am-cf" id='am-cf' >
	 <div id='totalnumber'></div>
                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination" id='am-pagination'>
                                        </ul>
                             </div>
                                </div>
								</div>
								
	<div id="edit_account_show" class="am-u-sm-9 am-form" style="display:none;">
<div id="alert_show_edit"></div>
<strong><h2>编辑用户</h2></strong>
<hr>
<table class="am-table am-scrollable-horizontal">
		   <thead>
        <tr>
            <th>名称</th>
            <th>数值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>用户名:</strong></td>
            <td><input type="text" class="am-form-field" id="account_username" placeholder="用户名" disabled></td>
        </tr>
		<tr>
            <td><strong>SteamID:</strong></td>
            <td><input type="text" class="am-form-field" id="account_steamid" placeholder="用户steamID" disabled></td>
        </tr>
		<tr>
            <td><strong>交易链接:</strong></td>
            <td><input type="text" class="am-form-field" id="account_tradelink" placeholder="用户交易链接" disabled></td>
        </tr>
		<tr>
            <td><strong>用户邮箱:</strong></td>
            <td>
			<input type="text" class="am-form-field" id="account_email" placeholder="用户邮箱" >
			&nbsp;
			<div id='email_save'></div></td>
        </tr>
		<tr>
            <td><strong>重置用户的密码:</strong></td>
            <td id='resetting_button'></td>
        </tr>
		<tr>
            <td><strong>设置用户状态:</strong></td>
            <td id='state_button'>
			</td>
        </tr>
		<tr>
            <td><strong>设置用户权限:</strong></td>
            <td id='access_select'>
			
			</td>
        </tr>
		<tr>
            <td><strong>Uwallet余额:</strong></td>
			<td>			
		<div class="am-input-group" id='resetting_wallet_button'>
		
		</div>
		</div></td>
        </tr>
		<tr>
            <td><strong>支付宝账户:</strong></td>
            <td><input type="text" class="am-form-field" id="wallet_alipay_account" disabled></td>
        </tr>
		<tr>
            <td><strong>支付宝用户名:</strong></td>
            <td><input type="text" class="am-form-field" id="wallet_alipay_realname"  disabled></td>
        </tr>
    </tbody>
  </table>
<small>用户拥有知情权,请在更改此数据前与用户先取得联系,获取书面授权再修改</small>  
<hr>
<button type="button" class="am-btn am-btn-default" onclick="edit_account(1);">返回列表</button>
</div>   
								
								
								
								
								
								
								
								
								
								<div>
								
								</div>
								
								
								
								
								
                                </div>
								
		<script>
	function update_account_log(page){
			if(!arguments[0]) page = 1;
			$.ajax({
				type: 'POST',
				async: false, 
				url: 'index.php?mod=admin:account',
				data: {  query_data: true ,page:page },
			beforeSend:function(){
			var data="<div class='am-alert am-alert-success' id='query_alert'><i class='am-icon-spinner am-icon-spin'></i> 数据提交中...</div>";
			$('#alert_show_list').html(data);	
			setTimeout(function (){$('#query_alert').alert('close')},3000);
			},
			success:function(data){
				if($("div#account_log",data).text()=="without-log"){
		$('#account_log_show').html("<tr><td>0</td><td>暂无注册数据</td><td>-</td><td>-</td><td>-</td></tr>");
	}else{
	var template="";
	var i;
	var body = $("div#account_log",data).html();
	body=form_data(body);
	$('#account_log_show').html(body);
	$('#totalnumber').text("共"+$("div#totalnumber",data).text()+"条记录");
	var page = parseInt($("div#page",data).text());
	var totalpage = parseInt($("div#totalpage",data).html());
	
	if(totalpage<5){
	for (i=1; i <= totalpage; i++){
    if (page == i) {
        template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_account_log("+i+")'>"+i+"</a></li>";
    }else{
        template =  template+"<li><a href='javascript:void(0);' onclick='update_account_log("+i+")' >"+i+"</a></li>";
		}
	}		
	}else{
	for (i=1; i < page+5; i++){
    if (page == i) {
         template = template+"<li class='am-active'><a href='javascript:void(0);' onclick='update_account_log("+i+")'>"+i+"</a></li>";
    }else{
         template = template+"<li><a href='javascript:void(0);' onclick='update_account_log("+i+")'>"+i+"</a></li>";
    }
		}
	}
		}
			}
		});
		}
		window.onload=function(){update_account_log();}
		</script>
                    </div>   
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
										
                                    <div style="position: absolute; z-index: 9999; width: 7px; bottom: 2px; top: 2px; right: 1px; overflow: hidden; transform: translateZ(0px); transition-duration: 0ms; opacity: 0;" class="iScrollVerticalScrollbar iScrollLoneScrollbar"></div>
									</div>
                                </div>

                            </div>
                        </div>

                    </div>
        </div>