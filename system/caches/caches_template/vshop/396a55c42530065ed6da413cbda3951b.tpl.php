<?php defined('G_IN_SYSTEM')or exit('No permission resources.'); ?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no, maximum-scale=1.0"/>
    <title>个人中心</title>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
	<link rel="stylesheet" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/top.css">
  <link rel="stylesheet" type="text/css" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/activity.css">
    <link href="<?php echo G_TEMPLATES_CSS; ?>/mobile/comm.css?v=130715" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" media="screen,projection,tv" href="<?php echo G_TEMPLATES_CSS; ?>/mobile/header_footer.css">
    
	<script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery190.js" language="javascript" type="text/javascript"></script>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?3467e923fe63a9c0d612efb4d9309c8d";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</head>


<!-- 内页顶部 -->


<body style="background-color:#fff">
    <div class="container-fluid">
      <div class="a_account_top">
       <div class="blur-cover"></div> 
      <form id="formid" class="user-img" method="post" enctype="multipart/form-data" action="<?php echo WEB_PATH; ?>/mobile/home/changeheadimg" >
        
        <input  class='hidImgsel' type="file" name="Filedata"/>
        <img class="a_user_img" id="headPic" src="<?php echo G_UPLOAD_PATH; ?>/<?php echo get_user_key($member['uid'],'img'); ?>">
        <p class="a_account_p1">
          <em id='nickname'><?php echo get_user_name($member['uid']); ?></em>
        </p>
        <div class='a_account_data'>
          <span>余额：<?php echo $member['money']; ?>元</span>
          <span>积分：<?php echo $member['score']; ?></span>
          <span class="nobor">
            <a href="<?php echo WEB_PATH; ?>/mobile/home/userrecharge">充值</a>
          </span>
          
        </div>
      </form>
      </div>
      <ul class="a_account_ul">

        <li class="col-xs-4">
          <a class='check-order' href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist">
            装备订单
            <em>查看全部订单</em>
          </a>
          <ul class="user-order-item ">
            <li><a href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist/1"><i class='order-item-unpayed'></i>待付款<?php if(isset($orderCount[1])): ?><b class="order-item-num"><?php echo $orderCount['1']['num']; ?></b><?php endif; ?></a></li>
            <li><a href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist/2"><i class='order-item-unsend'></i>待发货<?php if(isset($orderCount[2])): ?><b class="order-item-num"><?php echo $orderCount['2']['num']; ?></b><?php endif; ?></a></li>
            <li><a href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist/3"><i class='order-item-unchecked'></i>待收货<?php if(isset($orderCount[3])): ?><b class="order-item-num"><?php echo $orderCount['3']['num']; ?></b><?php endif; ?></a></li>
          </ul>
        </li>
        <li class="col-xs-4">
          <a class='check-order' href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities">
            我的活动
            <em>查看全部活动</em>
          </a>
          <ul class="user-order-item user-act-item">
            <li><a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities/<?php echo $waiting['state']; ?>"><i class='act-item-waiting'></i>等待中<?php if(isset($waiting['num']) && !empty($waiting['num'])): ?><b class="order-item-num"><?php echo $waiting['num']; ?></b><?php endif; ?></a>
            </li>
            <li><a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities/<?php echo $execute['state']; ?>"><i class='act-item-on'></i>进行中<?php if(isset($execute['num']) && !empty($execute['num'])): ?><b class="order-item-num"><?php echo $execute['num']; ?></b><?php endif; ?></a>
            </li>
            <li><a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities/<?php echo $over['state']; ?>"><i class='act-item-done'></i>已结束<?php if(isset($over['num']) && !empty($over['num'])): ?><b class="order-item-num"><?php echo $over['num']; ?></b><?php endif; ?></a>
            </li>
            <li><a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities/<?php echo $close['state']; ?>"><i class='act-item-closed'></i>已关闭<?php if(isset($close['num']) && !empty($close['num'])): ?><b class="order-item-num"><?php echo $close['num']; ?></b><?php endif; ?></a>
            </li>

          </ul>
        </li>
        <li class="col-xs-4">
          <p class="data-edit-title">资料编辑</p>
          <ul class='data-edit-bar'>
            <li class="data-edit-items">
              <a href="<?php echo WEB_PATH; ?>/mobile/home/address">
                <i class='data-address'></i>地址管理
              </a>
            </li>
            <li class="data-edit-items">
              <a href="<?php echo WEB_PATH; ?>/mobile/user/profile">
                <i class='data-name'></i>昵称修改
              </a>
            </li>
<!--             <li class="data-edit-items noborder-r">
              <a id='changeHeadimg' href="javascript:;">
                <i class='data-headimg'></i>头像修改
              </a>
            </li> -->
            <li class="data-edit-items noborder-r">
              <a href="<?php echo WEB_PATH; ?>/mobile/user/password">
                <i class='data-password'></i>密码修改</a>
            </li>
<!--             <li class="data-edit-items noborder-b">
              <a>
                <i class='data-bg'></i>背景修改
                </a>
            </li> -->
            <!-- <li class="data-edit-items data-edit-more  noborder-b">...</li> -->
          </ul>
          
        </li>

        <a class="infos-clean" href="<?php echo WEB_PATH; ?>/mobile/user/cook_end">退出账号</a>

  
      </ul>
	  

    </div>
    <div style="height: 49px;
    "></div>
   <!--S 底部导航 -->
    <ul id="c_main_menu">
      <li id="nav_index" >
        <?php if($homePage == 1): ?>
        <a href="<?php echo WEB_PATH; ?>/mobile/activity/activityhome">
          <span class="c_index"></span>
          <b>首页</b>
        </a>
        <?php  else: ?>
        <a href="<?php echo WEB_PATH; ?>/mobile/mobile">
          <span class="c_index"></span>
          <b>首页</b>
        </a>
        <?php endif; ?>
      </li>
      <li id="nav_goods">
        <?php if($homePage == 1): ?>
        <a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities">
          <span class="a-my-activity"></span>
          <b>我的活动</b>
        </a>
        <?php  else: ?>
        <a href="<?php echo WEB_PATH; ?>/mobile/home/userbuylist">
          <span class="c_all_good"></span>
          <b>订单</b>
        </a>
        <?php endif; ?>
      </li>

      <li id="nav_member" class="c_menu_this">
        <a href="<?php echo WEB_PATH; ?>/mobile/home">
          <span class="c_new_know"></span>
          <b>个人中心</b>
        </a>
      </li>
    </ul> 
    <!--E 底部导航 -->
    
    <!--s 底部导航 拼团入口 -->
    <ul class="a-main-menu">
      <li >
        <a href="<?php echo WEB_PATH; ?>/mobile/activity/activityhome">
          <span class="a-index"></span>
          <b>首页</b>
        </a>
      </li>
      <li>
        <a href="<?php echo WEB_PATH; ?>/mobile/activity/myactivities">
          <span class="a-my-activity"></span>
          <b>我的活动</b>
        </a>
      </li>

      <li class="a-menu-this">
        <a href="<?php echo WEB_PATH; ?>/mobile/home">
          <span class="c-home"></span>
          <b>个人中心</b>
        </a>
      </li>
    </ul> 
    <!--e 底部导航 拼团入口 -->


    <script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery190.js" language="javascript" type="text/javascript"></script>
    <script src="<?php echo G_TEMPLATES_JS; ?>/mobile/jquery.cookie.js" language="javascript" type="text/javascript"></script>
    <script type="text/javascript">
      $(function(){
        var path = $.cookie('gate');
        if(path == 'act'){
          $('#c_main_menu').hide();
          $('.a_main_menu').show();
        }
      });
      
    </script>
    <script type="text/javascript">
      $(function(){
        var file=$('.hidImgsel');
        var img=$('#headPic');
        var form=$('#formid');
        file.change(function(){
         $('form[enctype="multipart/form-data"]').submit();
        });

        $('#changeHeadimg').click(function(){
          $('input[name="Filedata"]').trigger('click');
        })
      })
    </script>

<script language="javascript" type="text/javascript">
  var Path = new Object();
  Path.Skin="<?php echo G_TEMPLATES_STYLE; ?>";  
  Path.Webpath = "<?php echo WEB_PATH; ?>";
  
var Base={head:document.getElementsByTagName("head")[0]||document.documentElement,Myload:function(B,A){this.done=false;B.onload=B.onreadystatechange=function(){if(!this.done&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){this.done=true;A();B.onload=B.onreadystatechange=null;if(this.head&&B.parentNode){this.head.removeChild(B)}}}},getScript:function(A,C){var B=function(){};if(C!=undefined){B=C}var D=document.createElement("script");D.setAttribute("language","javascript");D.setAttribute("type","text/javascript");D.setAttribute("src",A);this.head.appendChild(D);this.Myload(D,B)},getStyle:function(A,B){var B=function(){};if(callBack!=undefined){B=callBack}var C=document.createElement("link");C.setAttribute("type","text/css");C.setAttribute("rel","stylesheet");C.setAttribute("href",A);this.head.appendChild(C);this.Myload(C,B)}}
function GetVerNum(){var D=new Date();return D.getFullYear().toString().substring(2,4)+'.'+(D.getMonth()+1)+'.'+D.getDate()+'.'+D.getHours()+'.'+(D.getMinutes()<10?'0':D.getMinutes().toString().substring(0,1))}
Base.getScript('<?php echo G_TEMPLATES_JS; ?>/mobile/Bottom.js');
</script>
 

</body>
</html>
