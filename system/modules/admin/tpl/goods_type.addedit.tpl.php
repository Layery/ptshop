<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<style>
body{ background-color:#fff}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="header-data lr10">

	<b style="color:red;">提示:</b> <b>商品类型</b> 是为了区分不同商品的<b>商品属性</b> 和 <b>商品规格</b>

</div>
<div class="bk10"></div>

<div class="table_form lr10">
<?php if(ROUTE_A=='edit'){ ?>
<table width="100%"  cellspacing="0" cellpadding="0">
<form name="form" action="" method="post">
    <tr>
			<td align="right">类型名称：</td>
			<td><input type="text"  name="name" class="input-text wid100" value="<?php echo $type['name'] ; ?>"></td>
	</tr>
    <tr height="60px">
			<td align="right"><input type="hidden" name="id" value="<?php echo $type['id'];?>"></td>
			<td><input class="button" type="submit" name="dosubmit" value=" 修改 " /></td>
	</tr>
</form>
</table>
<?php } ?>
<?php if(ROUTE_A=='add'){ ?>
<table width="100%"  cellspacing="0" cellpadding="0">
<form name="form" action="" method="post">
    <tr>
			<td align="right" width="200">类型名称：</td>
			<td><input type="text"  name="name" class="input-text wid100" style="width: 150px;"></td>
	</tr>
    <tr height="60px">
			<td align="right"></td>
			<td><input class="button" type="submit" name="dosubmit" value=" 添加 " /></td>
	</tr>
</form>
</table>
<?php } ?>
</div>
</body>
</html> 
