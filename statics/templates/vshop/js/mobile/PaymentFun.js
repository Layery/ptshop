$(function() {

    var a = false;

    var b = function() {

        //17.3.17 修改
        var status = $('#status').val(); //用来判断团购是否已完成

        var x = parseInt($("#hidShopMoney").val());//合计价格

        var ffdk = parseInt($("#pointsbl").val());//福分

        var d = $("#hidBalance").val();//账户余额

        var t = parseInt($("#hidPoints").val());//会员积分

        var c = $("#spPoints");//福分勾选框

        var p = $("#spBalance");//余额支付

        var h = null;

        var m = $("#bankList");//微信、支付宝方式支付

        var shopnum = parseInt($("#shopnum").val());//

        var r = "支付方式";

        //var g = parseInt(t / 100) > x ? x: parseInt(t / 100);

        var g = ffdk > x ? x: ffdk;//g=0;

        var w = 0;

        var e = 0;

		var checkpay='nosel';//选择支付方式

		var banktype='nobank';


		//17.3.17 判断是否已成团
        if(status == 0){
            $('#btnPay').unbind('click').css('background','#9e9e9e').text('已成团');
            return;
        }
        if(status == 2){
            $('#btnPay').unbind('click').css('background','#9e9e9e').text('已参团');
            return;
        }
        /**
         * 微商城的支付直接看false分支，g是判断是否福分大于支付总金额，大于，g=支付总金额，小于，g=福分
         */
        if (g < x) {

            var j = parseInt(d);//账户余额

            if (j > 0) {

                var i = x - g;//合计价格

                if (j >= i) {//余额大于合计价格

                    w = i;
                    e = i;

                } else {//余额小于合计价格

                    if(p.attr("sel") == 1){ w = j;

                    e = i-j ;//差值
                }
                    else{
                        e=i;
                    }

                   

                }

            } else {//账户余额为0:项目没有余额，直接进入这个分支

                e = x - g;//此次购买的总金额

            }

        }



        var f = function(y) {//选中或取消余额支付
            w = y;

            if (y > 0) {

              //  p.parent().removeClass("z-pay-grayC");

                p.attr("sel", "1").addClass('all_checked1').next("span").html('余额支付<em class="orange">' + y + ".00</em>元（账户余额：" + d + " 元）")

				checkpay='money';

				banktype='nobank';

            } else {

                p.attr("sel", "0").attr("class", "z-pay-ment").next("span").html('余额支付<em class="orange">0.00</em>元（账户余额：' + d + " 元）")

            }

        };

        var k = function(y) {

            e = y;

            if (y > 0) {

                h.html('选择支付方式'+ "支付" + ((g > 0 || w > 0) ? "剩余": "") + '<em class="orange">' + e + ".00</em>元");

               // h.nextAll().show();

                o = true;

				checkpay='bank';

				if(r=='建设银行'){

				  banktype='CCB-WAP';

				}else if(r=='支付方式'){

				  banktype='wxpay_wap';

				}else if(r=='工商银行'){

				  banktype='ICBC-WAP';

				}

            } else {

                h.html('选择支付方式支付').nextAll().find('i').removeClass("all_checked");
                e=x;

                o = false;

            }

        };

        //项目没有福分，这段代码可以省略不看
        if (ffdk > 0) {

            c.parent().click(function() {

                k(0);

                if (c.attr("sel") == 1) {

                    q(0);

                    n(x)

                } else {

                    var y = ffdk;

                    if (y > 0) {

                        q(y >= x ? x: y);

                        n(y >= x ? 0 : x - y)

                    } else {

                        n(x)

                    }

                }

            });

            var n = function(z) {

                if (p.attr("sel") == 1) {

                    var y = parseInt(d) - z;

                    if (y > 0) {

                        f(z)

                    } else {

                        f(parseInt(d));

                        k( - y)

                    }

                } else {

                    k(z)

                }

            }

        }
        //项目没有余额，这段代码可以省略不看
        if (parseInt(d) > 0) {
	
            p.parent().click(function() {



                k(0);

                if (p.attr("sel") == 1) {

                    f(0);//取消选中

                    k(x);

                } else {

                    var y = parseInt(d);

                    if (y > 0) {

                        f(y >= x ? x: y);

                        k(y >= x ? 0 : x - y)

                    } else {

                        k(x)

                    }

                }

            });

            var l = function(z) {

                if (c.attr("sel") == 1) {

                    var y = ffdk - z;

                    if (y > 0) {

                        q(z)

                    } else {

                        q(ffdk);

                        k( - y)

                    }

                } else {

                    k(z);

                }

            }

        }

        /**
         * 微商城的支付从这里开始
         */
        var o = false;

        var v = 1;

        $("li", m).each(function(y) {

            var z = $(this);    

            z.click(function() {
                //alert(y)
                v = y;
                r = z.text();
                //alert(r)
                m=parseInt(d);

                z.children("i").addClass("all_checked");
                if(m>0 && m>=x){ //微商城项目没有使用余额，此处可是省略不看
                    f(0);
                }

                z.siblings().each(function() {
                    //支付是单选，移除同辈选择的类
                    $(this).children("i").removeClass("all_checked");
                });
                    
                // h.html('选择<b class="z-mlr">' + r + "</b>支付" + ((g > 0 || w > 0) ? "剩余": "") + '<em class="orange">' + e + ".00</em>元")
                banktype='wxpay_wap';

                checkpay='bank';

                w=x;

            })
            //z.trigger("click");
        });
        //默认选择第一个支付方式
        m.find('li').eq(0).trigger('click');
        /*if (e > 0) {
            alert(e);
            h.html('选择<b class="z-mlr">' + r + "</b>支付" + ((g > 0 || w > 0) ? "剩余": "") + '<em class="orange">' + e + ".00</em>元").nextAll().show();
            o = true;
            banktype='wxpay_wap';
            checkpay='bank';
        }else{
            //余额大于0情况下默认选择余额支付 7.15郑龙改
        	p.parent().removeClass("z-pay-grayC");
            p.attr("sel", "1").addClass('all_checked').next("span").html('余额支付<em class="orange">' + x + ".00</em>元（账户余额：" + d + " 元）");
            //h.addClass("z-pay-grayC").html('选择支付方式支付').nextAll().hide();
            o = false
        }*/

        var s = $("#btnPay");

        var u = function() {
            var addrId = $('.addrId').val()?$('.addrId').val():0;
            var remark = $("input[name='remark']").val();
            //addrId = addrId?addrId:0;
        	var payment = $('.all_checked').length; //支付方式的个数，由于没有余额，所以只有1

        	if (payment) {
        		banktype = $('.all_checked').parent('li').attr('urm');
        	}
        	//alert(banktype);return;

			var submitcode = Path.submitcode

			if(!this.cc){ //判断是否重复提交订单，点击一次，cc=1，再次提交就为!cc为false

				this.cc = 1;

			}else{

				alert("不可以重复提交订单!")

				return false;

			}
			//alert(checkpay);return;

            if(checkpay=='nosel' && banktype=='nobank'){

                alert("请选择一种支付方式！");

			    if(this.cc){ //程序顺序执行，cc为1

				    this.cc = false;

			    }
			    return;
			}
            if (!a) {
                return;
            }

            if (w + g >=x) {

                a = false;

                s.unbind("click").addClass("dis");

			    if (shopnum != -1 && addrId > 0) {

					if (shopnum == 0) {
                    
						location.replace(Gobal.Webpath+"/mobile/cart/paysubmit/"+checkpay+"/"+banktype+"/"+x+"/"+t+"/"+submitcode+"/"+addrId+'/'+remark);

					} else {

						if (shopnum == 1) {

							alert("亲，您的购物车中没有商品哦，去选购一些吧。");

							location.replace(Gobal.Webpath+"/mobile/cart/cartlist");

						} else {

							if (shopnum == 10) {

								location.reload();

							}

						}

					}

				}else{
                    alert("请选择或添加收货地址");

                    location.replace(Gobal.Webpath+"/mobile/cart/pay");
                }

				s.bind("click", u).removeClass("dis");

				a = true

            } else {

                if (e > 0) {

                    if (v == 1 || v == 2 || v == 3 && addrId > 0) {
                    
                        //location.href = Gobal.Webpath+"/mobile/cart/paysubmit/"+checkpay+"/"+banktype+"/"+x+"/"+t;
                        location.replace(Gobal.Webpath+"/mobile/cart/paysubmit/"+checkpay+"/"+banktype+"/"+x+"/"+t+"/"+submitcode+"/"+addrId+'/'+remark)
                    }else{
                        alert("请选择或添加收货地址");
                        location.replace(Gobal.Webpath+"/mobile/cart/pay");
                    }

                }

            }

        }

        s.bind("click", u);

        a = true;

    }

    Base.getScript(Gobal.Skin + "/js/mobile/pageDialog.js", b);

});