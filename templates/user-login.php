 <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                登录/注册
            </div>
			</br>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 登录商城
                    </div>
                </div>
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12 am-u-md-9">
						<?php loadalert(); ?>
                            <form class="am-form am-form-horizontal" name="login" method="post" action="index.php?mod">
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">用户名 / Username</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" name="username" placeholder="请输入您的用户名">
                                    </div>
                                </div>


                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">密码 / Password</label>
                                    <div class="am-u-sm-9">
                                        <input type="password" name="password" placeholder="请输入您的密码">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-success">登录</button>&nbsp;
										<button type='button'  onclick="javascript:window.location.href='index.php?mod=register'" class='am-btn am-btn-warning'>注册</button>&nbsp;
                                      <?php 
									  if(EMAIL_AUTH){
									  ?>
									  <button type='button'  onclick="javascript:window.location.href='index.php?mod=forgetpw'" class='am-btn'>找回密码</button>
									  <?php } ?>
									</div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>