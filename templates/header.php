<header class="am-topbar am-topbar-inverse admin-header">
        <div class="am-topbar-brand">
            <a href="javascript:;" class="tpl-logo">
                <img src=<?php echo SYSTEM_HEADER_ICON ; ?> alt="">
            </a>
        </div>
		 <div class="am-icon-list tpl-header-nav-hover-ico am-fl am-margin-right">

        </div>
        <div class="am-collapse am-topbar-collapse" id="topbar-collapse">

            <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list tpl-header-list">
        

                <li class="am-dropdown" data-am-dropdown data-am-dropdown-toggle>
                    <?php if(isset($_COOKIE['username'])){?>
					<a class="am-dropdown-toggle tpl-header-list-link" href="javascript:;">
                        <span class="tpl-header-list-user-nick"><span class="label label-danger"><?php echo $_COOKIE['username'];?></span></span>
                    </a>
                    <ul class="am-dropdown-content">
                        <li><a href="index.php?mod=panel:inventory"><span class="am-icon-bell-o"></span> 我的库存</a></li>
                        <li><a href="index.php?mod=panel:payment"><span class="am-icon-cog"></span> 我的钱包</a></li>
                        <li><a href="index.php?mod=user:logout"><span class="am-icon-power-off"></span> 退出</a></li>
                    </ul>
					<?php }else{ ?>
					<a class="am-dropdown-toggle tpl-header-list-link" href="javascript:;">
                        <span class="tpl-header-list-user-nick"><span class="label label-danger">未登录</span></span>
                    </a>
					<ul class="am-dropdown-content">
                        <li><a href="index.php?mod=login"><span class="am-icon-bell-o"></span> 登录/注册</a></li>
                    </ul>
					<?php } ?>
                </li>
            </ul>
        </div>
    </header>
	