$(function(){

			//判断是否手机端
			var MobileUA = (function() {  
			    var ua = navigator.userAgent.toLowerCase();  
			    console.log(ua);
			
			    var mua = {  
			        IOS: /ipod|iphone|ipad/.test(ua), //iOS  
			        IPHONE: /iphone/.test(ua), //iPhone  
			        IPAD: /ipad/.test(ua), //iPad  
			        ANDROID: /android/.test(ua), //Android Device  
			        WINDOWS: /windows/.test(ua), //Windows Device  
			        TOUCH_DEVICE: ('ontouchstart' in window) || /touch/.test(ua), //Touch Device  
			        MOBILE: /mobile/.test(ua), //Mobile Device (iPad)  
			        ANDROID_TABLET: false, //Android Tablet  
			        WINDOWS_TABLET: false, //Windows Tablet  
			        TABLET: false, //Tablet (iPad, Android, Windows)  
			        SMART_PHONE: false //Smart Phone (iPhone, Android)  
			    };  
			
			    mua.ANDROID_TABLET = mua.ANDROID && !mua.MOBILE;  
			    mua.WINDOWS_TABLET = mua.WINDOWS;  
			    mua.TABLET = mua.IPAD || mua.ANDROID_TABLET || mua.WINDOWS_TABLET;  
			    mua.SMART_PHONE = mua.MOBILE && !mua.TABLET;  
			
			    return mua;  
			}());  

			if(MobileUA.WINDOWS_TABLET){
				// $('body').empty();
			}



			var contentH = $('.activity-detail').height();
				// console.log(contentH);
				if(contentH > 300){
			var half = contentH*0.3;
				contentH+=85;
			var hasopen = false;
			$('.activity-detail').height(half);

			$('.detail-cover').click(function(){
				// console.log(contentH);


				if (!hasopen) {
					hasopen = true;
					$('.activity-detail').animate({height:contentH});
					$(this).find('span').text("");
					$(this).find('i').addClass('rot');


				}
				else{
					hasopen = false;
					$('.activity-detail').animate({height:half});
					$(this).find('span').text("展开详情");
					$(this).find('i').removeClass('rot');

				}
			});
		}
		else{
			$('.detail-cover').hide();
		}


			//点击立即报名按钮

			$('.apply-btn').click(function(){

				$(this).hide();
				$('.apply-bar').show();
				$('.apply-form').stop().animate({
					opacity:'1',
					bottom:'0'
				});

				$('.apply-bar-close').on('click touchstart',function(){
					// $('.apply-form').slideUp();
					// $('.apply-bar').fadeOut();
				$('.apply-form').stop().animate({
					opacity:'0',
					bottom:'-264px'
				},500);
				$('.apply-bar').hide();
				$('.apply-btn').show();

				})

			});

			//装备推荐
			var mainWrap = $('.activity-suit-list'),
				suitItem = $('.activity-suit-list').find('li'),
				prevBtn = $('.suit-list-prev'),
				nextBtn = $('.suit-list-next');
			var len = suitItem.length;
			var posL = parseInt(mainWrap.css('left'));
			var maxL = suitItem.width()*(len-1);
			var wid = len*100 + '%';
			var itemW = 100/len +'%';
			// var run = fa
			
			mainWrap.width(wid);
			suitItem.width(itemW);
			nextBtn.click(function(){
				if(!mainWrap.is(':animated')){
					mainWrap.animate({
						left:"-=100%"
					},function(){
						prevBtn.show();
						posL = parseInt(mainWrap.css('left'));
						if(Math.abs(posL) >= Math.abs(maxL)){

							nextBtn.hide();
						}

					});
				}
	
			});
			if(posL <= 0){
				prevBtn.hide();
			}
			prevBtn.click(function(){
				if(!mainWrap.is(':animated')){
					mainWrap.stop(true,false).animate({
						left:'+=100%'
					},function(){
						nextBtn.show();
						posL = parseInt(mainWrap.css('left'));
						if (Math.abs(posL) <= 0) {
							prevBtn.hide();
						}

					});
				}

			});
			var touchItem = document.getElementById('mainWrap');
			touches(touchItem,'swipeleft',function(){
				if(!nextBtn.is(':hidden')){
					nextBtn.trigger('click');
				}else{
					return false;
				}
			})
			touches(touchItem,'swiperight',function(){
				if(!prevBtn.is(':hidden')){
					prevBtn.trigger('click');
				}else{
					return false;
				}
			})
			
});

		//活动参与人数进度条
		
		function progress(){
			var point = $('.point-item');
				step = point.length - 1;
			var group = [];

			//已报名人数
			var curnum = parseInt($('.already-num').text());
			// var curnum = 30;
			//人数上限
			var maxnum = parseInt($('.last-num').text());
			var progWid = curnum/maxnum*100 + '%';

			point.each(function(index){
				if(index != 0 && index != step){
					var limit = parseInt($(this).find('.point-desc i').text());
					var pleft = limit/maxnum*100 - 12 + '%';
					$(this).css("left",pleft);
				}
			})
			point.find('.point-desc').each(function(index){
				var limit = parseInt($(this).find('i').text());
				if(curnum >= limit){
					$(this).siblings('.point').addClass('arrive');
				}
			})
			$('.signed-num').text(curnum + '人');

			$('.runner').animate({
				width:progWid
			});
		}
		//报名提交

		function isChinaName(name) {
		 var pattern = /^[\u4E00-\u9FA5a-zA-Z]{2,20}$/;
		 return pattern.test(name);
		};
		 
		// 验证手机号
		function isPhoneNo(phone) { 
		 var pattern = /^1[34578]\d{9}$/; 
		 return pattern.test(phone); 
		};
		//验证身份证号
		function isIdCard(idnum){
			var pattern = /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/;
			return pattern.test(idnum);
		};
		function applySubmit(){
			var name = $.trim($('#userName').val());
			var telNum = $.trim($('#userTel').val());
			var idnum = $.trim($('#idCard').val());
			var data = $('#applyInfo').serialize();

			if(!isChinaName(name)){
				showtips('请输入2~20位正确姓名！');
			}else if(!isPhoneNo(telNum)){
				showtips('请输入正确的手机号码！');

			}else if(!isIdCard(idnum)){
				showtips('请输入正确的身份证号！');
			}else{
				//免费和付费
				$.ajax({
					type:"POST",
					url:"/mobile/ajax/actIsFree/",
					data:data,
					dataType:"json",
					async:false,
					success:function (msg) {
						if(msg.status == 0){
							location.href = '/mobile/cart/activity_free';
						}else if(msg.status == 1){
							showtips(msg.msg);
						}else{
							$('.activity-payment-bar').slideDown('fast',function(){
								progress();
								$('.apply-bar-close').trigger('click');
								var self = $(this);
								$('.payment-bar-close').click(function(){
									self.hide();
									$('.apply-btn').trigger('click');
								})
								/* 点击支付按钮，开始支付 --start-- */
								var wx=false;
								var ua = navigator.userAgent.toLowerCase();
								if(ua.match(/MicroMessenger/i)=="micromessenger"){
									wx = true;
								}
								if(!wx){
									var pay_type = 1;
								}else{
									var pay_type = 0;
								}

								$('.activity-pay').click(function () {
									$.ajax({
										type:"POST",
										url:"/mobile/ajax/addSignToCookie/",
										data:data,
										dataType:"json",
										success:function (data) {
											if(data.status==0){
												location.href = '/mobile/cart/activity_pay/'+pay_type;
											}else{
												showtips(data.msg);
											}return false;
										}
									});
								});
								/* 点击支付按钮，开始支付 --end-- */
							});
						}
					}
				});
				/*$('.activity-payment-bar').slideDown('normol',function(){
					$('.apply-bar-close').trigger('click');
					var self = $(this);
					$('.payment-bar-close').click(function(){
						self.hide();
						$('.apply-btn').trigger('click');
					})
					/!* 点击支付按钮，开始支付 --start-- *!/
					var wx=false;
					var ua = navigator.userAgent.toLowerCase();
					if(ua.match(/MicroMessenger/i)=="micromessenger"){
						wx = true;
					}
					if(!wx){
						var pay_type = 1;
					}else{
						var pay_type = 0;
					}

					//alert(pay_type)
					//获取报名表单信息
					var data = $('#applyInfo').serialize();
					$('.activity-pay').click(function () {
						$.ajax({
							type:"POST",
							url:"/mobile/ajax/addSignToCookie/",
							data:data,
							dataType:"json",
							success:function (data) {
								if(data.status==0){
									location.href = '/mobile/cart/activity_pay/'+pay_type;
								}else{
									showtips(data.msg);
								}return false;
							}
						});
					});
					/!* 点击支付按钮，开始支付 --end-- *!/
				});*/
			}

		};

		function showDeals(type){
			var html,tit;
			var tar = $('.activity-deal-bar');
			if(type == 'duty'){
				tit = '免责协议';
				html = '<p>1.活动有风险，请谨慎报名，可以自行购买保险。</p><p>2.参与者须对自己的行为及后果负责，不建议患有高血压、心脏病及其他不适宜参与剧烈运动的疾病的患者参加高强度高风险的户外活动。</p><p>3.代他人报名者，代报名者有义务把活动情况详细告知被代报名者，被代报名者由代报名者承担责任。</p><p>4.凡参与者均视为具有完全民事行为能力人。如在活动中发生非人为主观故意造成的人身损害后果，领队及活动召集者会全力组织救助，但不承担赔偿及相关的责任。</p><p>【特别提示】当您按照报名页面提示填写信息、阅读并同意协议且完成全部报名内容后，即表示您已充分阅读、理解并接受协议的全部内容。</p>';
			}else if(type == 'group'){
				tit = '拼团说明';
				html = '<p>1、一天活动，活动开始时间前一天中午12:00以前，如因故无法参加活动的，退款需承担保险等一系列支付的费用。</p><p>2、一天活动，活动开始时间前一天中午12:00以后，需承担拼车费用，拼车费用按最终拼团价格计算。</p><p>3、两天及两天以上活动，活动开始前五天中午12:00以前，如因故无法参加活动的，退款需承担保险、及帐篷住宿等相关的定金费用。</p><p>4、两天及两天以上活动，活动开始前五天12:00之后，如因故无法参加活动的，退款需承担拼团车费及保险等一系列费用，根据实际情况退款。</p><p>5、所有活动报名截止后，如因故无法参加活动者，不予退款。</p><p>6、活动报名截止后，系统会根据当前报名人数和对应的拼团价格自动返还您差价。</p>';

			}else {
				return false;
			}
			tar.find('h3').text(tit);
			tar.find('.deal-content').html(html);
			$('.activity-deal-wrap').show();
		};

		function hideDeals(){
			$('.activity-deal-wrap').hide();
		};

		function showtips(val){
			var cont = '<div class="tips" style="position: fixed;width: 50%;left: 25%;top: 40%;height: 44px;line-height: 44px;color: #fff;text-align: center;z-index: 100;background-color: rgba(0,0,0,0.8);border-radius: 5px;font-size: 15px; ">'+val+'</div>';
			$('body').append(cont);
			setTimeout(function(){
				$('.tips').hide();
			},1000);
		}

		//手机端手势
		function touches(obj,direction,fun){  
        //obj:ID对象  
        //direction:swipeleft,swiperight,swipetop,swipedown,singleTap,touchstart,touchmove,touchend  
        //          划左，    划右，     划上，   划下，    点击，    开始触摸， 触摸移动， 触摸结束  
        //fun:回调函数  
        var defaults = {x: 5,y: 5,ox:0,oy:0,nx:0,ny:0};  
        direction=direction.toLowerCase();  
        //配置：划的范围在5X5像素内当点击处理  
        obj.addEventListener("touchstart",function() {  
            defaults.ox = event.targetTouches[0].pageX;  
            defaults.oy = event.targetTouches[0].pageY;  
            defaults.nx = defaults.ox;  
            defaults.ny = defaults.oy;  
            if(direction.indexOf("touchstart")!=-1)fun();  
        }, false);  
        obj.addEventListener("touchmove",function() {  
            event.preventDefault();  
            defaults.nx = event.targetTouches[0].pageX;  
            defaults.ny = event.targetTouches[0].pageY;  
            if(direction.indexOf("touchmove")!=-1)fun();  
        }, false);  
        obj.addEventListener("touchend",function() {  
            var changeY = defaults.oy - defaults.ny;  
            var changeX = defaults.ox - defaults.nx;  
            if(Math.abs(changeX)>Math.abs(changeY)&&Math.abs(changeY)>defaults.y){  
                //左右事件  
                if(changeX > 0) {  
                    if(direction.indexOf("swipeleft")!=-1)fun();  
                }else{  
                    if(direction.indexOf("swiperight")!=-1)fun();  
                }  
            }else if(Math.abs(changeY)>Math.abs(changeX)&&Math.abs(changeX)>defaults.x){  
                //上下事件  
                if(changeY > 0) {  
                    if(direction.indexOf("swipetop")!=-1)fun();  
                }else{  
                    if(direction.indexOf("swipedown")!=-1)fun();  
                }  
            }else{  
                //点击事件  
                if(direction.indexOf("singleTap")!=-1)fun();  
            }  
            if(direction.indexOf("touchend")!=-1)fun();  
        }, false);  
    } 