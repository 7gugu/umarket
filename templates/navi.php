<?php global $count,$sql; ?>
<div class="tpl-page-container tpl-page-header-fixed">
<div class="tpl-left-nav tpl-left-nav-hover">
            <div class="tpl-left-nav-title">
              <?php echo SYSTEM_SNAME." | ";global $mode;$parse=array();if(strstr($mode , ':')) {$parse = explode(':',$mode);}else{$parse[0]=$mode;$parse[1]="";}switch ($parse[0]) {case 'admin':echo "管理员后台";$param="admin";break;case 'panel':echo "后台管理";$param="panel";break;default:echo "商城列表";$param="default";break;} ?>
            </div>
            <div class="tpl-left-nav-list">
                <ul class="tpl-left-nav-menu">
				<?php 
				if($param==""||$param=="default"){
				?>
                    <li class="tpl-left-nav-item">
					<a href="index.php" class="nav-link">
                            <i class="am-icon-home"></i>
                            <span>首页</span>
                    </a>
                    </li>
					  <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-list-ul"></i>
                            <span>游戏列表</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
							<?php 
							$connect=$sql->connect();
							$column_index=$sql->fetch_array($sql->query($connect,"select * from umarket_option where `option_name`='column_index'"));
							$column_index=unserialize($column_index['option_context']);
							if(count($column_index>0)){
							foreach($column_index as $ci){
								$context="select * from umarket_option where `option_name`='column_{$ci}'";
								$column_name=$sql->fetch_array($sql->query($connect,$context));
								$column_name=unserialize($column_name['option_context']);
								$column_name=$column_name['column_name'];
								echo "<a href='index.php?mod=commoditylist&gameid=".$ci."'>";
								echo "<i class='am-icon-angle-right'></i>";
								echo "<span>".$column_name."</span>";
								echo "</a>";
							}
							}else{
								echo "<a href='javascript:;'>";
								echo "<i class='am-icon-angle-right'></i>";
								echo "<span>暂无可交易的游戏</span>";
								echo "</a>";
							}
								?>
                               
                            </li>
                        </ul>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=user:shoppingcart" class="nav-link">
                            <i class="am-icon-shopping-cart"></i>
                            <span>购物车</span>
                    </a>
                    </li>
					<?php }elseif($param=="panel" && $count!="0" && $parse[0]!="admin"){ ?>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:index" class="nav-link">
                            <i class="am-icon-shield"></i>
                            <span>我的信息</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:inventory" class="nav-link">
                            <i class="am-icon-suitcase"></i>
                            <span>我的背包</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:order" class="nav-link">
                            <i class="am-icon-reorder"></i>
                            <span>我的订单</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:payment" class="nav-link">
                            <i class="am-icon-credit-card"></i>
                            <span>我的钱包</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:broadcast" class="nav-link">
                            <i class="am-icon-bullhorn"></i>
                            <span>系统广播</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=panel:option" class="nav-link">
                            <i class="am-icon-cog"></i>
                            <span>账号设置</span>
                    </a>
                    </li>
					<?php 
					}
					if(isset($_SESSION['status'])){
						if($_SESSION['status']=="1" && $count!=0){
							if($param=="default"){
					?>
					<li class="tpl-left-nav-item">
                        <a href="index.php?mod=panel:index" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-archive"></i>
                            <span>前往后台</span>

                        </a>
                    </li>
					<?php 
							}else{
					?>
					<li class="tpl-left-nav-item">
                        <a href="index.php?mod=index" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-home"></i>
                            <span>商城首页</span>

                        </a>
                    </li>
					<?php			
							}
							if($parse[0]=="admin"&&@$_SESSION['access']==2){
							?>
							<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:stat" class="nav-link">
                            <i class="am-icon-bar-chart"></i>
                            <span>数据统计</span>
                    </a>
                    </li><li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:botmanage" class="nav-link">
                            <i class="am-icon-terminal"></i>
                            <span>机器人管理</span>
                    </a>
                    </li><li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:account" class="nav-link">
                            <i class="am-icon-users"></i>
                            <span>用户管理</span>
                    </a>
                    </li><li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:broadcast" class="nav-link">
                            <i class="am-icon-comments"></i>
                            <span>公告管理</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:order" class="nav-link">
                            <i class="am-icon-edit"></i>
                            <span>订单管理</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:column" class="nav-link">
                            <i class="am-icon-plus-square"></i>
                            <span>栏目管理</span>
                    </a>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:payment" class="nav-link">
                            <i class="am-icon-lock"></i>
                            <span>钱包管理</span>
                    </a>
                    </li>
					
					 <li class="tpl-left-nav-item">
                        <a href="javascript:;" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-server"></i>
                            <span>站点管理</span>
                            <i class="am-icon-angle-right tpl-left-nav-more-ico am-fr am-margin-right"></i>
                        </a>
                        <ul class="tpl-left-nav-sub-menu">
                            <li>
								<a href='index.php?mod=admin:option:basic'>
								<i class='am-icon-angle-right'></i>
								<span>基础设置</span>
								</a> 
								<a href='index.php?mod=admin:option:appearance'>
								<i class='am-icon-angle-right'></i>
								<span>外观设置</span>
								</a>  								
								<a href='index.php?mod=admin:option:node'>
								<i class='am-icon-angle-right'></i>
								<span>节点设置</span>
								</a>  
								<a href='index.php?mod=admin:option:signup'>
								<i class='am-icon-angle-right'></i>
								<span>注册设置</span>
								</a>  
                            </li>
                        </ul>
                    </li>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:about" class="nav-link">
                            <i class="am-icon-info"></i>
                            <span>关于站点</span>
                    </a>
                    </li>
							<?php
							}
							if(@$_SESSION['access']==2&&ISSET($_SESSION['access'])){
							?>
					<li class="tpl-left-nav-item">
					<a href="index.php?mod=admin:stat" class="nav-link">
                            <i class="am-icon-dashboard"></i>
                            <span>管理站点</span>
                    </a>
                    </li>
					<?php							
						}
							?>
							<li class="tpl-left-nav-item">
					<a href="index.php?mod=user:logout" class="nav-link">
                            <i class="am-icon-sign-out"></i>
                            <span>登出系统</span>
                    </a>
                    </li>
						<?php	}else{
					?>
                    <li class="tpl-left-nav-item">
                        <a href="index.php?mod=login" class="nav-link tpl-left-nav-link-list">
                            <i class="am-icon-sign-in"></i>
                            <span>登录/注册</span>

                        </a>
                    </li>
					<?php 
					}}
					?>
					
                </ul>
            </div>
        </div>
		<div id="modal_show"></div>