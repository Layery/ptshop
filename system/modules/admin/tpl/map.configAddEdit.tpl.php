<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<style>
tr{height:40px;line-height:40px}
</style>
</head>
<body>
<div class="header lr10">
    <?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<div class="table_form lr10">	
<form action="" method="post" id="myform">
<table width="100%" class="lr10">
  <tr>
    <td width="100"><font style="color: #d80000;">*</font>腾讯地图</td>
    <td>
     <input name="type" value="1" type="radio" <?php if($config['on_off']==1){echo 'checked';}?>> 开启
     <input name="type" value="0" type="radio" <?php if($config['on_off']==0){echo 'checked';}?>> 关闭
	</td>
  </tr>
  <tr>
    <td><font style="color: #d80000;">*</font>应用名称：</td>
    <td><input type="text" class="input-text wid150" id="appName" name="appName" value="<?php echo $config['app_name']; ?>"/></td>
  </tr>
  <tr>
    <td><font style="color: #d80000;">*</font>应用密钥（key）</td>
    <td><input type="text" class="input-text wid250" id="key" name="key" value="<?php echo $config['key']; ?>"/>
	<a href="http://lbs.qq.com/mykey.html"  target="_blank" >点击登录配置</a>
	</td>
  </tr> 
	<tr>
    	<td width="100"></td>
   		<td>
            <input type="hidden" name="id" value="<?php echo $config['id']?>">
            <input type="hidden" name="appType" value="腾讯地图">
            <input type="button" value=" 提交 " name="dosubmit" class="button">
        </td>
    </tr>
</table>
</form>

</div><!--table-form end-->

<script>
    $(function () {
        $('.button').click(function () {
            var reg = /^(([0-9a-zA-Z]{5})\-){5}([0-9a-zA-Z]{5})$/;
            var key = $('#key').val();
            if($('#appName').val()==''){
                window.parent.message('请输入应用名称',8,1)
                return;
            }
            if(key==''){
                window.parent.message('请输入应用密钥key',8,1)
                return;
            }
            if(!reg.test(key)){
                window.parent.message('请输入正确格式的应用密钥key',8,1)
                return;
            }
            $('#myform').submit();
        });
    })
</script>
</body>
</html> 