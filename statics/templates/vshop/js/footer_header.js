var bd_brand = "";// 百度采集品牌
var bd_class1 = "";// 百度采集第一分类
var bd_class2 = "";// 百度采集第二分类
var bd_word = "";// 百度采集关键字
// 加载购物车信息
var row = 0;
var failure = new Array();
var showCart = true;

// 页面初始化
$(function() {
	//右侧返回顶部事件2015-11-24
	$(window).scroll(function(){
		if($(window).scrollTop()>200){
			$(".r-fixed").css({visibility:"visible"});
		}else{
			$(".r-fixed").css({visibility:"hidden"});
		}
	})
	$("#back").click(function(){
        $(document.documentElement).animate({
            scrollTop: 0
            },200);
        //支持chrome
        $(document.body).animate({
            scrollTop: 0
            },200);

     })
	ajaxCategoryIndex(bindCategoryIndex); // 绑定分类
	ajaxMemberInfo(bindMemberInfo);// 绑定会员信息
	// 延迟一秒初始化
	ajaxRegisterTotal(bindRegisterTotal);// 绑定头部注册人数
	setInterval(function() {
		ajaxRegisterTotal(bindRegisterTotal);// 绑定头部注册人数
	}, 5000);
	setTimeout(function() {
		ajaxHotWord(bindHotWord);// 获取热点关键词
		ajaxFund(bindFund);// 绑定基金信息
	}, 1000)

	// 默认Banner
	$($(".yBannerList")[7]).addClass("ybannerExposure");
	actionTopBar();// 鼠标事件，页面最顶部 便捷帮助条

	actionNavPullDown();// 鼠标停留到菜单，则弹出 移走后 隐藏
	actionNavFixed();// 悬浮菜单 页面向下滑动后超过 导航菜单 高度，则导航菜单漂浮至页面顶部

	cartCount();// 购物车统计数量
	actionCartEmpty();// 购物车为空时 鼠标漂浮事件

	actionScrollTop();// 点击置顶方法
	actionWindowResize();// 浏览器窗口大小调整时动态修改弹窗位置及 其他相关 元素的 宽高

	actionWXHover();// 鼠标移动到右侧工具条的微信 li 上展示 微信二维码
	ajaxCmsNavigation(bindNavigation);//加载导航栏信息
	$(window).scroll(function() {
		// $(".Left-fixed-divs").css("width","43px");
		// 2015-08-18 fanbin 首页滚动条在顶部时，不现实右侧栏
		if ($(window).scrollTop() > 0) {
			$(".Left-fixed-divs").fadeIn(600);
		} else {
			$(".Left-fixed-divs").fadeOut(600);
		}
	})
})
/**
 * 绑定导航栏信息回调函数
 * @param result
 */
function bindNavigation(result){
    		
    	if(result.status){
    		var list = result.list;
    		$(list).each(function(index,Navigation){
    			if(Navigation.child.length==0){
    				if(index==0){
    					if(Navigation.openOther==1){
    				$(".yMenuIndex").append('<li><a href="'+Navigation.url+'" class="yMenua" target="_blank">'+Navigation.title+'</a><em>/</em></li>');
    					}else{
    				$(".yMenuIndex").append('<li><a href="'+Navigation.url+'" class="yMenua">'+Navigation.title+'</a><em>/</em></li>');
    					}
    					}else{
    						if(Navigation.openOther==1){
    					$(".yMenuIndex").append('<li><a href="'+Navigation.url+'" target="_blank">'+Navigation.title+'</a><em>/</em></li>');
    						}else{
    					$(".yMenuIndex").append('<li><a href="'+Navigation.url+'" >'+Navigation.title+'</a><em>/</em></li>');
    						}
    				}
    			}else{
    				var yMenuhtml='';
    				yMenuhtml+='<li class="hide-menu-nav" style="padding: 0 13px 0px 15px;"><span></span>';
    				yMenuhtml+='<a href="'+Navigation.url+'" class="hide-menu-nava">'+Navigation.title+'</a>';
    				yMenuhtml+='<dl>';
    				$(Navigation.child).each(function(index,child){
    					if(index==0){
    						if(child.openOther==1){
    				yMenuhtml+='<dd><a href="'+child.url+'" target="_blank">'+child.title+'</a></dd>';
    						}else{
    				yMenuhtml+='<dd><a href="'+child.url+'" >'+child.title+'</a></dd>';
    						}
    					}else{
    						if(child.openOther==1){
    		    	yMenuhtml+='<dd style="border-top:1px solid #ddd;"><a href="'+child.url+'" target="_blank">'+child.title+'</a></dd>';
    		    			}else{
    		    	yMenuhtml+='<dd style="border-top:1px solid #ddd;"><a href="'+child.url+'" >'+child.title+'</a></dd>';
    		    			}
    					}
    				})
    				yMenuhtml+='</dl>';
    				yMenuhtml+='</li>';
    				$(".yMenuIndex").append(yMenuhtml);
    			}
    		})
    	}
    }
/*
 * 描述：购物车为空时 事件类型：鼠标停留事件 位置及元素：页面右侧 购物车
 */
function actionCartEmpty() {
	// 购物车无数据时的js效果
	$(".shoppingCartNone").hover(function() {
		$(".shoppingCartNoneCon").show();
	}, function() {
		$(".shoppingCartNoneCon").hide();
	})

}

/*
 * 描述：页面最顶部 便捷帮助条 事件类型：鼠标停留事件 位置及元素：页面最顶部
 */
function actionTopBar() {
	// 我的夺宝全球
	$(".header1 ul li.MyzhLi").hover(function() {
		$(".header1 ul li.MyzhLi .Myzh").show();
		$(".MyzhLi a i").removeClass("top");
		$(".MyzhLi a i").addClass("bottom");
	}, function() {
		$(".header1 ul li.MyzhLi .Myzh").hide();
		$(".MyzhLi a i").removeClass("bottom");
		$(".MyzhLi a i").addClass("top");
	})
}

/*
 * 描述： 鼠标停留到菜单，则弹出 移走后 隐藏 事件类型：鼠标停留事件 位置及元素：导航菜单栏 所有商品分类
 */
function actionNavPullDown() {
	var index = $("#index").html();
	if (index == null) {
		$(".pullDown").hover(function() {
			$(".pullDownList").show();
		}, function() {
			$(".pullDownList").hide();
		});
	}
}

/*
 * 描述：顶部点击按钮搜索 事件类型：鼠标点击或 回车事件
 */
function actionHeaderSearch() {

	bd_word = $("#q").val(); // 百度采集关键字
	bd_brand = "";// 百度采集品牌
	bd_class1 = "";// 百度采集第一分类
	bd_class2 = "";// 百度采集第二分类
	baiduTag_search();// 百度搜索行为采集
	location.href = '/goods/allCat.do?q=' + $("#q").val();
}

// 按回车键搜索
$(document).keydown(function(event) {
	if (event.keyCode == 13) {
		// 如果输入不为零,则生效。
		if ($("#q").val() == "" || $("#q").val() == null) {
		} else {
			actionHeaderSearch();
		}
	}
});

/*
 * 描述：1、搜索框获取焦点后样式 2、搜索框失去焦点后样式 3、搜索框回车键按下
 */
function actionSearchEvent() {
	// 搜索框获取焦点后样式
	$(".search_header2 input").focus(function() {
		$(this).css({
			color : "#333"
		});
	})
	// 搜索框失去焦点后样式
	$(".search_header2 input").blur(function() {
		$(this).css({
			color : "#a9a9a9"
		});
	})
	// 搜索框回车键按下
	$('.search_header2 input').bind('keypress', function(event) {
		if (event.keyCode == "13") {
			location.href = '/goods/allCat.do?q=' + $("#q").val();
		}
	});

}

/*
 * 描述：页面向下滑动后超过 导航菜单 高度，则导航菜单漂浮至页面顶部
 * 
 */
function actionNavFixed() {
	if ($(".yNavIndexOut_fixed").offset()) {

		var s_top = $(".yNavIndexOut_fixed").offset().top;
		$(window).scroll(function() {
			if ($(window).scrollTop() > s_top) {
				$(".header_fixed").css({
					marginBottom : "46px"
				});
				$(".yNavIndexOut_fixed").addClass("yNavIndexOutfixed");
			} else {
				$(".yNavIndexOut_fixed").removeClass("yNavIndexOutfixed");
				$(".header_fixed").css({
					marginBottom : "0px"
				});
			}
		})
	}
}

/*
 * 描述：1、 2、浏览器窗口大小调整时动态修改弹窗位置及 其他相关 元素的 宽高
 * 
 */
function actionWindowResize() {
	$(window).resize(function() {
		$(".yungouworld_wx").click(function() {
			$("#y_modalWeixin").fadeIn(500);
			$(".y_modalWeixinBJ").fadeIn(500);
			$(".y_modalWeixinBJ").css({
				width : $(window).width() + "px",
				height : $(window).height() + "px"
			});
			$("#y_modalWeixin").css({
				left : ($(window).width() - 480) / 2 + "px",
				top : ($(window).height() - 520) / 2 + "px"
			});
		})
		$(".ymodal-close").click(function() {
			$("#y_modalWeixin").css({
				top : "0px"
			});
			$("#y_modalWeixin").fadeOut(500);
			$(".y_modalWeixinBJ").fadeOut(500);
		})

		$(".Left-fixed-divs").css({
			height : $(window).height() + "px"
		});// 右侧悬浮框的位置
		$(".Left-fixed-divs2").css({
			height : $(window).height() + "px"
		});// 购物车悬浮框的位置
		$(".y-fixed-divs").css({
			left : ($(window).width() - 1210) / 2 - 60 + "px"
		});// 左侧left
		$(".Left-fixed-login").css({
			top : ($(window).height() - 425) + "px"
		});// 登陆框的位置
		$(".yNocommodity").css({
			lineHeight : $(window).height() + "px"
		});
	})

	$(window).resize();
}

/*
 * 描述：置顶
 * 
 */
function actionScrollTop() {
	$(".Left-fixed-divs .lifixTop").click(function(e) { // 置顶
		e.preventDefault();
		$(document.documentElement).animate({
			scrollTop : 0
		}, 300);
		// 支持chrome
		$(document.body).animate({
			scrollTop : 0
		}, 300);
	});

}

/*
 * 描述：鼠标移动到右侧工具条 的微信 li 上显示展示微信 二维码
 */
function actionWXHover() {
	$(".Left-fixed-divs .otherlifixw").hover(function() {
		$(this).find("img").show();

	}, function() {

		$(this).find("img").hide();

	});

}
/*
 * 描述：置底
 * 
 */
function actionScrollBottom() {

	$(document.documentElement).animate({
		scrollTop : $(document).height()
	}, 300);
	// 支持chrome
	$(document.body).animate({
		scrollTop : $(document).height()
	}, 300);
}


/*
 * 描述：左侧导航的鼠标事件
 */
function actionLeftNav() {
	// 导航左侧栏js效果 start
	$('.pullDownList li').mouseover(
			function() {
				$(this).addClass("menulihover").siblings().removeClass(
						"menulihover");
				bd_class1 = $(this).find("a").html();// 为百度统计获取第一分类 名称
			})
	$(".pullDown").mouseleave(function() {
		$(".pullDownList li").removeClass("menulihover");
	})
	// 导航左侧栏js效果 end
}

/*
 * 描述：绑定当前基金 参数：result json 当前基金信息 返回值：无
 */
function bindFund(result) {
	$('.fund').text('￥' + parseFloat(result.fund).toFixed(2));
	showLeftTime(result.now - new Date().getTime());
}

// 计算购物车数量
function cartCount() {
	var list = cookieToList();
	if (list.length > 0) {
		row = list.length;
		$("#cartCount").html(row);
		$("#cartCount").addClass("num-rt");
		$("#row").html(row);
	} else {
		$("#cartCount").html('');
	}
	showCart = true;
}

/* 数字转动 */
function showSun() {

	var attr = 0;
	attr = $(".yJoinNum input").val();

	var attr1 = [];
	var nums = 0;
	$('.yNumList').remove();
	for (i = 0; i < attr.length; i++) {
		var nums = attr.slice(i, i + 1);
		attr1.push(nums);
		$('.w_ci_bg')
				.before(
						'<span class="yNumList"><ul style="margin-top: -270px;">'
								+ '<li t="9">9</li><li t="8">8</li><li t="7">7</li><li t="6">6</li><li t="5">5</li>'
								+ '<li t="4">4</li><li t="3">3</li><li t="2">2</li><li t="1">1</li><li t="0">0</li></ul></span>');
	}
	$(".yNumList ul").css("marginTop", "-270px");
	var list = 0;
	for (i = 0; i < attr1.length; i++) {
		list = attr[i];
		$($(".yNumList ul")[i]).animate({
			marginTop : (list * 30 - 270)
		}, 1000)
	}
	if ($(".yNumList").length < attr1.length) {
		var more = attr1.length - $(".yNumList").length;
		for (i = 0; i < more; i++) {
			$($(".yNumList")[0]).clone(true).insertAfter(
					$($(".yNumList")[$(".yNumList").length - 1]))
		}
	}
}

// 时间显示
function showLeftTime(time) {
	$(".sysTime").text(formatDate(time + new Date().getTime(), "HH:mm:ss"));
	// 一秒刷新一次显示时间
	setTimeout(function() {
		showLeftTime(time);
	}, 100);
}

/*
 * 描述：绑定搜索热词 参数：result json 返回值：无
 */
function bindHotWord(result) {
	var html = '';
	words = JSON.parse(result.words);
	$(words).each(
			function(index, word) {
				html += '<a href="/goods/allCat' + word.cid + '.html">'
						+ word.word + '</a>';
			});
	$(".search_span_a").html(html);
}

/*
 * 描述：绑定导航分类 参数：无 返回值：无
 */
function bindCategoryIndex(result) {
	var lis_c = [];
	var con = [];
	var banner = [];
	$(result.categorys)
			.each(
					function(index1, category) {

						lis_c.push('<li');
						lis_c.push('><i><img src="/static/img/front/index/index_icon_nav_11'
										+ (index1 + 1) + '.png"/></i>');
						/*if (index1 == 7)
							lis_c.push('<a href="/index.do?yg_sq=true">'
									+ category.cname + '</a>');// 一级分类
						else*/
							lis_c.push('<a href="/goods/allCat' + category.id
									+ '.html" >' + category.cname + '</a>');// 一级分类
						lis_c.push('<span></span>');
						lis_c.push('</li>');
					});
	$(".pullDownList").append(lis_c.join(""));
	actionLeftNav();// 绑定左侧导航的鼠标事件
}

var signToken = "";

// 签到
function sign() {
	var signTime = $('#signTime').val();
	var memberMid = $('#mid').val();
	if (memberMid != '') {
		var date = new Date(signTime * 1000);
		signTime = date.getFullYear().toString() + (date.getMonth() + 1)
				+ date.getDate().toString() + "";
		date = new Date();
		var today = date.getFullYear().toString() + (date.getMonth() + 1)
				+ date.getDate().toString() + "";
		if (today != signTime) {
			ajaxSign(signToken, signBindData);// 会员签到
		}
	} else {
		go2Login();//登陆方法
	}
}

/*
 * 描述：会员签到 参数：result json 返回值：无
 */
function signBindData(result) {
	$("#sign").html("已签到");
	$("#memberSign").html("已连续签到" + result.days + "天");
}

/*
 * 描述：检查是否签到 参数：无 返回值：无
 */
function checkSign() {
	var signTime = $('#signTime').val();
	var date = new Date(parseInt(signTime));
	signTime = date.getFullYear().toString() + (date.getMonth() + 1)
			+ date.getDate().toString() + "";
	date = new Date();
	var today = date.getFullYear().toString() + (date.getMonth() + 1)
			+ date.getDate().toString() + "";
	if (today == signTime) {
		$("#sign").html("已签到")
		$("#memberSign").html('已连续签到' + $("#signDays").val() + '天');
	} else {
		ajaxGetSign(bindSignToken);
	}

}

/*
 * 描述：绑定Token 参数：result json 返回值：
 */
function bindSignToken(result) {
	signToken = result.signToken;
}
// 替换undefined
function replaceUndefined(data) {
	if (typeof (data) == "undefined") {
		return '';
	} else {
		return data;
	}
}
// 跳转商品页面
function gotoGoods(gid, pid) {
	window.location.href = "/goods/goods" + gid + "-" + pid + ".html";
}

/*
 * 描述：绑定会员信息 参数：result 返回值：无
 */
function bindMemberInfo(result) {
	if (result.status && result.user) {
		$('.headerul2 li:eq(1)').html(
				'<a href="/member_new/account.do">' + result.user.mobile
						+ '</a>');
		$('.headerul2 li:eq(2)').html('<a href="/member/logout.do">退出</a>');
		$("#mid").val(result.user.mid);
		$("#signTime").val(result.user.signTime);
		$("#signDays").val(result.user.signDays);
		checkSign();
	}
}

/*
 * 描述：绑定注册人数 参数：无 返回值：无
 */
function bindRegisterTotal(result) {
	$(".yJoinNum input").val(result.total);
	showSun();
}
/*
 * Author：gaoxiaopeng@ddtkj.com Time:2015-9-10 描述：百度的统计代码，用于用户点击行为
 * 用法：在页面所有，分类与品牌的点击效果上全部加上 该函数 参数1：brand //品牌名称，多个品牌以|分隔 参数2： 一级品类 参数3： 二级品类
 */
function baiduTag_search() {
	var rtTag = {
		"data" : {
			"ecom_search" : {
				"word" : bd_word, // 搜索关键词
				"p_brand" : bd_brand, // 品牌名称，多个品牌以|分隔
				"p_class1" : bd_class1, // 一级品类
				"p_class2" : bd_class2
			// 二级品类
			}
		}
	};

	_hmt.push([ '_trackRTEvent', rtTag ]);

}

// 点击选中的品牌时触发的事件
function submitBrand(cid, bid, class2, brand) {
	bd_word = "";
	bd_class2 = class2;// 为百度统计获取品牌 二级栏目
	bd_brand = brand;// 为百度统计获取品牌 名称
	baiduTag_search();
	if (bid != '') {
		location.href = '/goods/allCat' + cid + '-' + bid + '.html';
	} else {
		location.href = '/goods/allCat' + cid + '.html';
	}
}
/*跳到充值页面*/
function gotoRechange(){
	var mid = $('#mid').val();
	if(mid!=null&&mid!=''){
		window.open('/member/recharge/recharge.do'); 
	}else{
		go2Login(true);
	}
}