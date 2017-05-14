<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台首页</title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo G_PLUGIN_PATH; ?>/uploadify/api-uploadify.js" type="text/javascript"></script> 
<style>
	.bg{background:#fff url(<?php echo G_GLOBAL_STYLE; ?>/global/image/ruler.gif) repeat-x scroll 0 9px }
    .pos{
        position: relative;
    }
	.color_window_td a{ float:left; margin:0px 10px;}
    .address-tips{
        position: absolute ;
        left:0;
        background-color: #fff;
        width: 165px;
        overflow: auto;
        height:200px;



    }
    .address-tips li{
        height: 18px;
        margin: 0 5px 0 0;
        padding: 2px 0 2px 5px;
        white-space: nowrap;
    }
    .address-tips li:hover{
        background-color: #d4d4d4;
    }

    .latlng{
        color: #FFF;
        background: #aaa;
        border: 0px;
        height: 30px;
        border-top: 0px;
        border-left: 0px;
        border-bottom: 0px solid #666;
        border-right: 0px solid #666;
        padding: 0px 20px;
        font-size: 12px;
        line-height: 30px;
        display: inline-block;
        border-radius: 5px;
    }
    .latlng:hover{
        background: #444;
    }


</style>
</head>
<body>
<div class="header lr10">
	<?php echo $this->headerment();?>
</div>
<div class="bk10"></div>

<div class="table_form lr10">
	<form method="post" action="">
		<table width="100%"  cellspacing="0" cellpadding="0">
			<tr>
				<td align="right" style="width:120px"><font color="red">*</font>标记名称：</td>
				<td>
					<input  type="text" id="mark_name"  name="mark_name" value="<?php if(!empty($mark)){echo $mark['mark_name'];}?>" class="input-text bg">
				</td>
			</tr>
			<tr>
				<td align="right" style="width:120px"><font color="red">*</font>联系电话：</td>
				<td>
					<input  type="text" id="tel_num"  name="tel_num" value="<?php if(!empty($mark)){echo $mark['mobile'];}?>" class="input-text bg">
				</td>
			</tr>
			<tr>
				<td align="right" style="width:120px">标记地址：</td>
				<td class="pos">
					<input  type="text" id="address"  name="address" value="<?php if(!empty($mark)){echo $mark['address'];}?>" class="input-text bg "><span style="margin-left:5px;"><b>提示：</b><font style="color: #d80000;">填写地址，会自动查询经纬度坐标或者使用 ‘坐标拾取器’ 拾取对应的坐标和地址</font></span>
				</td>
			</tr>
			<tr>
				<td align="right" style="width:120px"><font color="red">*</font>标记坐标：</td>
				<td>
					<input  type="text" id="latlng"  name="latlng" value="<?php if(!empty($mark)){echo $mark['latlng'];}?>" class="input-text bg">
                    <input type="button" class="latlng" value="坐标拾取器">
				</td>
			</tr>
			<tr height="60px">
				<td align="right" style="width:120px"></td>
				<td>
					<input type="hidden" name="id" value="<?php echo $id;?>">
					<input type="submit" name="dosubmit" class="button" value="保存标记" />&nbsp;&nbsp;
					<input type="reset" class="button" id="resetbtn" value="重置标记"/>
				</td>
			</tr>
		</table>
	</form>

	<div class="lr10">
		<iframe id='mapIframe' src="http://lbs.qq.com/tool/getpoint/getpoint.html" style="width: 100%;height: 650px;border: 0;display: none;" scrolling="no"></iframe>
	</div>

</body>
<script type="text/javascript">
    $(function () {
        $('#address').keyup(function () {
            var self = $(this);
            var keyword = $(this).val();
            if(keyword == ''){
                return
            }
            var tag = '';
            //console.log(keyword);
            $('#address-tips').remove();
            $.ajax({
                type:'get',
                url:'<?php echo G_ADMIN_PATH?>/map/ajaxGetAddress/',
                dataType:'json',
                data:{keyword:keyword},
                success:function (msg) {
                    if(msg.status==0){
                        // console.log(msg.data);
                        if(msg.count > 0){
                        tag += '<ul id="address-tips" class="address-tips">';
                        for(var i in msg.data){
                            tag += '<li data-lat="'+msg.data[i].location.lat+'" data-lng="'+msg.data[i].location.lng+'">'+msg.data[i].title+'</li>';
                        }
                        tag += '</ul>';
                        self.after(tag);
                        }
                        $(document).on('click','#address-tips li',function(){
                            var val = $(this).text();
                            var latlng = $(this).data('lat')+','+$(this).data('lng');
                            self.val(val);
                            $(this).parent().remove();
                            $('#latlng').val(latlng);
                        })

                        $('div').not('#address-tips').click(function(){
                               $('#address-tips').remove();
                          })

                    }
                }
            })
        })


        
        //坐标拾取器
        $('.latlng').click(function () {
            $('#mapIframe').toggle(1000)
        })
    })

</script>
</html> 