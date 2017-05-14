<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<style>
iframe{ font-size:36px;}
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
	margin-top:-1px; padding-top:30px;
	background-color:#fff; position:relative; z-index:1;}

#tab-i li.on{ background-color:#fff;color:#444; font-weight:bold; border-bottom:1px solid #fff;  position:relative;z-index:2;}

table th{ border-bottom:1px solid #eee; font-size:12px; font-weight:100; text-align:right; width:200px;}
table td{ padding-left:10px;}
.con-tabv tr{ text-align:left}
input.button{ display:inline-block}
</style>
<div class="bk10"></div>
<div class="table-list con-tab lr10" id="con-tab">
	<ul id="tab-i">
        <li name="con-tabk">基本选项</li>
    </ul>
    <div name='con-tabv' class="con-tabv">
    <form action="" id="form" method="post" enctype="multipart/form-data">
    <table width="100%" class="table_form">
        <tbody>
        <tr>
            <th>分类名称：</th>
            <td>
                <input type="text" name="name" value="<?php echo $category['c_name']?>" class="input-text wid140" onKeyUp="value=value.replace(/[^a-zA-Z0-9\u4E00-\u9FA5\_]/g,'')">
                <span><font color="#0c0">※ </font>请输入分类名称</span>
            </td>
        </tr>
        <tr>
            <th width="200">上级分类：</th>
            <td>
                <select name="parentid" class="wid150">
                    <option value="0">≡ 顶级分类 ≡</option>
                    <?php echo $categoryshtml; ?>
                </select>
            </td>
        </tr>
       <tr>
            <th>分类图片：</th>
            <td>
                <img id="show-img" src="<?php echo G_UPLOAD_PATH.'/'.$category['c_icon'] ?>" style="border:1px solid #eee; padding:1px; width:50px; height:50px;">
                <input type="text" id="imagetext" name="act_icon" value="<?php echo $category['c_icon']?>" class="input-text wid300">
                <input type="button" class="button"
             onClick="GetUploadify('<?php echo WEB_PATH; ?>','uploadify','分类图标上传','image','act_icon',1,500000,'imagetext','call_back')"
             value="上传图片"/>
                <span><font color="#0c0">※ </font>若首页需要显示分类，需上传图标；反之，无需上传；图片大小最好 <font color="D80000">64×64</font></span>
            </td>
        </tr>
        <tr>
            <th>是否显示：</th>
            <td>
                <?php
                    if($category['c_is_show']){?>
                <input type="radio" name="is_show" id="show" value="1" checked><label for="show"> 是　</label>
                <input type="radio" name="is_show" id="hide" value="0" ><label for="hide"> 否</label>
                <span style="margin: 77px;"><font color="#0c0">※ </font>首页显示</span>

                <?php }else{?>

                <input type="radio" name="is_show" id="show" value="1"><label for="show"> 是　</label>
                <input type="radio" name="is_show" id="hide" value="0" checked><label for="hide"> 否</label>
                <span style="margin: 77px;"><font color="#0c0">※ </font>首页显示</span>
                <?php }?>
            </td>
        </tr>
        <tr>
            <th>排序：</th>
            <td>
                <input type="text" name="sort" class="input-text wid140" value="<?php echo $category['c_sort']?>" onKeyUp="value=value.replace(/\D/g,'')">
                <span><font color="#0c0">※ </font>值越大排列越前</span>
            </td>
        </tr>
        </tbody>
    </table>
  </div>
</div>
<!--table-list end-->

   <div class="table-button lr10">
       <input type="hidden" name="c_id" value="<?php echo $category['c_id']?>">
       <input type="button" value=" 提交 " onClick="checkform();" class="button">
       </form>
   </div>
<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE; ?>/global/js/GgTab.js"></script>
<script type="text/javascript">
Gg.Tab({i:"li con-tabk ~on",o:"div con-tabv",events:"click",num:1});
// 上传商品图片成功回调函数
function call_back(fileurl_tmp){
    $("#show-img").attr('src','<?php echo G_UPLOAD_PATH;?>/'+fileurl_tmp);
}

function upImage(){
	return document.getElementById('imgfield').click();
}
function checkform(){
	var form=document.getElementById('form');
	var error=null;
	if(form.elements[0].value==''){error='请输入分类名称!';}
	//if(form.elements[3].value==''){error='请输入英文目录名称!';}
	if(error!=null){window.parent.message(error,8,2);return false;}
	form.submit();	
}
</script>
</body>
</html> 