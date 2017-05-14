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
    <script type="text/javascript" src="<?php echo G_GLOBAL_STYLE; ?>/global/js/ZeroClipboard-2.4.js"></script>

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
                    <th width="10%">活动分类</th>
                    <th width="5%">活动内容</th>
                    <th width="10%">活动时间</th>
                    <th width="10%">活动地点</th>
                    <th width="5%">活动费用</th>
                    <th width="5%">拼车费(元)</th>
                    <th width="5%">已报名/名额</th>
                    <th width="8%">活动咨询方式</th>
                    <th width="3%">推荐</th>
                    <th width="3%">上架</th>
                    <th width="5%">添加时间</th>
                    <th width="10%">管理</th>

				</tr>

        </thead>

        <tbody>

        	<?php foreach($act_info as $k => $v) { ?>

            <tr>

                <td><?php echo $v['act_id'];?></td>
                <td><?php echo _strcut($v['act_title'],45);?></td>
                <td><?php echo $v['c_name'];?></td>
                <td><a class='show' style="cursor: pointer;" data-title="<?php echo $v['act_title']?>">查看全文</a></td>
                <td><?php echo date('y-m-d',$v['act_start_time']).'-'.date('y-m-d',$v['act_end_time'])?></td>
                <td><?php echo trim($v['act_address'])?></td>
                <td><?php echo trim($v['act_charge'])?></td>
                <td><?php echo trim($v['act_fare'])?></td>
                <td><?php echo $v['act_num_signed'];?> / <font color="#ff0000"><?php echo $v['act_num_limit'];?></font></td>
                <td><?php echo $v['act_consult']?></td>
                <td>
                    <img src="<?php if($v['act_recommend']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='recommend' data-act-id='<?php echo $v['act_id']?>' onclick="changeStatus(this)" />
                </td>
                <td>
                    <img src="<?php if($v['act_active']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='active' data-act-id="<?php echo $v['act_id']?>" onclick="changeStatus(this)" />
                </td>

                <td><?php echo date('y-m-d',$v['act_time'])?></td>
                <td class="action">

                   <!-- [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/lists/<?php /*echo $v['id'];*/?>">查看货品</a>]
                    [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/add/<?php /*echo $v['id'];*/?>">添加货品</a>]-->
                    [<a target="_blank" href="<?php echo WEB_PATH ?>/mobile/activity/activity/<?php echo $v['act_id'];?>">查看</a>]
                    [<a href="<?php echo G_ADMIN_PATH; ?>/activity/copy/<?php echo $v['act_id'];?>">复制</a>]
                    <input type="hidden" name="link" id="url<?php echo $v['act_id'];?>" value="<?php echo WEB_PATH ?>/mobile/activity/activity/<?php echo $v['act_id'];?>">
                    [<a href="javascript:;" data-clipboard-target="url<?php echo $v['act_id'];?>"  onclick="copyText(this)">链接</a>]

                    [<a href="<?php echo G_ADMIN_PATH; ?>/activity/edit/<?php echo $v['act_id'];?>">修改</a>]

                    [<a href="javascript:;" onclick="del(<?php echo $v['act_id']?>)">删除</a>]

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
<!--data-clipboard-text="<?php /*echo WEB_PATH;*/?>/mobile/activity/activity/<?php /*echo $v['act_id'];*/?>"-->

    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div>
<script type="text/javascript">
    //复制URL到剪切板
    function copyText(obj) {
        var clip = new ZeroClipboard(obj);
        /*clip.on('aftercopy',function () {
            alert('已复制到剪切板');
        })*/
        window.parent.message('已复制到剪切板',1);
    }
    /*function copyText(obj,objName) {
        /!*var url = $('#'+objName).val();
        window.clipboardData.setData("Text",url);
        alert("复制链接成功！");*!/
        /!*document.getElementById(objName).focus();
        document.getElementById(objName).select();
        document.execCommand("copy",false,null);*!/
    }*/
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
    //删除活动
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/activity/del";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','确认要删除吗？');
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