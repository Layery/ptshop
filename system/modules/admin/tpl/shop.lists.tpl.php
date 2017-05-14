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

	<b>提示:</b> 根据属性为每一个商品添加响应的商品，便于前台判断是否有库存

</div>

<div class="bk10"></div>

<div class="header-data lr10">

<form action="#" method="post">

 添加时间: <input name="posttime1" type="text" id="posttime1" class="input-text posttime"  readonly="readonly" /> -  

 		  <input name="posttime2" type="text" id="posttime2" class="input-text posttime"  readonly="readonly" />

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



<select name="sotype">

<option value="title">商品标题</option>

<option value="id">商品id</option>

<option value="cateid">栏目id</option>

<option value="catename">栏目名称</option>

<option value="brandid">品牌id</option>

<option value="brandname">品牌名称</option>

</select>

<input type="text" name="sosotext" class="input-text wid100"/>

<input class="button" type="submit" name="sososubmit" value="搜索">

</form>

</div>

<div class="bk10"></div>

<form action="#" method="post" name="myform">

<div class="table-list lr10">

	<?php if($this->segment(4)=='money' || $this->segment(4)=='moneyasc'): ?>

        <table width="100%" cellspacing="0">

     	<thead>

        		<tr>

                	<th width="5%">排序</th>

                    <th width="5%">ID</th>                          

                    <th width="25%">商品标题</th>  

                    <th width="8%">所属分类</th>

                    <th width="5%">单价/元</th>

                    <th width="10%">已购买/剩余数</th>

                    <th width="10%">库存</th>

                    <th width="5%">上架</th>
                    <th width="5%">推荐</th>
                    <th width="5%">热卖</th>

                    <th width="15%">管理</th>

				</tr>

        </thead>

        <tbody>				

        	<?php foreach($shoplist as $v) { ?>

            <tr>

              <td align='center'><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['order']; ?>' class='input-text-c'></td>  

                <td><?php echo $v['id'];?></td>

                <td><span  ><?php echo _strcut($v['title'],30);?></span>

                </td>

                <td><a href="<?php echo G_ADMIN_PATH; ?>/content/goods_list/<?php echo $v['cateid']; ?>"><?php echo $this->categorys[$v['cateid']]['name']; ?></a></td>

                <td><?php echo $v['money'];?></td>

                <td><font color="#ff0000"><?php echo $v['buy_yet'];?></font> / <?php echo $v['surplus'];?></td>

                <td><?php echo $v['inventory'];?></td>

                <td>
                    <img src="<?php if($v['is_on_sale']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='sale' data-goods-id='<?php echo $v['id']?>' onclick="changeStatus(this)" />
                </td>
                <td>
                    <img src="<?php if($v['pos']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='recommend' data-goods-id="<?php echo $v['id']?>" onclick="changeStatus(this)" />
                </td>
                <td>
                    <img src="<?php if($v['renqi']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='hot' data-goods-id='<?php echo $v['id']?>' onclick="changeStatus(this)" />
                </td>

                <td class="action">

                   <!-- [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/lists/<?php /*echo $v['id'];*/?>">查看货品</a>]
                    [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/add/<?php /*echo $v['id'];*/?>">添加货品</a>]-->
                    [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_set_money/<?php echo $v['id'];?>">重置价格</a>]

                    [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_edit/<?php echo $v['id'];?>">修改</a>]

                    [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_del/<?php echo $v['id'];?>">删除</a>]

                </td>

            </tr>

            <?php } ?>

        </tbody>

     </table>

	<?php endif; ?>


	<?php if($this->segment(4)!='moneyasc' && $this->segment(4)!='money'): ?>

        <table width="100%" cellspacing="0">

     	<thead>

        		<tr>

                	<th width="5%">排序</th>

                    <th width="5%">ID</th>        

                    <th width="25%">商品标题</th>    

                    <th width="8%">所属分类</th>

                    <th width="5%">单价/元</th>

                    <th width="10%">已购买/剩余数</th>

                    <th width="10%">库存</th>

                    <th width="5%">上架</th>
                    <th width="5%">推荐</th>
                    <th width="5%">热卖</th>

                    <th width="15%">管理</th>

				</tr>

        </thead>

        <tbody>				

        	<?php foreach($shoplist as $v) { ?>

            <tr>

              <td align='center'><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['order']; ?>' class='input-text-c'></td>  

                <td><?php echo $v['id'];?></td>

                <td><span style=""><?php echo _strcut($v['title'],30);?></span></td>

                <td><a href="<?php echo G_ADMIN_PATH; ?>/content/goods_list/<?php echo $v['cateid']; ?>"><?php echo $this->categorys[$v['cateid']]['name']; ?></a></td>

                <td><?php echo $v['money'];?></td>

                <td><font color="#ff0000"><?php echo $v['buy_yet'];?></font> / <?php echo $v['surplus'];?></td>

                <td><?php echo $v['inventory'];?></td>

                <td>
                    <img src="<?php if($v['is_on_sale']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='sale' data-goods-id='<?php echo $v['id']?>' onclick="changeStatus(this)" />
                </td>
                <td>
                    <img src="<?php if($v['pos']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='recommend' data-goods-id="<?php echo $v['id']?>" onclick="changeStatus(this)" />
                </td>
                <td>
                    <img src="<?php if($v['renqi']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='hot' data-goods-id='<?php echo $v['id']?>' onclick="changeStatus(this)" />
				</td>


                <td class="action">

                <!--[<a href="<?php /*echo G_ADMIN_PATH; */?>/products/lists/<?php /*echo $v['id'];*/?>">货品列表</a>]
                [<a href="<?php /*echo G_ADMIN_PATH; */?>/products/add/<?php /*echo $v['id'];*/?>">添加货品</a>]-->
                [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_set_money/<?php echo $v['id'];?>">重置价格</a>]
                [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_edit/<?php echo $v['id'];?>">修改</a>]
                [<a href="<?php echo G_ADMIN_PATH; ?>/content/goods_del/<?php echo $v['id'];?>">删除</a>]

				</td>

            </tr>

            <?php } ?>

        </tbody>

     </table>     

    <?php endif; ?>	

    </form>

	

   <div class="btn_paixu">

  	<div style="width:80px; text-align:center;">

          <input type="button" class="button" value=" 排序 "

        onclick="myform.action='<?php echo G_MODULE_PATH; ?>/content/goods_listorder/dosubmit';myform.submit();"/>

    </div>

  </div>

    	<div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div>

<script type="text/javascript">
    function changeStatus(obj) {
        var col_name = $(obj).data('col-name');
        var goods_id = $(obj).data('goods-id');
        if($(obj).attr('src').indexOf("cancel1.png") > 0 )
        {
            src = $(obj).attr('src').replace(/cancel1.png/gi,'sure.png');
            var status = 1;
        }else{
            src = $(obj).attr('src').replace(/sure.png/gi,'cancel1.png');
            var status = 0;
        }
        //alert(src)
        $.getJSON("<?php echo WEB_PATH; ?>/admin/content/ajaxGoodsSet/",{col_name:col_name,goods_id:goods_id,status:status},function(data){
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