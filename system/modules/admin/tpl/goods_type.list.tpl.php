<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
 <style>
 	th{ border:0px solid #000;}
	tr{ line-height:30px;}
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
            <th align='center' width="10%">ID</th>
            <th align='center' width="20%">类型名称</th>
			<th align='center' width="20%">管理操作</th>
            </tr>
    </thead>
     <tbody>
     <?php foreach($type as $v){?>
     <tr>
         <td align='center' width="10%"><?php echo $v['id'];?></td>
         <td align='center' width="20%"><?php echo $v['name'];?></td>
         <td align='center' width="20%">
             <a href="<?php echo G_ADMIN_PATH?>/goods_type/edit/<?php echo $v['id']?>">修改</a><span class='span_fenge lr5'>|</span>
             <a href="<?php echo G_ADMIN_PATH?>/goods_type/del/<?php echo $v['id']?>">删除</a>
         </td>
     </tr>
     <?php }?>
     </tbody>
   <!--<tbody>
   	<?php /*foreach($type as $v){ */?>
       <tr>
         <td align='center'><?php /*echo $v['id']; */?></td>
         <td align='center'><?php /*echo $v['name']; */?></td>
		 <td align='center'>
         	<a href="<?php /*echo G_ADMIN_PATH; */?>/goods_type/edit/<?php /*echo v['id']; */?>">修改</a><span class='span_fenge lr5'>|</span>
            <a href="<?php /*echo G_ADMIN_PATH; */?>/goods_type/del/<?php /*echo v['id']; */?>">删除</a>
         </td>
      </tr>
     <?php /*} */?>
   </tbody>-->
 </table>
 <div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div><!--table-list end-->

</body>
</html> 
