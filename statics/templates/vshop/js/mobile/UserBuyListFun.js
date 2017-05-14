$(function() {

    var d = 10;

    var timestamp = Date.parse(new Date())/1000;

    var g = {

        FIdx: 0,

        EIdx: d,

        isCount: 1,

        state: -1

    };

	 

    var a = 0;

    var f = null;

    var e = $("#divGoodsLoading");  //正在加载

    var h = $("#btnLoadMore");  //加载更多

    var i = false;

    var b = false;

    var c = function() {

	

        var j = function() {

            return "/" + g.FIdx + "/" + g.EIdx + "/" + g.isCount + "/" + g.state

        };

		 

        var k = function() {		    

            e.show();	            

			GetJPData(Gobal.Webpath, "shopajax", "getUserBuyList"+j(),

            function(q) {

                if (q.code == 0) {				 

                    var n = q.listItems;//console.log(n);					 

                    if (g.isCount == 1) {					

                        a = q.count;

                        g.isCount = 0

                    }

                    var r = n.length;

                    var o = "";

                    var s = 0;

                    var t = 0;

                    var l = 0;

                    var u = 0;

                    for (var p = 0; p < r; p++) {
                        if(n[p] instanceof Array){ //一个订单有多种商品
                            //alert(n[p].length);
                            //var products = n[p];
                            //alert(products.length)
                            o += '<li>';
                            var gonumber = 0;
                            var moneycount = 0.00;
                            for(var item=0; item<n[p].length; item++){
                                gonumber = gonumber+parseInt(n[p][item].gonumber)
                                moneycount = moneycount+parseFloat(n[p][item].moneycount);
                                m = parseInt(n[p][item].codeState);
                                pro_info = eval('('+n[p][item].pro_info+')');
								var ordercode = n[p][item].code;
                                o += '<a href="'+Gobal.Webpath+'/mobile/mobile/goodsdesc/'+n[p].shopid+'"><div class="iteminfo-top"><span class="fl z-Limg">';
                                o += '<img src="' + Gobal.LoadPic + '" src2="'+Gobal.imgpath+'/uploads/' + pro_info.goods_img + '" border=0 alt=""></span>';
                                o += '<div class="u-sgl-r "><p class="z-sgl-tt"><span class="gray6">'+ n[p][item].shopname + "</span></p>";
                                o +='<p class="thesort">';
                                if(pro_info.flag == 0){
                                    o += pro_info.key_name;
                                }else{
                                    o += '颜色分类：'+subBothString(pro_info.title,25,1);
                                }
                                o +='</p><p class="theprice">￥<em>'+pro_info.cart_xiaoji+'</em><b>元</b><span>x<i>'+pro_info.goods_count+'</i></span></p></div></div></a>';
                            }
                            switch (m){
                                case 1:
                                    o += '<div class="iteminfo-bottom"><span class="goosdState">待付款</span><p>共<em>'+gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="cancelOrder" href="javascript:;" data-order="'+ordercode+'">取消订单</a><a class="finishPay" href="'+Gobal.Webpath+'/mobile/cart/orderPay/'+ordercode+'">付款</a></div></div></li>';
                                    break;
                                case 2:
                                    o += '<div class="iteminfo-bottom"><span class="goosdState">已付款</span><p>共<em>'+gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="hasten" href="javascript:;">催发货</a></div></div></li>';
                                    break;
                                case 3:
                                    o += '<div class="iteminfo-bottom"><span class="goosdState">待收货</span><p>共<em>'+gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="logistics" href="'+Gobal.Webpath+'/mobile/home/logistics/'+ordercode+'">查看物流</a><a class="accept" href="javascript:;" data-order="'+ordercode+'">确认收货</a></div></div></li>';
                                    break;
                                case 4:
                                    o += '<div class="iteminfo-bottom"><span class="goosdState">已收货</span><p>共<em>'+gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="logistical" href="'+Gobal.Webpath+'/mobile/home/logistics/'+ordercode+'">查看物流</a></div></div></li>';
                                    break;
                                default:
                                    o += '<div class="iteminfo-bottom"><span class="goosdState">已关闭</span><p>共<em>'+gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+moneycount+'</em><b>元</b></span></p><div class="userBtn"></div></div></li>';
                            }
                        }else{ //一个订单只有一件商品
                            var m = parseInt(n[p].codeState);
                            //alert(n[p].length);
                            o += '<li><a href="'+Gobal.Webpath+'/mobile/mobile/goodsdesc/'+n[p].shopid+'"><div class="iteminfo-top"><span class="fl z-Limg">';
                            var pro_info = eval('('+n[p].pro_info+')');
                                //alert(pro_info.p_shopimg)
                            o += '<img src="' + Gobal.LoadPic + '" src2="'+Gobal.imgpath+'/uploads/'+pro_info.goods_img+'" border=0 alt=""></span>';
                            o += '<div class="u-sgl-r "><p class="z-sgl-tt"><span class="gray6">'+ n[p].shopname + "</span></p>";
                            o +='<p class="thesort">';
                            if(pro_info.flag == 0){
                                o += pro_info.key_name;
                            }else{
                                o += '颜色分类：'+subBothString(pro_info.title,25,1);
                            }
                            o +='</p><p class="theprice">￥<em>'+pro_info.cart_xiaoji+'</em><b>元</b><span>x<i>'+pro_info.goods_count+'</i></span></p>';
                            switch (m){
                                case 1:
                                    o += '</div></div></a><div class="iteminfo-bottom"><span class="goosdState">待付款</span><p>共<em>'+n[p].gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+n[p].moneycount+'</em><b>元</b></span></p><div class="userBtn"><a href="javascript:;" class="cancelOrder" data-order="'+n[p].code+'">取消订单</a><a href="'+Gobal.Webpath+'/mobile/cart/orderPay/'+n[p].code+'" class="finishPay">付款</a></div></div></li>';
                                    break;
                                case 2:
                                    o += '</div></div></a><div class="iteminfo-bottom"><span class="goosdState">已付款</span><p>共<em>'+n[p].gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+n[p].moneycount+'</em><b>元</b></span></p><div class="userBtn"><a href="javascript:;" class="hasten">催发货</a></div></div></li>';
                                    break;
                                case 3:
                                    o += '</div></div></a><div class="iteminfo-bottom"><span class="goosdState">待收货</span><p>共<em>'+n[p].gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+n[p].moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="logistics" href="'+Gobal.Webpath+'/mobile/home/logistics/'+n[p].code+'">查看物流</a><a href="javascript:;" class="accept" data-order="'+n[p].code+'">确认收货</a></div></div></li>';
                                    break;
                                case 4:
                                    o += '</div></div></a><div class="iteminfo-bottom"><span class="goosdState">已收货</span><p>共<em>'+n[p].gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+n[p].moneycount+'</em><b>元</b></span></p><div class="userBtn"><a class="logistical" href="'+Gobal.Webpath+'/mobile/home/logistics/'+n[p].code+'">查看物流</a></div></div></li>';
                                    break
                                default:
                                    o += '</div></div></a><div class="iteminfo-bottom"><span class="goosdState">已关闭</span><p>共<em>'+n[p].gonumber+'</em>件商品&nbsp;合计:<span>￥<em>'+n[p].moneycount+'</em><b>元</b></span></p><div class="userBtn"></div></div></li>';
                            }
                        }
                    }

                    if (g.FIdx > -1) {

                        e.prev().removeClass("bornone")

                    }

                    e.before(o).prev().addClass("bornone");

                    if (g.EIdx < a) {

                        i = false;

                        h.show()

                    }

                    loadImgFun(0)

                } else {

                    if (g.FIdx == 0) {

                        if (g.state == -1) {

                            b = true

                        }

                        e.before(Gobal.NoneHtml)

                    }

                }

                e.hide()

            })

        };

        this.getInitPage = function() {

            g.FIdx = 0;

            g.EIdx = d;  //初始化10

            g.isCount = 1;

            k()

        };

        this.getNextPage = function() {

            g.FIdx += d;

            g.EIdx += d;

            k()

        }

    };

    $("#navBox").children("div").each(function() {

        var j = $(this);

        j.click(function() {

            g.state = j.attr("state");			

            j.addClass("z-sgl-crt").siblings().removeClass("z-sgl-crt");

            if (!b) {

                h.hide();

                e.prevAll().remove();

                f.getInitPage();
            }
        })

    });
    //个人中心订单显示
    /*if($('.state').data('state')==j.attr('state')){
        j.triggerHandler('click');
    }*/
    $("#navBox").children("div").each(function() {
        if($('.state').data('state')==$(this).attr('state')){
            g.state = $(this).attr('state');
            $(this).addClass("z-sgl-crt").siblings().removeClass("z-sgl-crt");
            //alert(b);
        }
    })

/*
    h.click(function() {

        if (!i) {

            i = true;

            h.hide();

            f.getNextPage()

        }

    }); */
	
	$(window).scroll(function() {
                    if ($(document).height() - $(this).scrollTop() - $(this).height() < 1 && $('#btnLoadMore').css('display') != 'none') {
                       if (!i) {

					i = true;

					h.hide();

					f.getNextPage()

        }
                    }
       });	
	//催发货
	$(document).on('click','.hasten',function(){
		sendsuccess('催单成功,我们会尽快为您发货!');
	});

	
	//取消订单
    $(document).on('click','.cancelOrder',function () {
        //alert($(this).attr('data-order'));
        var delOrder = $(this);
        var ordercode = $(this).attr('data-order');
        $.ajax({
            url:Gobal.Webpath+"/mobile/shopajax/delOrder",
            data:{ordercode:ordercode},
            type:'post',
            dataType:'json',
            success:function (data) {
                if(data.code == 0){
                    delOrder.parent().parent().parent().remove();
                    sendsuccess(data.msg);
                }else {
                    sendsuccess(data.msg);
                }
            }
        })
    });
    //确认订单
    $(document).on('click','.accept',function () {
        //alert($(this).attr('data-order'));
        var confirmAccept = $(this);
        var ordercode = $(this).attr('data-order');
        $.ajax({
            url:Gobal.Webpath+"/mobile/shopajax/confirmAccept",
            data:{ordercode:ordercode},
            type:'post',
            dataType:'json',
            success:function (data) {
                if(data.code == 0){
                    //confirmAccept.parent().parent().parent().remove();
                    confirmAccept.siblings('a').eq(0).attr('class','logistical');
                    confirmAccept.parent().siblings('span').text('已收货');
                    confirmAccept.remove();
                    sendsuccess(data.msg);
                    //location.reload();
                }else {
                    sendsuccess(data.msg);
                }
            }
        })
    });
	
	
	function sendsuccess(dat){
		$("#pageDialogBG .Prompt").text("");
		var w=($(window).width()-255)/2,
			h=($(window).height()-45)/2;
		$("#pageDialogBG").css({top:h,left:w,opacity:0.8});
		$("#pageDialogBG").stop().fadeIn(1000);
		$("#pageDialogBG .Prompt").append(dat);
		$("#pageDialogBG").fadeOut(1000);

	}

	
	 

    f = new c();

    f.getInitPage()


    /**
     * 截取字符串：包含中文和英文
     */
    function subBothString(str, len, hasDot) {
        var newLength = 0;
        var newStr = "";
        var chineseRegex = /[^\x00-\xff]/g;
        var singleChar = "";
        var strLength = str.replace(chineseRegex, "**").length;
        for (var i = 0; i < strLength; i++) {
            singleChar = str.charAt(i).toString();
            if (singleChar.match(chineseRegex) != null) {
                newLength += 2;
            }
            else {
                newLength++;
            }
            if (newLength > len) {
                break;
            }
            newStr += singleChar;
        }
        if (hasDot && strLength > len) {
            newStr += "...";
        }
        return newStr;
    }

});