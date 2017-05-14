<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<style>
    th{ border:0px solid #000;}
    img{
        width: 22px;
        height: 22px;
        cursor: pointer;
    }
    img:hover {
        transform: scale(1.2);
        -webkit-transform: scale(1.2);
        -moz-transform: scale(1.2);
        -o-transform: scale(1.2);
        -ms-transform: scale(1.2);
    }
    .icon:hover{
        transform: scale(2);
        -webkit-transform: scale(2);
        -moz-transform: scale(2);
        -o-transform: scale(2);
        -ms-transform: scale(2);
    }
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>

<div class="bk10"></div>
<div class="table-list lr10">
 <table width="100%" cellspacing="0">
    <thead>
            <tr>
            <th width="10%">排序</th>
            <th width="10%">id</th>
            <th width="20%" align='center'>分类名称</th>
            <th width="10%" align='center'>分类图标</th>
            <th width="10%" align='center'>是否显示</th>
			<th width="10%" align='center'>管理操作</th>
            </tr>
    </thead>
    <tbody>
    	  <form action="#" method="post" name="myform">
          <?php echo $html; ?>
          </form>
    </tbody>
  </table>
  <div class="btn_paixu">
  	<div style="width:80px; text-align:center;">
        <input type="button" class="button" value=" 排序 "
        onclick="myform.action='<?php echo G_MODULE_PATH; ?>/activity/listorder/dosubmit';myform.submit();"/>
    </div>
  </div>
</div><!--table-list end-->
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script>
    //更改状态
    function changeStatus(obj) {
        var col_name = $(obj).data('col-name');
        var id = $(obj).data('id');
        if($(obj).attr('src').indexOf("cancel1.png") > 0 )
        {
            src = $(obj).attr('src').replace(/cancel1.png/gi,'sure.png');
            var status = 1;
        }else{
            src = $(obj).attr('src').replace(/sure.png/gi,'cancel1.png');
            var status = 0;
        }
        //alert(src)
        $.getJSON("<?php echo WEB_PATH; ?>/admin/activity/ajaxCateSateSet/",{col_name:col_name,id:id,status:status},function(data){
            if(data == 'ok'){
                $(obj).attr('src',src);
            }else{
                window.parent.message(data,8);
            }
        })
    }
</script>
</body>
</html> 