<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>

<div class="bk10"></div>
<div class="header-data lr10">
    <b>提示:</b> 作为欢迎页的筛选条件，请置为勾，排序会觉得欢迎的位置，排序值越小排在前面；筛选条件尽量是偶数个，页面排版会比较好看
    <br>
    <b>提示:</b> 添加或修改是左区间一定要小于右区间，要大于某个价格时请不用填写；如（100，∞）
</div>
<div class="bk10"></div>

<div class="table-list lr10">
<form action="" method="post" name="myform">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
            <th width="10%">排序</th>
            <th width="15%">左区间</th>
            <th width="15%">右区间</th>
            <th width="20%">区间名称</th>
            <th width="10%">欢迎页</th>
            <th width="20%">管理操作</th>
        </tr>
        </thead>
        <tbody id="trlist">
        <?php foreach($interval as $key=>$v){ ?>
        <tr>
            <td width="10%" align='center' data-name="sort"><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['sort']; ?>' class='input-text-c'></td>
            <td width="15%" align="center" data-name="open_interval"><?php echo $v['open_interval']; ?></td>
            <td width="15%" align="center" data-name="close_interval"><?php if($v['close_interval']==0){echo '∞';}else{echo $v['close_interval'];} ?></td>
            <td width="20%" align="center" data-name="interval_name"><?php echo $v['interval_name']; ?></td>
            <td width="10%" align="center">
                <?php if($v['showtop']){?>
                    <span class="showtop" onclick="change(this,<?php echo $v['id'];?>,'cancel')"></span>
                <?php }else{?>
                    <span class="cancel" onclick="change(this,<?php echo $v['id'];?>,'show')"></span>
                <?php }?>
            </td>
            <td width="20%" align="center">
                <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                <a href="javascript:void(0)" onclick="update(this)">修改</a>
                <span class="lr10">|</span>
                <a href="javascript:void(0)" onclick="removes(this)">删除</a>
            </td>
        </tr>
        <?php } ?>
	    </tbody>
    </table>
</form>
</div><!--table-list end-->
<div class="bk10"></div>
<div class="header lr10">
    <input type="button" class="button" style=" margin-top:8px;" value=" 排序 " onclick="myform.action='<?php echo G_MODULE_PATH; ?>/interval/listorder/dosubmit';myform.submit();"/>
	<input type="button" class="button" style=" margin-top:8px; margin-left:20px;" onClick="add_band()" name="install" value=" 添加价格区间 " />
    <!--<input type="text" name="showNum" onKeyUp="value=value.replace(/\D/g,'')" style="margin-top:8px; margin-left:20px;" size='3' class='input-text-c'>-->
</div>

<script>
    var gid;

function input_to_string(obj,A,t){
	if(t == 'string'){
		obj.each(function(i){
		    if(i < 4){
                if ($(this).attr('name')=='sort' || $(this).attr('name')=='listorders['+gid+']'){
                    $(this).css({'border-left':'1px solid #989898','border-top':'1px solid #989898','border-right':'1px solid #e6e6e6','border-bottom':'1px solid #e6e6e6'});
                    return;
                }else {
                    if($(this).val() == ''){
                        $(this).parent().text('∞');
                    }else{
                        $(this).parent().text($(this).val());
                    }
                }
            }
		});
		$(A).text("修改");
        $(A).attr("onclick","update(this)");
        //$(A).next().next().remove();
        //$(A).next().remove();
	}
	
	/************************************************************/
	
	if(t == 'input'){
		
		var tds = obj.find("td");
		var upkey = $(tds[5]).find("input[name='id']").val();
        //alert(upkey);
        gid = upkey;
		
		tds.each(function(i){
			if(i < 4){
			    var name = $(this).attr('data-name');
                if(name == 'sort'){
                    return;
                }else{
                    $(this).html('<input class="input-text" type="text" name="'+name+'" style="width:70%" value="'+$(this).text()+'">');
                }
            }
		});
		$(A).text("确定");
		$(A).attr("onclick","install(this,'"+upkey+"')");
		$(A).after("<span id='cancel' class='lr10'>|</span><a href='javascript:;' onclick='cancel(this)'>取消</a>");
	}
}

function cancel(T) {
    var tds = $($(T).parent().parent()).find('td');
    tds.each(function (i) {
        var input = $(this).find('input');
        if(i < 4){
            var name = $(this).attr('data-name');
            if(name == 'sort'){
                return;
            }else{
                $(this).html(input.val());
            }
        }
    });
    $(T).prev().prev().text('修改');
    $(T).prev().prev().attr('onclick','update(this)');
    $(T).prev().remove();
    $(T).remove();
}


function update(T){
	var tr = $($(T).parent().parent());
			 input_to_string(tr,T,'input');	
}
function install(T,y){
	var domain = '';
	var module = '';
	var action = '';
	var func   = '';
	var values = new Array();
    var regex  = /\S/ ;
    var regexNum = /^[0-9]*$/;
	var ret    = false;	
	
	var tr = $($(T).parent().parent());
	var input = tr.find("input");
	
	input.each(function(i){
		if($(this).val() != ''){
            ret = regex.test($(this).val());
        }
		if(!ret){
			window.parent.message("左区间，右区间或区间名称不能为空！");
			$(this).css("border","1px solid #ff0000");
			return;
		}
		$(this).css("border","1px solid #0c0");
		values[i] =  $(this).val();
    });
	
	
	var submit_name = '';
    var id = 0;
	if(y != '' && y != null){
        var retNum = regexNum.test(y);
	    if(retNum){
            id = y;
        }else{
            y = y;
        }

	}else{
		y = 'install';
	}
	//alert(y);
	if(ret){
		$.post("<?php echo G_MODULE_PATH; ?>/interval/lists/",{'id':id,'sort':values[0],'open_interval':values[1],'close_interval':values[2],'interval_name':values[3],'dosubmit':y},function(data){
		    var msgarr = eval('('+data+')');
            if(msgarr.msg == 'ok'){
                if(id==0){
                    var str = "<input type='hidden' name='id' value='"+msgarr.insert_id+"'>";
                    $(T).before(str);
                    $(T).parent().prev().find('span').attr('onclick',"change(this,"+msgarr.insert_id+",\'show\')");
                    window.parent.message('添加成功',1);
                }else {
                    $(T).next().next().remove();
                    $(T).next().remove();
                    window.parent.message('修改成功',1);
                }
                input_to_string(input,T,'string');
            }else{
                window.parent.message(data,8);
            }
		});
	}
	
}

function removes(T){
	var tr = $(T).parent().parent();
    var id = $(tr.find("td")[5]).find("input[name='id']").val();
    //alert(id);
    if(typeof id == 'undefined'){
        tr.remove();
        return;
    }
	$.post("<?php echo G_MODULE_PATH; ?>/interval/lists/",{'id':id,'dosubmit':'del'},function(data){
				if(data == 'ok'){
					window.parent.message("删除成功！",1);
					tr.remove();
				}else{
					window.parent.message(data,8);
				}
	});
}

function add_band(){
	if(!this.n){
		this.n = 0;
	}
	this.n++;
	var html = '';
		html+='<tr>';
		html+='<td width="10%" align="center" data-name="sort"><input type="text" name="sort" size="3" value="50" class="input-text-c"></td>';
		html+='<td width="15%" align="center" data-name="open_interval"><input class="input-text" name="open_interval" style="width:70%" value="" type="text" placeholder="输入左区间"></td>';
		html+='<td width="15%" align="center" data-name="close_interval"><input class="input-text" name="close_interval" style="width:70%" value="" type="text" placeholder="输入右区间"></td>';
		html+='<td width="20%" align="center" data-name="interval_name"><input class="input-text" name="interval_name" style="width:70%" value="" type="text" placeholder="区间名称"></td>';
        html+='<td width="10%" align="center"><span class="cancel" onclick="change(this,\'\',\'show\')"></span></td>';
		html+='<td width="20%" align="center">';
		html+='<a href="javascript:void(0)" onclick="install(this)">添加</a>';
		html+='<span class="lr10">|</span>';
		html+='<a href="javascript:void(0)" onclick="removes(this)">删除</a>';
		html+='</td>';
		html+='</tr>';
	
	$("#trlist").append(html);
}
function change(obj,id,flag) {
    if(id == ''){
        window.parent.message('先添加再置欢迎页',3);
        return;
    }
    $.post("<?php echo G_MODULE_PATH; ?>/interval/setWelcome",{'id':id,'flag':flag},function(data){
        if(data == 'ok'){
            window.parent.message("设置成功",1);
            switch (flag){
                case 'show':
                    $(obj).attr('class','showtop');
                    $(obj).attr('onclick',"change(this,"+id+",'cancel')");
                    break;
                case 'cancel':
                    $(obj).attr('class','cancel');
                    $(obj).attr('onclick',"change(this,"+id+",'show')");
            }
        }else{
            window.parent.message(data,8);
        }
    });
}
</script>
</body>
</html> 