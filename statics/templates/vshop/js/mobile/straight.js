$(function(){
    //Base.getScript(Gobal.Skin+"/js/mobile/jquery.js");
    var cut=$('.cut');
    var plus=$('.plus');
    var count=$('.goods_count em');
    var price=$('.total_price b').text();
    var show=$('.select_num');
    var total_price=$('#total-price');
    var know = $('.know');
    var submitBtn=$('#straight_btn');
    know.click(function () {
        $('.dialog-comfirm').hide();
    });

    plus.click(function(){
        var i=show.text();
        i++;
        total_price.text(price*i);
        show.text(i);
        count.text(i);
    })

    cut.click(function() {
        if(show.text()>=1){
            var j=show.text();
            j--
            if(j>=1){
                show.text(j);
                count.text(j);
                total_price.text(price*j);
            }
            else{
                show.text(1);
                count.text(1);
            }


        }
        else{
            show.text(1);
            count.text(1);
        }

    });
    //获取cookie，修改商品数量
    function getCookie(cookie_name)
    {
        var allcookies = document.cookie;
        var cookie_pos = allcookies.indexOf(cookie_name);   //索引的长度
        // 如果找到了索引，就代表cookie存在，
        // 反之，就说明不存在。
        if (cookie_pos != -1)
        {
        // 把cookie_pos放在值的开始，只要给值加1即可。
            cookie_pos += cookie_name.length + 1;  //这里我自己试过，容易出问题，所以请大家参考的时候自己好好研究一下。。。
            var cookie_end = allcookies.indexOf(";", cookie_pos);
            if (cookie_end == -1)
            {
                cookie_end = allcookies.length;
            }
            var value = unescape(allcookies.substring(cookie_pos, cookie_end)); //这里就可以得到你想要的cookie的值了。。。
        }
        return value;
    }
    function strToJson(str){
        var json = eval('(' + str + ')');
        return json;
    }
    var shopinfo = getCookie('Cartlist');
    //alert(shopinfo)
    if(typeof (shopinfo) == 'string'){
        var shopJson = strToJson(shopinfo);
        var shopId = $('.shopId').val();
        var shopitem = shopJson[shopId];
        var num = shopitem.num;
        //var price = shopitem.price;
        //alert(price)
        if(num>1){
            $('.shopNum').html(num);
            $('.select_num').html(num);
            $('#total-price').html(price*num);
        }
    }


    submitBtn.click(function(){
        var shopId = $('.shopId').val();
        var uid = $('.uid').val();
        var addrId = $('.addrId').val();
        var shopNum = $('.shopNum').text();
        $.ajax({
            url:Gobal.Webpath+"/mobile/ajax/addShop",
            data:{
                shopid:shopId,
                uid:uid,
                addrid:addrId,
                shopnum:shopNum,
                price:price
            },
            type:'post',
            dataType:'json',
            success:function (msg) {
                if(msg.code){
                    //alert(msg.uid)
                    window.location.href = Gobal.Webpath+"/mobile/cart/shop_pay";
                }else{
                    alert('请确认商品信息');
                }
            }

        });


    });



});