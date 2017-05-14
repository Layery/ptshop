<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">
<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/style.css" type="text/css">
<style type="text/css">
/*tr{height:30px;line-height:30px}*/
table{
	width:100%;
	border:1px solid #d5dfe8;
}
.content{
	width:35%;
}
.dingdan_content{
	width:100%;
	border:1px solid #d5dfe8;
	background:#eef3f7;
	float:left;
	text-align: center
}
.dingdan_content table tr{
	height:35px;
	line-height:35px;
}
.dingdan_content table td{
	padding: 0px;
}
/*.dingdan_content li{
	float:left;
	width:310px;
	border: 1px solid #d5dfe8;
}*/
.dingdan_content_user{
	width:100%;
	border:1px solid #d5dfe8;
	background:#eef3f7;
	float:left;
	text-align: center;
}
.btn-face{
	border-radius: 3px;
	margin-left: 10px;
}
.text-center{
	text-align: center;
}

.dingdan_content_user li{
	line-height:25px;
}
.api_b{
	width:80px;
	display:inline-block;
	font-weight:normal
}
.yun_ma{
	word-break:break-all;
	width:200px;
	background:#fff;
	overflow:auto;
	height:100px;
	border:5px solid #09F;
	padding:5px;
}
    /* 退款弹窗 start */
*{padding: 0;margin: 0;}
ul,li{
    list-style: none;
}
.refund-shade{
    position:fixed;
    left:0;
    right:0;
    bottom:0;
    top:0;
    background-color:rgba(0,0,0,.5);
    display: none;
}
.refund-dialog{
    width:350px;
    height: 250px;
    position:fixed;
    background-color: #fff;
    left:calc(50% - 175px);
    top:calc(50% - 125px);
    display: inherit;
}
.refund-dialog a{
    width: 85px;
    height: 35px;
    line-height: 35px;
    color: #fff;
    background-color: #d80000;
    display: block;
    text-align: center;
    -webkit-border-radius:3px;
    -moz-border-radius:3px;
    border-radius:3px;
    text-decoration: none;
}
.refund-dialog a:hover{
    background-color: #666;
}
.refund-dialog a:first-child{
    float: left;
}
.refund-dialog a:last-child{
    float:right;
}
.refund-dialog h3{
    text-align: center;
    font-size: 20px;
    display: block;
    height: 70px;
    line-height: 70px;
}
.refund-dialog ul {
    padding: 0 30px;
}
.refund-dialog ul li{
    font-size: 16px;
    height:24px;
    line-height: 24px;
    margin-bottom: 31px;
}
.refund-dialog ul li:last-child{
    height:42px;
    line-height: 24px;
    margin-bottom: 28px;
    padding:0 37px;
}
    /* 退款弹窗 end */
</style>
	<script type="text/javascript" src="<?php echo G_GLOBAL_STYLE;?>/global/js/jquery.min.js"></script>
</head>
<body>
<div class="header lr10">
	<h3 class="nav_icon">订单详情</h3>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
	<div class="dingdan_content">
		<table>
			<tr>
				<th colspan="4" style="color: #444">基本信息</th>
			</tr>
			<tr>
				<td align="right"><strong>订单号：</strong></td>
				<td class="content"><?php echo $orderInfo['o_code'];?></td>
				<td align="right"><strong>订单状态：</strong></td>
				<td class="content"><?php echo $orderInfo['o_status'];?></td>
			</tr>
			<tr>
				<td align="right"><strong>报名用户：</strong></td>
				<td class="content"><?php echo $orderInfo['o_username'];?></td>
				<td align="right"><strong>退款状态：</strong></td>
				<td class="content"><?php echo $orderInfo['refund_status'];?></td>
			</tr>
			<tr>
				<td align="right"><strong>下单时间：</strong></td>
				<td class="content"><?php echo date('Y-m-d H:i:s',$orderInfo['o_time'])?></td>
				<td align="right"><strong>支付方式：</strong></td>
				<td class="content"><?php echo $orderInfo['o_pay_type'];?></td>
			</tr>
			<tr>
				<td align="right"><strong>活动费用：</strong></td>
				<td class="content"><?php echo $orderInfo['o_money'];?></td>
				<td align="right"><strong>积分折扣：</strong></td>
				<td class="content"><?php echo $orderInfo['o_discount'];?></td>
			</tr>
			<tr>
				<td align="right"><strong>实际付款(不含余额支付)：</strong></td>
				<td class="content"><?php echo $orderInfo['o_payment'];?></td>
				<td align="right"><strong>退款金额（理论）：</strong></td>
				<td class="content"><?php echo $orderInfo['o_refund'];?></td>
			</tr>
		</table>
		<!--<b style="clear:both;display:block; line-height:30px;color: #666;">基本信息</b>
		<li><b class="api_b">订单号：</b><?php /*echo $shop['surplus']; */?> 件	</li>
		<li><b class="api_b">总库存：</b><?php /*echo $shop['inventory']; */?>件 	</li>
		<li><b class="api_b">商品价格：</b><?php /*echo $shop['money']; */?></li>
		<li><b class="api_b">购买数量：</b><?php /*echo $record['gonumber']; */?>件</li>
		<li><b class="api_b">支付金额：</b><?php /*echo $record['moneycount']; */?>元</li>-->
	</div>
	<div class="bk10"></div>

	<div class="dingdan_content_user">
		<table>
			<tr>
				<th colspan="4" style="color: #444">报名信息</th>
			</tr>
			<tr>
				<td align="right"><strong>报名人：</strong></td>
				<td class="content"><?php echo $signInfo['s_username'];?></td>
				<td align="right"><strong>联系方式：</strong></td>
				<td class="content"><?php echo $signInfo['s_mobile'];?></td>
			</tr>
			<tr>
				<td align="right"><strong>身份证号：</strong></td>
				<td class="content"><?php echo $signInfo['s_ID_card'];?></td>
				<td align="right"></td>
				<td class="content"></td>
			</tr>
		</table>
	</div>
	<div class="bk10"></div>
	<div class="dingdan_content_user">
		<table>
			<tr>
				<th colspan="4" style="color: #444">活动信息</th>
			</tr>
			<tr>
				<td align="right"><strong>活动主题：</strong></td>
				<td class="content"><?php echo _strcut($orderInfo['o_act_title'],0,50);?></td>
				<td align="right"><strong>活动开始时间：</strong></td>
				<td class="content"><?php echo date('Y-m-d H:i:s',$actInfo['act_start_time']);?></td>
			</tr>
			<tr>
				<td align="right"><strong>活动费用：</strong></td>
				<td class="content"><?php echo $orderInfo['o_money'];?></td>
				<td align="right"><strong>活动结束时间：</strong></td>
				<td class="content"><?php echo date('Y-m-d H:i:s',$actInfo['act_end_time']);?></td>
			</tr>
			<tr>
				<td align="right"><strong>报名人数/人数限制：</strong></td>
				<td class="content"><?php echo $actInfo['act_num_signed'].'/'.$actInfo['act_num_limit'];?></td>
				<td align="right"><strong>拼车费：</strong></td>
				<td class="content"><?php echo $actInfo['act_fare'];?></td>
			</tr>
		</table>
	</div>
	<div class="bk10"></div>
	<div class="dingdan_content_user">
		<table>
			<tr>
				<th colspan="2" style="color: #444">操作信息</th>
			</tr>
			<tr>
				<td align="right"><strong>当前可执行操作：</strong></td>
				<td class="content" style="width: 83%;">
					<?php foreach ($btn as $k => $v){?>
						<input type="button" class="button btn-face" value="<?php echo $v?>" onclick="ajax_action(this,{order_id:<?php echo $orderInfo['o_id']?>,type:'<?php echo $k?>'})">
					<?php }?>
				</td>
			</tr>
		</table>
	</div>
	<div class="bk10"></div>
	<div class="dingdan_content_user">
		<table>
			<tr>
				<th colspan="7" style="color: #444">操作记录</th>
			</tr>
			<tr>
				<td class="text-center">操作者</td>
				<td class="text-center">操作时间</td>
				<td class="text-center">订单状态</td>
				<td class="text-center">退款状态</td>
				<td class="text-center">退款类型</td>
				<td class="text-center">描述</td>
			</tr>
			<?php foreach ($action_log as $key => $item){
				switch ($item['refund_status']){
					case 0:
						$refund_status = '未退款';
						break;
					case 1:
						$refund_status = '审核中';
						break;
					case 2:
						$refund_status = '退款中';
						break;
					case 3:
						$refund_status = '已退款';
						break;
				}
				switch ($item['refund_type']){
					case 0:
						$refund_type = '未申请退款';
						break;
					case 1:
						$refund_type = '手动退款';
						break;
					case 2:
						$refund_type = '自动退款';
						break;
				}
				?>
			<tr>
				<td class="text-center"><?php if($item['action_user']==0){echo $info['username'];}elseif($item['action_user']==-1){echo '系统';}else{echo $orderInfo['o_username']; }?></td>
				<td class="text-center"><?php echo date('Y-m-d H:i:s',$item['action_time']);?></td>
				<td class="text-center"><?php echo $item['order_status'];?></td>
				<td class="text-center"><?php echo $refund_status;?></td>
				<td class="text-center"><?php echo $refund_type;?></td>
				<td class="text-center"><?php echo $item['status_desc'];?></td>
			</tr>
			<?php }?>
		</table>
	</div>
</div>
<!--table-list end-->

<!-- 退款弹窗 start -->
<div class="refund-shade" id="refund-show">
    <div class="refund-dialog">
        <h3>退款确认</h3>
        <ul>
            <li>
                退款金额：<?php echo $orderInfo['o_refund'];?>
            </li>
            <li>
                实际退款：<input type="text" class="input-text" id="refund-actual" name="refund-actual" value="<?php echo $refund_fee?>" data-amount="<?php echo $orderInfo['o_refund'];?>">
            </li>
            <li>
                <a class="confirm-btn" href="javascript:void(0);">确认</a>
                <a class="cancel-btn" href="javascript:void(0);">取消</a>
            </li>
        </ul>
    </div>
</div>
<!-- 退款弹窗 end -->

<script>
    $(function () {
        //确定退款
        $('.confirm-btn').click(function () {
            var order_id = <?php echo $orderInfo['o_id']?>;
            //console.log(order_id);
            var refund = parseFloat($('#refund-actual').val());
            var amount_refund = parseFloat($('#refund-actual').data('amount'));
            if(refund <= 0.00){
                window.parent.message('实际退款不能为零',8,2);
            }else if(refund > amount_refund){
                window.parent.message('实际退款不能大于退款金额',8,2);
            }
            //开始进行退款操作
            $.ajax({
                type:'post',
                url:'<?php echo WEB_PATH; ?>/admin/act_order/order_refund',
                data:{order_id:order_id,refund:refund},
                dataType:'json',
                success:function (data) {
                    if(data.state == 0){
                        window.parent.message(data.msg,1,1);
                        $('#refund-show').hide();
                        location.href = location.href;
                    }else{
                        window.parent.message(data.msg,8);
                    }
                }
            });
        })
        //隐藏退款
        $('.cancel-btn').click(function () {
            $('#refund-show').hide();
        })
    })
	/**
	 * 可执行操作的函数
	 */
	function ajax_action(obj,jsonData) {
		var val = $(obj).val();
		if(jsonData.type == 'confirm_refund'){
		    $('#refund-show').show();
        }else{
            if(confirm('确认'+val)){
                $.ajax({
                    type:"POST",
                    url:"<?php echo WEB_PATH; ?>/admin/act_order/order_action",
                    data:jsonData,
                    dataType:"json",
                    success:function (data) {
                        if(data.state==0){
                            if(data.hasOwnProperty('url')){
                                location.href = data.url;
                            }else{
                                location.href = location.href;
                            }
                        }else{
                            window.parent.message(data.msg,8);
                        }
                    }
                });
            }
        }
	}
</script>
</body>
</html>