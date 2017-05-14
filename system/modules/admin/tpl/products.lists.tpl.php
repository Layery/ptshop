<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>后台首页</title>

<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">

<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">

<link rel="stylesheet" href="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar-blue.css" type="text/css"> 

<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar.js"></script>

<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>

<style>

body{ background-color:#fff}

tr{ text-align:center}

</style>

</head>

<body>

<div class="header lr10">

	<?php echo $this->headerment();?>

</div>

<div class="bk10"></div>

<div class="header-data lr10">

	<b>提示:</b> 根据属性为每一个商品添加响应的商品，便于前台判断是否有库存

</div>

<div class="bk10"></div>

<form action="#" method="post" name="myform">

<div class="table-list lr10">

        <table width="100%" cellspacing="0">

     	<thead>

        		<tr>

                	<th width="5%">排序</th>

                    <th width="5%">商品ID</th>

                    <th width="10%"><?php echo current($attr_name);?></th>

                    <th width="10%">价格(元)</th>

                    <th width="10%">库存</th>

                    <th width="10%">已购买/剩余数</th>

                    <th width="25%">货品图片</th>

                    <th width="15%">管理</th>

				</tr>

        </thead>

        <tbody>				

        	<?php foreach($products as $v) { ?>

            <tr>

              <td align='center'><input name='listorders[<?php echo $v['p_id']; ?>]' type='text' size='3' value='<?php echo $v['sort']; ?>' class='input-text-c'></td>

                <td><?php echo $v['goods_id'];?></td>

                <td><span  ><?php echo $v['attr_value'];?></span></td>
                <td><?php echo $v['p_price'];?></td>

                <td><?php echo $v['p_inventory'];?></td>

                <td><font color="#ff0000"><?php echo $v['p_buy_num'];?></font> / <?php echo $v['p_surplus'];?></td>

                <td><?php echo $v['p_shopimg'];?></td>


                <td class="action">

                    [<a href="<?php echo G_ADMIN_PATH; ?>/products/edit/<?php echo $v['p_id'];?>">修改</a>]

                    [<a href="<?php echo G_ADMIN_PATH; ?>/products/del/<?php echo $v['p_id'];?>">删除</a>]

                </td>

            </tr>

            <?php } ?>

        </tbody>

     </table>

    </form>

	

   <div class="btn_paixu">

  	<div style="width:80px; text-align:center;">

          <input type="button" class="button" value=" 排序 "

        onclick="myform.action='<?php echo G_MODULE_PATH; ?>/content/goods_listorder/dosubmit';myform.submit();"/>

    </div>

  </div>

    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div>

</body>

</html> 