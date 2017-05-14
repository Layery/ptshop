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
    <form action="" method="post" style="display:inline-block; ">
	<select name="paixu" id="selected">
    	<option value="time1"> 按购买时间倒序 </option>
        <option value="time2"> 按购买时间正序 </option>
		<option value="num1"> 按购买次数倒序 </option>
        <option value="num2"> 按购买次数正序 </option>
        <option value="money1"> 按购买总价倒序 </option>
        <option value="money2"> 按购买总价正序 </option>
	</select>
	<input type ="submit" value=" 排序 " name="paixu_submit" class="button"/>
    </form>
</div>
<div class="bk10"></div>

<div class="header-data lr10">
    <form action="<?php echo G_ADMIN_PATH?>/dingdan/export_order" method="post">
        下单时间: <input name="startTime" type="text" id="posttime1" class="input-text posttime"  readonly="readonly" /> -
        <input name="endTime" type="text" id="posttime2" class="input-text posttime"  readonly="readonly" />
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

        </script>
        订单状态：
        <select name="order_status" id="export">
            <option value="0" <?php echo $haspay?>>已付款</option>
            <option value="nopay" <?php echo $nopay?>>未付款</option>
            <option value="notsend" <?php echo $notsend?>>未发货</option>
            <option value="sendok" <?php echo $sendok?>>已发货</option>
            <option value="ok" <?php echo $ok?>>已完成</option>
            <option value="del" <?php echo $del?>>已关闭</option>
        </select>
        <input type="submit" class="button" id="export_order" value="导出excel">
        <span>提示：根据筛选条件进行导出</span>
        </form>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
  <table width="100%" cellspacing="0">
    <thead>
		<tr>
        	<th align="center">订单号</th>
            <th align="center">商品标题</th>
			<th align="center">用户ID</th>
            <th align="center">购买用户</th>
            <th align="center">购买数量</th>
            <th align="center">购买总价</th>
            <th align="center">购买日期</th>
            <th align="center">订单状态</th>
            <th align="center">管理</th>
		</tr>
    </thead>
    <tbody>
		<?php foreach($recordlist AS $v) {	?>		
            <tr>
                <td align="center"><?php echo $v['code'];?> <?php if($v['code_tmp'])echo " <font color='#ff0000'>[多]</font>"; ?></td>
                <td align="center">
                <a  target="_blank" href="<?php echo WEB_PATH.'/goods/'.$v['shopid']; ?>">
                <?php echo _strcut($v['shopname'],0,25);?></a>
                </td>  
                 <td align="center"><?php echo $v['uid']; ?></td>				
                 <td align="center"><?php echo $v['username']; ?></td>
                <td align="center"><?php echo $v['gonumber']; ?></td>
                <td align="center">￥<?php echo $v['moneycount']; ?>元</td>
                <td align="center"><?php echo date("Y-m-d H:i:s",$v['time']);?></td>
                <td align="center"><?php echo $v['status'];?></td>
                <td align="center">
                    [<a href="<?php echo G_MODULE_PATH;?>/dingdan/get_dingdan/<?php echo $v['id']; ?>">详细</a>]
                    [<a href="javascript:;" onclick="del(<?php echo $v['id']?>)">删除</a>]
                </td>
            </tr>
            <?php } ?>
  	</tbody>
</table>
<div class="btn_paixu"></div>
<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>
</div><!--table-list end-->

<script>
    $(function () {
        /*$('#export_order').click(function () {
            var startTime = $('#posttime1').val();
            var endTime = $('#posttime2').val();
            var order_status = $('#export').val();
            if(startTime == ''){
                alert('请选择下单时间');
                return;
            }
            if(order_status == ''){
                alert('请选择订单状态');
                return;
            }
            var data = {
                startTime:startTime,
                endTime:endTime,
                order_status:order_status
            }
            $.ajax({
                type:'POST',
                url:'php echo G_ADMIN_PATH/dingdan/export_order',
                data:data,
                success:function (msg) {
                    
                }
            })
        })*/
    })
    //删除活动
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/dingdan/del";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','您确定要删除订单吗？删除后不可恢复！');
    }
</script>
</body>
</html> 