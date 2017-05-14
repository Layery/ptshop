
setTimeout(function() {
    var num1 = 0;
    $($(".yscroll_list_left p")[0]).clone(true).insertAfter($($(".yscroll_list_left p")[$(".yscroll_list_left p").length - 1]));
    function move() {
        num1 = num1 - 24;
        if (num1 >= -($(".yscroll_list_left p").length - 2) * 24) {
            $(".yscroll_list_left").animate({
                marginTop: num1
            }, 2000);
        } else {
            $(".yscroll_list_left").animate({
                marginTop: num1
            }, 2000, function() {
                num1 = 0;
                $(".yscroll_list_left").css({
                    marginTop: 0
                });
            });
        }
    }
    ;var t = setInterval(move, 3000);
    $(".yscroll_list_left").hover(function() {
        clearInterval(t);
    }, function() {
        t = setInterval(move, 4000);
    })

    $(".yscroll_list_right li").hover(function() {
        clearInterval(t);
    }, function() {
        var mls = $(".yscroll_list_left").css("marginTop");
        num1 = mls;
        t = setInterval(move, 4000);
    })
    $(".yConulout").hover(function() {
        $(this).find(".yConuloutbtn").show();
    }, function() {
        $(this).find(".yConuloutbtn").hide();
    });
   
   }, 500);

