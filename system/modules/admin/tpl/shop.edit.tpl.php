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

</style>
</head>
<body>
<script>
$(function(){
	$("#category").change(function(){
	var parentId=$("#category").val(); 
	if(null!= parentId && ""!=parentId){ 
		$.getJSON("<?php echo WEB_PATH; ?>/api/brand/json_brand/"+parentId,{cid:parentId},function(myJSON){
		var options="";
		if(myJSON.length>0){ 			
			//options+='<option value="0">≡ 请选择品牌 ≡</option>'; 
			for(var i=0;i<myJSON.length;i++){ 
				options+="<option value="+myJSON[i].id+">"+myJSON[i].name+"</option>"; 
			} 
			$("#brand").html(options);		} 
		});
		/*$('#attribute-table').children().remove();
		$.getJSON("<\?php echo WEB_PATH;?>/admin/attribute/showAttr/"+parentId,function(data){
			$('#attribute-table').append(data);
		});*/
	}  
	}); 
});
//table页选择
function charea(a) {
	var spans = ['general','attribute'];
	for(i=0;i<3;i++) {
		var o = document.getElementById(spans[i]+'-tab');
		var tb = document.getElementById(spans[i]+'-table');
		o.className = o.id==a+'-tab'?'tab-front':'tab-back';
		tb.style.display = tb.id==a+'-table'?'block':'none';
	}
}
//复制新的一行
function copythis(o){
	//获取当前行
	var curr_tr = $(o).parent().parent();
	//判断a标签的内容，如果是[+],就克隆 如果是[-],就自我删除
	if($(o).html()=='[+]'){
		//完成克隆
		var new_tr = curr_tr.clone();
		//把新行里面的a标签内容变成[-]
		new_tr.find('a').html('[-]');
		//把新行放到当前和的后面
		curr_tr.after(new_tr);
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
		<li name="con-tabk">通用信息</li>
		<li name="con-tabk">商品规格</li>
	</ul>
<form method="post" action="">
	<div name='con-tabv' class="con-tabv">
	<table width="100%" id="general-table" cellspacing="0" cellpadding="0">
    	<tr style="background-color:#FFe;height:50px;">
			<td align="right" style="width:120px" style="font-weight:bold"></td>
			<td>
            	<b><?php echo $shopinfo['title']; ?></b>
				<br />
				商品价格 <b style="color:red"><?php echo $shopinfo['money']; ?></b>&nbsp;&nbsp;&nbsp;
				已购买/剩余数&nbsp; <b style="color:red"><?php echo $shopinfo['buy_yet']; ?>/<?php echo $shopinfo['surplus']; ?></b> 件&nbsp;&nbsp;&nbsp;
				库存 <b style="color:red"><?php echo $shopinfo['inventory']; ?></b>件&nbsp;&nbsp;&nbsp;
            </td>
			
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>所属分类：</td>
			<td>
             <select name="cateid" id="category" class="wid100">               
                <?php echo $categoryshtml; ?>                
             </select> 
            </td>
		</tr>
        <tr>
			<td align="right" style="width:120px"><font color="red">*</font>所属品牌：</td>
			<td>
            	<select id="brand" name="brand" class="wid100">                	
                	<option value="<?php echo $shopinfo['brandid']; ?>"><?php echo $BrandList[$shopinfo['brandid']]['name']; ?></option>                    
					<?php foreach($BrandList as $brand): ?>		
                    <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                    <?php endforeach; ?>
				</select>
			</td>
		</tr>      
        <tr>
			<td align="right" style="width:120px"><font color="red">*</font>商品标题：</td>
			<td>
            <input  type="text" id="title" value="<?php echo $shopinfo['title']; ?>" 
             name="title" onKeyUp="return gbcount(this,100,'texttitle');"  class="input-text wid400 bg">
            <span style="margin-left:10px">还能输入<b id="texttitle">100</b>个字符</span>
           
            </td>
		</tr>
        <tr>
			<td align="right" style="width:120px">关键字：</td>
			<td><input type="text" value="<?php echo $shopinfo['keywords']; ?>" name="keywords"  name="title"  class="input-text wid300" />
            <span class="lr10">多个关键字请用   ,  号分割开</span>
            </td>
		</tr>
        <tr>
			<td align="right" style="width:120px">商品描述：</td>
			<td><textarea name="description" class="wid400"  onKeyUp="gbcount(this,250,'textdescription');" style="height:60px"><?php echo $shopinfo['description']; ?></textarea><br /> <span>还能输入<b id="textdescription">250</b>个字符</span>
            </td>
		</tr>	
        <tr>      
			<td align="right" style="width:120px"><font color="red">*</font>商品价格：</td>
            <td><input type="text" name="money" value="<?php echo $shopinfo['money'];?>" class="input-text" style="width:65px; text-align:center" onKeyUp="value=value.replace(/\D/g,'')">元&nbsp;&nbsp;&nbsp;</td>
		</tr>
		<tr>
			<td align="right" style="width:120px">市场价格：</td>
			<td><input type="text" id="money" name="market_price" value="<?php echo $shopinfo['market_price'];?>" style="width:65px; text-align:center" class="input-text" onKeyUp="value=value.replace(/\D/g,'')">元</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>库存：</td>
            <td><input type="text" name="inventory" id="inventory" value="<?php echo $shopinfo['inventory'];?>" class="input-text" style="width:65px; text-align:center" onKeyUp="value=value.replace(/\D/g,'')">件&nbsp;&nbsp;&nbsp;</td>
		</tr>

		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>已购买：</td>
			<td>
				<input type="text" name="buy_yet" id="buy_yet" class="input-text" value="<?php echo $shopinfo['buy_yet']; ?>" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px; text-align:center" class="input-text"/>件
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>剩余量：</td>
			<td>
				<input type="text" name="surplus" id="surplus" class="input-text" value="<?php echo $shopinfo['surplus']; ?>" onKeyUp="value=value.replace(/\D/g,'')" style="width:65px; text-align:center" class="input-text"/>件
			</td>
		</tr>
		<script type="text/javascript">
			$(function () {
				//获取商品数量，进行算术运算
				$('#inventory').keyup(function () {
					var inventory = parseInt($(this).val());
					if(isNaN(inventory)){
						inventory = 0;
					}
					var buy_yet = parseInt($('#buy_yet').val());
					if(isNaN(buy_yet)){
						buy_yet = 0;
					}
					if(buy_yet){
						$('#surplus').val(inventory+buy_yet);
					}else{
						$('#surplus').val(inventory);
					}
				});

				$('#buy_yet').keyup(function () {
					var buy_yet = parseInt($(this).val());
					if(isNaN(buy_yet)){
						buy_yet = 0;
					}
					var inventory = parseInt($('#inventory').val());
					if(isNaN(inventory)){
						inventory = 0;
					}
					if(buy_yet > inventory){
						$(this).val(inventory);
						$('#surplus').val(0);
					}else {
						$('#surplus').val(inventory-buy_yet);
					}
				})
				$('#surplus').keyup(function () {
					var surplus = parseInt($(this).val());
					if(isNaN(surplus)){
						surplus = 0;
					}
					var inventory = parseInt($('#inventory').val());
					if(isNaN(inventory)){
						inventory = surplus;
					}
					if(surplus > inventory){
						$(this).val(inventory);
						$('#buy_yet').val(0);
					}else {
						$('#buy_yet').val(inventory-surplus);
					}
				})
			});
		</script>

        <tr>
         <td align="right" style="width:120px"><font color="red">*</font>缩略图：</td>
        <td>
			<img id="show-img" src="<?php echo G_UPLOAD_PATH.'/'.$shopinfo['thumb']; ?>" style="border:1px solid #eee; padding:1px; width:50px; height:50px;">
           	<input type="text" id="imagetext" value="<?php echo $shopinfo['thumb']; ?>" name="thumb" class="input-text wid300">
			<input type="button" class="button"
             onClick="GetUploadify('<?php echo WEB_PATH; ?>','uploadify','缩略图上传','image','shopimg',1,500000,'imagetext','call_back')"
             value="上传图片"/>
			
        </td>
      </tr>
		<tr>
			<td align="right" style="width:120px">商品属性：</td>
			<td width="900">
				<input name="goods_key[pos]" value="1" type="checkbox" <?php if($shopinfo['pos']){echo "checked";} ?>/>&nbsp;推荐&nbsp;&nbsp;
				<input name="goods_key[renqi]" value="1" type="checkbox" <?php if($shopinfo['renqi']){echo "checked";} ?>/>&nbsp;人气&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
         <td height="300" style="width:120px"  align="right"><font color="red">*</font>商品内容详情：</td>
			<td><script name="content" id="myeditor" type="text/plain"><?php echo $shopinfo['content']; ?></script>
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
                <input type="text" name="sub_text_len" class="input-text" value="250" size="3">字符至内容摘要<label>         
            	</div>                
            </td>                   
		</tr>
        <!--<tr height="60px">
			<td align="right" style="width:120px"></td>
			<td><input type="submit" name="dosubmit" class="button" value="修改商品" /></td>
		</tr>-->
	</table>
	</div>
	<div name='con-tabv' class="con-tabv">
		<table width="100%" id="attribute-table" cellspacing="0" cellpadding="0">
			<tr>
				<td align="right" style="width:120px">商品类型：</td>
				<td style="width: 993px;">
					<select name="goods_type" id="goods_type">
						<option value="0">≡ 请选择商品类型 ≡</option>
						<?php foreach($goods_type as $v){?>
							<option value="<?php echo $v['id'];?>" <?php if($shopinfo['goods_type']==$v['id']){echo "selected='selected'";}?>><?php echo $v['name'];?></option>
						<?php }?>
					</select>
				</td>
			</tr>
		</table>
		<div id="ajax_spec_data"><!-- ajax 返回规格--></div>
	</div>
	<div class="submit-btn" style="padding-left: 120px;height: 60px;">
		<input type="submit" name="dosubmit" class="button" value="修改">
		<input type="reset" class="button" value="重置">
	</div>
</form>
</div>
 <span id="title_colorpanel" style="position:absolute; left:568px; top:155px" class="colorpanel"></span>
<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE; ?>/global/js/GgTab.js"></script>
<script type="text/javascript">
	//table页切换
	Gg.Tab({i:"li con-tabk ~on",o:"div con-tabv",events:"click",num:1});
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

	/*$(function () {
		var parentId=$("#category").val();
		//alert(parentId )
		var shopid = <//?php echo $shopinfo['id'];?>;
		$('#attribute-table').children().remove();
		$.getJSON("<//?php echo WEB_PATH; ?>/admin/attribute/editAttr/"+parentId+'/'+shopid,function(data){
			$('#attribute-table').append(data);
		});
	});*/


	/**
	 * 上传商品图片成功回调函数
	 */
	function call_back(fileurl_tmp){
		$("#show-img").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}
	function spec_call_back(fileurl_tmp,id){
		$('#img-'+id).attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
	}


	/**
	 * 商品规格
	 */
	$(function () {
		//获取商品类型对象
		$('#goods_type').change(function () {
			var goods_id = <?php echo $shopinfo['id']?>;
			var typeId = $(this).val();
			$.getJSON("<?php echo WEB_PATH; ?>/admin/goods_spec/ajaxGetSpecSelect/",{typeId:typeId,goods_id:goods_id},function(data){
				if(data.msg	 == 'ok'){
					$("#ajax_spec_data").html('')
					$("#ajax_spec_data").append(data.data);
					ajaxGetSpecInput();
				}else{
					window.parent.message(data,8);
				}
			});
		});
		$("#goods_type").trigger('change');

		$(document).on('click',"#ajax_spec_data button",function() {
			if($(this).hasClass('btn-success')){
				$(this).removeClass('btn-success');
				$(this).addClass('btn-default');
			}else{
				$(this).removeClass('btn-default');
				$(this).addClass('btn-success');
			}
			ajaxGetSpecInput();
		})
	});

//API JS
//window.parent.api_off_on_open('open');
</script>
</body>
</html> 