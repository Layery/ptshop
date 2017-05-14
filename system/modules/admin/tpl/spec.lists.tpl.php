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
<form action="" method="get" name="searchForm">
<div class="search">
    <label><img src="<?php echo G_GLOBAL_STYLE?>/global/image/icon_search.gif">按商品类型显示：</label>
    <select id="type" name="typeId">
        <option value="0">≡ 请选择类型 ≡</option>
        <?php foreach ($goods_type as $v){?>
        <option value="<?php echo $v['id'];?>" <?php if($typeId==$v['id'])echo "selected='selected'";?>><?php echo $v['name'];?></option>
        <?php }?>
    </select> 
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<form action="" method="post" name="myform">
 <table width="100%" cellspacing="0">
    <thead>
            <tr>
            <th width="90">排序</th>
            <th width="100">id</th>
            <th align='center'>规格名称</th>
            <th align='center'>商品类型</th>
            <th align='center'>规格项录入方式</th>
            <th align='center'>规格项列表</th>
			<th align='center'>管理操作</th>
            </tr>
    </thead>


     <tbody>
   	<?php foreach($goods_spec as $v){ ?>
       <tr>
         <td align='center'><input name='listorders[<?php echo $v['id']; ?>]' type='text' size='3' value='<?php echo $v['sort']; ?>' class='input-text-c'></td>
         <td align='center'><?php echo $v['id']; ?></td>
         <td align='center'><?php echo $v['name']; ?></td>
         <td align='center'><?php echo $v['type_name'];?></td>
         <td align='center'>
         <?php 
            if($v['spec_input_type']==1){
                echo '列表选择';
            }else{
                echo '手工录入';
            }
         ?>
         </td>
         <td align='center'><?php echo $v['item'];?></td>
		 <td align='center'>
         	<a href="<?php echo G_ADMIN_PATH; ?>/goods_spec/addEdit/<?php echo $v['id']; ?>">修改</a><span class='span_fenge lr5'>|</span>
            <a href="javascript:;" onclick="del(<?php echo $v['id'];?>)">删除</a>
         </td>
      </tr>
     <?php } ?>
   </table>
   </form>
   </form>
   <div class="btn_paixu">
  	<div style="width:80px; text-align:center;">
          <input type="button" class="button" value=" 排序 "
        onclick="myform.action='<?php echo G_MODULE_PATH; ?>/goods_spec/listorder/dosubmit';myform.submit();"/>
    </div>
  </div>
 <div id="pages"><ul><li>共 <?php echo $total; ?> 条</li><?php echo $page->show('one','li'); ?></ul></div>

</div><!--table-list end-->

</body>
<script type="text/javascript">
    function del(id) {
        var url = "<?php echo G_ADMIN_PATH; ?>/goods_spec/del";
        var data = {id:id};
        window.parent.ajaxDel(url,data,'get','确认要删除吗？');
    }
</script>
</html> 
