<?php if(isset($_COOKIE['suc'])){ ?>
<div id="alert" class="am-alert am-alert-success" data-am-alert><button type="button" class="am-close">&times;</button><?php echo $_COOKIE['suc']; ?><script>window.setTimeout(function (){$('#alert').alert('close')},5000);</script></div>
<?php }elseif(isset($_COOKIE['fail'])){ ?>
<div id="alert" class="am-alert am-alert-danger" data-am-alert><button type="button" class="am-close">&times;</button><?php echo $_COOKIE['fail']; ?><script>window.setTimeout(function (){$('#alert').alert('close')},5000);</script></div>
<?php }elseif(isset($_COOKIE['warning'])){ ?>
<div id="alert" class="am-alert am-alert-warning" data-am-alert><button type="button" class="am-close">&times;</button><?php echo $_COOKIE['warning']; ?><script>window.setTimeout(function (){$('#alert').alert('close')},5000);</script></div>
<?php }elseif(isset($_COOKIE['default'])){ ?>
<div id="alert" class="am-alert am-alert-secondary" data-am-alert><button type="button" class="am-close">&times;</button><?php echo $_COOKIE['default']; ?><script>window.setTimeout(function (){$('#alert').alert('close')},5000);</script></div>
<?php } ?>