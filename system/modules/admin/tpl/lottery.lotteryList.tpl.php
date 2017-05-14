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
<script type="text/javascript" src="<?php echo G_PLUGIN_PATH; ?>/idialog/jquery.iDialog.min.js"  dialog-theme="default"></script>

<style>

body{ background-color:#fff}

tr{ text-align:center}

img{
    width: 21px;
    height: 21px;
    cursor: pointer;
}
img:hover{
    transform: scale(1.2);
    -webkit-transform: scale(1.2);
    -moz-transform: scale(1.2);
    -o-transform: scale(1.2);
    -ms-transform: scale(1.2);
}

</style>

</head>

<body>

<div class="header lr10">

	<?php echo $this->headerment();?>

</div>

<div class="bk10"></div>

<form action="#" method="post" name="myform">

<div class="table-list lr10">
        <table width="100%" cellspacing="0">

     	<thead>

        		<tr>
                    <th width="5%">中奖ID</th>
                    <th width="20%">中奖人昵称</th>
                    <th width="10%">中奖人头像</th>
                    <th width="10%">奖项名称</th>
                    <th width="15%">管理</th>

				</tr>

        </thead>

        <tbody>

        	<?php foreach($lottery_temp as $k => $v) { ?>

            <tr>

                <td><?php echo $v['l_uid'];?></td>
                <td><?php echo $v['l_username']?></td>
                <td><img class="lottery_img" src="<?php echo $v['l_userheader']?>" alt=""></td>
                <td><?php echo $v['prize_name']?></td>
                <td class="action">

                    [<a href="javascript:;" onclick="del(<?php echo $v['l_id']?>)">删除</a>]

                </td>

            </tr>

            <?php } ?>

        </tbody>

     </table>


    </form>

	

   <!--<div class="btn_paixu">

  	<div style="width:80px; text-align:center; position:absolute; right: 50px;">
          <input type="button" class="button" value="初始化配置信息" onclick="init()"/>
    </div>

  </div>-->

    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div>

<script type="text/javascript">
    //更改状态
    function changeStatus(obj) {
        var col_name = $(obj).data('col-name');
        var prize_id = $(obj).data('prize-id');
        if($(obj).attr('src').indexOf("cancel1.png") > 0 )
        {
            src = $(obj).attr('src').replace(/cancel1.png/gi,'sure.png');
            var status = 1;
        }else{
            src = $(obj).attr('src').replace(/sure.png/gi,'cancel1.png');
            var status = 0;
        }
        //alert(col_name)
        $.getJSON("<?php echo WEB_PATH; ?>/admin/lottery/ajaxLotteryStatus/",{col_name:col_name,prize_id:prize_id,status:status},function(data){
            if(data == 'ok'){
                $(obj).attr('src',src);
            }else{
                window.parent.message(data,8);
            }
        })
    }
    //删除活动
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/lottery/lotteryDel";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','确认要删除吗？');
    }
    //初始化奖项信息
    function init() {
        $.ajax({
            type:'get',
            url:'<?php echo G_MODULE_PATH;?>/lottery/initPrizeState',
            dataType:'json',
            success:function (msg) {
                if(msg.state == 0){
                    location.reload();
                }else{
                    window.parent.message(msg.msg,8);
                }
            }
        })
    }
</script>

</body>

</html> 