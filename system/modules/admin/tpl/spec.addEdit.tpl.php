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
<form method="post" action="">
	<table width="100%"  cellspacing="0" cellpadding="0">
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>规格名称：</td>
			<td>
				<input  type="text" id="spec_name"  name="spec_name" value="<?php echo $goods_spec['name']?>" class="input-text bg">
			</td>
		</tr>
		<tr>
			<td align="right" style="width:120px"><font color="red">*</font>所属商品类型：</td>
			<td>
            <select id="type" name="typeId">
				<option value="0">≡ 请选择类型 ≡</option>
                <?php foreach ($goods_type as $v){ ?>
					<option value="<?php echo $v['id'];?>" <?php if($goods_spec['type_id']==$v['id'])echo "selected='selected'"?>><?php echo $v['name'];?></option>
				<?php } ?>
             </select> 
            </td>
		</tr>
		<tr>
            <td align="right" style="width:120px">规格选项录入方式：</td>
            <td>
				<?php if(empty($goods_spec)){?>
                <label><input type="radio" name="spec_input_type" value="0" checked="checked" /> 手工录入&nbsp;&nbsp;</label>
                <label><input type="radio" name="spec_input_type" value="1"/> 列表选择&nbsp;&nbsp;</label>
				<?php }else{if($goods_spec['spec_input_type']==1){?>
					<label><input type="radio" name="spec_input_type" value="1" checked="checked"/> 列表选择&nbsp;&nbsp;</label>
					<?php }else{?>
					<label><input type="radio" name="spec_input_type" value="0" checked="checked"/> 手工录入&nbsp;&nbsp;</label>
					<?php }?>
				<?php }?>
            </td>
		</tr>
		<tr style="background-color:#FFC">
			<td align="right" style="width:120px">
				<b>提示：</b>
			</td>
			<td>
				<font color="red">规格选项录入方式：手工录入，请不要填写“规格选项列表”；列表选择，请填写下面选项列表，商品多个规格选项请用逗号隔开(中/英文都可以)</font><br />
			</td>
		</tr>
        <tr>
			<td align="right" style="width:120px">规格选项列表：</td>
			<td><textarea name="spec_value" class="wid400" onKeyUp="gbcount(this,250,'textdescription');" style="height:60px"><?php if($goods_spec['spec_input_type']==1)echo $goods_spec['item'];?></textarea><br><span>还能输入<b id="textdescription">250</b>个字符</span>
            </td>
		</tr>
		<tr>
			<td align="right" style="width:120px">排序：</td>
			<td>
				<input type="text" name="sort" value="<?php echo $goods_spec['sort']|50?>" class="input-text">
			</td>
		</tr>
		<tr height="60px">
			<td align="right" style="width:120px"></td>
			<td>
				<input type="hidden" name="id" value="<?php echo $id;?>">
			    <input type="submit" name="dosubmit" class="button" value="保存规格" />&nbsp;&nbsp;
                <input type="reset" class="button" id="resetbtn" value="规格重置"/>
			</td>
		</tr>
	</table>
</form>
</div>
<script type="text/javascript">
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
    //当页面加载完成后
	$(function(){
		$('#resetbtn').bind('click',function(){
			$('textarea[name=spec_value]').val('').attr('disabled',true).css('background','#EBEBE4');
		});
        //获取规格项录入方式对象
		if($('input[name=spec_input_type]:checked').val()==1){
			$('textarea[name=spec_value]').attr('disabled',false).css('background','#fff');
		}else{
			$("textarea[name=spec_value]").attr('disabled',true).css('background','#EBEBE4');
		}
        //为录入方式绑定事件
        $('input[name=spec_input_type]').change(function(){
        if($(this).val() == 1){
        	$('textarea[name=spec_value]').attr('disabled',false).css('background','#fff');
        }else{
			$('textarea[name=spec_value]').val('').attr('disabled',true).css('background','#EBEBE4');
        }
        });
    });
</script>
</body>
</html> 