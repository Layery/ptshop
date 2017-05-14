<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<style>
	.bg{background:#fff url(<?php echo G_GLOBAL_STYLE; ?>/global/image/ruler.gif) repeat-x scroll 0 9px }
	.color_window_td a{ float:left; margin:0px 10px;}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>    
</div>

<div class="bk10"></div>
<div class="table_form lr10">
<form method="post" action="#" id="form_post">
	<table width="100%"  cellspacing="0" cellpadding="0">
		<tr>
			<td align="right" style="width:120px">商品标题：</td>
			<td>
            	<a target="_blank" href="<?php echo WEB_PATH;?>/goods/<?php echo $shopinfo['id'];?>"><b><?php echo $shopinfo['title']; ?></b></a>
            </td>			
		</tr>
		<tr>
			<td align="right" style="width:120px">商品售价：</td>
			<td><b style="color:red"><?php echo $shopinfo['money']; ?></b> <b>元</b></td>			
		</tr>
		<tr>
			<td align="right" style="width:120px">已购买：</td>
			<td><b style="color:red"><?php echo $shopinfo['buy_yet']; ?></b> <b>件</b></td>
		</tr>
		<tr>
			<td align="right" style="width:120px">库存：</td>
			<td><b style="color:red"><?php echo $shopinfo['inventory']; ?></b> <b>件</b></td>
		</tr>

		<tr>
			<td align="right" style="width:120px;color:red">新商品售价：</td>
			<td><input type="text" name="money" id="money" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px;padding-left:0px;text-align:center" class="input-text"> 元</td>			
		</tr>
       
      

        <tr height="60px">
			<td align="right" style="width:120px"></td>
			<td><input type="submit" name="dosubmit" class="button" value=" 确认更改 " /></td>
		</tr>
	</table>
</form>
</div>
<!--JS-->
<script type="text/javascript">
$("#form_post").submit(function(){
								
	var buy_yet = <?php echo $shopinfo['buy_yet']; ?>;
	var inventory = <?php echo $shopinfo['inventory']; ?>;
	var y_money = <?php echo $shopinfo['money']; ?>;
	var money = parseInt($("#money").val());
	
	if((y_money == money)){
		window.parent.message("商品价格没有改变!",8,2);
		return false;
	}
	
	if(!money){
		window.parent.message("商品价格输入不正确!",8,2);
		return false;
	}
	return true;
		
	
});
</script>
<!--JS-->
</body>
</html> 