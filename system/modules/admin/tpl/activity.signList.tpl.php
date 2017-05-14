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

<div class="header-data lr10">

<form action="#" method="post">

 活动时间: <input name="startTime" type="text" id="posttime1" class="input-text posttime"  readonly="readonly" value="<?php if(!empty($startTime))echo date('Y-m-d H:i:s',$startTime)?>"/> -

 		  <input name="endTime" type="text" id="posttime2" class="input-text posttime"  readonly="readonly" value="<?php if(!empty($endTime))echo date('Y-m-d H:i:s',$endTime)?>" />

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

<input class="button" type="submit" name="search" value="搜索">
    <span><b style="color: #D80000; margin-left: 20px;">提示：</b>下载报名信息，请点击“管理->报名列表”进行下载</span>

</form>

</div>

<div class="bk10"></div>

<form action="#" method="post" name="myform">

<div class="table-list lr10">
        <table width="100%" cellspacing="0">

     	<thead>

        		<tr>
                    <th width="5%">ID</th>
                    <th width="20%">活动主题</th>
                    <th width="5%">活动内容</th>
                    <th width="20%">活动时间</th>
                    <th width="15%">活动地点</th>
                    <th width="10%">添加时间</th>
                    <th width="5%">已报名/名额</th>
                    <th width="8%">活动咨询方式</th>
                    <th width="15%">管理</th>

				</tr>

        </thead>

        <tbody>

        	<?php foreach($act_info as $k => $v) { ?>

            <tr>

                <td><?php echo $v['act_id'];?></td>
                <td><?php echo _strcut($v['act_title'],45);?></td>
                <td><a class='show' style="cursor: pointer;" data-title="<?php echo $v['act_title']?>">查看全文</a></td>
                <td><?php echo date('Y-m-d H:i:s',$v['act_start_time']).' - '.date('Y-m-d H:i:s',$v['act_end_time'])?></td>
                <td><?php echo trim($v['act_address'])?></td>
                <td><?php echo date('Y-m-d H:i:s',$v['act_time'])?></td>
                <td><?php echo $v['act_num_signed'];?> / <font color="#ff0000"><?php echo $v['act_num_limit'];?></font></td>
                <td><?php echo $v['act_consult']?></td>
                <td class="action">

                   <!-- [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/lists/<?php /*echo $v['id'];*/?>">查看货品</a>]
                    [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/add/<?php /*echo $v['id'];*/?>">添加货品</a>]-->
                    [<a href="<?php echo G_ADMIN_PATH; ?>/activity/signListDetail/<?php echo $v['act_id'];?>"><b>报名信息</b></a>]

                    [<a href="javascript:;" onclick="del(<?php echo $v['act_id']?>)">清空</a>]

                </td>

            </tr>

            <?php } ?>

        </tbody>

     </table>


    </form>

	

   <!--<div class="btn_paixu">

  	<div style="width:80px; text-align:center;">

          <input type="button" class="button" value=" 排序 "

        onclick="myform.action='<?php /*echo G_MODULE_PATH; */?>/content/goods_listorder/dosubmit';myform.submit();"/>

    </div>

  </div>-->

    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('two','li'); ?></ul></div>

</div>

<script type="text/javascript">
    //更改状态
    function changeStatus(obj) {
        var col_name = $(obj).data('col-name');
        var act_id = $(obj).data('act-id');
        if($(obj).attr('src').indexOf("cancel1.png") > 0 )
        {
            src = $(obj).attr('src').replace(/cancel1.png/gi,'sure.png');
            var status = 1;
        }else{
            src = $(obj).attr('src').replace(/sure.png/gi,'cancel1.png');
            var status = 0;
        }
        //alert(src)
        $.getJSON("<?php echo WEB_PATH; ?>/admin/activity/ajaxActSignSet/",{col_name:col_name,act_id:act_id,status:status},function(data){
            if(data == 'ok'){
                $(obj).attr('src',src);
            }else{
                window.parent.message(data,8);
            }
        })
    }
    //删除报名信息
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/activity/delSign";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','确认要删除所有的报名信息吗？');
    }
    $(function () {
        //获取显示内容btnShow按钮并绑定相关事件
        $('.show').bind('click',function(){
            var id = $(this).parent().siblings('td').eq(0).text();
            //var title = $(this).parent().siblings('td').eq(1).text();
            var title = $(this).data('title');
            //通过Ajax从服务器端获取数据
            var data = {
                id:id,
                _:new Date().getTime()
            };
            $.get('<?php echo G_ADMIN_PATH?>/activity/ajaxGetContent',data,function(msg){
                iDialog({
                    title:title,
                    id:'DemoDialog'+id,   //DemoDialog 可以去掉
                    content:msg,
                    lock: true,
                    width:800,
                    fixed: true
                });
            });
        });
    })
</script>

</body>

</html> 