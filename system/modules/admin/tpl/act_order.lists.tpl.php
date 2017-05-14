<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE;?>/global/js/jquery.min.js"></script>

<link rel="stylesheet" href="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar-blue.css" type="text/css">
<script type="text/javascript" charset="utf-8" src="<?php echo G_PLUGIN_PATH; ?>/calendar/calendar.js"></script>

<style>
tbody tr{ line-height:30px; height:30px;}
.order{
    display: inline-block;
}
</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
	<span class="lr10"> </span>
    <span class="lr10"> </span>
</div>
<div class="bk10"></div>

<div class="header-data lr10">
    <form action="" method="post">
        订单编号：<input type="text" name="order_sn" id="order_sn" class="input-text" value="<?php echo $order_sn?>">
        下单时间：<input name="startTime" type="text" id="posttime1" class="input-text posttime"  readonly="readonly" value="<?php if($startTime)echo date("Y-m-d H:i:s",$startTime);?>"/> -
        <input name="endTime" type="text" id="posttime2" class="input-text posttime"  readonly="readonly" value="<?php if($endTime)echo date("Y-m-d H:i:s",$endTime);?>"/>
        <script type="text/javascript">
            date = new Date();
            Calendar.setup({
                inputField     :    "posttime1",
                ifFormat       :    "%Y-%m-%d %H:%M:%S",
                showsTime      :    true,
                timeFormat     :    "24"
            });
            Calendar.setup({
                inputField     :    "posttime2",
                ifFormat       :    "%Y-%m-%d %H:%M:%S",
                showsTime      :    true,
                timeFormat     :    "24"
            });

        </script>&nbsp;
        订单状态：
        <select name="order_status" id="order_status">
            <option value="0">请选择</option>
            <option value="未支付" <?php if($order_status == '未支付'){echo 'selected';}?>>未支付</option>
            <option value="已支付" <?php if($order_status == '已支付'){echo 'selected';}?>>已支付</option>
            <option value="已结束" <?php if($order_status == '已结束'){echo 'selected';}?>>已结束</option>
            <option value="已关闭" <?php if($order_status == '已关闭'){echo 'selected';}?>>已关闭</option>
        </select>&nbsp;&nbsp;
        退款状态：
        <select name="refund_status" id="refund_status">
            <option value="-1">请选择</option>
            <option value="0" <?php if($refund_status == 0){echo 'selected';}?>>未退款</option>
            <option value="1" <?php if($refund_status == 1){echo 'selected';}?>>审核中 </option>
            <option value="2" <?php if($refund_status == 2){echo 'selected';}?>>退款中</option>
            <option value="3" <?php if($refund_status == 3){echo 'selected';}?>>已退款</option>
        </select>&nbsp;&nbsp;
        <input type="submit" name="search" class="button" id="export_order" value="搜索">
        </form>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
  <table width="100%" cellspacing="0">
    <thead>
		<tr>
        	<th align="center">订单号</th>
            <th align="center">报名用户</th>
            <th align="center">活动主题</th>
            <th align="center">总金额</th>
            <th align="center">应付金额</th>
            <th align="center">积分抵现</th>
            <th align="center">支付方式</th>
            <th align="center">订单状态</th>
            <th align="center">退款状态</th>
            <th align="center">下单时间</th>
            <th align="center">管理</th>
		</tr>
    </thead>
    <tbody>
    <?php foreach ($orderInfo as $v){?>
        <tr>
            <td align="center"><?php echo $v['o_code']?></td>
            <td align="center"><?php echo $v['o_username']?></td>
            <td align="center"><?php echo _strcut($v['o_act_title'],0,25)?></td>
            <td align="center"><?php echo $v['o_money']?></td>
            <td align="center"><?php echo $v['o_payment']?></td>
            <td align="center"><?php echo $v['o_discount']?></td>
            <td align="center"><?php echo $v['o_pay_type']?></td>
            <td align="center"><?php echo $v['o_status']?></td>
            <td align="center"><?php echo $v['refund_status']?></td>
            <td align="center"><?php echo date("Y-m-d H:i:s",$v['o_time']);?></td>
            <td align="center">
                [<a href="<?php echo G_MODULE_PATH;?>/act_order/detail/<?php echo $v['o_id']; ?>">详细</a>]
                [<a href="javascript:;" onclick="del(<?php echo $v['o_id']?>)">删除</a>]
            </td>
        </tr>
    <?php }?>
  	</tbody>
</table>
<div class="btn_paixu"></div>
<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>
</div><!--table-list end-->

<script>
    //删除活动
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/act_order/del";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','您确定要删除订单吗？删除后不可恢复！');
    }
</script>
</body>
</html> 