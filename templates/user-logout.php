 <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                登出系统
            </div>
			</br>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 登出系统
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-9">
						<?php 
						$_SESSION['access']=0;
						setcookie("username","",time()-3600);
						setcookie("password","",time()-3600);
						setcookie("steamid","",time()-3600);
						setcookie("email","",time()-3600);
						setcookie("phone","",time()-3600);
						setcookie("suc","登出成功",time()+10);
						redirect('index.php'); 
						?>       
                        </div>
                    </div>
                </div>

            </div>
        </div>