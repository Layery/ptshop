$(function() {

    var d = 10;

    var g = false;

    var a = null;

    var f = $(".g-Total");

    var b = null;

    var c = 1;

	var banktype='wxpay_wap';

    var e = function() {

        var k = function(p, o, n, m) {

            $.PageDialog.fail(p, o, n, m)

        };

        function l(m) {

            m = Math.round(m * 1000) / 1000;

            m = Math.round(m * 100) / 100;

            if (/^\d+$/.test(m)) {

                return m + ".00"

            }

            if (/^\d+\.\d$/.test(m)) {

                return m + "0"

            }

            return m;

        }

        var h = /^[1-9]{1}\d*$/;

        var j = "";

        var i = function() {

            var m = a.val();

            if (m != "") {

                if (j != m) {

                    if (!h.test(m)) {

                        a.val(j).focus()

                    } else {

                        j = m;

                        f.html('充值金额：<span class="gcolor arial">'+ l(m) +"</span>（元）");

                    }

                }

            } else {

                j = "";

                a.focus();

                f.html('充值金额：<span class="gcolor arial">0.00</span>（元）');

            }

        };

        $("#ulOption > li").each(function(m) {

            var n = $(this);

            if (m < 5) {

                n.click(function() {

                    g = false;

                    d = n.attr("money");

                    n.children("a").addClass("z-sel");

                    n.siblings().children().removeClass("z-sel").removeClass("z-initsel");

                    f.html('充值金额：<span class="gcolor arial">'+ n.attr("money") +"</span>（元）");


                })

            } else {

                a = n.find("input");

                a.focus(function() {

                    g = true;

                    if (a.val() == "输入金额") {

                        a.val("")

                    }

                    a.parent().addClass("z-initsel").parent().siblings().children().removeClass("z-sel");

                    if (b == null) {

                        b = setInterval(i, 200)

                    }

                }).blur(function() {

                    clearInterval(b);

                    b = null

                })

            }

        });

        $("#ulBankList > li").each(function(m) {

            var n = $(this);

			if (m == 0) {

                //f = n;

            } else {

                n.click(function() {

                    c = m;

					banktype=n.attr('urm');

                    n.children("i").addClass("all_checked");

                    n.siblings().children("i").removeClass("all_checked");

                })

            }

        });

        $("#btnSubmit").click(function() {

            d = g ? a.val() : d;


            if (d == "" || parseInt(d) == 0) {

                k("请输入充值金额")

            } else {

                var m = /^[1-9]\d*\.?\d{0,2}$/;

                if (m.test(d)) {

                    if (c == 1 || c==2 ||c==3) {
                        if(banktype=='wxpay_wap'){

                            k("请选择支付方式！");

                        }
                        else{

                        location.href = Gobal.Webpath+"/mobile/cart/addmoney/" + d+"/"+banktype

                        }
                    }

                } else {

                    k("充值金额输入有误");

                }

            }

        });



		//$("#ulBankList>li:eq(1)").click();

    };

    Base.getScript(Gobal.Skin + "/js/mobile/pageDialog.js", e)

});