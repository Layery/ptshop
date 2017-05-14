<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
<style>
	tr{height:40px;line-height:40px}
	.add-btn{
		background: #EEF3F7;
		padding:5px;
	}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
<form name="myform" action="" method="post">
	<table width="100%" cellspacing="0">
		<tr style="background:#f8f8f8">
			<td width="130"  align="right">分享类型：　　</td>
			<td class="add-type">
				<input type="button" class="add add-btn" name="add" value="新增类型">
				<input type="checkbox" name="type" value="0">
				<input type="radio" name="s_type" value="0" <?php echo ($type==0 ? 'checked' : ''); ?> /> <font color="#666">中奖不通知</font>
				<input type="radio" name="s_type" value="1" <?php echo ($type==1 ? 'checked' : ''); ?> /> <font color="#D80000">只发邮件通知</font>
				<input type="radio" name="s_type" value="2" <?php echo ($type==2 ? 'checked' : ''); ?> /> <font color="#D80000">只发短信通知</font>
				<input type="radio" name="s_type" value="3" <?php echo ($type==3 ? 'checked' : ''); ?> /> <font color="#D80000">短信和邮件通知</font></BR>
				<input type="radio" name="s_type" value="4" <?php echo ($type==4 ? 'checked' : ''); ?> /> <font color="#D80000">微信通知</font>
				<input type="radio" name="s_type" value="5" <?php echo ($type==5 ? 'checked' : ''); ?> /> <font color="#D80000">邮件和微信通知</font>
				<input type="radio" name="s_type" value="6" <?php echo ($type==6 ? 'checked' : ''); ?> /> <font color="#D80000">短信和微信通知</font>
				<input type="radio" name="s_type" value="7" <?php echo ($type==7 ? 'checked' : ''); ?> /> <font color="#D80000">邮件，短信，微信通知</font>
			</td>
		</tr>
		<tr  style="background:#eee">
        	<td width="100" align="right"></td>
            <td>
				<input type="submit" class="button" name="dosubmit"  value="提交" >
			</td>
		</tr>
</table>
</form>

</div><!--table-list end-->
<script>
	$(function () {
		$('.add').click(function () {
			//alert(2)
			var input = "<input type='text' class='input-text wid100' name='type' />";
			$(this).before(input);
		});
	})
</script>
</body>
</html> 