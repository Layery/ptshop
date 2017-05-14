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
<script type="text/javascript">
	//当页面加载完成后
	$(function(){
		//给下拉框绑定事件
		$("select").change(function(){
			//为表单绑定提交事件
			//alert('hello');
			$("form[name=searchForm]").submit();
			$('#category').attr('selected','selected');
		});
	});
</script>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>
<!--<form action="" method="get" name="searchForm">
    <div class="search">
        <label><img src="<?php /*echo G_GLOBAL_STYLE*/?>/global/image/icon_search.gif">按活动分类显示：</label>
        <select id="category" name="cateid">
            <?php /*echo $categoryshtml; */?>
        </select>
</div>
</form>-->
<div class="bk10"></div>
<div class="table-list lr10">
<form action="" method="post" name="myform">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
            <th width="90">排序</th>
            <th width="100">id</th>
            <th align='center'>属性名称</th>
            <th align='center'>属性值</th>
            <!--<th align='center'>所属活动分类</th>-->
			<th align='center'>是否显示</th>
			<th align='center'>管理操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($attributes as $v){ ?>
            <tr>
                <td align='center'><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['sort']; ?>' class='input-text-c'></td>
                <td align='center'><?php echo $v['id']; ?></td>
                <td align='center'><?php echo $v['name']; ?></td>
                <td align='center'><?php echo $v['value']?></td>
                <!--<td align='center'><?php /*if(empty($v['cate_name'])){echo '全部分类';}else{echo $v['cate_name'];}*/?></td>-->
                <td align='center'>
                    <img src="<?php if($v['is_show']){
                        echo G_GLOBAL_STYLE."/global/image/sure.png";
                    }else{
                        echo G_GLOBAL_STYLE."/global/image/cancel1.png";
                    }?>" data-col-name='is_show' data-id='<?php echo $v['id']?>' onclick="changeStatus(this)" />
                </td>
                <td align='center'>
                    <a href="<?php echo G_ADMIN_PATH; ?>/act_attr/edit/<?php echo $v['id']; ?>">修改</a><span class='span_fenge lr5'>|</span>
                    <a href="javascript:;" onclick="del(<?php echo $v['id']?>)">删除</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</form>
<div class="btn_paixu">
  	<div style="width:80px; text-align:center;">
        <input type="button" class="button" value=" 排序 " onclick="myform.action='<?php echo G_MODULE_PATH; ?>/act_attr/listorder/dosubmit';myform.submit();"/>
    </div>
</div>
<div id="pages">
    <ul>
        <li>共 <?php echo $total; ?> 条</li>
        <?php echo $page->show('one','li'); ?>
    </ul>
</div>

</div><!--table-list end-->

<script>
    //更改状态
    function changeStatus(obj) {
        var col_name = $(obj).data('col-name');
        var id = $(obj).data('id');
        //alert(id)
        if($(obj).attr('src').indexOf("cancel1.png") > 0 )
        {
            src = $(obj).attr('src').replace(/cancel1.png/gi,'sure.png');
            var status = 1;
        }else{
            src = $(obj).attr('src').replace(/sure.png/gi,'cancel1.png');
            var status = 0;
        }
        //alert(src)
        $.getJSON("<?php echo WEB_PATH; ?>/admin/act_attr/ajaxAttrSet/",{col_name:col_name,id:id,status:status},function(data){
            if(data == 'ok'){
                $(obj).attr('src',src);
            }else{
                window.parent.message(data,8);
            }
        })
    }
    //删除活动
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/act_attr/del";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','确认要删除吗？');
    }
</script>
</body>
</html> 
