/*
 * Description : 所有商品接口 请求函数
 * Author : gaoxiaopeng@ddtkj.com
 * Time : 2015年 6月10日
 *   */
/*
 * 方法描述：获取分类
 * 参数1： gid Int 商品I的
 * 参数2：callbackFun 回调函数
 * 返回值：Json格式   往期揭晓集合
 * 返回值主体：goods
 */
function ajaxGoodsList(cid,callbackFun,isshow,i) {
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/goods/goods4.do",
		data: {
			cid:cid,size:4
		},
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result,cid,isshow,i);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
			setTimeout(function(){
				ajaxGoodsList(cid,callbackFun,isshow,i)
			},1000)//定时一秒后重新执行错误请求
		} //错误处理
	});
}

/*
 * 方法描述：获取首页分类楼层 
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：categorys
 */
function ajaxCategoryIndex(callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url: "/goods/categoryIndex.do",
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
 * 方法描述：获取获取正在揭晓的商品 N条
 * 作用：1、初始化获取N条最新揭晓商品
 *     2、（用于定时动态追加到揭晓列表顶部）
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：notices
 */
function ajaxPublishBeingList(size,callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data: {
			size: size
		},
		url:"/goods/jiexiaoNew.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result);    //调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 方法描述：获取已揭晓商品 
 * 参数：size int 展示条数
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：pageModel.dataList
 */
function ajaxPublishResultList(size,callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data: {
			size: size
		},
		url:"/goods/jiexiaoIndex.do",
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(result,size);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}



/*
 * 方法描述：获取网站最新公共
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：notices
 */
function ajaxNoticesList(callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/notices.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
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
 * 方法描述：获取晒列表top 6
 * 参数：page Int 页面（首页只取1）
 * 参数：size Int 页码 
 * 参数：indexShow string  值为 sd
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：showsPage
 */
function ajaxExposeList(page,size,indexShow,callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:{
			page:page,
			size:size,
			indexShow:indexShow
		},
		url:"/other/allshow.do",
		beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
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
 * 方法描述：获取网站首页广告位
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：ads
 */
function ajaxAdvertisementList(callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		url:"/cms/ads.do",
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
 * 方法描述：获取获取最新揭晓的商品（用于定时动态追加到揭晓列表顶部）
 * 参数：gid Int 商品Id
 * 参数：pid Int 期数Id
 * 参数：publishResidueTimes  揭晓失败后的剩余揭晓次数
 * 参数：callbackFun 回调函数
 * 返回值：Json格式   
 * 返回值主体：goods
 */
function ajaxPublishResultDetail(gid,pid,publishResidueTimes,callbackFun){
	$.ajax({
		type: "post",
		timeout:timeout,
		dataType: "json",
		cache: true,
		data:{
			gid:gid,
			pid:pid
		},
		url:'/goods/querygoods.do',
		success: function(result) {
			//调用绑定页面数据方法
			if (verification(result)) { //验证result 是否有效
				callbackFun(gid,pid,publishResidueTimes,result);//调用回调函数 传输json结果
			}
		},
		error: function(r) {
			handlingException(r);
		} //错误处理
	});
}

/*
 * 描述：使用jsonp跨域访问论坛新闻
 * 参数 callbackFun  回调函数
 * 返回值：无
 * */
function ajaxNewsList(callbackFun) {
	$.ajax({  
        type : "get",  
        url : "http://bbs.ygqq.com/java.php",  
        dataType : "jsonp",//数据类型为jsonp  
        jsonp: "jsonpCallback",//服务端用于接收callback调用的function名的参数  
        beforeSend: ajaxBeforeSendOfList,
		complete: ajaxCompleteOfList,
        success : function(data){
    		callbackFun(data);
        },  
        error:function(){  
          
        }  
    });
}


/*
 * 描述：事例  获取单个信息  beforeSend \complete
 * 参数 callbackFun  回调函数
 * 返回值：无
 * */
function  getMemberInfo(uid,callbackFun){
	
	$.ajax({  
        type : "get",  
        url : "http://bbs.ygqq.com/java.php",  
        dataType : "jsonp",//数据类型为jsonp  
        jsonp: "jsonpCallback",//服务端用于接收callback调用的function名的参数  
        beforeSend: ajaxBeforeSendOfDetail,
    	complete: ajaxCompleteOfDetail,
        success : function(data){
    		callbackFun(data);
        },  
        error:function(){  
          
        }  
    });
}

/*描述：更具商品ID和期数获取商品详情
 * 参数  gid 商品id pid商品期数 callbackFun回调函数
 * 返回值 商品信息
 * 
 */

function ajaxGoodsDetailInfo(gid,pid,callbackFun){
	$.ajax({
		url:'/goods/querygoods.do?t='+ Math.random(),
		type:'post',
		dataType:"json",
		async: false,
		data:{
			gid:gid,
			pid:pid
		},
		 success : function(data){
	    		callbackFun(data);
	        },  
	        error:function(){  
	        	handlingException(r);
	        } 
	})
}
/*描述：更具商品ID获取详情页面包屑数据
 * 参数  gid 商品id  callbackFun回调函数
 * 返回值 商品信息
 * 
 */

	function ajaxGoodsDetailCB(gid,callbackFun){
		$.ajax({
			url:'/goods/getGoodsCB.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid
			},
			 success : function(data){
		    		callbackFun(data);
		        },  
		        error:function(){  
		        	handlingException(r);
		        } 
		})
	}
	/*描述：更具商品ID和期数获取揭晓信息
	 * 参数  gid 商品id pid 商品期数  callbackFun回调函数
	 * 返回值 商品信息
	 * 
	 */
	
	function ajaxGoodsDetailPublish(gid,pid,callbackFun){
		$.ajax({
			url:'/goods/querygoods.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid,
				pid:pid
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/*描述：更具商品ID获取当前商品有关的最近的云购记录
	 * 参数  gid 商品id size 数量  callbackFun回调函数
	 * 返回值 商品信息
	 * 
	 */
	
	function ajaxGoodsListWinGoods(gid,size,callbackFun){
		$.ajax({
			url:'/goods/winGoods.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid,
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/*描述：根据条件获取商品列表
	 * 参数：param{
	 * 			size:行数，page：页数，cid：栏目ＩＤ，bid：品牌ID，q：关键字，
	 * 			order：排序方式（即将揭晓：publicTime，剩余人次：takedout 人气：periods 最新商品：addTime 价格：totalPrice_up\totalPrice_down）
	 * 			type：价格专区(几元区就传几比如10元就传10)
	 * 			baoyuan：包圆(bao_yuan)
	 * 			maxBuy：限购(maxbuy)			
	 * }
	 * 数量 callbackFun回调函数
	 * 返回值 商品列表
	 * 
	 */
	
	function ajaxGoodsListTopGoods(param,callbackFun){
		$.ajax({
			url:'/goods/getGoods.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:param,
			success : function(data){
				callbackFun(data,param);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/*描述：更具用户ID 商品ID 期数 获取云购码
	 * 参数：mid会员ID gid商品iD pid商品期数 callbackFun回调函数
	 * 返回值 json云购码
	 */
	function ajaxShowYgCodes(mid,gid,pid,callbackFun){
		$.ajax({
			url:'/goods/querycodes.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				mid:mid,
	 			gid:gid,
	 			pid:pid
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	
	/*
	 * 描述：更具 商品ID 期数 获取当前用户云购码
	 * 参数： gid商品iD pid商品期数 callbackFun回调函数
	 * 返回值 json云购码
	 */
	function ajaxShowMyYgCodes(gid,pid,callbackFun){
		$.ajax({
			url:'/goods/querycodes.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid,
				pid:pid
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 描述：获取一级分类
	 * 
	 * @param callbackFun 回调函数
	 */
	function ajaxGetCategoryOne(callbackFun){
		$.ajax({
			url:'/goods/categoryIndex.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 更具父类栏目ID获取栏目列表
	 * @param param 参数{id:栏目ＩＤ}
	 * @param callbackFun 回调函数
	 */
	function ajaxAllCategoryByFid(param,callbackFun){
		$.ajax({
			url:'/goods/getAllCategory.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:param,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 获取所有品牌
	 * @param param 参数{id:栏目ID}
	 * @param callbackFun 回调函数
	 */
	function ajaxGetAllBrand(param,callbackFun){
		$.ajax({
			url:'/goods/getAllBrand.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:param,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 根据栏目ID获取栏目详情
	 * @param id 栏目ＩＤ
	 * @param callbackFun　回调函数
	 */
	function ajaxCategoryById(id,callbackFun){
		$.ajax({
			url:'/goods/getCategory.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				cid:id
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 更具商品分页查询 购买记录
	 * @param gid 商品ＩＤ
	 * @param pid　商品期数
	 * @param size 行数
	 * @param page 页数
	 * @param callbackFun 回调函数
	 */
	function ajaxYgBuyList(gid,pid,size,page,callbackFun){
		$.ajax({
			url:'/goods/timeline.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid,
				pid:pid,
				size:size,
				page:page
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 根据商品分页获取晒单列表
	 * @param gid
	 * @param size
	 * @param page
	 * @param callbackFun
	 */
	function ajaxYGShowListByGid(gid,size,page,callbackFun){
		$.ajax({
			url:'/goods/shows.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				gid:gid,
				size:size,
				page:page
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 根据条件查询揭晓商品列表
	 * @param jsdCard 是否是晋商贷卡 1是 0 不是
	 * @param size 行数
	 * @param page 页数
	 * @param flag 是否查询待揭晓（揭晓列表使用）其他地方调用的使用请传null
	 * @param callbackFun 回调函数
	 */
	function ajaxQueryJieXiao(jsdCard,size,page,flag,callbackFun){
		$.ajax({
			url:'/goods/jiexiaoQuery.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				jsdCard:jsdCard,
				size:size,
				page:page
			},
			success : function(data){
				callbackFun(data,flag);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 根据条件查询揭晓商品列表
	 * @param jsdCard 是否是晋商贷卡 1是 0 不是
	 * @param size 行数
	 * @param page 页数
	 * @param flag 是否查询待揭晓（揭晓列表使用）其他地方调用的使用请传null
	 * @param callbackFun 回调函数
	 */
	function ajaxPublishWait(jsdCard,size,page,flag,callbackFun){
		$.ajax({
			url:'/goods/publishwait.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				jsdCard:jsdCard,
				size:size,
				page:page
			},
			success : function(data){
				callbackFun(data,flag);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 分页获取晒单列表
	 * @param size 行数
	 * @param page 页数
	 * @param callbackFun 回调函数
	 */
	function ajaxShowList(size,page,callbackFun){
		$.ajax({
			url:'/other/allshow.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				size:size,
				page:page
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 免税商品列表页获取免税商品列表
	 * @param callbackFun
	 */
	function ajaxDFGoodsList(callbackFun){
		$.ajax({
			url:'/free/list.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 根据免税商品ID 获取免税商品详情
	 * @param id 商品ID
	 * @param callbackFun 回调函数
	 */
	function ajaxDFGoodsDetail(id,callbackFun){
		$.ajax({
			url:'/free/getDetail.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			data:{
				id:id,
			},
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 获取首页幻灯片列表
	 */
	function ajaxCategorySlideList(callbackFun){
		$.ajax({
			url:'/categorySlide.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
	/**
	 * 获取头部导航栏数据
	 * @param callbackFun
	 */
	function ajaxCmsNavigation(callbackFun){
		$.ajax({
			url:'/cms/navigation.do?t='+ Math.random(),
			type:'post',
			dataType:"json",
			async: false,
			success : function(data){
				callbackFun(data);
			},  
			error:function(){  
				handlingException(r);
			} 
		})
	}
