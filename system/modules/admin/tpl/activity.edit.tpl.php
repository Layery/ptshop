<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
<link rel="stylesheet" href="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar-blue.css" type="text/css"> 
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar.js"></script>
<script type="text/javascript">
var editurl=Array();
editurl['editurl']='<?php echo G_PLUGIN_PATH; ?>/ueditor/';
editurl['imageupurl']='<?php echo G_ADMIN_PATH; ?>/ueditor/upimage/';
editurl['imageManager']='<?php echo G_ADMIN_PATH; ?>/ueditor/imagemanager';
</script>
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/ueditor/ueditor.all.min.js"></script>
<style>
	.bg{background:#fff url(<?php echo G_GLOBAL_STYLE; ?>/global/image/ruler.gif) repeat-x scroll 0 9px }
	.color_window_td a{ float:left; margin:0px 10px;}

	.con-tab{ margin:10px; color:#444}
	.con-tab #tab-i{ margin-left:20px; overflow:hidden; height:27px; _height:28px; }
	.con-tab #tab-i li{
		float:left;background:#eaeef4;
		padding:0 8px;border:1px solid #dce3ed;
		height:25px;_height:26px;line-height:26px;
		margin-right:5px;cursor: pointer; position:relative;z-index:0;
	}
	.con-tab div.con-tabv{
		clear:both; border:1px solid #dce3ed;
		width:100%;
		margin-top:-1px; padding-top:20px;
		background-color:#fff; position:relative; z-index:1;}

	#tab-i li.on{ background-color:#fff;color:#444; font-weight:bold; border-bottom:1px solid #fff;  position:relative;z-index:2;}

	table th{ border-bottom:1px solid #eee; font-size:12px; font-weight:100; text-align:right; width:200px;}
	table td{ padding-left:10px;}
	.con-tabv tr{ text-align:left}
	input.button{ display:inline-block}
	.btn{
		border-radius: 3px;
		border: 1px solid transparent;
		display: inline-block;
		padding: 6px 12px;
		text-align: center;
		vertical-align: middle;
		cursor: pointer;
		background-color: #e7e7e7;
	}
	.btn-default:hover{
		background-color: #adadad;
	}
	.btn-success{
		background-color: #D80000;
		color: #fff;
	}
	.btn-success:hover{
		background-color: rgb(172, 0, 0);
	}
	.spec-img{
		display: inline-block;
		width:32px;
		height:32px;
		text-align: center;
		vertical-align: middle;
		cursor: pointer;
		border: 1px solid transparent;
	}
	.spec-img:hover{
		transform: scale(1.05);
		-webkit-transform: scale(1.05);
		-moz-transform: scale(1.05);
		-o-transform: scale(1.05);
		-ms-transform: scale(1.05);
	}
	.showImg{
		display: inline-block;
		background:url("<?php echo G_GLOBAL_STYLE?>/global/image/setting.png") no-repeat center center;
		background-size: 100% 100%;
		width:20px;
		height: 20px;
		vertical-align: middle;
		margin: 0 5px;
		cursor: pointer;
	}
	.showImg:hover{
		transform: scale(1.1);
		-webkit-transform: scale(1.1);
		-moz-transform: scale(1.1);
		-o-transform: scale(1.1);
		-ms-transform: scale(1.1);
	}
	.img{
		width: 20px;
		height: 20px;
		vertical-align: middle;
		cursor: pointer;
	}
	.img:hover{
		transform: scale(1.1);
		-webkit-transform: scale(1.1);
		-moz-transform: scale(1.1);
		-o-transform: scale(1.1);
		-ms-transform: scale(1.1);
	}

	/* ueditor的字体和字号下拉显示样式 */
	.edui-popup-content.edui-default{
		height: auto !important;
	}

	.address-tips{
		position: absolute ;
		left:140px;
		background-color: #fff;
		width:307px;
		overflow: auto;
		height:200px;
	}
	.address-tips li{
		height: 18px;
		margin: 0 5px 0 0;
		padding: 2px 0 2px 5px;
		white-space: nowrap;
	}
	.address-tips li:hover{
		background-color: #d4d4d4;
	}

	.latlng{
		color: #FFF;
		background: #aaa;
		border: 0px;
		height: 30px;
		border-top: 0px;
		border-left: 0px;
		border-bottom: 0px solid #666;
		border-right: 0px solid #666;
		padding: 0px 20px;
		font-size: 12px;
		line-height: 30px;
		display: inline-block;
		border-radius: 5px;
	}
	.latlng:hover{
		background: #444;
	}

	img{
		width: 21px;
		height: 21px;
		cursor: pointer;
	}
	img:hover{
		transform: scale(1.2);
		-webkit-transform: scale(1.2);
		-moz-transform: scale(1.2);
		-o-transform: scale(1.2);
		-ms-transform: scale(1.2);
	}
	.act-notice{
		margin-bottom:6px;
	}
	.price-step{
		margin-bottom:2px;
	}
    .act-category{
        margin-bottom:2px;
    }
	.btn-face{
		padding: 3px;
		margin:3px;
		background: #EEF3F7;
		border-radius: 4px;
		cursor: pointer;
		/*border: 1px solid transparent;*/
		display: inline-block;
		text-align: center;
		vertical-align: middle;
	}
	.btn-add{
		margin-right:14px;
	}

	/*关联商品的样式*/
	.div-title{
		width: 100%;
		position: inherit;
		clear: both;
	}
	p{
		float: left;
		text-align: center;
		vertical-align: middle;
	}
	.div-content{
		width: 100%;
		position: inherit;
		margin-top: 22px;
		clear: both;
	}
	.div-face{
		float: left;
	}
	.btn-operate{
		display: block;
	}
	.div-operate{
		height:34px;
		line-height:34px;
		vertical-align: middle;
	}

</style>
</head>
<body>
<script>

function CheckForm(){
	var money = parseInt($("#money").val());
	//alert(money);
		if(money >= 100000){
			window.parent.message("价格大于10万，商品添加会很慢,请耐心等待，不要关闭窗口!",1,5);
		}	
		return true;
}

function copythis(o){
	//获取当前行
	var curr_tr = $(o).parent();
	var td = $(o).parent().parent();
	var count = td.children().length;
	//alert(count)
	//判读img标签的src是否是add，如果是，就克隆，不是就删除
	if($(o).attr('src').indexOf("asc.png") > 0){
		//克隆当前行
		var new_tr = curr_tr.clone();
		var src = $(o).attr('src').replace(/asc.png/gi,'desc.png');
		new_tr.find('img').attr('src',src); //更改图标
		new_tr.find('img').attr('title','删除活动须知'); //更改title
		new_tr.find('span').text(count+1+'.'); //更改序号
		//把当前行的内容清空
		new_tr.find('input').val('');
		//把新行放到当前行的后面
		td.append(new_tr);
	}else{
		//删除当前行
		curr_tr.remove();
	}
}
function addLadder(o){
	//获取当前行
	var curr_tr = $(o).parent();
	var td = $(o).parent().parent();
	var count = td.children().length;
	//alert(count)
	//判读img标签的src是否是add，如果是，就克隆，不是就删除
	if($(o).attr('src').indexOf("asc.png") > 0){
		//克隆当前行
		var new_tr = curr_tr.clone();
		var src = $(o).attr('src').replace(/asc.png/gi,'desc.png');
		new_tr.find('img').attr('src',src); //更改图标
		new_tr.find('img').attr('title','删除价格阶梯'); //更改title
		//把当前行的内容清空
		var inputObj = new_tr.find('input');
		inputObj[0].value = '';
		inputObj[1].value = 0;
		inputObj[2].value = '';
		//alert(inputObj.length);
		//new_tr.find('input').val('');
		//把新行放到当前行的后面
		td.append(new_tr);
	}else{
		//删除当前行
		curr_tr.remove();
	}
}
//添加分类
function addCategory(o){
    //获取当前行
    var curr_tr = $(o).parent();
    var td = $(o).parent().parent();
    var count = td.children().length;
    //var all_select = td.find('select').not(':first');
    //alert(count)
    //判读img标签的src是否是add，如果是，就克隆，不是就删除
    if($(o).attr('src').indexOf("asc.png") > 0){
        var curr_select = curr_tr.find('select');
        //克隆当前行
        var new_tr = curr_tr.clone();
        //console.log(new_select.val(option));
        var new_select = new_tr.find('select');
        new_select.find('option:selected').removeAttr('selected'); //取消下拉选中
        var src = $(o).attr('src').replace(/asc.png/gi,'desc.png');
        new_tr.find('img').attr('src',src); //更改图标
        new_tr.find('img').attr('title','删除分类'); //更改title
        //把新行放到当前行的后面
        td.append(new_tr);
    }else{
        //删除当前行
        curr_tr.remove();
    }
}
</script>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="table_form lr10 con-tab" id="con-tab">
	<ul id="tab-i">
		<li name="con-tabk">基本信息</li>
	</ul>
<form method="post" action="">
	<div name='con-tabv' class="con-tabv">
	<table width="100%" id="general-table" cellspacing="0" cellpadding="0">
        <tr>
			<td align="right" style="width:120px"><font color="red">*</font>活动主题：</td>
			<td colspan="2">
            <input  type="text" id="title"  name="title" value="<?php echo $activity['act_title']?>" onKeyUp="return gbcount(this,100,'texttitle');"  class="input-text wid400 bg">
            <span style="margin-left:10px"><font color="#0c0">※ </font>还能输入<b id="texttitle">100</b>个字符</span>
           
            </td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>所属分类：</td>
			<td colspan="2">
                <?php foreach ($categorys as $k => $item){?>
                    <div class="act-category">
                        <select id="category" name="cid[]">
                            <option value="0">≡ 请选择分类 ≡</option>
                            <?php echo $item; ?>
                        </select>
                        <?php if($k == 0){?>
                            <img src="<?php echo G_GLOBAL_STYLE.'/global/image/asc.png'?>" onclick="addCategory(this)" title="添加分类">
                        <?php }else{?>
                            <img src="<?php echo G_GLOBAL_STYLE.'/global/image/desc.png'?>" onclick="addCategory(this)" title="删除分类">
                        <?php }?>
                    </div>
                <?php }?>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">活动上架：</td>
			<td colspan="2">
				<?php if($activity['act_active']){?>
					<input type="radio" name="active" value="1" checked> 是　
					<input type="radio" name="active" value="0"> 否
				<?php }else{?>
					<input type="radio" name="active" value="1"> 是　
					<input type="radio" name="active" value="0" checked> 否
				<?php }?>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">活动设置：</td>
			<td colspan="2">
				<label for="recommend"><input name="recommend" id="recommend" value="1" type="checkbox" <?php if($activity['act_recommend']){echo 'checked';}else{echo '';}?>/>&nbsp;推荐&nbsp;</label>&nbsp;
				<label for="best"><input name="best" id="best" value="1" type="checkbox" <?php if($activity['act_best']){echo 'checked';}else{echo '';}?>/>&nbsp;精品</label>&nbsp;
				<label for="best"><input name="sale" id="sale" value="1" type="checkbox" <?php if(!$activity['act_is_group']){echo 'checked';}else{echo '';}?>/>&nbsp;特价</label>&nbsp;
				<span class="lr10"><font color="#0c0">※ </font>选择精品、特价分类时，请勾选对应的设置；用来前台展示对应的功能；精品：表示精品，特价：不参与拼团</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>活动时间：</td>
			<td colspan="2">
				<input name="startTime" type="text" value="<?php echo date('Y-m-d H:i:s', $activity['act_start_time'])?>" id="posttime1" class="input-text posttime"  readonly="readonly" /> -
				<input name="endTime" type="text" value="<?php echo date('Y-m-d H:i:s', $activity['act_end_time'])?>" id="posttime2" class="input-text posttime"  readonly="readonly" />
				<script type="text/javascript">
					date = new Date();
					Calendar.setup({
						inputField     :    "posttime1",
						ifFormat       :    "%Y-%m-%d %H:%M:%S",
						showsTime      :    true,
						timeFormat     :    "24"
					});
					Calendar.setup({
						inputField     :    "posttime2",
						ifFormat       :    "%Y-%m-%d %H:%M:%S",
						showsTime      :    true,
						timeFormat     :    "24"
					});
				</script>
			</td>
		</tr>
		<!--<tr>
			<td align="right" style="width:120px"><font color="red">*</font>活动人数：</td>
			<td><input type="text" name="num" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px;padding-left:0px;text-align:center" class="input-text" >位　　<span class="lr10">人数为空默认不限制参与人数</span></td>
		</tr>-->
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>活动地点：</td>
			<td colspan="2">
				<input type="text" name="address" value="<?php echo $activity['act_address']?>" id="address" class="input-text wid300" />
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">活动坐标（经纬度）：</td>
			<td colspan="2">
				<input type="text" name="latlng" value="<?php echo $activity['act_latlng']?>" id="latlng" class="input-text" />
				<input type="button" class="latlng" value="坐标拾取器">
				<span class="lr10"><font color="#0c0">※ </font>可以点击坐标拾取器获取经纬度</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>报名设置：</td>
			<td colspan="2">
				<div class="act-charge">
					活动费用
					<input type="text" name="charge" value="<?php echo $activity['act_charge']?>" id="charge" class="input-text" style="width: 120px;" placeholder="免费请填0"/>&nbsp;&nbsp;
					名额限制
					<input type="text" name="number" value="<?php echo $activity['act_num_limit']?>" id="num-of-people" class="input-text" style="width: 120px;" placeholder="名额（默认无限制）" />
					<span class="lr10"><font color="#0c0">※ </font>免费或特价活动不会参加拼团</span>
				</div>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>价格阶梯：</td>
			<td width="500">
				<?php if(!empty($price_step)){?>
					<?php foreach ($price_step as $key => $val){?>
					<div class="price-step">
						<?php if($key == 0){?>
							人数达到
							<input type="text" name="num[]" value="<?php echo $val['num']?>" onKeyUp="value=value.replace(/\D/g,'')" id="num_step" class="input-text" placeholder="请输入阶梯人数"/>&nbsp;&nbsp;
							享受价格
							<input type="text" name="price[]" value="<?php echo $val['money']?>" id="enjoy_price" class="input-text" style="width: 120px;"" />
							<img src="<?php echo G_GLOBAL_STYLE.'/global/image/asc.png'?>" onclick="addLadder(this)" title="添加价格阶梯">
						<?php }else{?>
							人数达到
							<input type="text" name="num[]" value="<?php echo $val['num']?>" onKeyUp="value=value.replace(/\D/g,'')" id="num_step" class="input-text" placeholder="请输入阶梯人数"/>&nbsp;&nbsp;
							享受价格
							<input type="text" name="price[]" value="<?php echo $val['money']?>" id="enjoy_price" class="input-text" style="width: 120px;"" />
							<img src="<?php echo G_GLOBAL_STYLE.'/global/image/desc.png'?>" onclick="addLadder(this)" title="删除价格阶梯">
						<?php }?>
						<input type="hidden" name="step_id[]" value="<?php echo $val['id']?>">
					</div>
					<?php }?>
				<?php }else{?>
					<div class="price-step">
						人数达到
						<input type="text" name="num[]" value="" onKeyUp="value=value.replace(/\D/g,'')" id="num_step" class="input-text" placeholder="请输入阶梯人数"/>&nbsp;&nbsp
						享受价格
						<input type="text" name="price[]" value="0" id="enjoy_price" class="input-text" style="width: 120px;"" /><img src="<?php echo G_GLOBAL_STYLE.'/global/image/asc.png'?>" onclick="addLadder(this)" title="添加价格阶梯">
						<input type="hidden" name="step_id[]" value="<?php echo $val['id']?>">
					</div>
				<?php }?>
			</td>
			<td><span class="lr10"><font color="#0c0">※ </font>免费或特价活动不参与拼团，可以不用填写价格阶梯；阶梯团人数以递增录入，且不能超过名额限制；超过人数限制会自动过滤掉</span></td>
		</tr>
		<tr>
			<td align="right" style="width:120px">拼车费：</td>
			<td colspan="2">
				<input type="text" name="fare" value="<?php echo $activity['act_fare']?>" id="fare" class="input-text" />
				<span class="lr10"><font color="#0c0">※ </font>报名期间退款，需付拼车费；若无需拼车费请勿添加</span>
			</td>
		</tr>
		<!--<tr>
			<td align="right" style="width:120px">报名设置：</td>
			<td colspan="2">
				<label for="recommend"><input name="recommend" id="recommend" value="1" type="checkbox" />&nbsp;推荐&nbsp;</label>&nbsp;
				<label for="flag"><input name="flag" id="flag" value="1" type="checkbox" />&nbsp;活动结束前均可报名</label>
			</td>
		</tr>-->
		<tr>
			<td align="right" style="width:120px">赠送消费积分数：</td>
			<td colspan="2">
				<input type="text" name="give_integral" value="<?php echo $activity['give_integral']?>" id="give_integral" class="input-text" />
				<span class="lr10"><font color="#0c0">※ </font>报名活动时赠送消费积分数，-1表示按报名价格赠送，数据填写请填正数<font color="#D80000">（不可为负，-1例外）</font></span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">赠送分享积分数：</td>
			<td colspan="2">
				<input type="text" name="share_integral" value="<?php echo $activity['share_integral']?>" id="share_integral" class="input-text" />
				<span class="lr10"> <font color="#0c0">※ </font>用户分享后赠送的积分数，且必须接受分享后的用户有消费；-1表示按报名价格赠送，数据填写请填正数<font color="#D80000">（不可为负，-1例外）</font></span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">积分购买金额：</td>
			<td colspan="2">
				<input type="text" name="integral" value="<?php echo $activity['integral']?>" id="integral" class="input-text" />元
				<span class="lr10"> <font color="#0c0">※ </font>(此处需填写金额)活动报名时最多可以使用多少积分兑换金额，-1表示可以抵掉全部价格，数据填写请填正数<font color="#D80000">（不可为负，-1例外）</font></span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>活动咨询方式：</td>
			<td colspan="2">
				<input type="text" name="consult" value="<?php echo $activity['act_consult']?>" id="consult" class="input-text wid300" placeholder="请输入手机号、座机号（区号-座机号)"/>
			</td>
		</tr>

        <tr>
			<td align="right" style="width:120px"><font color="red">*</font>首页列表海报：</td>
			<td colspan="2">
				<img id="show-poster" src="<?php echo G_UPLOAD_PATH.'/'.$activity['act_home_poster']; ?>" style="border:1px solid #eee; padding:1px; width:50px; height:50px;">
				<input type="text" id="imagehome" name="home_poster" value="<?php echo $activity['act_home_poster']?>" class="input-text wid300">
				<input type="button" class="button"
             onClick="GetUploadify('<?php echo WEB_PATH; ?>','uploadify','海报图上传','image','poster',1,500000,'imagehome','call_back_poster')" value="上传图片"/>
			</td>
      	</tr>
        <tr>
            <td align="right" style="width:120px"><font color="red">*</font>活动海报：</td>
            <td colspan="2">
                <img id="show-img" src="<?php echo G_UPLOAD_PATH.'/'.$activity['act_poster']; ?>" style="border:1px solid #eee; padding:1px; width:50px; height:50px;">
                <input type="text" id="imagetext" name="poster" value="<?php echo $activity['act_poster']?>" class="input-text wid300">
                <input type="button" class="button"
                       onClick="GetUploadify('<?php echo WEB_PATH; ?>','uploadify','海报图上传','image','poster',1,500000,'imagetext','call_back')" value="上传图片"/>
            </td>
        </tr>
		<tr>
			<td style="width: 120px;" align="right"><font color="red">*</font>活动须知：</td>
			<td colspan="2">
				<?php if(!empty($notice)){?>
				<?php foreach ($notice as $k => $v){?>
					<div class="act-notice">
						<?php if($k == 0){?>
							<!--<span class="bullets">1.</span>-->
							<input type="text" name="notice[]" value="<?php echo $v['n_notice'];?>" id="num-of-people" class="input-text wid300" placeholder="活动须知内容" />
							<img src="<?php echo G_GLOBAL_STYLE.'/global/image/asc.png'?>" class="add-notice" onclick="copythis(this)" title="添加活动须知">
						<?php }else{?>
							<!--<span class="bullets"><?php /*echo $k+1;*/?>.</span>-->
							<input type="text" name="notice[]" value="<?php echo $v['n_notice'];?>" id="num-of-people" class="input-text wid300" placeholder="活动须知内容" />
							<img src="<?php echo G_GLOBAL_STYLE.'/global/image/desc.png'?>" class="add-notice" onclick="copythis(this)" title="添加活动须知">
						<?php }?>
						<input type="hidden" name="n_id[]" value="<?php echo $v['n_id']?>">
					</div>
				<?php }?>
				<?php }else{?>
					<div class="act-notice">
						<!--<span class="bullets">1.</span>-->
						<input type="text" name="notice[]" value="" id="num-of-people" class="input-text wid300" placeholder="活动须知内容" />
						<img src="<?php echo G_GLOBAL_STYLE.'/global/image/asc.png'?>" class="add-notice" onclick="copythis(this)" title="添加活动须知">
						<!--<span class="bullets"><?php /*echo $k+1;*/?>.</span>-->
						<input type="hidden" name="n_id[]" value="<?php echo $v['n_id']?>">
					</div>
				<?php }?>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">活动描述：</td>
			<td colspan="2"><textarea name="act_desc" class="wid400" onKeyUp="gbcount(this,250,'description');" style="height:60px"><?php echo $activity['act_desc']?></textarea><br /> <span>还能输入<b id="description">250</b>个字符</span>
			</td>
		</tr>
		<tr>
        	<td height="300" style="width:120px"  align="right"><font color="red">*</font>活动详情：</td>
			<td colspan="2"><script name="content" id="myeditor" type="text/plain"><?php echo htmlspecialchars_decode($activity['act_content'])?></script>
            	<style>
				.content_attr {
					border: 1px solid #CCC;
					padding: 5px 8px;
					background: #FFC;
					margin-top: 6px;
					width:915px;
				}
				</style>
                <div class="content_attr">
                <label><input name="sub_text_des" type="checkbox"  value="off" checked>是否截取内容</label>
                <input type="text" name="sub_text_len" class="input-text" value="1500" size="3">字符至内容摘要<label>
            	</div>
            </td>        
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>筛选属性：</td>
			<td colspan="2" id="filter">
				<?php foreach ($attribute as $val){?>
					<?php foreach ($act_attr as $vo){?>
						<?php if($val['id'] == $vo['attr_id']){?>
							<select name="attr[<?php echo $val['id']?>]">
								<option value="0">≡ 请选择<?php echo $val['name']?> ≡</option>
								<?php foreach($val['value'] as $v){?>
									<?php if($vo['attr_value'] == $v){?>
										<option value="<?php echo $v?>" selected><?php echo $v?></option>
									<?php }else{?>
										<option value="<?php echo $v?>"><?php echo $v?></option>
									<?php }?>
								<?php }?>
							</select>
							<input type="button" class="btn-face btn-add" value="添加条件" onclick="addOp(this)">
							<input type="hidden" name="attr_id[<?php echo $val['id']?>]" value="<?php echo $vo['id']?>">
						<?php }?>
					<?php }?>
				<?php }?>
				<span style="margin-left:10px"><font color="#0c0">※ </font>有筛选属性时不能为空，若下拉选项中没有请点击“添加条件”，增加条件；反之，不必填写</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">分享标题：</td>
			<td colspan="2">
				<input  type="text" id="share_title"  name="share_title" value="<?php echo $shareInfo['title']?>" class="input-text wid400 bg">

				<span style="margin-left:10px"><font color="#0c0">※ </font>分享时候的标题；为空时，默认以活动标题作为分享标题</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">分享描述：</td>
			<td colspan="2"><textarea name="share_desc" class="wid400" onKeyUp="gbcount(this,250,'textdescription');" style="height:60px"><?php echo $shareInfo['description']?></textarea><br /> <span>还能输入<b id="textdescription">250</b>个字符</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>分享图片：</td>
			<td colspan="2">
				<img id="share-img" src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $shareInfo['icon']?>" style="border:1px solid #eee; padding:1px; width:50px; height:50px;">
				<input type="text" id="sharetext" name="share_icon" value="<?php echo $shareInfo['icon']?>" class="input-text wid300">
				<input type="button" class="button"
					   onClick="GetUploadify('<?php echo WEB_PATH; ?>','uploadify','分享图片上传','image','share',1,500000,'sharetext','call_back1')" value="上传图片"/>
				<input type="hidden" name="share_id" value="<?php echo $shareInfo['id']?>">
			</td>
		</tr>
		<!--<tr>
			<td align="right" style="width:120px"><font color="red">*</font>关联商品：</td>
			<td colspan="2">
				<img src="<?php /*echo G_GLOBAL_STYLE*/?>/global/image/icon_search.gif">
				<select name="cat_id" id="">
					<option value="0">≡ 所有分类 ≡</option>
				</select>
				<select name="brand_id" id="">
					<option value="0">≡ 所有品牌 ≡</option>
				</select>
				<input type="text" class="input-text" name="keyword">
				<input type="button" style="width: 50px;" class="btn-face" value="搜索">
				<div class="div-title">
					<p style="width: 35%;">可选商品</p>
					<p style="width: 10%;">操作</p>
					<p style="width: 35%;">已关联商品</p>
				</div>
				<div class="div-content">
					<div class="div-face" style="width: 35%;">
						<select style="width: 100%;" name="" id="" size="10" multiple>
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
						</select>
					</div>
					<div class="div-face div-operate" style="width: 10%;" align="center">
						<input type="button" class="btn-operate btn-face btn-add-all" value=">>" title="全部添加">
						<input type="button" class="btn-operate btn-face btn-add-one" value=">" title="添加">
						<input type="button" class="btn-operate btn-face btn-drop" value="<" title="移除">
						<input type="button" class="btn-operate btn-face btn-drop-all" value="<<" title="全部移除">
					</div>
					<div class="div-face" style="width: 35%;">
						<select style="width: 100%;" name="" id="" size="10">
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
							<option value="0">1</option>
						</select>
					</div>
				</div>
			</td>
		</tr>-->
	</table>
	</div>
	<div class="submit-btn" style="background:#f6f6f6;margin-top: 10px;padding: 5px 140px;">
        <input type="hidden" name="typeId" class="typeId" value="0"> <!--筛选属性有用-->
		<input type="hidden" name="act_id" value="<?php echo $activity['act_id']?>">
		<input type="submit" name="dosubmit" class="button" value="修改">
		<input type="reset" class="button" value="重置">
	</div>
</form>

	<div class="lr10" style="width: 916px;background-color: #fff;position:absolute;top:364px;left: 140px;z-index: 99999;">
		<iframe id='mapIframe' src="http://lbs.qq.com/tool/getpoint/getpoint.html" style="width: 100%;height: 650px;border: 0;display: none;" scrolling="no"></iframe>
	</div>
</div>
 <span id="title_colorpanel" style="position:absolute; left:568px; top:155px" class="colorpanel"></span>
<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE; ?>/global/js/GgTab.js"></script>
<script type="text/javascript">
	//添加筛选条件
	function addOp(obj) {
		//alert($(obj).val());
		//console.log(obj.value);
		if($(obj).hasClass('hasOne')){
			return;
		}
		var str = "<input type='text' class='input-text wid80' name='condition'>";
		str += "<input type='button' class='btn-face btn-sure' onclick='sure(this)' value='确定'>";
		str += "<input type='button' class='btn-face btn-back' onclick='back(this)' value='<<'>";
		$(obj).before(str);
		$(obj).addClass('hasOne');
	}
	//增加筛选条件，并追加到下拉框后且选中
	function sure(obj) {
		var condition = $(obj).prev().val().trim();
		var typeId = $('.typeId').val();
		var attr_id = $(obj).prev().prev().val();
		//alert(attr_id)
		//判断添加的分类是否为空
		if(condition == ''){
			window.parent.message('筛选条件不能为空',8);
		}
		$.getJSON("<?php echo WEB_PATH; ?>/admin/act_attr/ajaxAddAttr/",{typeId:typeId,condition:condition,attr_id:attr_id},function(data){
			if(data	== 'ok'){
				var select = $(obj).prev().prev().prev();
				var option = "<option value='"+condition+"' selected>"+condition+"</option>";
				select.append(option);
				$(obj).next().remove();
				$(obj).next().removeClass('hasOne');
				$(obj).prev().remove();
				$(obj).remove();
			}else{
				window.parent.message(data,8);
			}
		});
	}
	//删除筛选条件输入框
	function back(obj) {
		$(obj).next().removeClass('hasOne');
		$(obj).prev().prev().remove();
		$(obj).prev().remove();
		$(obj).remove();
	}

	Gg.Tab({i:"li con-tabk ~on",o:"div con-tabv",events:"click",num:1});

	// 上传商品图片成功回调函数
    function call_back_poster(fileurl_tmp){
        $("#show-poster").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
    }
	function call_back(fileurl_tmp){
		$("#show-img").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}
	function spec_call_back(fileurl_tmp,id){
		$('#img-'+id).attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}
	function call_back1(fileurl_tmp){
		$("#share-img").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}

    //实例化编辑器
    var ue = UE.getEditor('myeditor');

    ue.addListener('ready',function(){
        this.focus()
    });
    function getContent() {
        var arr = [];
        arr.push( "使用editor.getContent()方法可以获得编辑器的内容" );
        arr.push( "内容为：" );
        arr.push(  UE.getEditor('myeditor').getContent() );
        alert( arr.join( "\n" ) );
    }
    function hasContent() {
        var arr = [];
        arr.push( "使用editor.hasContents()方法判断编辑器里是否有内容" );
        arr.push( "判断结果为：" );
        arr.push(  UE.getEditor('myeditor').hasContents() );
        alert( arr.join( "\n" ) );
    }
	
	var info=new Array();
    function gbcount(message,maxlen,id){
		
		if(!info[id]){
			info[id]=document.getElementById(id);
		}			
        var lenE = message.value.length;
        var lenC = 0;
        var enter = message.value.match(/\r/g);
        var CJK = message.value.match(/[^\x00-\xff]/g);//计算中文
        if (CJK != null) lenC += CJK.length;
        if (enter != null) lenC -= enter.length;		
		var lenZ=lenE+lenC;		
		if(lenZ > maxlen){
			info[id].innerHTML=''+0+'';
			return false;
		}
		info[id].innerHTML=''+(maxlen-lenZ)+'';
    }
	
function set_title_color(color) {
	$('#title2').css('color',color);
	$('#title_style_color').val(color);
}
function set_title_bold(){
	if($('#title_style_bold').val()=='bold'){
		$('#title_style_bold').val('');	
		$('#title2').css('font-weight','');
	}else{
		$('#title2').css('font-weight','bold');
		$('#title_style_bold').val('bold');
	}
}

	/**
	 * 商品规格
	 */
	$(function () {
		$('#integral').change(function () {
			var intergral = $(this).val();
			var charge = $('#charge').val();
			if(intergral > charge){
				window.parent.message('积分购买金额不能大于报名费用',8);
			}
		});
		$('#fare').change(function () {
			var fare = $(this).val();
			var charge = $('#charge').val();
			if(fare > charge){
				window.parent.message('拼车费不能大于报名费用',8);
			}
		});
		/*//活动分类改变时
		$('#category').change(function () {
            var cid = $(this).val();
            //alert($(this).val())
            $.getJSON("< ?php echo WEB_PATH;?>/admin/activity/ajaxChangeAttr/",{cid:cid},function (data) {
                if (data.code == 0) {
                    $('#filter').empty();
                    $('#filter').append(data.content);
                    $('.typeId').val(data.type_id);
                }else if(data.code == 1){
                    return;
                }else{
                    window.parent.message(data,8);
                }
            });
		})*/
		//获取商品类型对象
		$('#goods_type').change(function () {
			var typeId = $(this).val();
			$.getJSON("<?php echo WEB_PATH; ?>/admin/goods_spec/ajaxGetSpecSelect/",{typeId:typeId},function(data){
				if(data.msg	 == 'ok'){
					$("#ajax_spec_data").html('')
					$("#ajax_spec_data").append(data.data);
				}else{
                    window.parent.message(data,8);
                }
			});
		});

		//根据输入的地点获取全称
		/*$('#address').keyup(function () {
			var self = $(this);
			var keyword = $(this).val();
			if(keyword == ''){
				return
			}
			var tag = '';
			//console.log(keyword);
			$('#address-tips').remove();
			$.ajax({
				type:'get',
				url:'< ?php echo G_ADMIN_PATH?>/map/ajaxGetAddress/',
				dataType:'json',
				data:{keyword:keyword},
				success:function (msg) {
					if(msg.status==0){
						console.log(msg.data)
						tag += '<ul id="address-tips" class="address-tips">';
						for(var i in msg.data){
							tag += '<li data-lat="'+msg.data[i].location.lat+'" data-lng="'+msg.data[i].location.lng+'">'+msg.data[i].title+'</li>';
						}
						tag += '</ul>';
						self.after(tag);
						$(document).on('click','#address-tips li',function(){
							var val = $(this).text();
							var latlng = $(this).data('lat')+','+$(this).data('lng');
							self.val(val);
							$(this).parent().remove();
							$('#latlng').val(latlng);
						})
					}
				}
			})
		})*/
		/*$('#address').blur(function () {
			$('#address-tips').hide();
		});
		$('#address').focus(function () {
			$('#address-tips').show();
		});*/

		//坐标拾取器
		$('.latlng').click(function (e) {
			e.stopPropagation();
			$('#mapIframe').toggle(1000)
		});

		$('div').not('#mapIframe').click(function(e){
			e.stopPropagation();
			$('#mapIframe').hide();
		})
	})

//API JS
//window.parent.api_off_on_open('open');
</script>
</body>
</html>