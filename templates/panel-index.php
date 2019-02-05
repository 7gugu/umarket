<?php 
global $sql;
$connect=$sql->connect();
$context="select * from umarket_account where `username`='{$_COOKIE['username']}' and `password`='{$_COOKIE['password']}'";
$res=$sql->query($connect,$context);
$res=$sql->fetch_array($res);
$trade_link=$res['tradelink'];
if(USER_AVATAR){
$img_path=@file_get_contents("https://steamcommunity.com/profiles/".$_COOKIE['steamid']);
$pattern = '/<div class="playerAvatarAutoSizeInner"><img src="(.*?)"><\/div>/';
preg_match($pattern, $img_path, $matches);
@$img=$matches[1];
}else{
	$img="http://s.amazeui.org/media/i/demos/bing-1.jpg";
}
?>
<div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 我的信息
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
						<?php loadalert(); ?>
						<div class="am-u-md-3 am-show-lg-only">
						<img style="height:220px;border:none;overflow:hidden;" src="<?php echo $img; ?>" alt="..." class="am-img-thumbnail am-circle"> 
						</div> 
                        <div class="am-u-sm-12 am-u-md-9">
						<div class="am-panel am-panel-default">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">个人信息</h3>
  </div>
  <ul class="am-form am-list am-list-static">
    <li><strong>用户名: </strong><span class="label label-danger"><?php echo $_COOKIE['username']; ?></span></li>
	<li><strong>Steam64位ID: </strong><span class="label label-warning"><?php echo $_COOKIE['steamid']; ?></span></li>
	<li><strong>邮箱: </strong><span class="label label-success"><?php echo $_COOKIE['email']; ?></span></li>
	<li>
	<div class="am-input-group"><span class="am-input-group-label">交易链接:</span><input type="text" class="am-form-field"  placeholder="我的交易链接" value="<?php echo $trade_link;?>">
	<?php 
	if($trade_link==""){
		echo " <span class='am-input-group-btn'><button class='am-btn am-btn-default' type='button' onclick=\"javascript:window.location.href='index.php?mod=panel:option'\">补充交易链接</button></span>";
	}
	?> 
	</div>
	</li>
  </ul>
  <div class="am-panel-footer"><span class="label label-info">若交易链接为空,则账号的交易功能将被禁用</span></div>
</div>
							</div>
						</div>
                    </div>
                </div>

            </div>
        </div>
