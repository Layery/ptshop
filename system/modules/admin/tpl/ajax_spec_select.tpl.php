<?php
$str = '';
$src = G_GLOBAL_STYLE;
$up_src = G_UPLOAD_PATH;
$web_path = WEB_PATH;
foreach ($spec as $v){
    $str .= "<tr><td align='right' style='width:120px'>$v[name]：</td><td>";
    if($v['spec_input_type']==1){
        foreach ($v['spec_item'] as $k=>$vo){
            $str .= "<button type='button' data-spec-id='{$v[id]}' data-item-id='{$vo[id]}' class='btn ";
            if(in_array($vo['id'],$items_ids)){
                $str .= 'btn-success';
            }else{
                $str .= 'btn-default';
            }
            $str .= "'>$vo[item]</button>";

            $str .= "<input type='hidden' name='spec_img[{$vo[id]}]' id='spec-img-{$vo[id]}' value='{$specImageList[$vo['id']]['src']}'>";
            $str .= "<img src='";
            $spec_imge = isset($specImageList[$vo['id']]['src']) ? $up_src.'/'.$specImageList[$vo['id']]['src'] : $src.'/global/image/upload.png';
            $str .= $spec_imge;
            $str .= "' id='img-{$vo[id]}' class='spec-img img-{$vo[id]}' title='上传图片' onClick=\"GetUploadify('{$web_path}', 'uploadify', '缩略图上传', 'image', 'shopimg', 1, 500000, 'spec-img-{$vo[id]}','spec_call_back')\">&nbsp;&nbsp;&nbsp;";
        }
    }else{
        foreach ($v['spec_item'] as $vo){
            if(in_array($vo['id'],$items_ids) && $v['spec_input_type'] == 0){
                $str .= "<button type='button' data-spec-id='{$v[id]}' data-item-id='{$vo[id]}' class='btn btn-success'>$vo[item]</button>";
                $str .= "<img src='";
                $spec_imge = isset($specImageList[$vo['id']]['src']) ? $up_src.'/'.$specImageList[$vo['id']]['src'] : $src.'/global/image/upload.png';
                $str .= $spec_imge;
                $str .= "' id='img-{$vo[id]}' class='spec-img img-{$vo[id]}' title='上传图片' onClick=\"GetUploadify('{$web_path}', 'uploadify', '缩略图上传', 'image', 'shopSpecImg', 1, 500000, 'spec-img-{$vo[id]}', 'spec_call_back')\">";

                $str .= "<span tabindex='0' hidefocus='true' style='outline:0;' class='showImg' onclick='showOp(this)' onblur='hideOp(this)'>";

                $str .= "<img src='{$src}/global/image/edit.png' src1='{$src}/global/image/edit.png' src2='{$src}/global/image/sure.png' title='编辑' class='img btn-edit' data-spec-id='{$v[id]}' data-item-id='{$vo[id]}' onclick='editSpec(this)' style='display:none;'>";

                $str .= "<img src='{$src}/global/image/cancel1.png' title='删除' class='img btn-cancel' data-spec-id='{$v[id]}' data-item-id='{$vo[id]}' onclick='delSpec(this)' style='display:none;'></span>";
                $str .= "<input type='hidden' name='spec_img[{$vo[id]}]' id='spec-img-{$vo[id]}' value='{$specImageList[$vo['id']]['src']}'>";
            }
        }
        $str .= "<input type='text' name='item' class='spec-input input-text' data-spec-id='{$v[id]}' data-input-type='{$v[spec_input_type]}'>";
    }
    $str .= "</td></tr>";
}
return <<<HTML
<table class="table table-bordered" id="goods_spec_table1" width="100%">
    <tr>
        <td colspan="2"><b>商品规格：</b></td>
        <td><font>提示：商品规格是手工录入，请在下面的输入框输入后，</font></td>
    </tr>
    {$str}
</table>
<div id="goods_spec_table2"> <!--ajax 返回 规格对应的库存--> </div>

<script>
    $(function() {
        $('.spec-input').change(function() {
            var spec = $(this);
            var item = $(this).val();
            var spec_id = $(this).attr('data-spec-id');
            var spec_input_type = $(this).attr('data-input-type');
            $.getJSON("/admin/goods_spec/ajaxAddEditSpec/",{item:item,spec_id:spec_id,spec_input_type:spec_input_type},function(data) {
                if(data.msg == 'ok'){
                     var str = "<button type='button' data-spec-id='"+spec_id+"' data-item-id='"+data.item_id+"' class='btn btn-default'>"+item+"</button>";
                     str += "<img src='{$src}/global/image/upload.png' id='img-"+data.item_id+"' class='spec-img img-"+data.item_id+"' title='上传图片' onClick=\"GetUploadify('{$web_path}','uploadify','缩略图上传','image','shopSpecImg',1,500000,'spec-img-"+data.item_id+"','spec_call_back')\">";
                     str += "<span tabindex='0' hidefocus='true' style='outline:0;' class='showImg' onclick='showOp(this)' onblur='hideOp(this)'><img src='{$src}/global/image/edit.png' src1='{$src}/global/image/edit.png' src2='{$src}/global/image/sure.png' title='编辑' class='img btn-edit' data-spec-id='"+spec_id+"' data-item-id='"+data.item_id+"' onclick='editSpec(this)' style='display:none;'><img src='{$src}/global/image/cancel1.png' title='删除' class='img btn-cancel' data-spec-id='"+spec_id+"' data-item-id='"+data.item_id+"' onclick='delSpec(this)' style='display:none;'></span>";
                     str += "<input type='hidden' name='spec_img["+data.item_id+"]' id='spec-img-"+data.item_id+"' value=''>";
                     spec.before(str);
                     spec.val(''); 
                }else{
                    window.parent.message(data,8);
                }
            });
        });
    });
    function showOp(T) {
        var img = $(T).find('img');
        img.each(function() {
            $(T).removeClass('showImg');
            //$(T).css('width','20px');
            if($(this).is(':hidden')){
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }
    function hideOp(T) {
        var img = $(T).find('img');
        img.each(function() {
            $(this).hide();
        })
        if(!$(T).hasClass('showImg')){
            $(T).addClass('showImg')
        }
        
    }
    function editSpec(T) {
        var spec_id = $(T).attr('data-spec-id');
        var item_id = $(T).attr('data-item-id');
        var src = $(T).attr('src2');
        var span = $(T).parent();
        var item = span.prev().prev().html();
        var class_name = 'btn-default';
        if(span.prev().prev().hasClass('btn-success')){
            class_name = 'btn-success';
        }
        var str = "<input type='text' name='item' class='item-input input-text' data-spec-id='"+spec_id+"' data-item-id='"+item_id+"' data-class-name='"+class_name+"' value='"+item+"'>";
        span.prev().prev().remove();
        span.prev().before(str);
        showOp(span);
        $('.item-input').select();
        $(T).attr('src',src);
        $(T).attr('onclick','ajaxEditSpec(this)');
    }
    $(document).on('blur',".item-input",function() {
        var input = $(this);
        var spec_id = $(this).attr('data-spec-id');
        var item_id = $(this).attr('data-item-id');
        var class_name = $(this).attr('data-class-name');
        var item = $(this).val();
        var editImg = $(this).siblings('span').find('img').eq(0);
        var src = editImg.attr('src1');
        var span = input.next().next();
        if(item == 'undefined'){
            window.parent.message('规格项不能为空',8);
        }
        $.getJSON("/admin/goods_spec/ajaxAddEditSpec/",{item:item,spec_id:spec_id,item_id:item_id},function(data) {
            if(data.msg == 'ok'){
                 var str = "<button type='button' data-spec-id='"+spec_id+"' data-item-id='"+item_id+"' class='btn "+class_name+"'>"+item+"</button>";
                 input.before(str);
                 input.remove()
                 editImg.attr('src',src);
                 editImg.attr('onclick','editSpec(this)');
            }else{
                window.parent.message(data,8);
            }
            //showOp(span); 
            hideOp(span);
            ajaxGetSpecInput();
        })
    })
    function ajaxEditSpec(T) {
        var spec_id = $(T).attr('data-spec-id');
        var item_id = $(T).attr('data-item-id');
        var src = $(T).attr('src1');
        var item = $(T).parent().prev().prev().val();
        var span = $(T).parent();
        if(item == 'undefined'){
            window.parent.message('规格项不能为空',8);
        }
        $.getJSON("/admin/goods_spec/ajaxAddEditSpec/",{item:item,spec_id:spec_id,item_id:item_id},function(data) {
            if(data.msg == 'ok'){
                 var str = "<button type='button' data-spec-id='"+spec_id+"' data-item-id='"+item_id+"' class='btn btn-default'>"+item+"</button>";
                 span.prev().prev().remove();
                 span.prev().before(str);
                 $(T).attr('src',src);
                 $(T).attr('onclick','editSpec(this)');
            }else{
                window.parent.message(data,8);
            }
            showOp(span); 
            ajaxGetSpecInput();
        })
    }
    function delSpec(T) {
        var spec_id = $(T).attr('data-spec-id');
        var item_id = $(T).attr('data-item-id');
        if(spec_id == 0 || item_id == 0){
            window.parent.message('不存在的商品规格');
        }
        $.getJSON("/admin/goods_spec/ajaxDelSpec",{spec_id:spec_id,item_id:item_id},function(data) {
            if(data == 'ok'){
                //window.parent.message('删除成功',1);
                $(T).parent().prev().prev().remove();
                $(T).parent().prev().remove();
                $(T).parent().remove();
            }else{
                window.parent.message(data,8);
            }
            ajaxGetSpecInput();
        });
    }
    
    // 上传规格图片
    function GetUploadify3(k){        
        cur_item_id = k; //当前规格图片id 声明成全局 供后面回调函数调用
        GetUploadify(1,'','goods','call_back3');
    }
    
    
    // 上传规格图片成功回调函数
    function call_back3(fileurl_tmp){
        $("#item_img_"+cur_item_id).attr('src',fileurl_tmp); //  修改图片的路径
        $("input[name='item_img["+cur_item_id+"]']").val(fileurl_tmp); // 输入框保存一下 方便提交
    }    
    
   // 按钮切换 class
   /*$(document).on('click',"#ajax_spec_data button",function(event) {
       event.stopPropagation();
       if($(this).hasClass('btn-success')){
		  $(this).removeClass('btn-success');
		   $(this).addClass('btn-default');		   
	   }else{
		   $(this).removeClass('btn-default');
		   $(this).addClass('btn-success');		   
	   }
	   ajaxGetSpecInput();
   })*/
 /* $("#ajax_spec_data button").click(function(){
	   if($(this).hasClass('btn-success'))
	   {
		   $(this).removeClass('btn-success');
		   $(this).addClass('btn-default');		   
	   }
	   else
	   {
		   $(this).removeClass('btn-default');
		   $(this).addClass('btn-success');		   
	   }
	   ajaxGetSpecInput();	  	   	 
    });*/
	

/**
*  点击商品规格触发下面输入框显示
*/
function ajaxGetSpecInput()
{
//	  var spec_arr = {1:[1,2]};// 用户选择的规格数组 	  
//	  spec_arr[2] = [3,4]; 
	  var spec_arr = {};// 用户选择的规格数组 	  	  
	// 选中了哪些属性	  
	$("#goods_spec_table1  button").each(function(){
	    if($(this).hasClass('btn-success'))
		{
			var spec_id = $(this).data('spec-id');
			var item_id = $(this).data('item-id');
			if(!spec_arr.hasOwnProperty(spec_id))
				spec_arr[spec_id] = [];
		    spec_arr[spec_id].push(item_id);
			//console.log(spec_arr);
		}		
	});
		ajaxGetSpecInput2(spec_arr); // 显示下面的输入框
	
}
	
	
/**
* 根据用户选择的不同规格选项 
* 返回 不同的输入框选项
*/
function ajaxGetSpecInput2(spec_arr)
{		
    var goods_id = {$goods_id};
    var goods_type = $("#goods_type").val();
	$.ajax({
			type:'POST',
			data:{'spec_arr':spec_arr,'goods_type':goods_type,goods_id:goods_id},
			url:"/admin/goods_spec/ajaxGetSpecInput",
			success:function(data){
			       //alert(data)
				   $("#goods_spec_table2").html('')
				   $("#goods_spec_table2").append(data);
				   hbdyg();  // 合并单元格
			}
	});
}
	
 // 合并单元格
 function hbdyg() {
            var tab = document.getElementById("spec_input_tab"); //要合并的tableID
            var maxCol = 2, val, count, start;  //maxCol：合并单元格作用到多少列  
            if (tab != null) {
                for (var col = maxCol - 1; col >= 0; col--) {
                    count = 1;
                    val = "";
                    for (var i = 0; i < tab.rows.length; i++) {
                        if (val == tab.rows[i].cells[col].innerHTML) {
                            count++;
                        } else {
                            if (count > 1) { //合并
                                start = i - count;
                                tab.rows[start].cells[col].rowSpan = count;
                                for (var j = start + 1; j < i; j++) {
                                    tab.rows[j].cells[col].style.display = "none";
                                }
                                count = 1;
                            }
                            val = tab.rows[i].cells[col].innerHTML;
                        }
                    }
                    if (count > 1) { //合并，最后几行相同的情况下
                        start = i - count;
                        tab.rows[start].cells[col].rowSpan = count;
                        for (var j = start + 1; j < i; j++) {
                            tab.rows[j].cells[col].style.display = "none";
                        }
                    }
                }
            }
        }
</script>
HTML;
