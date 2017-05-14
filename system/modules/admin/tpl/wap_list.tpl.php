<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> </title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
	<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<style>
tbody tr{ line-height:30px; height:30px;}
.search{
	height: 40px;
	overflow: hidden;
	background-color: #eef3f7;
	border: 1px solid #d5dfe8;
	padding-left:20px;
	margin:0px 10px;

}
.search label,img,.select{
	line-height:40px;
	display:inline-block;
	margin:auto 0px;
	vertical-align: middle;
}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<form action="" method="get" name="searchForm">
	<div class="search">
		<label><img src="<?php echo G_GLOBAL_STYLE?>/global/image/icon_search.gif">按商城类型显示：</label>
		<select id="type" name="typeId">
			<option value="-1">≡ 请选择类型 ≡</option>
			<option value="0" <?php if($typeId==0)echo "selected='selected'";?>>活动商城</option>
			<option value="1" <?php if($typeId==1)echo "selected='selected'";?>>装备商城</option>
			<option value="2" <?php if($typeId==2)echo "selected='selected'";?>>一元拼商城</option>
		</select>
	</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
  <table width="100%" cellspacing="0">
    <thead>
		<tr>
			<th width="10%">id</th>
			<th width="10%" align="center">轮播名称</th>
			<th width="20%" align="center">轮播图片</th>
			<th width="10%" align="center">所属商城</th>
			<th width="20%" align="center">图片链接</th>
			<th width="10%" align="center">操作</th>
		</tr>
    </thead>
    <tbody>
		<?php foreach($lists as $v){ ?>
		<tr>
			<td align="center"><?php echo $v['id']; ?></td>
			<td align="center"><?php echo $v['title']; ?></td>
			<td align="center"><img height="50px" src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $v['img']; ?>"/></td>
			<td align="center">
				<?php
					switch ($v['where_is']){
						case 0:
							echo '活动商城';
							break;
						case 1:
							echo '装备商城';
							break;
						case 2:
							echo '一元拼商城';
							break;
					}
				?>
			</td>
			<td align="center"><?php echo $v['link']; ?></td>
			<td align="center">
				<a href="<?php echo WEB_PATH; ?>/admin/wap/update/<?php echo $v['id'];?>">修改</a>
				<a href="<?php echo WEB_PATH; ?>/admin/wap/delete/<?php echo $v['id'];?>">删除</a>
			</td>	
		</tr>
		<?php } ?>
  	</tbody>
</table>
	<div class="btn_paixu"></div>
	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>
</div><!--table-list end-->
</form>
<script>
	//当页面加载完成后
	$(function(){
		//给下拉框绑定事件
		$("select").change(function(){
			//为表单绑定提交事件
			//alert('hello');
			$("form[name=searchForm]").submit();
			$('#category').attr('selected','selected');
		});
	});
</script>
</body>
</html> 