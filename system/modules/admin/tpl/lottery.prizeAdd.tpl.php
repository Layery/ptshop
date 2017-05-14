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
	.act-charge{
		margin-bottom:6px;
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
	//判读img标签的src是否是add，如果是，就克隆，不是就删除
	if($(o).attr('src').indexOf("asc.png") > 0){
		//克隆当前行
		var new_tr = curr_tr.clone();
		var src = $(o).attr('src').replace(/asc.png/gi,'desc.png');
		new_tr.find('img').attr('src',src); //更改图标
		new_tr.find('img').attr('title','删除费用项'); //更改title
		//把当前行的内容清空
		new_tr.find('input').val('');
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
			<td align="right" style="width:120px"><font color="red">*</font>奖项名称：</td>
			<td>
            <input  type="text" id="title"  name="title" onKeyUp="return gbcount(this,100,'texttitle');"  class="input-text wid400 bg" placeholder="奖项名称，比如：一等奖、二等奖、三等奖...">

            <span style="margin-left:10px">还能输入<b id="texttitle">100</b>个字符</span>
           
            </td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>奖池倒计时：</td>
			<td>
				<input type="text" id="countdown" class="input-text" style="width:65px;padding-left:0px;text-align:center" name="countdown" />
				<span class="lr10">倒计时时间单位是分钟</span>
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>每次抽奖人数：</td>
			<td><input type="text" name="hit_num" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px;padding-left:0px;text-align:center" class="input-text" >（位/每次）　<span class="lr10">每次抽奖人数</span></td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>抽奖总人数：</td>
			<td><input type="text" name="award_num" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px;padding-left:0px;text-align:center" class="input-text" >位　<span class="lr10">中奖总人数</span></td>
		</tr>
		<tr>
			<td align="right" style="width:120px">奖品名称：</td>
			<td>
				<input type="text" name="prize_item" id="prize_item" class="input-text wid300" placeholder="奖品名称" />
				<span class="lr10">可以为空，现场宣布奖品</span>
			</td>
		</tr>
	</table>
	</div>
	<div class="submit-btn" style="background:#f6f6f6;margin-top: 10px;padding: 5px 140px;">
		<input type="submit" name="dosubmit" class="button" value="添加">
		<input type="reset" class="button" value="重置">
	</div>
</form>
</div>
 <span id="title_colorpanel" style="position:absolute; left:568px; top:155px" class="colorpanel"></span>
<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE; ?>/global/js/GgTab.js"></script>
<script type="text/javascript">
	Gg.Tab({i:"li con-tabk ~on",o:"div con-tabv",events:"click",num:1});

	// 上传商品图片成功回调函数
	function call_back(fileurl_tmp){
		$("#show-img").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}
	function spec_call_back(fileurl_tmp,id){
		$('#img-'+id).attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
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

//API JS
//window.parent.api_off_on_open('open');
</script>
</body>
</html>
