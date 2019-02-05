 <?php 
  global $sql;
 $connect=$sql->connect();
 ?>
 <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 系统公告
                    </div>
                </div>
                <div class="tpl-block ">
                     <div class="am-g tpl-amazeui-form">
               <div class="am-u-sm-12">
			   <?php loadalert();?>
<div class="am-u-sm-12">

<div class="am-panel-group" id="parent">
<?php
//编写遍历公告的逻辑代码
$count=$sql->fetch_array($sql->query($connect,"select count(*) from `umarket_broadcast` order by `broadcast_id` desc"));
$context="select * from `umarket_broadcast` order by `broadcast_id` desc";
$res=$sql->query($connect,$context);
if($count[0]>0){
$count=0;
while($bc=$sql->fetch_array($res)){
?>
  <div class="am-panel am-panel-default">
    <div class="am-panel-hd">
      <h4 class="am-panel-title" data-am-collapse="{parent: '#parent', target: '#<?php echo "broadcast-".$count; ?>'}">
        <?php echo $bc['broadcast_title']; ?> <span class="am-badge am-badge-danger"><?php echo $bc['broadcast_time']; ?></span>&nbsp;<span class="am-badge am-badge-success"><?php echo $bc['broadcast_author']; ?></span>
      </h4>
    </div>
    <div id="<?php echo "broadcast-".$count; ?>" class="am-panel-collapse am-collapse <?php if($count==0){echo "am-in";} ?>">
      <div class="am-panel-bd">
        <?php  echo  $bc['broadcast_body']; ?>
      </div>
    </div>
  </div>
  <?php 
  $count++;
}
}else{
	?>
	<div class="am-panel am-panel-default">
    <div class="am-panel-bd">暂无公告</div>
</div>
	<?php
}
  ?>
</div>

</div>

                        
                    
               </div>
</div>
            </div>
        </div>
		</div>