<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?><!DOCTYPE html>
<html lang="zh-CN"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php if(isset($title)): ?><?php echo $title; ?><?php  else: ?><?php echo _cfg("web_name"); ?><?php endif; ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="keywords" content="<?php if(isset($keywords)): ?><?php echo $keywords; ?><?php  else: ?><?php echo _cfg("web_key"); ?><?php endif; ?>" />
<meta name="description" content="<?php if(isset($description)): ?><?php echo $description; ?><?php  else: ?><?php echo _cfg("web_des"); ?><?php endif; ?>" />
<link rel="stylesheet" media="screen,projection,tv" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/swiper.min.css">
<link rel="stylesheet" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/bootstrap.css">
<link rel="stylesheet" media="screen,projection,tv" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/header_footer.css">
<link rel="stylesheet" media="screen,projection,tv" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/main.css">
<link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/comm.css" rel="stylesheet" type="text/css" />
<link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/goods.css" rel="stylesheet" type="text/css" />
<style>
    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }
  .swiper-slide img {
         width:100%; 
         height:auto;
         display:block;
    }
    .c_add_pbg {
        background: #000 none repeat scroll 0 0;
        bottom: 0;
        height: 36px;
        left: 0;
        opacity: 0.5;
        position: absolute;
        width: 100%;
  }
   .swiper-slide p {
        bottom: 0;
        color: #fff;
        font-size: 14px;
        height: 18px;
        left: 0;
        padding-left: 10%;
        position: absolute;
        z-index: 2;
    }
  .accumulative-number{
        width:300px;
        margin:0 auto;
        height:40px;
        color:#cccccc;
        line-height:40px;
        text-align: center;
  
  }
.yJoinNum{
	color: #3c3c3c;
	font-size: 14px;
	text-align:center;
	height:34px;
}
.yJoinNum .allNums{
	display:block;
	height:40px;
}
.yJoinNum span{
	display: inline-block;
	font-size: 14px;
	color: #fff;
	line-height:40px;
	vertical-align: middle;
}
.yJoinNum span.yBefore{
	padding-right: 5px;
	font-size:14px;
	color:#fff;
}
.yJoinNum span.yNumList{
	margin: 0 3px;
	width: 16px;
	height: 20px;
	overflow: hidden;
	font-size: 16px;
	line-height: 20px;
	font-family: "Arial";
	background-position: 0 -1099px;
	font-weight: bold;
	text-align: center;
	color: #dd2726;
	border: 1px solid #e4e4e4;
}
.seek_top{
    width:100%;
    height:50px;
    background-color:#dd2726;
    line-height:50px;
    background-image: url("<?php echo G_TEMPLATES_IMAGE; ?>/mobile/logo_index.png");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 108px auto;
    overflow: hidden;
}
.seek_bn{
    display: block;
    width:22px;   
    height:22px;
    background-image: url("<?php echo G_TEMPLATES_IMAGE; ?>/mobile/account_icon_index.png");
    background-repeat: no-repeat;
    background-size:auto 42px;
    background-position: -1527px -17px;
    margin-top: 14px;
    margin-left:20px;
    cursor: pointer;
    }
.b_float{width:100%;height:50px;background:rgba(0,0,0,0.8);position:fixed;top:0;left:0;z-index:150;}
    .b_logo{float:left;margin-top:10px;height:30px;margin-left:5%;}
    .b_text{float:left;margin-top:10px;margin-left:5%;height:30px;}
    .b_btn{float:right;margin-right:7%;height:15px;margin-top:18px;}
    .b_float em{
    display:block;width:20px;height:15px;position:absolute;top:10px;right:0;
     background:url(<?php echo G_TEMPLATES_IMAGE; ?>/mobile/close.png)no-repeat;background-size:40%;
        }	
.s_logo {
	width:100%;
	position:absolute;
	top:6%;
	left:0;
	z-index:22222;
	text-align:center
}
.s_logo img {
	width:27%
}
#btnLoadMore3 p{
	line-height: 20px;
	margin-top:10px;
}			
</style>

<body style="padding-bottom:70px;">

 <div class="a-swiper-container swiper-container swiper-container-horizontal">
        <div class="swiper-wrapper">
	 	<?php $ln=1;if(is_array($shop_ad)) foreach($shop_ad AS $ad): ?>
		<span class="swiper-slide">
		<a href="<?php echo $ad['link']; ?>"><img src="<?php echo G_UPLOAD_PATH; ?>/<?php echo $ad['img']; ?>" width="100%"></a><!--<p><?php echo $ad['title']; ?></p>
 		<div class="c_add_pbg">
		</div> -->
		</span>
		<?php  endforeach; $ln++; unset($ln); ?>  
        </div>
	</div>

   <!--S 商品 -->

   <div class="c_new_goods b_index_fixed1">
	    <!-- 导航 -->
            <ul class="c_goods_title b_index_fixed" style="position: relative; top: 0px; left: 0px; z-index: 149;" id="divGoodsNav">
       	 	    <li order="10" class="c_hot_color"><a href="javascript:;">公告<b></b></a></li>
                <li order="20"><a href="javascript:;">最热<b></b></a></li>
                <li order="40"><a href="javascript:;">最新<b></b></a></li>
                <li order="50" class="" type="price"><a href="javascript:;">价格</a>
				  <dl style="display:none;" class="priceOrder">
                    <dd  class="sOrange" order="60"><a href="javascript:;">升序</a></dd>
                    <dd  class="sOrange" order="50"><a href="javascript:;">降序</a></dd>
                      <?php $ln=1; if(is_array($interval)) foreach($interval AS $key => $val): ?>
                    <dd  class="sOrange"><a href="javascript:;" id="<?php echo $key; ?>"><?php echo $val['open_interval']; ?><?php if($val['close_interval']!=0): ?>~<?php echo $val['close_interval']; ?>元<?php  else: ?>元以上<?php endif; ?></a></dd>
                      <?php  endforeach; $ln++; unset($ln); ?>
				  </dl>
				</li>
                <li>
				<a href="javascript:;">筛选<span></span></a>
                    <dl style="display:none;">
                        <dd type="0" class="sOrange"><a href="javascript:;">全部</a></dd>
						<?php $data=$this->DB()->GetList("select * from `@#_category` where `model`='1'",array("type"=>1,"key"=>'',"cache"=>0)); ?>
						<?php $ln=1;if(is_array($data)) foreach($data AS $categoryx): ?>
						<dd><a id="<?php echo $categoryx['cateid']; ?>" href="javascript:;"><?php echo $categoryx['name']; ?></a></dd>
						<?php  endforeach; $ln++; unset($ln); ?>
						<?php if(defined('G_IN_ADMIN')) {echo '<div style="padding:8px;background-color:#F93; color:#fff;border:1px solid #D80000;text-align:center"><b>This Tag</b></div>';}?>
					</dl>
                </li>

           </ul>
        <!-- 列表 -->
	  <div class="goodsList">
             <div id="divGoodsLoading" class="c_new_goods b_index_fixed1" style="display:none;"><b></b>正在加载...</div>
        </div>
		 <a id="btnLoadMore" class="c_click_see" href="javascript:;" style="display:none;"><img id="bk" alt="loading" src="<?php echo G_TEMPLATES_IMAGE; ?>/mobile/loading2.gif" style="height:30px"></a>
            <a id="btnLoadMore2" class="c_click_see"  style="display:none;">没有数据</a>
            <a id="btnLoadMore3" class="c_click_see"  style="display:none;">已经到底了<p><?php echo _cfg('web_copyright'); ?></p></a>
      
    </div>
	
    <input id="urladdress" value="" type="hidden" />
    <input id="pagenum" value="" type="hidden" />
  <!--E 商品 -->


  	<!--购买页面可以在此处添加测试-->
  	<!--测试end-->

    <!-- 返回顶部 -->
	<a class="u_top" target="_self" style="display: none;"></a>

   <!--S 底部导航 -->
    <ul id="c_main_menu">
      <li id="nav_index" class="c_menu_this">
        <a href="<?php echo WEB_PATH; ?>/mobile/mobile">
          <span class="c_index"></span>
          <b>首页</b>
        </a>
      </li>
      <li id="nav_goods">
        <a href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist">
          <span class="c_all_good"></span>
          <b>订单</b>
        </a>
      </li>

      <li id="nav_member">
        <a href="<?php echo WEB_PATH; ?>/mobile/home">
          <span class="c_new_know"></span>
          <b>个人中心</b>
        </a>
      </li>
    </ul> 
    <!--E 底部导航 -->
    <!--固定购物车 
       <div class="shopcartFix">
        <a href="<?php echo WEB_PATH; ?>/mobile/cart/cartlist">
          <span class="c_cart_bag"></span>
          <em class="z-shop" id="btnCart">0</em>
        </a>
      </div>-->




<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery-2.1.4.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/ajax.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery.lazyload.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/bootstrap.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/common.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/common_ajaxfunction_main.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery.cookie.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/cart.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/swiper.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/index.min.js"></script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/groupPurchase.js"></script>
<script>
$(function(){
	var cartHelper = new CartHelper();
	var cartDetail=cartHelper.read();
	$("#c_total_num").html(cartDetail.Count);

});

</script>

<script>
  var menus = '[]';
</script>

<script type="text/javascript">

//打开页面加载数据
window.onload=function(){

	var res = <?php echo $res; ?>;
    //alert(res.interval_name);
    if(typeof res == 'object'){
        glist_json("interval/"+res.id);
    }else{
        glist_json("list/10");
    }


	$.getJSON('<?php echo WEB_PATH; ?>/mobile/ajax/cartnum',function(data){
		if(data.num){
			$("#btnCart").html(data.num);
		}
	});
}
//获取数据
function glist_json(parm){
	$("#urladdress").val('');
	$("#pagenum").val('');
	$.getJSON('<?php echo WEB_PATH; ?>/mobile/mobile/glistajax/'+parm,function(data){
		$("#divGoodsLoading").css('display','none');		
		if(data[0].sum){
			var fg=parm.split("/");
			$("#urladdress").val(fg[0]+'/'+fg[1]);
			$("#pagenum").val(data[0].page);
			for(var i=0;i<data.length;i++){	
			var money = data[i].money;
				money = new Number(money).toFixed(1);
			var ul='<dl class="c_goods_details" id="dataList">';
			    ul+='<dd>';
			    ul+='<div class="c_goods_size">';
				ul+='<a  id="'+data[i].id+'" href="<?php echo WEB_PATH; ?>/mobile/mobile/goodsdesc/'+data[i].id+'"><img class="img-responsive lazy1" style="width: 100%; display: block;" src="<?php echo G_UPLOAD_PATH; ?>/'+data[i].thumb+'"></a>';
				ul+='</div>';
				ul+='<a  id="'+data[i].id+'" href="<?php echo WEB_PATH; ?>/mobile/mobile/goodsdesc/'+data[i].id+'">'+data[i].title+'</a>';
				
				ul+='<div class="c_shopping_cart">';
				ul+='<p>￥<span>'+money+'</span></p>';
				// ul+='<div class="c_progress_box">';			
				// ul+='<span style="width:'+(data[i].canyurenshu / data[i].zongrenshu)*100+'%;"><span>';
				// ul+='</div>';
				//ul+='<a class="add" codeid="'+data[i].id+'" href="javascript:;"><s></s></a>';
				ul+='<a class="add" codeid="'+data[i].id+'" href="javascript:;"><s></s>去拼团</a>';
				ul+='</div></dd></dl>';
				$("#divGoodsLoading").before(ul);
			}
			if(data[0].page<=data[0].sum){
				$("#btnLoadMore").css('display','block');
				$("#btnLoadMore2").css('display','none');
				$("#btnLoadMore3").css('display','none');
			}else if(data[0].page>data[0].sum){
				$("#btnLoadMore").css('display','none');
				$("#btnLoadMore2").css('display','none');
				$("#btnLoadMore3").css('display','block');
			}
		}else{
			$("#btnLoadMore").css('display','none');
			$("#btnLoadMore2").css('display','block');	
			$("#btnLoadMore3").css('display','none');			
		}
	});
}
$(document).ready(function(){
	//即将揭晓,人气,最新,价格	
		$("#divGoodsNav li:not(:first,:last,:eq(3))").click(function(){

		var l=$(this).index();


		$("#divGoodsNav li").eq(l).addClass('current');
		var parm=$("#divGoodsNav li").eq(l).attr('order');
		if (l==3) {
			if(parm==50){
				$(this).attr('order','60');
			}
			else{
				$(this).attr('order','50');
			}
			
		}
		$("#divGoodsLoading").css('display','block');
		$(".goodsList dl").remove();
		var glist=glist_json("list/"+parm);
	
	});
	$("#divGoodsNav li:first").click(function(){
		$('.noticebg').css('display','flex');
		$('.noticebg .close,.noticebg .know').click(function(){
			$('.noticebg').css('display','none');
		})
	})
	
	// 商品分类
	var last=$("#divGoodsNav li:last"),
		first=$("#divGoodsNav dd:first");
	$("#divGoodsNav li:last a:first,#divGoodsNav li:eq(3) a:first").click(function(){	
		var dl=	$(this).siblings('dl');
		if(dl.css("display")=='none'){
			
			$(this).parent().siblings().find('dl').hide();
			dl.show();
			last.addClass("gSort");
			first.addClass("sOrange");			
		}else{
			dl.hide();
			last.removeClass("gSort");
			first.removeClass("sOrange");
		}
	});
	$("#divGoodsNav li:last dl dd").click(function(){
		var s=$(this).index();
		//var t=$("#divGoodsNav .gSort dd a").eq(s).html();
		var t=$(this).find('a').html();
		var id=$("#divGoodsNav .gSort dd a").eq(s).attr('id');
		//$("#divGoodsNav .gSort a:first").html(t);
		$(this).parent().siblings('a').html(t);
		var l=$("#divGoodsNav .current").index(),
			parm=$("#divGoodsNav li").eq(l).attr('order');
		if(id){			
			$("#divGoodsLoading").css('display','block');
			$(".goodsList dl").remove();
			glist_json(id+'/'+parm);
		}else{
			glist_json("list/"+parm);
			$(".goodsList dl").remove();
		}	
		$("#divGoodsNav  dl").hide();
		last.removeClass("gSort");
		first.removeClass("sOrange");
	});
	//价格下拉点击事件
	$("#divGoodsNav li:eq(3) dl dd").click(function(){
        var s=$(this).index(); //dd所在位置
        var t=$(this).find('a').html();
        $(this).parent().siblings('a').html(t);
        if(s==0||s==1){
            parm=$(this).attr('order');
            $(".goodsList dl").remove();
            glist_json("list/"+parm);
        }else{
            var id = $(this).find('a').attr('id');
            $(".goodsList dl").remove();
            glist_json("interval/"+id);
        }
        $("#divGoodsNav  dl").hide();
        last.removeClass("gSort");
        first.removeClass("sOrange");
    });
	//加载更多
	$(window).scroll(function() {
	    if ($(document).height() - $(this).scrollTop() - $(this).height() < 1 && $('#btnLoadMore').css('display') != 'none') {
	        var url = $("#urladdress").val(),
	            page = $("#pagenum").val();
	        glist_json(url + '/' + page);
	    }
	});	
	//返回顶部
	$(window).scroll(function(){
		if($(window).scrollTop()>50){
			$("#btnTop").show();
		}else{
			$("#btnTop").hide();
		}
	});
	$("#btnTop").click(function(){
		$("body").animate({scrollTop:0},10);
	});
	//添加到购物车
	// $(document).on("click",'.add',function(){
	// 	var codeid=$(this).attr('codeid');
	// 	$.getJSON('<?php echo WEB_PATH; ?>/mobile/ajax/addShopCart/'+codeid+'/1',function(data){
	// 		if(data.code==1){
	// 			addsuccess('添加失败');
	// 		}else if(data.code==0){
	// 			addsuccess('添加成功');				
	// 		}return false;
	// 	});
	// });
	//点击添加购物车按钮 （2016.9.20）：
	//  $(document).on("click",'.add',function(){
	//  	$('.selectBar-wrap').slideDown();

	// });
	$(document).on("click",'.add',function(){
    	var codeid=$(this).attr('codeid');
    	$.getJSON('<?php echo WEB_PATH; ?>/mobile/ajax/goodsSelect/'+codeid+'/'+1,function(data){
      		$("body").append(data);
			$('.selectBar-wrap').slideDown();
    	});
	});


	// function addsuccess(dat){
	// 	$("#pageDialogBG .Prompt").text("");
	// 	var w=($(window).width()-255)/2,
	// 		h=($(window).height()-45)/2;
	// 	$("#pageDialogBG").css({top:h,left:w,opacity:0.8});
	// 	$("#pageDialogBG").stop().fadeIn(1000);
	// 	$("#pageDialogBG .Prompt").append('<s></s>'+dat);
	// 	$("#pageDialogBG").fadeOut(1000);
	// 	//购物车数量
	// 	$.getJSON('<?php echo WEB_PATH; ?>/mobile/ajax/cartnum',function(data){
	// 		$("#btnCart").append('<em>'+data.num+'</em>');
	// 	});
	// }
	
});

</script>

<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jweixin.js"  language="javascript"  type="text/javascript"></script>
<script type="text/javascript">

  wx.config({
    debug: false,
    appId: "<?php  echo $wechat['appid']; ?>",
    timestamp: <?php  echo $signPackage["timestamp"]; ?>,
    nonceStr: '<?php  echo $signPackage["nonceStr"]; ?>',
    signature: '<?php  echo $signPackage["signature"]; ?>',
    jsApiList: ["checkJsApi", "onMenuShareAppMessage", "onMenuShareTimeline", "onMenuShareWeibo", "onMenuShareQQ"]
  });
wx.ready(function () {
var n ="<?php echo WEB_PATH; ?>/mobile/mobile";
var shareTitle = "拼团装备商场";
var shareDesc = "带上装备，跟我们一起浪起来！";
var shareIconUrl = "<?php echo G_TEMPLATES_IMAGE; ?>/shop/shareIcon.png";
var shareSuccess = function(){
  alert('分享成功');
};

var shareCancel = function(){
  alert('已取消');
}
    wx.onMenuShareTimeline({
        title: shareTitle, // 分享标题
        link: n, // 分享链接
        imgUrl: shareIconUrl, // 分享图标
        success: shareSuccess,
        cancel: shareCancel
    });
    wx.onMenuShareAppMessage({
        title: shareTitle, // 分享标题
        desc: shareDesc, // 分享描述
        link: n, // 分享链接
        imgUrl: shareIconUrl, // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
         success: shareSuccess,

        cancel: shareCancel
    });
    wx.onMenuShareQQ({
        title: shareTitle, // 分享标题
        desc: shareDesc, // 分享描述
        link: n, // 分享链接
        imgUrl: shareIconUrl, // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
         success: shareSuccess,

        cancel: shareCancel
    });
    wx.onMenuShareWeibo({
        title: shareTitle, // 分享标题
        desc: shareDesc, // 分享描述
        link: n, // 分享链接
        imgUrl: shareIconUrl, // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
         success: shareSuccess,

        cancel: shareCancel
    });

});
</script>

<style>
#pageDialogBG{-webkit-border-radius:5px; width:200px;height:45px;color:#fff;font-size:16px;text-align:center;line-height:45px;}
</style>
<div id="pageDialogBG" class="pageDialogBG">
<div class="Prompt"></div>
</div>
<div class="noticebg">
	<div class="notice">
		<a class="close"></a>
		<h2><?php echo $declare['title']; ?></h2>
		<p><?php echo $declare['content']; ?></p>
		<button class="know">确定</button>


	</div>
</div>

<script language="javascript" type="text/javascript">
	//在除微信外其他浏览器打开时

	var otherBrowser = false;//后台是否开启
	if(otherBrowser){
        var wx=false;
        var ua = navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i)=="micromessenger"){
            wx = true;
        }
        if(!wx){
        	var cont = "<div style='margin:0 auto;padding-top:20%;text-align:center;box-sizing:border-box;'><img src='<?php echo G_TEMPLATES_IMAGE; ?>/weixin.png'/><p style='color:#333;font-size:16px;'>请扫码关注公众号,在公众号内进行操作！</p></div>"
            $('body').empty().append(cont);
        }
    }

  var Path = new Object();
  Path.Skin="<?php echo G_TEMPLATES_STYLE; ?>";
  Path.Webpath = "<?php echo WEB_PATH; ?>";
  Path.submitcode = '<?php echo $submitcode; ?>';
  Path.Uploadpath = "<?php echo G_UPLOAD_PATH; ?>";


  
  
var Base={head:document.getElementsByTagName("head")[0]||document.documentElement,Myload:function(B,A){this.done=false;B.onload=B.onreadystatechange=function(){if(!this.done&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){this.done=true;A();B.onload=B.onreadystatechange=null;if(this.head&&B.parentNode){this.head.removeChild(B)}}}},getScript:function(A,C){var B=function(){};if(C!=undefined){B=C}var D=document.createElement("script");D.setAttribute("language","javascript");D.setAttribute("type","text/javascript");D.setAttribute("src",A);this.head.appendChild(D);this.Myload(D,B)},getStyle:function(A,B){var B=function(){};if(callBack!=undefined){B=callBack}var C=document.createElement("link");C.setAttribute("type","text/css");C.setAttribute("rel","stylesheet");C.setAttribute("href",A);this.head.appendChild(C);this.Myload(C,B)}}
function GetVerNum(){var D=new Date();return D.getFullYear().toString().substring(2,4)+'.'+(D.getMonth()+1)+'.'+D.getDate()+'.'+D.getHours()+'.'+(D.getMinutes()<10?'0':D.getMinutes().toString().substring(0,1))}
Base.getScript('<?php echo G_TEMPLATES_JS; ?>/mobile/Bottom.js?v='+GetVerNum());

</script>
<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/index_mobile.js"></script>
		


</body></html>