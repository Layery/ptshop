<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">

<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo G_PLUGIN_PATH; ?>/idialog/jquery.iDialog.min.js"  dialog-theme="default"></script>
<style type="text/css">
tr{height:40px;line-height:40px}
.dingdan_content{width:650px;border:1px solid #d5dfe8;background:#eef3f7;float:left; padding:20px;
	position: relative;}
.dingdan_content li{ float:left;width:310px;}
.dingdan_content_user{width:650px;border:1px solid #d5dfe8;background:#eef3f7;float:left; padding:20px;
	margin-bottom:10px;position: relative}
.dingdan_content_user li{ line-height:25px;}

.api_b{width:80px; display:inline-block;font-weight:normal}
.yun_ma{ word-break:break-all; width:200px; background:#fff; overflow:auto; height:100px; border:5px solid #09F; padding:5px;}

.charge{
	width: 20px;
	height: 20px;
	vertical-align: middle;
	cursor: pointer;
}
.charge:hover{
	transform: scale(1.1);
	-webkit-transform: scale(1.1);
	-moz-transform: scale(1.1);
	-o-transform: scale(1.1);
	-ms-transform: scale(1.1);
}
.dec-charge{
	position: absolute;
	top:20px;
	right:20px;
}
.add{
	position: absolute;
	bottom:20px;
	right:20px;
	width:65px;
	color: #D80000;
}
.add-charge{
	position: absolute;
	right:62px;
}
.table-pos{
	position: relative;
}
</style>
</head>
<body>
<div class="header-title lr10">
	<b>报名费用详情</b>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
	<form action="" method="post">
		<div class="dingdan_content">
			<h3 style="clear:both;display:block; line-height:30px;"><?php echo $act_info['act_title']; ?></h3>
			<li><b class="api_b">活动时间：</b><?php echo date('Y-m-d',$act_info['act_start_time']).' ~ '.date('Y-m-d',$act_info['act_end_time'])?></li>
			<li><b class="api_b">活动地点：</b><?php echo trim($act_info['act_address'])?></li>
			<li><b class="api_b">活动名额：</b><?php echo $act_info['act_num_limit'];?></li>
			<li><b class="api_b">已报名：</b><?php echo $act_info['act_num_signed'];?></li>
			<div class="bk10"></div>
			<li><b class="api_b">活动内容：</b><a class='show' style="cursor: pointer;" data-id="<?php echo $act_info['act_id']?>" data-title="<?php echo $act_info['act_title']?>">查看全文</a></li>
			<!--<a href="javascript:;" class="add" onclick="addCharge(this,<?php /*echo $act_info['act_id']*/?>)"><img src="<?php /*echo G_GLOBAL_STYLE.'/global/image/addCharge.png'*/?>" class="charge add-charge" onclick="" title="新增费用项">新增费用项</a>-->
		</div>
		<div class="bk10"></div>
		<?php foreach ($chargeInfo as $k => $v){?>
			<div class="dingdan_content_user">
				<li><b class="api_b">费用名称：</b><input type="text" name="charge_name[]" value="<?php echo $v['c_name']?>" id="charge_name" class="input-text" style="width: 200px;" placeholder="请输入费用名称"/></li>
				<li><b class="api_b">金额：</b><input type="text" name="charge[]" value="<?php echo $v['c_money']?>" id="charge" class="input-text" style="width: 200px;" placeholder="免费请填0，有人报名后不可修改" />元</li>
				<li><b class="api_b">名额限制：</b><input type="text" name="num[]" value="<?php echo $v['c_num_limit']?>" id="num-of-people" class="input-text" style="width: 200px;" placeholder="默认无限制" /></li>
				<!--<img src="<?php /*echo G_GLOBAL_STYLE.'/global/image/dec.png'*/?>" class="charge dec-charge" onclick="delCharge(this,<?php /*echo $v['c_id']*/?>)" title="删除费用项">-->
				<input type="hidden" name="c_id[]" value="<?php echo $v['c_id']?>">
			</div>
			<!--<div class="bk10"></div>-->
		<?php }?>
		<div class="submit-btn" style="background:#f6f6f6;padding: 5px 140px;clear: both;">
			<input type="hidden" name="act_id" value="<?php echo $act_info['act_id']?>">
			<input type="submit" name="dosubmit" class="button" value="修改">
			<input type="reset" class="button" value="重置">
		</div>
	</form>
</div><!--table-list end-->

<script>
	$(function () {
		//获取显示内容btnShow按钮并绑定相关事件
		$('.show').bind('click',function(){
			//var id = $(this).parent().siblings('td').eq(0).text();
			//var title = $(this).parent().siblings('td').eq(1).text();
			var id = $(this).data('id');
			var title = $(this).data('title');

			//通过Ajax从服务器端获取数据
			var data = {
				id:id,
				_:new Date().getTime()
			};
			$.get('<?php echo G_ADMIN_PATH?>/activity/ajaxGetContent',data,function(msg){
				iDialog({
					title:title,
					id:'DemoDialog'+id,   //DemoDialog 可以去掉
					content:msg,
					lock: true,
					width:800,
					fixed: true
				});
			});
		});
	})
	function addCharge(obj) {
		var tag = '<div class="dingdan_content_user">';
		tag += '<li><b class="api_b">费用名称：</b><input type="text" name="charge_name[]" id="charge_name" class="input-text" style="width: 200px;" placeholder="请输入费用名称"/></li>';
		tag += '<li><b class="api_b">金额：</b><input type="text" name="charge[]" id="charge" class="input-text" style="width: 200px;" placeholder="免费请填0，有人报名后不可修改" />元</li>';
		tag += '<li><b class="api_b">名额限制：</b><input type="text" name="num[]" id="num-of-people" class="input-text" style="width: 200px;" placeholder="默认无限制" /></li>';
		tag += '<img src="<?php echo G_GLOBAL_STYLE.'/global/image/dec.png'?>" class="charge dec-charge" onclick="delCharge(this,0)" title="删除费用项">';
		tag += '</div>';
		var formTag = $(obj).parent().parent(); //form 标签
		formTag.find('div').last().before(tag)
	}
	function delCharge(obj,id) {
		if(id==0){
			$(obj).parent().remove();
			return;
		}
		$.ajax({
			type:'get',
			url:'<?php echo G_ADMIN_PATH?>/activity/ajaxGetDelCharge',
			data:{id:id},
			dataType:'json',
			success:function (msg) {
				if(msg.status==0){
					$(obj).parent().remove();
					window.parent.message(msg.msg,4);
				}else{
					window.parent.message(msg.msg,8);
				}
			}
		})
	}

</script>
</body>
</html>