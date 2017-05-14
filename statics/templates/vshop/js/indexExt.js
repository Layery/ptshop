//首页全局变量

var modules_show=[];//页面初始化展示的子分类Id； 
var modules_hide=[];//页面初始化隐藏的子分类Id;
var timer_arr=[];//需要倒计时的商品信息
var timer_arr_timeObj=[];//已经开始的倒计时标识数组
var publishErrorCycle=5000;
var publishResidueTimes=5;
var isLoadPublishResult=true;
var publishAppendTime=30000;//30秒定时追加 待揭晓商品

/*
 * 页面加载顺序
 *1、加载分类 
 *2、加载模块及模块的子分类
 *3、顺序加载各模块初始化显示的第一分类数据
 *4、 顺序加载各模块初始化隐藏的其他分类数据
 * */

//初始化函数
$(function(){
	$(".yNavIndexOut").addClass("yNavIndexOut_fixed_index");
	$(".pullDownList").show();//下来分类展示
	ajaxPublishBeingList(5,bindPublishBeingList);//调用 ajax 请求5条最新揭晓数据（正在揭晓和已揭晓） 成功后回调 bindPublishBeingList绑定数据 
	ajaxCategoryIndex(bindModules);//获取楼层模块 成功后 回调 bindModules 绑定数据
	ajaxGoodsListTopGoods({size:8,page:1,order:"addTime"},bindGoodsListTop8);	//绑定新品上架
	ajaxNoticesList(bindNotices);//绑定公告 成功后 回调  bindNotices 绑定数据
	ajaxExposeList(1,6,"sd",bindExposeTop6);//绑定6条最新晒单 成后回调 bindExposeTop6 绑定数据
	ajaxAdvertisementList(bindAdvertisementList);//绑定广告
	ajaxNewsList(bindNewsList);//绑定官方动态
	//ajaxCategorySlideList(bindCategorySlideList);//绑定幻灯片数据
	//启动定时追加待揭晓
	setInterval(function(){
		//清除原有倒计时
		timer_arr=[];//清空  倒计时商品数组数组
		for(var n=0;n<timer_arr_timeObj.length;n++){
		    window.clearInterval(timer_arr_timeObj[n]);//关闭定时器
		}	
		
		ajaxPublishBeingList(5,bindPublishBeingList);//调用 ajax 请求5条最新揭晓数据（正在揭晓和已揭晓） 成功后回调 bindPublishBeingList绑定数据
	},publishAppendTime);
	
});


//业务逻辑函数

function bindCategorySlideList(result){ 
	var slidelist = result.slideList;
	var  slidesHtml = '';
	$(slidelist).each(function(index,slide){
		slidesHtml+='<li><div onclick="window.location=\''+slide.link+'\'" class="img" style="background:url('+imagePath+slide.image+') no-repeat center 0px;"></div></li>'
	})
	$(".slides").html(slidesHtml);
}
/*
 * 描述：绑定最新揭晓数据 如果 正在揭晓数据小于5条 则追加 已揭晓商品
 * 参数：result json 揭晓结果
 * 返回值：无
 * 
 * */
function bindPublishBeingList(result){
	var publish_html='<ul>';
	var nowTime=result.now;//系统当前时间
	$(result.goods).each(function(index,goods){
	    var href='/goods/goods'+goods.gid+'-'+goods.periods+'.html';
		var imgSrc=imageGoodsPath+goods.showImages.split(',')[0]+imgsize;
		
		var expectPublishTime=goods.expectPublishTime;//预计揭晓时间
		var remainingTime=goods.expectPublishTime-nowTime;//动画时间=预计揭晓时间  - 当前时间
		//添加商品至倒计时数组中
		var tag=goods.gid+"_"+goods.periods;
		var item = new Object();
		item.remainingTime = remainingTime;
		item.gid = goods.gid;
		item.period = goods.periods;
		item.tag=tag;
		timer_arr.push(item);
		
		publish_html+='<li class="yTimesLi" id="publish_'+tag+'">';
		publish_html+='<dl class="yTimesDl">';
		publish_html+='<dd class="yddImg">';
		publish_html+='<a href="'+href+'" target="_blank" >';
		publish_html+='<img data-original="'+imgSrc+'" class="lazy_publish" style="height:188px;width:188px;" >';
		publish_html+='</a>';
		publish_html+='</dd>';
		publish_html+='<dd class="yddName">';
		publish_html+='<a href="'+href+'" target="_blank">(第'+goods.periods+'期)'+goods.title+'</a>';
		publish_html+='</dd>';
		publish_html+='<dd class="yGray">价值  <span>￥'+goods.goodsPrice+'</span></dd>';
		publish_html+='<dd class="yTimes" id="yTimes_'+tag+'">';
		publish_html+='<p>';
		publish_html+='<b id="hh_0_'+tag+'">0</b><b id="hh_1_'+tag+'">0</b><span>:</span>';
		publish_html+='<b id="mm_0_'+tag+'">0</b><b id="mm_1_'+tag+'">0</b><span>:</span>';
		publish_html+='<b id="ss_0_'+tag+'">0</b><b id="ss_1_'+tag+'">0</b>';
		publish_html+='</p>';
		publish_html+='</dd>';
		publish_html+='</dl>';
		publish_html+='<strong></strong>';
		publish_html+='</li>';
	});
	publish_html+='</ul>';
	if(result.goods.length!=0){//存在最新揭晓 
		$("#publishList").html(publish_html);
		//启动倒计时
		for(var n=0;n<timer_arr.length;n++){
		    var item=timer_arr[n];
			timer(item.remainingTime,item.gid,item.period,item.tag);
		}	
	}
	//如果正在揭晓 数据小于5条，则调用 已揭晓数据不全5条
	var p_length = result.goods.length;
	if(p_length<5){
			ajaxPublishResultList((5-p_length),bindPublishResultList);
	}else{
		isLoadPublishResult=false;
		lazyload("_publish");//如果待揭晓超过5条；则直接启动待揭晓图片延迟加载
	}	
}



/*
 * 描述：绑定已揭晓商品  如果正在揭晓不够5条 则调用该函数 获取 5- 正在揭晓条数
 * 参数：result json 揭晓结果
 * 返回值：无
 * */
function bindPublishResultList(result,len){
	var publish_html='';
	$(result.pageModel.dataList).each(function(y,goods){
		if(y<len){
			var href='/goods/goods'+goods.gid+'-'+goods.period+'.html';
			var imgSrc=imageGoodsPath+goods.showImages.split(',')[0]+imgsize;
			var persionUrl='/other/recordBuy/'+goods.userId+'.html';
			publish_html+='<li>';
			publish_html+='<dl>';
			publish_html+='<dd class="yddImg"><a href="'+href+'" target="_blank" >';
			publish_html+='<img data-original="'+imgSrc+'" class="lazy_publish" style="height:188px;width:188px;">';
			publish_html+='</a></dd>';
			publish_html+='<dd class="yddName">恭喜 <a href="'+persionUrl+'" class="yddNameas" target="_blank" >'+goods.userNickname+'</a> 获得</dd>';
			publish_html+='<dd class="yGray"><a href="'+href+'" target="_blank">(第'+goods.period+'期)'+goods.title+'</a></dd>';
			publish_html+='<dd class="yGray">幸运号码：'+goods.userWinCode+'</dd>';
			publish_html+='</dl>';
			publish_html+='<i></i>';
			publish_html+='</li>';	
		}
	});
	publish_html+='';	
	if(len==5){
		$("#publishList").html("<ul>"+publish_html+"</ul>");//如果请求5条 已揭晓商品 则动画图片还在，需要直接覆盖
		isLoadPublishResult=false;
	}else{
		$("#publishList").find("ul").append(publish_html);//如果请求小于5条已揭晓 则需要追加
		isLoadPublishResult=false; //取消循环加载
	}
	lazyload("_publish");//启动 图片延迟加载
	if((result.pageModel.dataList.length)<len){
		var lessNum=len-result.pageModel.dataList.length;
		for(var i=0; i<lessNum;i++){
		$("#publishList").find("ul").append('<li style="background:url(\'/static/img/front/goods/expect_publish.jpg\') no-repeat center 0;" ></li>');
		}
	}
	if(isLoadPublishResult){ //成功绑定一次后 不会再次请求
		//启动循环加载，防止 第一次加载失败后，留空白 
	    setTimeout(function(){
	    	ajaxPublishResultList(len,bindPublishResultList);
	    },100);
	}
}

/*
 * 描述：根据商品ID 期数 获取商品中奖信息
 * 参数：gid Int 商品Id
 * 参数：period Int 商品期数
 * 返回值：无
 * */
function getPublishResultDetail(gid,period){
	ajaxPublishResultDetail(gid,period,bindPublishResultDetail);
}

/*
 * 描述：商品商品ID 期数绑定到对应的揭晓商品上
 * 参数：gid Int 商品Id
 * 参数：pid Int 商品期数
 * 参数：publishResidueTimes Int 揭晓剩余次数
 * 参数：result json Ajax请求的揭晓结果
 * 返回值:无
 * */
function bindPublishResultDetail(gid,pid,index,result){
	var goods = result.goods;
	var p = goods.period || goods.periodCurrent;
	if(p == pid && goods.userWinCode){//返回来的商品详情期数不对应或没有云购码则揭晓失败
		var href='/goods/goods'+goods.gid+'-'+goods.period+'.html';
		var imgSrc=imageGoodsPath+goods.showImages.split(',')[0]+imgsize;
		var html='<dl>';
		html+='<dd class="yddImg"><a href="'+href+'" target="_blank">';
		html+='<img src="'+imgSrc+'"></a></dd>';
		html+='<dd class="yddName">恭喜 <a href="/other/recordBuy/'+goods.userId+'.html" class="yddNameas">'+goods.userNickName+'</a> 获得</dd>';
		html+='<dd class="yGray"><a href="'+href+'" target="_blank" title="'+goods.title+'">(第'+goods.period+'期)'+goods.title+'</a></dd>';
		html+='<dd class="yGray">幸运号码：'+goods.userWinCode+'</dd>';
		html+='</dl><i></i>';
		if(typeof(goods.userNickName)=="undefined"){
			ajaxPublishResultDetail(gid,pid,index,bindPublishResultDetail);
			return;
		}
		$("#publish_"+gid+"_"+pid).html(html);
	}else{
		index--;//揭晓失败后将 减少揭晓次数 为0 是 则不会进行下次揭晓
	     if(index<0){
	    	 $("#yTimes_"+gid+"_"+pid).html('<p class="w_waiting">福彩中心通讯故障或福彩中心未开奖~请耐心等待</p>');
	     }else{
	    	 setTimeout(function(){
	    		 ajaxPublishResultDetail(gid,pid,index,bindPublishResultDetail);
			 },publishErrorCycle);
	     }
	}
}
/*
 * 描述：绑定楼层数据
 * 参数：result json  楼层数据
 * 
 * */
function bindModules(result){
	var leftNav = [];
	$(result.categorys).each(function(i,category){
		// 只加载到 7F，因为 8F 的 “抢购社区” 要做特殊处理
		if (i > 8) return;
		var module_index=i+1;//楼层数 = i+1
	    //绑定模块名称及跳转地址
		var module_href='/goods/allCat'+category.id+'.html';//楼层模块的跳转地址
		$("#module_"+module_index+"_name").attr("href",module_href).html(category.cname);//绑定模块名称及跳转地址
		$("#module_"+module_index+"_more").attr("href",module_href);//绑定模块更多跳转地址
		//style="background-position: -17px -'+(32+62*i)+'px;"
		//leftNav.push('<li><i ><img src="/static/img/front/index/index_icon_nav_0'+(i+1)+'.png"/></i><em>'+category.cname+'</em><b></b></li>');
		leftNav.push('<li>'+
 			'<i style="background-position: -21px -'+(25.5+20*i)+'px;"></i>'+
 			'<em>'+category.cname+'</em>'+
 			'<b></b>'+
 		'</li>');
		//绑定模块的字分类
		var child_nav='';
		var child_html='';
		modules_show.push(category.id);
		if(child_nav!=""){
		child_nav+='<span></span>';
		}
		$("#module_"+module_index+"_child_nav").html(child_nav);//绑定模块的二级分类到页面 楼层数 = i+1
		$("#module_"+module_index+"_child_html").append(child_html);//绑定模块的二级分类到页面 楼层数 = i+1
	});
	//最新上架
	var newGoodsHtml="";
	
	newGoodsHtml+='<li>';
	newGoodsHtml+='<i style="background-position: -21px -186px;"></i>';
	newGoodsHtml+='<em>新品上架</em>';
	newGoodsHtml+='<b></b>';
	newGoodsHtml+='</li>';
	$(".y-fixed-divs").html(leftNav.join("")+newGoodsHtml);
	bindModulesShowGoods();//绑定初始化显示的分类商品信息 各4条
}

/**
 * 描述：绑定初始化显示的分类商品信息 各4条
 * 参数：无 tag_id  = brandId + categoryId
 * 返回值：无
 * */
function bindModulesShowGoods(){
	$(modules_show).each(function(i,cid){
		ajaxGoodsList(cid,bindRecommendGoods,"show",i);
	})
	addHover();//绑定数遍事件
}
/**
 * 描述：绑定初始化隐藏的分类商品信息 各4条
 * 参数：无
 * 返回值：无
 * */
function bindModulesHideGoods(){
	$(modules_hide).each(function(i,tag_id){
		ajaxGoodsList(cid,bid,bindRecommendGoods);
	})
}


/**
 * 描述：根据分类Id获取推荐商品 4条
 * 参数：result Int  分类Id
 * 返回值：无
 * */
function bindRecommendGoods(result,cid,isShow,n){
	if (!result.goods){ //如果结果集中无 主体数据 
		result.goods = [];
	}
	
	var html='<ul class="w_goods_one" >';
	// 首页每个楼层的二级分类下可以展示四个商品
	for (var i = 0; i < 4; i++){
		var good = result.goods[i];
		// 如果根据当前下标能渠道商品，则展示该商品，如果取不到，则填充“敬请期待”图片
		if (good){
			var progress=(good.selledTime/(good.goodsPrice/good.priceArea))*100;
			if(progress<3&&progress>0){
				progress=3;
			}
			var href='/goods/goods'+good.gid+'-'+good.periodCurrent+'.html';
			html+='<li class="w_goods_details">';
			html+='<div class="w_imgOut">';
			html+='<a class="w_goods_img" href="'+href+'" target="_blank" >';
			html+='<img id="img_'+good.gid+'" data-original="'+imageGoodsPath+good.showImages.split(',')[0]+imgsize+'" class="lazy'+cid+'">';
			html+='</a>';
			html+='</div>';
			html+='<a class="w_goods_three" href="'+ href+'" target="_blank">(第'+good.periodCurrent+'期) '+good.title+'</a>';
			html+='<b>价值：￥'+good.goodsPrice+'</b>';
			html+='<div class="w_line">';
			html+='<span style="width:'+progress+'%"></span>';
			html+='</div>';
			html+='<ul class="w_number">';
			html+='<li class="w_amount">'+good.selledTime+'</li>';
			html+='<li class="w_amount">'+good.goodsPrice/good.priceArea+'</li>';
			html+='<li class="w_amount">'+(good.goodsPrice/good.priceArea-good.selledTime)+'</li>';
			html+='<li>已夺宝人次</li>';
			html+='<li>总需人次</li>';
			html+='<li>剩余人次</li>';
			html+='</ul>';
			html+='<dl class="w_rob">';
			html+='<dd>';
			html+='<a class="w_slip"  onclick="addCart('+good.gid+',1)" href="javascript:gotoCartOrder();" >立即夺宝</a>';
			html+='<a href="javascript:cartoon('+good.gid+','+good.periodCurrent+')" class="w_slip2"></a>';
			html+='<input type="hidden" value="'+good.priceArea+'" id="priceArea_'+good.gid+'">';
			html+='</dd>';
			html+='</dl>';
			if(good.priceArea == 10)
				html+='<span class="w_zone">十元专区</span>';
			if(good.priceArea == 100)
				html+='<span class="w_zone">百元专区</span>';
			if(good.maxBuy != 0)
				html+='<span class="w_zone w_zone_other">限购专区</span>';
			html+='</li>';
		} else {
			html+='<li class="w_goods_details" style="background:url(\'/static/img/front/goods/expect.jpg\') no-repeat center 0;"></li>';
		}
	}
	html+='</ul>';
    	$("#module_"+(n+1)+"_child_goodslist_show").html(html);
	
	lazyload(cid);//启动 图片延迟加载
	bindHideGoods(result,n,cid);//绑定隐藏商品
}
/**
 * 绑定隐藏的商品
 * @param result 商品数据
 * @param n 楼层布局标识
 * @param cid 栏目ID
 */
function bindHideGoods(result,n,cid){
	if (!result.goods){ //如果结果集中无 主体数据 
		result.goods = [];
	}
	var goods_length = result.goods.length;
	var goods_div_size = Math.ceil(goods_length/4);//获取商品的楼层数
	for(var j=1;j<goods_div_size-1;j++){
	var html='<div class="yConCenterInList" >';
	    html+='<ul class="w_goods_one" >';
	// 首页每个楼层的二级分类下可以展示四个商品
	for (var i = j*4; i < j*4+4; i++){
		var good = result.goods[i];
		// 如果根据当前下标能渠道商品，则展示该商品，如果取不到，则填充“敬请期待”图片
		if (good){
			var progress=(good.selledTime/(good.goodsPrice/good.priceArea))*100;
			if(progress<3&&progress>0){
				progress=3;
			}
			var href='/goods/goods'+good.gid+'-'+good.periodCurrent+'.html';
			html+='<li class="w_goods_details">';
			html+='<div class="w_imgOut">';
			html+='<a class="w_goods_img" href="'+href+'" target="_blank" >';
			html+='<img id="img_'+good.gid+'" data-original="'+imageGoodsPath+good.showImages.split(',')[0]+imgsize+'" class="lazy'+cid+'">';
			html+='</a>';
			html+='</div>';
			html+='<a class="w_goods_three" href="'+ href+'" target="_blank">(第'+good.periodCurrent+'期) '+good.title+'</a>';
			html+='<b>价值：￥'+good.goodsPrice+'</b>';
			html+='<div class="w_line">';
			html+='<span style="width:'+progress+'%"></span>';
			html+='</div>';
			html+='<ul class="w_number">';
			html+='<li class="w_amount">'+good.selledTime+'</li>';
			html+='<li class="w_amount">'+good.goodsPrice/good.priceArea+'</li>';
			html+='<li class="w_amount">'+(good.goodsPrice/good.priceArea-good.selledTime)+'</li>';
			html+='<li>已夺宝人次</li>';
			html+='<li>总需人次</li>';
			html+='<li>剩余人次</li>';
			html+='</ul>';
			html+='<dl class="w_rob">';
			html+='<dd>';
			html+='<a class="w_slip"  onclick="addCart('+good.gid+',1)" href="javascript:gotoCartOrder();" >立即夺宝</a>';
			html+='<a href="javascript:cartoon('+good.gid+','+good.periodCurrent+')" class="w_slip2"></a>';
			html+='<input type="hidden" value="'+good.priceArea+'" id="priceArea_'+good.gid+'">';
			html+='</dd>';
			html+='</dl>';
			if(good.priceArea == 10)
				html+='<span class="w_zone">十元专区</span>';
			if(good.priceArea == 100)
				html+='<span class="w_zone">百元专区</span>';
			if(good.maxBuy != 0)
				html+='<span class="w_zone w_zone_other">限购专区</span>';
			html+='</li>';
		} else {
			html+='<li class="w_goods_details" style="background:url(\'/static/img/front/goods/expect.jpg\') no-repeat center 0;"></li>';
		}
	}
	html+='</ul>';
	html+='</div>';
    	$("#module_"+(n+1)+"_child_goodslist_show").after(html);
	}
	lazyload(cid);//启动 图片延迟加载
}


/*
 * 描述：获取网站公告
 * 参数：无
 * 返回值：无
 * */
function bindNotices(result){
	var html='';
	$(result.notices).each(function(index, notice){
		html+='<li><p class="yscrollfont">';
		html+='<a href="/footer/new_dynamic'+notice.id+'.html" title="'+notice.title+'">'+notice.title+'</a>';
		html+='</p><p class="yscrolltime">'+formatDate(notice.publishTime, "yyyy/MM/dd")+'</p></li>';
	});
	$(".yscroll_list_left").html(html);
}

/*
 *描述：绑定6条最新晒单 
 *参数：result json 最新揭晓结果集
 **/
function bindExposeTop6(result){
	var html='<ul>';
	if(result.showsPage.total > 0){//存在 晒单
		$(result.showsPage.dataList).each(function(index,show){
			var href='/other/exposeDetail.do?showId='+show.showId+'&mid='+show.mid+'';
			var imgSrc=show.photos.split(",")[0];
			html+='<li><a href="'+href+'" class="List2Imga">';
			html+='<img class="lazy_expose" data-original="'+imgSrc+'" style="height:147px;width:220px;">';
			html+='</a>';
		    html+='<div class="List2ImgRight"><p><a href="'+href+'">'+show.title+'</a></p></div></li>';
		});
		var len = result.showsPage.dataList.length;
		for(var i=6-len; i>0; i--){
			html+='<li><img src="/static/img/front/index/shan_no.jpg"/></li>';
		}
		html+="</ul>";
		$("#expose6").html(html);
		lazyload("_expose");//启动 图片延迟加载
	}else{
		$("div.yCon8Centerscroll").html('<div class="w_empty_img"><img src="/static/img/front/index/shan.jpg"/><a class="w_add_tiao"  href="javascript:gotoRecordWin()">你晒单，我送积分</a></div>');
	}
}
/**
 * 跳转个人中心中奖页面
 */
function gotoRecordWin(){
	var mid = $('#mid').val();
	if(mid!=null&&mid!=''){
		window.location.href='/member_new/recordWin.do'; 
	}else{
		go2Login(true);
	}
}
/*
 * 描述：加载广告位
 * 参数：无
 * 返回值：无
 * */
function bindAdvertisementList(result){
	result.ads.floor1 && addAdFun(1, result.ads.floor1);
	result.ads.floor2 && addAdFun(2, result.ads.floor2);
	result.ads.floor3 && addAdFun(3, result.ads.floor3);
	result.ads.floor4 && addAdFun(4, result.ads.floor4);
	result.ads.floor5 && addAdFun(5, result.ads.floor5);
	result.ads.floor6 && addAdFun(6, result.ads.floor6);
	result.ads.floor7 && addAdFun(7, result.ads.floor7);
	result.ads.floor8 && addAdFun(8, result.ads.floor8);
	for(var i=0,len=$(".aBJCon").length; i<len; i++){
		adHH(i);
	}
}



/*
 * 描述：根据楼层添加广告
 * 参数：index 位置顺序
 * 参数：objes 广告详情（图片及连接）
 * 返回值:无
 * */
function addAdFun(index, objs){
	if(objs){
		var html='';
		html+='<div class="aBJCon">';
		$(objs).each(function(index, obj){
			html+='<a target="_blank" href="'+obj.link+'" title="'+obj.description+'">';
			html+='<img src="'+imagePath+obj.img+'"></a>';
		});
		html+='</div>';
		$(".yCon"+index).before(html);//添加广告
	}
}


/*
 * 描述：绑定最新上架商品
 * 参数： result json 最新上架8条结果集
 * 返回值：无
 * */
function bindGoodsListTop8(result){
	var html='<ul class="w_goods_one" >';
	if(typeof(result.goods)=="undefined"){
		for(var i=0;i<8;i++){
			html+='<li class="w_goods_details" style="background:url(\'/static/img/front/goods/expect.jpg\') no-repeat center 0;"></li>';
		}
	}else{
	$(result.goods.dataList).each(function(index,g){
		var href='/goods/goods'+g.gid+'-'+g.periodCurrent+'.html';
		var imgSrc=imageGoodsPath+g.showImages.split(',')[0]+imgsize;
		html+='<li class="w_goods_details">';
		html+='<div class="w_imgOut">';
		html+='<a class="w_goods_img" href="'+href+'" target="_blank" >';
		html+='<img class="lazy_addtime" data-original="'+imgSrc+'" style="height:200px;width:200px;">';
		html+='</a>';
		html+='</div>';
		html+='<a class="w_goods_three" href="'+href+'" target="_blank">(第'+g.periodCurrent+'期) '+g.title+'</a>';
		html+='<b>总需：'+g.goodsPrice/g.priceArea+' 人次</b>';
		html+='</li>';
	})
	}
	html+='</ul>';
	$("#GoodsOrderByAddTime").html(html);
	lazyload("_addtime");//启动 图片延迟加载
}

 

/*
 * 描述：加入购物车动画
 * 参数：gid Int 商品Id
 * 参数：pid Int 商品期数
 * */
function cartoon(gid, pid){
	addCart(gid, 1);//调用 加入购物车函数
	if($('.shoppingCartRightFix').is(":visible")){
		var img = $("#img_"+gid);
		var flyElm = img.clone().css('opacity', 0.75);
		$('body').append(flyElm);
		flyElm.css({
			'z-index': 9000,
			'display': 'block',
			'position': 'absolute',
			'top': img.offset().top +'px',
			'left': img.offset().left +'px',
			'width': img.width() +'px',
			'height': img.height() +'px'
		});
		var t=$('.shoppingCartRightFix').offset().top;
		var l=$('.shoppingCartRightFix').offset().left;
		console.log(t+"--"+l);
		flyElm.animate({
			top: t,
			left: l,
			width: 40,
			height: 26
		}, 'slow', function() {
			flyElm.remove();
		});
	}
}



/*
 * 描述：动画倒计时
 * 参数：intDiff Int 倒计时时间 
 * 参数：gid 倒计时及块父级Id  生成选择期  time_gid being_gid result_gid   
 * 参数：timeStyle html 倒计时样式
 * 返回值：无
 * */
function timer(intDiff,gid,period,tag){
	var objTimer=window.setInterval(function(){
	var day=0,
		hour=0,
	    min=0, //
		sec=0,//时间默认值		
		msec=0
	if(intDiff > 0){
		
		var total = intDiff ;//% 86400000;
		hour = parseInt(total / 3600000);
		
		total = intDiff % 3600000;
		min = parseInt(total / 60000)
		
		total = intDiff % 60000;
		sec =  parseInt(total / 1000) ;
		
		total = intDiff % 1000;
		msec =  parseInt(total / 10) ;//毫秒
	}
	
	if (hour <= 9) hour = '0' + hour;
	if (min <= 9) min = '0' + min;
	if (sec <= 9) sec = '0' + sec;
	
	if (msec <= 99) msec = '0' + msec; //毫秒
	if (msec <= 9) msec = '00' + msec; //毫秒
    var hh=hour.toString();
	var mm=min.toString();
    var ss=sec.toString();
    var msc=msec.toString();
   
    if (intDiff >=3600000) { //倒计时时大于60分钟启用秒级倒计时
	     document.getElementById("hh_0_"+tag).innerHTML=hh[0];
	   	 document.getElementById("hh_1_"+tag).innerHTML=hh[1];
	   	 document.getElementById("mm_0_"+tag).innerHTML=mm[0];
	   	 document.getElementById("mm_1_"+tag).innerHTML=mm[1];
	   	 document.getElementById("ss_0_"+tag).innerHTML=ss[0];
	   	 document.getElementById("ss_1_"+tag).innerHTML=ss[1];
	}else if(intDiff < 3600000&&intDiff >= 0){
		 document.getElementById("hh_0_"+tag).innerHTML=mm[0];
    	 document.getElementById("hh_1_"+tag).innerHTML=mm[1];
    	 document.getElementById("mm_0_"+tag).innerHTML=ss[0];
    	 document.getElementById("mm_1_"+tag).innerHTML=ss[1];
    	 document.getElementById("ss_0_"+tag).innerHTML=msc[1];
    	 document.getElementById("ss_1_"+tag).innerHTML=msc[2];
	} else if (intDiff<0) {
		  window.clearInterval(objTimer);//关闭定时器
		  $("#yTimes_"+tag).html('<p class="timeing">正在计算，请稍后...</p>');
		   setTimeout(function(){
	    		 ajaxPublishResultDetail(gid,period,publishResidueTimes,bindPublishResultDetail);////请求揭晓信息
	       },publishErrorCycle);
		
	} 
	   intDiff=intDiff-30;
	}, 30);
	timer_arr_timeObj.push(objTimer);
} 

/*
 * 描述：绑定官方动态
 * 参数：result json 
 * 
 * */
function bindNewsList(data){
	var html='<ul>';
	for (var i = 0; i < 4; i++) {
		var news = data[i];
		var datetime = new Date(parseInt(news.time) * 1000);
		var href = "http://sq.modole.com/viewthread-"+news.id+".html";

		html+='<li>';
		html+='<div class="yCon8Calendar">';
		html+='<b>'+datetime.getDate()+'</b><br/>';
		html+='<span>'+datetime.getFullYear()+'-'+(datetime.getMonth()+1)+'</span>';
		html+='</div>';
		html+='<div class="yCon8Li1a">';
		html+='<a href="'+href+'" title="'+news.title+'"><span>'+news.title+'</span></a>';
		html+='<span class="new-con8Cale-span">'+news.news || news.title+'</span></div>';
		html+='</a>';
		html+='</li>';

	}
	html+='</ul>';
     $("#news_loading").html(html);
}
/*
 * 描述：页面向下滑动后超过 导航菜单 高度，则导航菜单漂浮至页面顶部
 * 
 */
function actionNavFixed() {
	if ($(".yscroll_list").offset()) {

		var s_top = $(".yscroll_list").offset().top;
		$(window).scroll(function() {
			if ($(window).scrollTop() > s_top) {
				$(".header_fixed").css({
					marginBottom : "46px"
				});
				$(".yNavIndexOut_fixed_index").addClass("yNavIndexOutfixed");
				$(".pullDownList").hide();//下来分类展示
				$(".pullDown").hover(function(){
					$(".pullDownList").show();
				},function(){
					$(".pullDownList").hide();
				})
			} else {
				$(".yNavIndexOut_fixed_index").removeClass("yNavIndexOutfixed");
				$(".header_fixed").css({
					marginBottom : "0px"
				});
				$(".pullDownList").show();//下来分类展示
				$(".pullDown").hover(function(){
					$(".pullDownList").show();
				},function(){
					$(".pullDownList").show();
				})
			}
		})
	}
}