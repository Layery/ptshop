<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar-blue.css" type="text/css"> 
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar.js"></script>
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
  <table width="100%" cellspacing="0">
    <thead>
		<tr>
		<th width="80px">id</th>
		<th width="80px">uid</th>
		<th width="*" align="center">抽奖用户</th>
		<th width="*" align="center">奖项</th>
		<th width="*" align="center">奖励</th>
		<th width="200px" align="center">中奖日期</th>
		</tr>
    </thead>
    <tbody>
		<?php foreach($arr as $v){ ?>
		<tr>
			<td align="center"><?php echo $v['id']; ?></td>
			<td align="center"><?php echo $v['uid']; ?></td>
			<td align="center"><?php
				if ($v['username']) {
					echo $v['username'];
				}
				else if ($v['mobile']) {
					echo $v['mobile'];
				}
				else if ($v['email']) {
					echo $v['email'];
				}
			?></td>
			<td align="center"><?php echo $v['title'];?></td>
			<td align="center"><?php echo $v['desc']?></td>
			<td align="center"><?php echo date("Y-m-d H:m:s",$v['time']);?></td>
		</tr>
		<?php } ?>
  	</tbody>
</table>
</div><!--table-list end-->

<script>
</script>
</body>
</html> 