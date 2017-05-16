<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>确认订单</title>
    <meta content="app-id=518966501" name="apple-itunes-app" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta http-equiv="Cache-Control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Expires" content="-1">
    <link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/comm.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/cartList.css" rel="stylesheet" type="text/css" />
</head>
<body style="height:100%;" >
<div class="h5-1yyg-v1">
    
<!-- 栏目页面顶部 -->


<!-- 内页顶部 -->

    <header class="g-header">
        <div class="head-l">
	        <a href="javascript:;" onclick="history.go(-1)" class="z-HReturn"></a>
        </div>
        <h2>确认订单</h2>
    </header>
    <input name="hidShopMoney" type="hidden" id="hidShopMoney" value="<?php echo $MoneyCount; ?>" />
    <input name="hidBalance" type="hidden" id="hidBalance" value="<?php echo $Money; ?>" />
    <input name="hidPoints" type="hidden" id="hidPoints" value="<?php echo $member['score']; ?>" />
    <input name="shopnum" type="hidden" id="shopnum" value="<?php echo $shopnum; ?>" />
    <input name="pointsbl" type="hidden" id="pointsbl" value="<?php echo $fufen_dikou; ?>" />
    <input type="hidden" name="status" id="status" value="<?php echo $status; ?>">
    <div class="empty-space"></div>
    <section class="clearfix g-pay-lst" style="">
      <div class="clients_info">
          <a href="<?php echo WEB_PATH; ?>/mobile/home/address" class="address add-addr"><span>+</span>添加地址</a>
      </div>
    </section><!--  -->
    <?php $ln=1; if(is_array($shoplist)) foreach($shoplist AS $key => $val): ?>
    <section class="clearfix goods_li">
        <ul>
            <?php 
            if(empty($val['flag'])){
            $spec = explode(' ',$val['key_name']);
            //var_dump($spec);exit;
            $spec_name = $spec_value = array();
            foreach($spec as $v){
            $spec_arr = explode(':',$v);

            $spec_name[] = $spec_arr[0].':';
            $spec_value[] = $spec_arr[1];
            }
            }else{
            $spec_name[] = '颜色分类:';
            $spec_value[] = _strcut($val['title'],25);
            }
            //var_dump($spec_name);
             ?>
            <li>
                <div class="item-left">
                    <img  class="img-responsive lazy1" src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $val['goods_img']; ?>">
                </div>
                <div class="item-right">
                    <a href="<?php echo WEB_PATH; ?>/mobile/mobile/goodsdesc/<?php echo $val['goods_id']; ?>" class="gray6"><?php echo $val['title']; ?></a>
                    <p class="goods_class">
                        <?php $ln=1; if(is_array($spec_name)) foreach($spec_name AS $k => $v): ?>
                        <?php echo $v; ?><em><?php echo $spec_value[$k]; ?></em>
                        <?php  endforeach; $ln++; unset($ln); ?>
                    </p>
                    <span class="total_price">￥<b><?php echo $val['price']; ?></b></span>
                    <span class="goods_count">x<em class="shopNum"><?php echo $val['cart_gorenci']; ?></em></span>
                    <input type="hidden" class="shopId" name="shopId" value="<?php echo $val['goods_id']; ?>" />
                </div>
            </li>
        </ul>
    </section>
<?php  endforeach; $ln++; unset($ln); ?>


    <!-- 页面参团部分 -->    
    
    <div class="groupPay <?php echo $group_id; ?>"><!-- 给计时器获取显示唯一标志id -->
        <div class="members">
            <ul>
                <?php if($is_head == 1): ?>
                <li>
                    <img src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $member['img']; ?>">
                    <span class="groupHead">团长</span>
                </li>
                <?php  else: ?>
                <?php $ln=1; if(is_array($groupSingle)) foreach($groupSingle AS $key => $val): ?>
                <li>
                    <img src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $val['img']; ?>">
                    <?php if($val['is_head'] == 1): ?>
                    <span class="groupHead">团长</span>
                    <?php endif; ?>
                </li><!--获取链接data-head属性值判断是否显示span标签-->
                <?php  endforeach; $ln++; unset($ln); ?>
                <?php endif; ?>
                
            </ul>
        </div>
        <!-- <?php if(($groupSingle['state']=='null' || $groupSingle['uid']==$member['uid'])): ?> --><!-- <?php endif; ?> -->
        <div class="number">
            <p>还差<span><?php echo $surple_num; ?></span>人</p>
        </div>
        
        <div class="time"><span class="a"></span>
        <?php if($timer != 0): ?>
            <p>剩余<span class="groupHou">00</span>:<span class="groupMin">00</span>:<span class="groupSec">00</span>结束</p>
        <?php endif; ?>
        </div>
    </div>
    <section class="remarks">
        <div>
            <span>买家留言：</span><input type="text" name="remark" placeholder="选填，对本次交易的说明">
        </div>
    </section>
    <section class="pay-type">
                <!--此处id可能要修改-->
        <article id="bankList" class="clearfix  g-bank-ct">
            <ul>
                <?php $ln=1;if(is_array($paylist)) foreach($paylist AS $pay): ?>
                <li class="gray9" urm="<?php echo $pay['pay_id']; ?>"><span><?php echo $pay['pay_name']; ?></span><i class="z-pay-ment "></i></li>
                <?php  endforeach; $ln++; unset($ln); ?>
            </ul>
        </article>
    </section> 


	<div class="empty-space"></div>
    <section class="payBar">
        <span>合计：<em>￥<i id="total-price"><?php echo $MoneyCount; ?></i></em></span>
        <a id="btnPay" href="javascript:;" class="straight_btn">去付款</a>
    </section>
    
    <div class="dialog-comfirm" style="display: none;">
        <div class="dialog-addr">
            <h2>提示</h2>
            <p id="tip">确认订单前请完善您的收货地址</p>
            <div class="dialog-option">
                <a href="javascript:;" class="know">我知道了</a>
                <a href="<?php echo WEB_PATH; ?>/mobile/home/address" class="add-addr">添加地址</a>
            </div>
        </div>
    </div>
</div>
</body>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery190.js" language="javascript" type="text/javascript"></script>
<script id="pageJS" data="<?php echo G_TEMPLATES_JS; ?>/mobile/Payment.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/groupPurchase.js" language="javascript" type="text/javascript"></script>
<!-- <script>百度统计访问量
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?3467e923fe63a9c0d612efb4d9309c8d";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script> -->
<script>
    $(function () {
        //通过ajax请求判断用户是否已经添加地址
        function addrJudge() {
            $.ajax({
              type:'get',
              url:'<?php echo WEB_PATH; ?>/mobile/ajax/getTip/',
              cache:false,
              dataType:'json',
              success:function(data){
                //alert(data.flag);
                if(data.flag == 1){
                    //用户已经登录且填写了地址
                    addrHtml = '<a href="<?php echo WEB_PATH; ?>/mobile/home/address">';
                    addrHtml += '<div class="clients_data">收货人：<span class="clients_name">'+data.address['shouhuoren']+'</span>';
                    addrHtml += '<span class="clients_tel">'+data.address['mobile']+'</span></div>';
                    addrHtml += '<span class="arrow-right"></span>';
                    addrHtml += '<div class="clients_address"><em></em>收货地址：'+data.address['sheng']+data.address['shi']+data.address['xian']+data.address['jiedao'];
                    addrHtml += '<input type="hidden" name="uid" class="uid" value="'+data.address['uid']+'"/>';
                    addrHtml += '<input type="hidden" name="addrId" class="addrId" value="'+data.address['id']+'"/>';
                    addrHtml += '</div></a>'
                    $('.address').hide();
                    $('.clients_info').append(addrHtml);
                    }else{
                        $('.dialog-comfirm').show();
                        $('a.know').click(function(){
                          $('.dialog-comfirm').hide();
                        })
                    }
                }
            });
        }
        addrJudge();
        //支付方式
        function payFun() {
            var wx=false;
            var ua = navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i)=="micromessenger"){
                wx = true;
            }
            if(!wx){
                $(".gray9:contains('微信支付')").remove();
            }else{
                $(".gray9:contains('支付宝')").remove();
            }
        }
        payFun();
    });

    //调用倒计时函数
    var timer = <?php echo $timer; ?>;
    if(timer != 0){
        var id = <?php echo $group_id; ?>;
        var act_time = <?php echo $group_time; ?>;
        getNowTime(timer,id,act_time);
    }

</script>

<script language="javascript" type="text/javascript">
    //全局参数调用相关，历史遗留问题
    var Path = new Object();
    Path.Skin="<?php echo G_TEMPLATES_STYLE; ?>";
    Path.Webpath = "<?php echo WEB_PATH; ?>";
    Path.submitcode = '<?php echo $submitcode; ?>';

    var Base={head:document.getElementsByTagName("head")[0]||document.documentElement,Myload:function(B,A){this.done=false;B.onload=B.onreadystatechange=function(){if(!this.done&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){this.done=true;A();B.onload=B.onreadystatechange=null;if(this.head&&B.parentNode){this.head.removeChild(B)}}}},getScript:function(A,C){var B=function(){};if(C!=undefined){B=C}var D=document.createElement("script");D.setAttribute("language","javascript");D.setAttribute("type","text/javascript");D.setAttribute("src",A);this.head.appendChild(D);this.Myload(D,B)},getStyle:function(A,B){var B=function(){};if(callBack!=undefined){B=callBack}var C=document.createElement("link");C.setAttribute("type","text/css");C.setAttribute("rel","stylesheet");C.setAttribute("href",A);this.head.appendChild(C);this.Myload(C,B)}}
    function GetVerNum(){
        var D=new Date();
        return D.getFullYear().toString().substring(2,4)+'.'+(D.getMonth()+1)+'.'+D.getDate()+'.'+D.getHours()+'.'+(D.getMinutes()<10?'0':D.getMinutes().toString().substring(0,1))
    }
    Base.getScript('<?php echo G_TEMPLATES_JS; ?>/mobile/Bottom.js?v='+GetVerNum());
    
</script>
</html>