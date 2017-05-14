var isGotoCart=false;//是否跳转到购物车页面
var gotoHtml="";//跳转链接
$(function(){
	
		$(".loginContent #user input").focus(function(){
			$(".loginContent #user input").css({color:"#333"});
			$(".c_error_username").hide();
			if($(".loginContent #user input").val() == "用户名/邮箱/手机号"){
				$(".loginContent #user input").val("");
			}
		})
		
		$(".loginContent #user input").blur(function(){
			var username = $(".loginContent #user input").val();
			if(username =="用户名/邮箱/手机号" || username =="" || username == null){
				$("#username").val("用户名/邮箱/手机号").css({color:"#c9c9cf"});
			}
		})
		
		$("#prompt").focus(function(){
			 $(this).hide();
			 $("#password").show();
			 $("#password").focus();
		})
		$("#password").blur(function(){
			var password = $("#password").val();
			if( password=="请输入密码" || password == "" || password == null){
				$(this).hide();
				 $("#prompt").show();
			}
		})
		//点击用户名错误提示信息
		$(".c_error_username").click(function(){
			$(".loginContent #user input").focus()
			$(".c_error_username").hide();
		});
  
	    $("#password").focus(function(){
			$("#password").css({color:"#333"});
			$(".c_error_password").hide();
			if($("#password").val() == "请输入密码"){
				$("#password").val("");
			}
		})
		//点击密码错误提示信息
		$(".c_error_password").click(function(){
			$(".loginContent #pas input").focus()
			$(".c_error_password").hide();
		});
	 	//按回车键执行下一步操作(按回车登录)
	   $(document).keydown(function(event){ 
	   	 if(event.keyCode == 13){
	   		$("#loginSubmit").trigger("click");
	   	}
	   });  
		//会员登录
		$("#loginSubmit").click(function(){
				var username = $("#username").val();		
				var password = $("#password").val();	
				if(username==null || username == "" || username == "用户名/邮箱/手机号" ){
					$(".c_error_username").html("请输入用户名或邮箱或手机号");
					$("#username").siblings(".c_error_username").show(100).delay(2000).hide(0);
					return ;
				}else if(password==null ||password =="" || password == "请输入密码"){
					$(".c_error_password").html("请输入密码");
					$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
					return ;
				}else{
					/*var passwordTips = password;
					doEncrypt();*/
					password = $("#password").val();
					
					//$("#loginSubmit").val("等待...");
					$.ajax({
						type: "post",
						url: "/api/uc/popupDoLogin.do?id="+Math.random(),
						dataType:'json',
						data:{
							username :username,
							password : password,
						},
						success:function(data){
							if(data.status){
								jaaulde.utils.cookies.set('orderUrl', '',{path:"/"});
								//登录弹窗关闭
								$(".a_world_bg").hide();
					            $(".a_login_fixed_box").hide();
					            if(isGotoCart){
					            	window.location.href=gotoHtml;
					            }
					            //修改登录与注册信息
								$('.li_popup_login').html('<a href="/member/logout.do">退出</a>');
								$('.li_popup_doregister').html('<a href="/member_new/account.do">'+data.member.mobile+'</a>');
								//设置userMid
								userMId=data.member.mid;
								$('#mid').val(userMId);
								$("#signTime").val(data.member.signTime.time);
								$("#signDays").val(data.member.signDays);
								checkSign();
							}else{ 
								//$("#loginSubmit").val("立即登录");
								if(data.code == 121010 || data.code == "login-e1"){
									$(".c_error_username").html(data.msg);
									$("#username").siblings(".c_error_username").show(100).delay(2000).hide(0);
								}else if(data.code == 121004 || data.code == "login-e2" ){
									$(".c_error_password").html(data.msg);
									$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
								}else{
									$(".c_error_password").html(data.msg);
									$("#password").siblings(".c_error_password").show(100).delay(2000).hide(0);
								}
								
							}
						}
					});		
			}
			 		
		});
})
//调用登录弹窗
function go2Login(gotoCart){
	$(window).resize(function(){
		var worldw=$(window).width();
		var worldh=$(window).height();
		$(".a_world_bg").css({height:worldh+"px"});
		$(".a_login_fixed_box").css({left:(worldw-$(".a_login_fixed_box").outerWidth())/2+"px",top:(worldh-$(".a_login_fixed_box").outerHeight())/2+"px"});
		$(".a_register_fixed_box").css({left:(worldw-$(".a_register_fixed_box").outerWidth())/2+"px",top:(worldh-$(".a_register_fixed_box").outerHeight())/2+"px"});
		//S 登录弹窗
		
		$(".a_login_fixed_box").show();
		$(".a_world_bg").show();
		
		$(".a_login_world_close").click(function(){
			$(".a_world_bg").hide();
			$(".a_login_fixed_box").hide();
		});
		//E 登录弹窗 
	})
	$(window).resize();
	if(gotoCart){
		isGotoCart = true;
		gotoHtml=gotoCart;
	}
}
