$(function(){
	var close=$('#selectBar-close');
	var sectionWrap=$('.selectBar-wrap');
	var section=$('.selectBar');
	var classChose=$('.goodsClass-list li'),
		price = $('.detail-price span');
		last=$('.detail-last span'),
		selected=$('.detail-selected');
	var lastNum=last.text();
	var more=$('#More');
	var less=$('#Less');
	var currentN=$('#nums');
	var goodsImg = $('.goodsInfo-img img');
	var goodsWrap=$('.goodsInfo-img');
 	var w=($(document).width()-30)*(goodsWrap.width()/100);
	//goodsWrap.css('height',w);


	
	//关闭
	close.click(function(){
		//clearInterval(countDown);//调用倒计时前先清除定时器
		sectionWrap.slideUp(function(){
			$(this).remove();
		});

	});
	
	//分类
	classChose.click(function(){
		var src = $(this).data('src');
		var src_default = $('.goodsInfo-img').find('img').attr('src');//默认商品图片，用来判断商品规格是否有上传图片
		var cur=$(this);
		var cont=cur.text();
		//alert(cont)
		if(cur.hasClass('sellOut') || cur.hasClass('classSel')){
			return false;
		}
		var flagId = cur.data('id'); //用来判断是否有商品规格
		//alert(flagId);
		if(typeof flagId == "undefined"){
			//有商品规格
			get_goods_spec(cur);
			cur.addClass('classSel');
			cur.siblings().removeClass('classSel');
			if(!src){
				goodsImg.attr('src',src_default);
			}else {
				goodsImg.attr('src',Gobal.Uploadpath+'/'+src);
			}
			get_goods_price();
		}else{
			//没有添加商品规格的
			cur.addClass('classSel');
			cur.siblings().removeClass('classSel');
			goodsImg.attr('src',Gobal.Uploadpath+'/'+src);
			selected.html('已选:<span>"'+cont+'"<span>');
		}
	})
	//classChose.trigger('click')

	//购买数量
	more.click(function(){
		var i=currentN.text();
		i++;
		if(i>lastNum){
			currentN.text(lastNum);
		}
		else{
			currentN.text(i);
		}
	});
	less.click(function(){
		var i=currentN.text();
		i--;
		if(i<=1){
			currentN.text(1);
		}
		else{
			currentN.text(i);
		}
	});
	
	//加入购物车
	$('#addBtn').click(function(){
		var ul_count = $('.goodsClass-wrap').find('ul').length;
		//alert(ul_count)
		//商品id、购买数量、规格id、规格名称、规格类型、商品图片id
		var id=$('.shopId').val() || $("#hidCodeID").val(); //点击购物车和详情页点击加入购物车，都是商品的id
		var num=$('#nums').text(); //购买的商品数量
		var item_id_arr = new Array();
		var item_name = new Array();
		var flagId;
		$('.classSel').each(function(){
			flagId = $(this).data('id');
			if(typeof flagId == 'undefined'){
				item_id_arr.push($(this).data('item-id'));
				item_name.push($(this).attr('title'));
			}else{
				item_id_arr.push(flagId);
			}
		});
		//alert(flagId)
		var price = $('.detail-price span').text();
		var goods_img = goodsImg.attr('src');
		var reg = new RegExp(Gobal.Uploadpath+'/');
		goods_img = goods_img.replace(reg,'');
		if(typeof flagId == 'undefined'){
			var item_id = item_id_arr.sort(sortNumber).join('_');  //排序后组合成 商品规格id='1_13'
			var choose_name = item_name.join('_');
			var data = {
				goods_id:id,
				num:num,
				price:price,
				item_id_arr:item_id_arr,
				item_id:item_id,
				item_name:choose_name,
				flag:0,
				goods_img:goods_img,
				mark:'cart'
			}
		}else{
			var data = {
				goods_id:id,
				num:num,
				price:price,
				flag:flagId,
				goods_img:goods_img,
				mark:'cart'
			}
		}
		if(ul_count == item_id_arr.length){
			$.ajax({
				type:"POST",
				url:Gobal.Webpath+"/mobile/ajax/addShopCart/",
				data:data,
				dataType:"json",
				success:function (data) {
					if(data.code==1){
						addsuccess('添加失败');
					}else if(data.code==0){
						addsuccess('添加成功');
					}return false;
				}
			});
		}else{
			showerror("请选择商品属性");
		}
	});
	//修改购物车
	$('#editBtn').click(function(){
		var ul_count = $('.goodsClass-wrap').find('ul').length;
		//alert(ul_count)
		//商品id、购买数量、规格id、规格名称、规格类型、商品图片id
		var id=$('.shopId').val() || $("#hidCodeID").val(); //点击购物车和详情页点击加入购物车，都是商品的id
		var num=$('#nums').text(); //购买的商品数量
		var item_id_arr = new Array();
		var item_name = new Array();
		var cartkey = $('.cartkey').val();
		var flagId;
		$('.classSel').each(function(){
			flagId = $(this).data('id');
			if(typeof flagId == 'undefined'){
				item_id_arr.push($(this).data('item-id'));
				item_name.push($(this).attr('title'));
			}else{
				item_id_arr.push(flagId);
			}
		});
		//alert(flagId)
		var price = $('.detail-price span').text();
		var goods_img = goodsImg.attr('src');
		var reg = new RegExp(Gobal.Uploadpath+'/');
		goods_img = goods_img.replace(reg,'');
		if(typeof flagId == 'undefined'){
			var item_id = item_id_arr.sort(sortNumber).join('_');  //排序后组合成 商品规格id='1_13'
			var choose_name = item_name.join('_');
			var data = {
				goods_id:id,
				num:num,
				price:price,
				item_id_arr:item_id_arr,
				item_id:item_id,
				item_name:choose_name,
				flag:0,
				goods_img:goods_img,
				cartkey:cartkey,
				mark:'cart'
			}
		}else{
			var data = {
				goods_id:id,
				num:num,
				price:price,
				flag:flagId,
				goods_img:goods_img,
				cartkey:cartkey,
				mark:'cart'
			}
		}
		if(ul_count == item_id_arr.length){
			$.ajax({
				type:"POST",
				url:Gobal.Webpath+"/mobile/ajax/editShopCart/",
				data:data,
				dataType:"json",
				success:function (data) {
					if(data.code==1){
						addsuccess('修改失败');
					}else if(data.code==0){
						sectionWrap.slideUp(function(){
							$(this).remove();
						});
						addsuccess('修改成功');
						window.location.replace(Gobal.Webpath+'/mobile/cart/cartlist');

					}return false;
				}
			});
		}else{
			showerror("请选择商品属性");
		}


	})

	function showerror(dat){
		$("#pageDialogBG .Prompt").text("");
		var w=($(window).width()-200)/2,
			h=($(window).height()-100);
			//h=100;
		$("#pageDialogBG").css({top:h,left:w,opacity:0.8});
		$("#pageDialogBG").stop().fadeIn(1000);
		$("#pageDialogBG .Prompt").append(dat);
		$("#pageDialogBG").fadeOut(1000);

	}

	function addsuccess(dat){
		$("#pageDialogBG .Prompt").text("");
		var w=($(window).width()-200)/2,
			h=($(window).height()-45)/2;
		$("#pageDialogBG").css({top:h,left:w,opacity:0.8});
		$("#pageDialogBG").stop().fadeIn(1000);
		$("#pageDialogBG .Prompt").append('<s></s>'+dat);
		$("#pageDialogBG").fadeOut(1000);
		//购物车数量
		$.getJSON(Gobal.Webpath+'/mobile/ajax/cartnum',function(data){
			$("#btnCart").html(data.num);
		});
	}

	//立即购买：
	$('#parchaseBtn').click(function(){

		var ul_count = $('.goodsClass-wrap').find('ul').length;
		//alert(ul_count)
		//商品id、购买数量、规格id、规格名称、规格类型、商品图片id
		var id=$('.shopId').val() || $("#hidCodeID").val(); //点击购物车和详情页点击加入购物车，都是商品的id
		var num=$('#nums').text(); //购买的商品数量
		var item_id_arr = new Array();
		var item_name = new Array();
		var flagId;
		$('.classSel').each(function(){
			flagId = $(this).data('id');
			if(typeof flagId == 'undefined'){
				item_id_arr.push($(this).data('item-id'));
				item_name.push($(this).attr('title'));
			}else{
				item_id_arr.push(flagId);
			}
		});
		//alert(flagId)
		var price = $('.detail-price span').text();
		var goods_img = goodsImg.attr('src');
		var reg = new RegExp(Gobal.Uploadpath+'/');
		goods_img = goods_img.replace(reg,'');
		if(typeof flagId == 'undefined'){
			var item_id = item_id_arr.sort(sortNumber).join('_');  //排序后组合成 商品规格id='1_13'
			var choose_name = item_name.join('_');
			var data = {
				goods_id:id,
				num:num,
				price:price,
				item_id_arr:item_id_arr,
				item_id:item_id,
				item_name:choose_name,
				flag:0,
				goods_img:goods_img,
				mark:'shopping'
			}
		}else{
			var data = {
				goods_id:id,
				num:num,
				price:price,
				flag:flagId,
				goods_img:goods_img,
				mark:'shopping'
			}
		}
		if(ul_count == item_id_arr.length){
			$.ajax({
				type:"POST",
				url:Gobal.Webpath+"/mobile/ajax/addShopCart/",
				data:data,
				dataType:"json",
				success:function (data) {
					if(data.code==0){
						location.href = Gobal.Webpath+'/mobile/cart/pay';
					}else if(data.code==1){
						showerror("请选择商品属性");
					}return false;
				}
			});
		}else{
			showerror("请选择商品属性");
		}

		/*var id=$('.shopId').val() || $("#hidCodeID").val();
		var num=$('#nums').text();
		var proid = $('.classSel').attr('pro-id');
		var g_aid = $('.classSel').attr('attr-data');
		var pro_price = $('.classSel').attr('pro-price');
		var pro_info = $('.classSel').attr('pro-info');
		if(classChose.hasClass('classSel')){
			$.getJSON(Gobal.Webpath+'/mobile/ajax/addShopCart/'+id+'/'+num+'/'+proid+'/'+pro_price+'/'+pro_info+'/'+g_aid+'/shopping',function(data){
				if(data.code==0){
					location.href = Gobal.Webpath+'/mobile/cart/pay';
				}else if(data.code==1){
					showerror("请选择商品属性");
				}return false;
			});
		}else{
			showerror("请选择商品属性");
		}*/
	});

	//团购按钮
	$('#addGroup').click(function(){

		var ul_count = $('.goodsClass-wrap').find('ul').length;
		
		//alert(ul_count)
		//团长标志为1
		var head_code = $(this).attr('data-head');
		//是否团购
		var is_group = 1;
		//console.log(head_code);
		
		//商品id、购买数量、规格id、规格名称、规格类型、商品图片id
		var id=$('.shopId').val() || $("#hidCodeID").val(); //点击购物车和详情页点击加入购物车，都是商品的id
		var num=$('#nums').text(); //购买的商品数量
		//商品订单号
		var groupOrderCode = $('.groupOrderCode').val();//alert(groupOrderCode);
		var item_id_arr = new Array();
		var item_name = new Array();
		var flagId;
		$('.classSel').each(function(){
			flagId = $(this).data('id');  //flagId 用来判断商品是否有添加规格
			if(typeof flagId == 'undefined'){//有规格
				item_id_arr.push($(this).data('item-id'));
				item_name.push($(this).attr('title'));
			}else{//没有规格
				item_id_arr.push(flagId);
			}
		});
		//alert(flagId);return false;
		var price = $('.detail-price span').text();
		var goods_img = goodsImg.attr('src');
		var reg = new RegExp(Gobal.Uploadpath+'/');
		goods_img = goods_img.replace(reg,'');
		if(typeof flagId == 'undefined'){
			var item_id = item_id_arr.sort(sortNumber).join('_');  //排序后组合成 商品规格id='1_13'
			var choose_name = item_name.join('_');
			var data = {
				is_head:head_code,
				is_group:is_group,
				groupOrderCode:groupOrderCode,
				goods_id:id,
				num:num,
				price:price,
				item_id_arr:item_id_arr,
				item_id:item_id,
				item_name:choose_name,
				flag:0,
				goods_img:goods_img,
				mark:'shopping'
			}
		}else{
			var data = {
				is_head:head_code,
				is_group:is_group,
				groupOrderCode:groupOrderCode,
				goods_id:id,
				num:num,
				price:price,
				flag:flagId,
				goods_img:goods_img,
				mark:'shopping'
			}
		}
		if(ul_count == item_id_arr.length){
			$.ajax({
				type:"POST",
				url:Gobal.Webpath+"/mobile/ajax/addShopCart/",
				data:data,
				dataType:"json",
				success:function (data) {
					if(data.code==0){
						location.href = Gobal.Webpath+'/mobile/cart/grouppay';
					}else if(data.code==1){
						showerror("请选择商品属性");
					}return false;
				}
			});
		}else{
			showerror("请选择商品属性");
		}
	});

	//参团按钮
	$('.join a').click(function(){

		var ul_count = $('.goodsClass-wrap').find('ul').length;
		//alert(ul_count)

		//团员标志为0
		var head_code = $(this).attr('data-head');//alert(head_code);
		//是否团购
        var is_group = 1;
		//商品id、购买数量、规格id、规格名称、规格类型、商品图片id
		var id=$('.shopId').val() || $("#hidCodeID").val(); //点击购物车和详情页点击加入购物车，都是商品的id
		var num=$('#nums').text(); //购买的商品数量
		//商品订单号
		var groupOrderCode = $(this).data('group-code');
		//alert(groupOrderCode);
		var item_id_arr = new Array();
		var item_name = new Array();
		var flagId;
		$('.classSel').each(function(){
			flagId = $(this).data('id');
			if(typeof flagId == 'undefined'){
				item_id_arr.push($(this).data('item-id'));
				item_name.push($(this).attr('title'));
			}else{
				item_id_arr.push(flagId);
			}
		});
		//alert(flagId);return false;
		var price = $('.detail-price span').text();
		var goods_img = goodsImg.attr('src');
		var reg = new RegExp(Gobal.Uploadpath+'/');
		goods_img = goods_img.replace(reg,'');
		if(typeof flagId == 'undefined'){
			var item_id = item_id_arr.sort(sortNumber).join('_');  //排序后组合成 商品规格id='1_13'
			var choose_name = item_name.join('_');
			var data = {
				is_head:head_code,
				groupOrderCode:groupOrderCode,
                is_group:is_group,
				goods_id:id,
				num:num,
				price:price,
				item_id_arr:item_id_arr,
				item_id:item_id,
				item_name:choose_name,
				flag:0,
				goods_img:goods_img,
				mark:'shopping'
			}
		}else{
			var data = {
				is_head:head_code,
				groupOrderCode:groupOrderCode,
                is_group:is_group,
				goods_id:id,
				num:num,
				price:price,
				flag:flagId,
				goods_img:goods_img,
				mark:'shopping'
			}
		}
		if(ul_count == item_id_arr.length){
			$.ajax({
				type:"POST",
				url:Gobal.Webpath+"/mobile/ajax/addShopCart/",
				data:data,
				dataType:"json",
				success:function (data) {
					if(data.code==0){
						location.href = Gobal.Webpath+'/mobile/cart/grouppay';
					}else if(data.code==1){
						showerror("请选择商品属性");
					}return false;
				}
			});
		}else{
			showerror("请选择商品属性");
		}
	});
})