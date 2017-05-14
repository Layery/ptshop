<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
 <style>
    th{ border:0px solid #000;}
    tr{ line-height:30px;}
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
<script type="text/javascript">
	//当页面加载完成后
	$(function(){
		//给下拉框绑定事件
		$("select").change(function(){
			//为表单绑定提交事件
			//alert('hello');
			$("form[name=searchForm]").submit();
			$('#declare_act').attr('selected','selected');
		});
	});
</script>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<form action="" method="get" name="searchForm">
<div class="search">
    <label><img src="<?php echo G_GLOBAL_STYLE?>/global/image/icon_search.gif">按公告/活动显示：</label>
    <select class="declare_act" name="declare_act">
        <option value="0" <?php if($flag=='0'){echo "selected='selected'";}else{echo '';}?>>≡请选择类型≡</option>
        <option value="a" <?php if($flag=='a'){echo "selected='selected'";}else{echo '';}?>>活动</option>
        <option value="d" <?php if($flag=='d'){echo "selected='selected'";}else{echo '';}?>>公告</option>
    </select>
</div>
</form>
<div class="bk10"></div>
<div class="table-list lr10">
<form action="" method="post" name="myform">
 <table width="100%" cellspacing="0">
    <thead>
            <tr>
            <th width="90">排序</th>
            <th align='center'>(公告/活动)标题</th>
            <th align='center'>(公告/活动)内容详情</th>
            <th align='center'>内容类型</th>
            <th align='center'>发布时间</th>
			<th align='center'>管理操作</th>
            </tr>
    </thead>
   
   <tbody>
   	<?php foreach($res as $v){ ?>
       <tr>
         <td align='center'><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['sort']; ?>' class='input-text-c'></td>
         <td align='center'><?php echo $v['title']; ?></td>
         <td align='center'><?php echo $v['content'];?></td>
         <td align='center'>
             <?php
             if($v['flag']=='a'){
                 echo '活动';
             }else{
                 echo '公告';
             }
             ?>
         </td>
         <td align='center'><?php echo date('Y-m-d H:i:s',$v['updatetime'])?></td>
		 <td align='center'>
         	<a href="<?php echo G_ADMIN_PATH; ?>/declare_activity/edit/<?php echo $v['id']; ?>">修改</a><span class='span_fenge lr5'>|</span>
            <a href="<?php echo G_ADMIN_PATH; ?>/declare_activity/del/<?php echo $v['id']; ?>">删除</a>
         </td>
      </tr>
     <?php } ?>
   </table>
    <div class="btn_paixu">
        <div style="width:80px; text-align:center;">
            <input type="button" class="button" value=" 排序 "
                   onclick="myform.action='<?php echo G_MODULE_PATH; ?>/declare_activity/listorder/dosubmit';myform.submit();"/>
        </div>
    </div>
   </form>
 <div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div><!--table-list end-->

</body>
</html> 
