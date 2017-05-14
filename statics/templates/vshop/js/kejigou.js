		 function setCookie(name,value,time){ 
      var strsec = getsec(time); 
      var exp = new Date(); 
      exp.setTime(exp.getTime() + strsec*1); 
      document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
  } 

  function getsec(str){ 
     //alert(str); 
     var str1=str.substring(1,str.length)*1; 
     var str2=str.substring(0,1); 
     if (str2=="s"){ 
          return str1*1000; 
     }else if (str2=="h"){ 
         return str1*60*60*1000; 
     }else if (str2=="d"){ 
         return str1*24*60*60*1000; 
     } 
  } 

  function getCookie(name) { 
      var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
   
      if(arr=document.cookie.match(reg))
          return unescape(arr[2]); 
      else 
          return null; 
  } 

  function delCookie(name) { 
      var exp = new Date(); 
      exp.setTime(exp.getTime() - 1); 
      var cval=getCookie(name); 
      if(cval!=null) 
          document.cookie= name + "="+cval+";expires="+exp.toGMTString(); 
  } 


  function init(){
    $(".outbj").show();
    $(".ad").show();
    $(window).resize(function(){
      $(".outbj").css({width:$(document).width()+"px",height:$(document).height()+"px"});
      $(".ad").css({top:($(window).height()-591)/2+"px",left:($(window).width()-436)/2+"px"});
    })
    $(window).resize();
    $(".closeBtn").click(function(){
      $(".outbj").hide();
      $(".ad").hide();
    })
  }
  $(function() {
    if(!getCookie("my")){
        init();
        setCookie('my','111','d1');
      }
  });
   $(document).ready(function(){

      $("#pageflip").hover(function(){
        $("#pageflip img , .msg_block").stop().animate({width: '100px', height: '100px'}, 500); 
      },function(){
        $("#pageflip img").stop().animate({width: '50px', height: '50px'}, 220);
        $(".msg_block").stop().animate({width: '50px', height: '50px'}, 200);
      });

    });  
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   
		   function delheader(id) {
                var Cartlist = $.cookie('Cartlist');
                var info = $.evalJSON(Cartlist);
                var num = $("#sCartTotal2").html() - 1;
                var sum = $("#sCartTotalM").html();
                info['MoenyCount'] = sum - info[id]['money'] * info[id]['num'];

                delete info[id];
                //$.cookie('Cartlist','',{path:'/'});
                $.cookie('Cartlist', $.toJSON(info), {
                    expires: 30,
                    path: '/'
                });
                $("#sCartTotalM").html(info['MoenyCount']);
                $('#sCartTotal2').html(num);
                $('#sCartTotal').text(num);
                $('#btnMyCart em').text(num);
                $("#mycartcur" + id).remove();
            }
            $(function() {
                $(".h_1yyg").mouseover(function() {
                    $(".h_1yyg_eject").show();
                });
                $(".h_1yyg").mouseout(function() {
                    $(".h_1yyg_eject").hide();
                });
                $(".h_news").mouseover(function() {
                    $(".h_news_down").show();
                });
                $(".h_news").mouseout(function() {
                    $(".h_news_down").hide();
                });
            });
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  $(function() {
                    $($(".pullDownList li")[0]).addClass("menuliselected");
                    $($(".yBannerList")[0]).addClass("ybannerExposure");
                    $($(".yBannerList")[0]).show().siblings().hide();
                })
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 var Base = { head: document.getElementsByTagName("head")[0] || document.documentElement, Myload: function (B, A) { this.done = false; B.onload = B.onreadystatechange = function () { if (!this.done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) { this.done = true; A(); B.onload = B.onreadystatechange = null; if (this.head && B.parentNode) { this.head.removeChild(B) } } } }, getScript: function (A, C) { var B = function () { }; if (C != undefined) { B = C } var D = document.createElement("script"); D.setAttribute("language", "javascript"); D.setAttribute("type", "text/javascript"); D.setAttribute("src", A); this.head.appendChild(D); this.Myload(D, B) }, getStyle: function (A, CB) { var B = function () { }; if (CB != undefined) { B = CB } var C = document.createElement("link"); C.setAttribute("type", "text/css"); C.setAttribute("rel", "stylesheet"); C.setAttribute("href", A); this.head.appendChild(C); this.Myload(C, B) } }
     function GetVerNum() { var D = new Date(); return D.getFullYear().toString().substring(2, 4) + '.' + (D.getMonth() + 1) + '.' + D.getDate() + '.' + D.getHours() + '.' + (D.getMinutes() < 10 ? '0' : D.getMinutes().toString().substring(0, 1)) }
     Base.getScript('/statics/templates/vshop/js/Bottom2.js?v=' + GetVerNum());



     var Base = { head: document.getElementsByTagName("head")[0] || document.documentElement, Myload: function (B, A) { this.done = false; B.onload = B.onreadystatechange = function () { if (!this.done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) { this.done = true; A(); B.onload = B.onreadystatechange = null; if (this.head && B.parentNode) { this.head.removeChild(B) } } } }, getScript: function (A, C) { var B = function () { }; if (C != undefined) { B = C } var D = document.createElement("script"); D.setAttribute("language", "javascript"); D.setAttribute("type", "text/javascript"); D.setAttribute("src", A); this.head.appendChild(D); this.Myload(D, B) }, getStyle: function (A, CB) { var B = function () { }; if (CB != undefined) { B = CB } var C = document.createElement("link"); C.setAttribute("type", "text/css"); C.setAttribute("rel", "stylesheet"); C.setAttribute("href", A); this.head.appendChild(C); this.Myload(C, B) } }
     function GetVerNum() { var D = new Date(); return D.getFullYear().toString().substring(2, 4) + '.' + (D.getMonth() + 1) + '.' + D.getDate() + '.' + D.getHours() + '.' + (D.getMinutes() < 10 ? '0' : D.getMinutes().toString().substring(0, 1)) }
     Base.getScript('/statics/templates/vshop/js/Bottom.js?v=' + GetVerNum());