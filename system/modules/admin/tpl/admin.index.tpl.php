<?php defined('G_IN_ADMIN')or exit('No permission resources.'); ?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv=Content-Type content="text/html; charset=utf-8"> 

<link rel="Shortcut Icon" href="<?php echo G_WEB_PATH;?>/favicon.ico">

<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/global.css" type="text/css">

<link rel="stylesheet" href="<?php echo G_GLOBAL_STYLE; ?>/global/css/index.css" type="text/css">

<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/jquery-1.8.3.min.js"></script>

<script src="<?php echo G_PLUGIN_PATH; ?>/layer/layer.min.js"></script>

<script src="<?php echo G_GLOBAL_STYLE; ?>/global/js/global.js"></script>

<title>京宁汇商城后台管理系统首页</title>

<script type="text/javascript">

var ready=1;

var kj_width;

var kj_height;

var header_height=100;

var R_label;

var R_label_one = "当前位置: 系统设置 >";





function left(init){

	var left = document.getElementById("left");

	var leftlist = left.getElementsByTagName("ul");

	

	for (var k=0; k<leftlist.length; k++){

		leftlist[k].style.display="none";

	}

	document.getElementById(init).style.display="block";

}



function secBoard(elementID,n,init,r_lable) {

			

	var elem = document.getElementById(elementID);

	var elemlist = elem.getElementsByTagName("li");

	for (var i=0; i<elemlist.length; i++) {

		elemlist[i].className = "normal";		

	}

	elemlist[n].className = "current";

	R_label_one="当前位置: "+r_lable+" >";

	R_label.text(R_label_one);

	left(init);

}





function set_div(){

		kj_width=$(window).width();

		kj_height=$(window).height();

		if(kj_width<1000){kj_width=1000;}

		if(kj_height<500){kj_height=500;}



		$("#header").css('width',kj_width); 

		$("#header").css('height',header_height);

		$("#left").css('height',kj_height-header_height); 

	    $("#right").css('height',kj_height-header_height); 

		$("#left").css('top',header_height); 

		$("#right").css('top',header_height);

		

		$("#left").css('width',180);		

		$("#right").css('width',kj_width-182); 

		$("#right").css('left',182);

		

		$("#right_iframe").css('width',kj_width-206); 

		$("#right_iframe").css('height',kj_height-148);

		 		

		$("#iframe_src").css('width',kj_width-208); 

		$("#iframe_src").css('height',kj_height-150); 	

		

		$("#off_on").css('height',kj_height-180);

		

		var nav=$("#nav");		

		nav.css("left",(kj_width-nav.get(0).offsetWidth)/2);

		nav.css("top",61);

}





$(document).ready(function(){	

		set_div();		

		$("#off_on").click(function(){

				if($(this).attr('val')=='open'){

					$(this).attr('val','exit');

					$("#right").css('width',kj_width);

					$("#right").css('left',1);

					$("#right_iframe").css('width',kj_width-25); 

					$("iframe").css('width',kj_width-27);

				}else{

					$(this).attr('val','open');

					$("#right").css('width',kj_width-182);

					$("#right").css('left',182);

					$("#right_iframe").css('width',kj_width-206); 

					$("iframe").css('width',kj_width-208);

				}

		});

		

		left('setting');

		$(".left_date a").click(function(){

				$(".left_date li").removeClass("set");						  

				$(this).parent().addClass("set");

				R_label.text(R_label_one+' '+$(this).text()+' >');

				$("#iframe_src").attr("src",$(this).attr("src"));

		});

		$("#iframe_src").attr("src","<?php echo G_MODULE_PATH; ?>/index/Tdefault");

		R_label=$("#R_label");

		$('body').bind('contextmenu',function(){return false;});

		$('body').bind("selectstart",function(){return false;});

				

});



function api_off_on_open(key){

	if(key=='open'){

				$("#off_on").attr('val','exit');

				$("#right").css('width',kj_width);

				$("#right").css('left',1);

				$("#right_iframe").css('width',kj_width-25); 

				$("iframe").css('width',kj_width-27);

	}else{

					$("#off_on").attr('val','open');

					$("#right").css('width',kj_width-182);

					$("#right").css('left',182);

					$("#right_iframe").css('width',kj_width-206); 

					$("iframe").css('width',kj_width-208);

	}

}
</script>



<style>

.header_case{  position:absolute; right:10px; top:10px; color:#0D0D0D}

.header_case a{ padding-left:5px;color:#222;}

.header_case a:hover{ color:#D80000; }

.left_date a{text-decoration:none;word-wrap:break-word;outline:none;hide-focus:expression(this.hideFocus=true);transition:all 0.2s ease-in-out;-webkit-transition:all 0.2s ease-in-out;-moz-transition:all 0.2s ease-in-out;-o-transition:all 0.2s ease-in-out;-ms-transition:all 0.2s ease-in-out;}
.left_date{overflow:hidden;}
.left_date ul{ margin:0px; padding:0px;}
.left_date li{line-height:40px; height:40px; margin:1px 0px; margin-left:0px; overflow:hidden;}
.left_date li a{display:block;height:40px;background:#f9f9f9;border-bottom:1px solid #eee;border-top:3px solid #E7E7E7;border-left:3px solid #D80000;border-right:0;color:#0D0D0D;padding-left:40px; line-height: 33px;}
.left_date li a:hover{border-left:5px solid #111;background-color:#D80000;color:#fff;font-weight:bold;}
.left_date .set a{background-color:#D80000;border-radius:3px; color:#fff; font-weight:bold}
.head{display:block;height:40px;background:#222;font:bold 14px/40px "宋体"; color:#fff;padding-left:30px;}



</style>



</head>

<body onResize="set_div();">



<div id="header">

	<div class="logo"></div>

    <div class="header_case">

    欢迎您：<a href="javascript:;" title="<?php echo $info['username']; ?>"><?php echo $info['username']; ?> [超级管理员]</a>

    <a href="<?php echo G_MODULE_PATH; ?>/user/out" title="退出">[退出]</a>

    <a href="<?php echo G_WEB_PATH; ?>" title="网站首页" target="_blank">网站首页</a>
    <a href="<?php echo G_MODULE_PATH; ?>/index/map" title="地图">地图</a>
    <button  style="width:0px;height:0px;" onClick="document.location.hash='hello'"></button>

    </div>

    <div class="nav" id="nav">    

    	<ul>	

 <li class="current"><a href="#" onClick="secBoard('nav',0,'setting','系统设置');">系统设置</a></li>

            <!--<li class="normal"><a href="#" onClick="secBoard('nav',1,'content','内容管理');">内容管理</a></li>-->

            <li class="normal"><a href="#" onClick="secBoard('nav',1,'shop','装备管理');">装备管理</a></li>

            <li class="normal"><a href="#" onClick="secBoard('nav',2,'activity','活动管理');">活动管理</a></li>

            <li class="normal"><a href="#" onClick="secBoard('nav',3,'user','用户管理');">用户管理</a></li>

             <li class="normal"><a href="#" onClick="secBoard('nav',4,'template','界面管理');">界面管理</a></li>

            <li class="normal"><a href="#" onClick="secBoard('nav',5,'wechat','微信商城');">微信商城</a></li>
            <!--<li class="normal"><a href="#" onClick="secBoard('nav',6,'activity','活动管理');">活动管理</a></li>-->
           <!-- <li class="normal"><a href="#" onClick="secBoard('nav',7,'lottery','抽奖管理');">抽奖管理</a></li>-->

 

           

        </ul>

    </div>

</div><!--header end-->


<div id="left">

    <ul class="left_date" id="setting">   

    	<li class="head">站点设置</li>
<?php
//    exit;
//var_dump($info['gl']);
	if($qx['setting/webcfg']!=1 || $info['gl']=='0'){
?>
	               <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/webcfg">SEO设置</a></li> 
<?php }?>


<?php
    if($qx['setting/config']!=1 || $info['gl']=='0'){
?>
	    

    	               <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/config">基本设置</a></li>
<?php }?>


<?php
    if($qx['setting/upload']!=1 || $info['gl']=='0'){
?>
                            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/upload">上传设置</a></li> 
<?php }?>


<?php
    if($qx['setting/watermark']!=1 || $info['gl']=='0'){
?>
                            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/watermark">水印设置</a></li>	
<?php }?>


<?php
    if($qx['setting/email']!=1 || $info['gl']=='0'){
?>
                            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/email">邮箱配置</a></li> 
<?php }?>


<?php
    if($qx['setting/mobile']!=1 || $info['gl']=='0'){
?>
                            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/setting/mobile">短信配置</a></li>
<?php }?>


<?php
    if($qx['pay/pay_list']!=1 || $info['gl']=='0'){
?>
                            <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/pay/pay/pay_list">支付方式</a></li>
<?php }?>

        <?php
/*        if($qx['setting/share']!=1 || $info['gl']=='0'){
            */?><!--
            <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/setting/share">分享设置</a></li>
        --><?php /*}*/?>

     <li class="head">管理员管理</li>		



<?php
    if($qx['user/lists']!=1 || $info['gl']=='0'){
?>
		<li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/user/lists">管理员管理</a></li>
<?php }?>


<?php
    if($qx['user/reg']!=1 || $info['gl']=='0'){
?>
                        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/user/reg">添加管理员</a></li>
<?php }?>


<?php
    if($qx['user/edit']!=1 || $info['gl']=='0'){
?>
                        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/user/edit/<?php echo $info['uid']; ?>">修改密码</a></li>
<?php }?>



	<li class="head">站长运营</li>


<?php
    if($qx['yunwei/websubmit']!=1 || $info['gl']=='0'){
?>
		<li><span></span><a href="javascript:void(0);" src="<?php echo G_ADMIN_PATH; ?>/yunwei/websubmit">网站提交</a></li>
<?php }?>


<?php
    if($qx['yunwei/webtongji']!=1 || $info['gl']=='0'){
?>
		<li><span></span><a href="javascript:void(0);" src="<?php echo G_ADMIN_PATH; ?>/yunwei/webtongji">站长统计</a></li>
<?php }?>



        <li class="head">后台首页</li>



<?php
    if($qx['index/Tdefault']!=1 || $info['gl']=='0'){
?>
                        <li><span></span><a href="javascript:void(0);" src="<?php echo G_ADMIN_PATH; ?>/index/Tdefault">后台首页</a></li>

<?php }?>



        <li class="head">其他</li>

        <?php
        if($qx['upload/lists']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/upload/lists">上传文件管理</a></li>
        <?php }?>

<?php
    if($qx['cache/init']!=1 || $info['gl']=='0'){
?>

		<li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/cache/init">清空缓存</a></li>
<?php }?>
    </ul>

     <ul class="left_date" id="content">

     	<!--<li class="head">文章管理</li>



<?php
/*    if($qx['content/article_add']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/content/article_add">添加文章</a></li>
<?php /*}*/?>


<?php
/*    if($qx['content/article_list']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/content/article_list">文章列表</a></li>
<?php /*}*/?>


<?php
/*    if($qx['category/lists']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/category/lists/article">文章分类</a></li>
--><?php /*}*/?>



        <!--<li class="head">单页管理</li>



<?php
/*    if($qx['category/addcate']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/category/addcate/danweb">添加单页</a></li>
<?php /*}*/?>


<?php
/*    if($qx['category/lists']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/category/lists/single">单页列表</a></li>
--><?php /*}*/?>


    <!--<li class="head">附件管理</li>



<?php
/*    if($qx['upload/lists']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/upload/lists">上传文件管理</a></li>
--><?php /*}*/?>

        <!--<li class="head">其他</li>



<?php
/*    if($qx['content/model']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/content/model">内容模型</a></li>
<?php /*}*/?>


<?php
/*    if($qx['category/lists']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/category/lists">栏目管理</a></li>
--><?php /*}*/?>

    </ul>

     <ul class="left_date" id="shop">   

     	<li class="head">商品管理</li>



<?php
    if($qx['content/goods_add']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/content/goods_add">添加新商品</a></li>
<?php }?>


<?php
    if($qx['content/goods_list']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/content/goods_list">商品列表</a></li>
<?php }?>



<?php
    if($qx['category/lists']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/category/lists">商品分类</a></li>
<?php }?>


         <?php
         if($qx['goods_type/lists']!=1 || $info['gl']=='0'){
             ?>
             <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/goods_type/lists">商品类型</a></li>
         <?php }?>

         <?php
         if($qx['goods_spec/lists']!=1 || $info['gl']=='0'){
             ?>
             <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/goods_spec/lists">商品规格</a></li>
         <?php }?>

<?php
    if($qx['brand/lists']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/brand/lists">品牌管理</a></li>    	
<?php }?>


<?php
    if($qx['brand/insert']!=1 || $info['gl']=='0'){
?>
    	<li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/brand/insert">添加品牌</a></li>      
<?php }?>

<?php
    if($qx['content/goods_del_list']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/content/goods_del_list">商品回收站</a></li>
<?php }?>


        <li class="head">订单管理</li>


<?php
    if($qx['dingdan/lists']!=1 || $info['gl']=='0'){
?>
       <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/dingdan/lists">订单列表</a></li>
<?php }?>


<?php
    if($qx['dingdan/select']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/dingdan/select">订单查询</a></li>
<?php }?>


<?php
    if($qx['dingdan/lists']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/dingdan/lists/notsend">未发货订单</a></li> 		
<?php }?>

    </ul>

    <ul class="left_date" id="activity">

        <li class="head">活动管理</li>

        <?php if($qx['activity/categoryAdd']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/activity/categoryAdd">新增分类</a></li>
        <?php }?>
        <?php if($qx['activity/category']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/activity/category">活动分类</a></li>
        <?php }?>
        <?php if($qx['act_attr/lists']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/act_attr/lists">筛选属性</a></li>
        <?php }?>
        <?php if($qx['activity/add']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/activity/add">发布活动</a></li>
        <?php }?>
        <?php if($qx['activity/lists']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/activity/lists">活动列表</a></li>
        <?php }?>

        <li class="head">订单管理</li>

        <?php if($qx['act_order/lists']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/act_order/lists">订单列表</a></li>
        <?php }?>

        <?php if($qx['activity/signList']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/activity/signList">报名列表</a></li>
        <?php }?>

    </ul>

     <ul class="left_date" id="user">   

     	<li class="head">用户管理</li>



<?php
    if($qx['member/lists']!=1 || $info['gl']=='0'){
?>
        <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/member/member/lists">会员列表</a></li> 	
<?php }?>


<?php
    if($qx['member/select']!=1 || $info['gl']=='0'){
?>
	<li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/member/member/select">查找会员</a></li> 	
<?php }?>


<?php
    if($qx['member/insert']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/member/member/insert">添加会员</a></li> 	
<?php }?>


<?php
    if($qx['member/config']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/member/member/config">会员配置</a></li>
<?php }?>


<?php
    if($qx['member/pay_list']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/member/member/pay_list">消费记录</a></li>
<?php }?>

    </ul>

    <ul class="left_date" id="template">   

     	<li class="head">界面管理</li>


        <?php
        if($qx['wap/init']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wap">轮播图管理</a></li>
        <?php }?>

        <?php
/*        if($qx['wap/wap_background_list']!=1 || $info['gl']=='0'){
            */?><!--
            <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/wap/background_list">背景管理</a></li>
        --><?php /*}*/?>

        <?php
        if($qx['qqlogin/qq_set_config']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo WEB_PATH; ?>/api/wxlogin/wx_set_config">登陆设置</a></li>
        <?php }?>

        <?php
        if($qx['declare_activity/lists']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/declare_activity/lists">公告活动</a></li>
        <?php }?>

        <li class="head">地图管理</li>

        <?php
        if($qx['map/configList']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/map/configList">应用管理</a></li>
        <?php }?>
        <?php
        if($qx['map/configAddEdit']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/map/configAddEdit">创建应用(key)</a></li>
        <?php }?>
        <?php
        if($qx['map/markerLists']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/map/markerLists">标记管理</a></li>
        <?php }?>
        <?php
        if($qx['map/addEditMarker']!=1 || $info['gl']=='0'){
            ?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/map/addEditMarker">添加标记</a></li>
        <?php }?>


        <!--<li class="head">模板风格</li>
		



<?php
/*    if($qx['template/init']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/template/">模板设置</a></li>
<?php /*}*/?>


<?php
/*    if($qx['template/see']!=1 || $info['gl']=='0'){
*/?>
        <li><span></span><a href="javascript:void(0);" src="<?php /*echo G_MODULE_PATH; */?>/template/see">查看模板</a></li>
--><?php /*}*/?>





<?php
    if($qx['index/map']!=1 || $info['gl']=='0'){
?>
        <li class="head">后台界面</li>

        <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/index/map">后台地图</a></li>   
<?php }?>



        

    </ul>

         <ul class="left_date" id="wechat">

            <li class="head">微信基本设置</li>



<?php
    if($qx['wechat/wechatcfg']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wechat/wechatcfg">微信接口</a></li> 
<?php }?>


<?php
    if($qx['wechat/cfg']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wechat/cfg">微信设置</a></li> 
<?php }?>


<?php
    if($qx['wechat/menu']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wechat/menu">微信菜单</a></li> 
<?php }?>


<?php
    if($qx['wechat/reply']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wechat/reply">关注回复内容</a></li>
<?php }?>


<?php
    if($qx['wechat/keywordlists']!=1 || $info['gl']=='0'){
?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/wechat/keywordlists">关键词自动回复</a></li>
<?php }?>


        </ul>

    <ul class="left_date" id="lottery">

        <li class="head">抽奖管理</li>

        <?php if($qx['lottery/prizeAdd']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/lottery/prizeAdd">奖项设置</a></li>
        <?php }?>
        <?php if($qx['lottery/prizeList']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/lottery/prizeList">奖项列表</a></li>
        <?php }?>
        <?php if($qx['lottery/lotteryList']!=1 || $info['gl']=='0'){?>
            <li><span></span><a href="javascript:void(0);" src="<?php echo G_MODULE_PATH; ?>/lottery/lotteryList">中奖列表</a></li>
        <?php }?>

    </ul>


 <div style="padding:30px 10px; color:#676767">
     	<p>
        	© 2017 北京拼团户外
        </p>
     </div>
</div><!--left end-->

<div id="right">

	<div class="right_top">

    	<ul class="R_label" id="R_label">

        	当前位置: 系统设置 >  后台主页 >

        </ul>

    	<ul class="R_btn">
	<a href="javascript:;" onClick="btn_map('<?php echo G_MODULE_PATH; ?>/index/Tdefault');" class="system_button"><span>后台首页</span></a>
    	<a href="javascript:;" onClick="btn_iframef5();" class="system_button"><span>刷新框架</span></a>

     <!--  <a href="javascript:;" onClick="btn_delahche('<?php echo G_MODULE_PATH; ?>/cache/init');" class="system_button"><span>清空缓存</span></a>-->
            <a href="javascript:;" onClick="btn_map('<?php echo G_MODULE_PATH; ?>/index/map');" class="system_button"><span>后台地图</span></a>

        </ul>

    </div>

    <div class="right_left">

    	<a href="#" val="open" title="全屏" id="off_on">全屏</a>

    </div>

    <div id="right_iframe">

        

         <iframe id="iframe_src" name="iframe" class="iframe"

         frameborder="no" border="1" marginwidth="0" marginheight="0" 

         src="" 

         scrolling="auto" allowtransparency="yes" style="width:100%; height:100%">

         </iframe>

    </div>

</div><!--right end-->

</body>

</html>