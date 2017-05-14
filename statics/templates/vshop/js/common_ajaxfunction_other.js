/*
 * Description : 所有商品接口 请求函数
 * Author : gaoxiaopeng@ddtkj.com
 * Time : 2015年 9月21日
 *   */
/*
 * 方法描述：获取当前基金
 * 参数1： gid Int 商品I的
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 * 返回值主体：goods
 */
function ajaxFund(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:'/footer/now.do',
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：获取搜索热词
 * 返回值：Json格式   往期揭晓集合
 * 返回值主体：words
 */
function ajaxHotWord(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/goods/hotwords.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：获取注册人数
 * 返回值：Json格式   往期揭晓集合
 * 返回值主体：total
 */
function ajaxRegisterTotal(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/footer/total.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}




/*
 * 方法描述：获取登录会员信息
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxMemberInfo(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/getMemberInfo.do",
		data:{
			time:new Date().getTime()
		},
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：获取会员签到信息
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxGetSign(callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/member/getSign.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：会员签到
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxSign(signToken,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		url:"/member/sign.do?signToken="+signToken,
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：校验购买商品
 * 参数：buyPeriod Int 购买期数
 * 参数：times Int 购买次数
 * 参数：gid String 商品Id
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   会员信息
 * 返回值主体：total
 */
function ajaxCheck(buyPeriod,times,gid,i,callbackFun) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		data:{
			buyPeriod:buyPeriod,
			times:times,
			gid:gid
		},
		url:"/cart/check.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result,i);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 描述：加载购物车
 * 
 * */
function ajaxloadCart(callbackFun){
	var cart = JSON.stringify(eval(localStorage.getItem("cart")))
	if(cart=="null"){
		cart='';
	}
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		data:{
			cart:cart
		},
		url:"/cart/select.do?randomTime=" + Math.random(),
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 描述：加载个人中心广告
 * 
 * */
function ajaxloadMemberCenterAds(callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url: "/cms/getMemberCenterAdList.do",
		success: function(result) {
			//调用绑定页面数据方法
			callbackFun(result);//调用回调函数 传输json结果
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}