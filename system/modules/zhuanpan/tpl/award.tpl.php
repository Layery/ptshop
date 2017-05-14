<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<style>
tbody tr{ line-height:30px; height:30px;} 
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
  <table width="100%" cellspacing="0" id="mytable">
    <thead>
		<tr>
		<th width="80px">id</th>
		<th width="*" align="center">奖项</th>
		<th width="*" align="center">奖励金额</th>
		<th width="*" align="center">中奖几率%</th>
		<th width="*" align="center">操作</th>
		</tr>
    </thead>
    <tbody>
		<?php foreach($arr as $v){ ?>
		<tr>
			<form action="" method="post">

			<td align="center"><?php echo $v['id']; ?><input type="hidden" name="id" value="<?php echo $v['id'];?>">
</td>
			<td align="center">
				<?php echo $v['name'];?>
			</td>
			<td align="center">
				<input type="text" name="money" value="<?php echo $v['money'];?>">
			</td>
			<td align="center">
				<input type="text" name="jilv" value="<?php echo $v['jilv'];?>">
			</td>	
			<td align="center">
<input style="
    margin-bottom: 10px;
" type="submit" class="button" name="dosubmit" value="  保存设置  ">			</td>
		</tr>
		
	</form>
		<?php } ?>
		
        	<div>
            
			</div>
  	</tbody>
	
  	
</table>
</div><!--table-list end-->

<table width="100%" class="lr10">
  <tbody>
  			<form action="" method="post">

  <tr>
	 <td>消耗福分</td>
	 <td><input type="test" class="input-text" name="z_fufen" size="30" value="<?php echo $fufen['z_fufen'];?>"></td>
	 <input type="hidden" class="input-text" name="id" size="30" value="<?php echo $fufen['id'];?>">
  </tr>  
  
	<tr>
    	<td width="100"></td> 
   		<td> <input type="submit" value=" 提交 " name="do" class="button"></td>
    </tr>
</tbody>
</form>
</table>
</body>
</html> 