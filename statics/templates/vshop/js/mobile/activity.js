$(function(){
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

				$('.apply-bar-close').on('click touchstart',function(e){ //关闭按钮
					e.stopPropagation();
				$('.apply-form').stop().animate({
					opacity:'0',
					bottom:'-264px'
				},500);
				$('.apply-bar').hide();
				$('.apply-btn').show();

				})

			});


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
			var maxnum = parseInt($('.last-num').text()) || 300;
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
				//拼团：不免费；不拼团：免费、不免费
				$.ajax({
					type:"POST",
					url:"/mobile/ajax/actIsFree/",
					data:data,
					dataType:"json",
					async:false,
					success:function (msg) {
						/* 点击支付按钮，开始支付 --start-- */
						var wx=false;
						var ua = navigator.userAgent.toLowerCase();
						if(ua.match(/MicroMessenger/i)=="micromessenger"){
							wx = true;
						}
						if(!wx){ //确认支付方式的环境，同时区别登录方式
							var pay_type = 1;
						}else{
							var pay_type = 0;
						}
						if(msg.status == 0){ //免费
							location.href = '/mobile/cart/activity_free/'+pay_type;
						}else if(msg.status == 1){
							showtips(msg.msg);
						}else{ //付费
							$('.activity-payment-bar').slideDown('fast',function(){

								progress();
								$('.apply-bar-close').trigger('click');
								var self = $(this);
								$('.payment-bar-close').click(function(){
									self.hide();
									$('.apply-btn').trigger('click');
								})

								$('.activity-pay').click(function () {
									location.href = '/mobile/activity/pay/'+pay_type;
									/*$.ajax({
										type:"POST",
										url:"/mobile/ajax/addSignToCookie/",
										data:data,
										dataType:"json",
										success:function (data) {
											if(data.status==0){
												//location.href = '/mobile/cart/activity_pay/'+pay_type;
												location.href = '/mobile/activity/pay/'+pay_type;
											}else{
												showtips(data.msg);
											}return false;
										}
									});*/
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
				html = '<p>1.活动报名限时，在活动开始时间前一天下午17:00截止报名。</p><p>2.活动开始时间前一天中午12:00之前，如因故无法参加活动，可申请全额退款。</p><p>3.活动开始时间前一天中午12:00之后，活动报名截止之前，如因故无法参加活动，可申请退款，但是需要承担拼车费用。</p><p>4.活动报名截止后，如因故无法参加活动者，不予退款。</p><p>5.活动报名截止后，根据当前报名人数和对应的拼团价格返还差价。</p>';

			}else if(type == 'refund'){
				tit = '退款说明';
				html = '<<p>1.活动开始时间前一天中午12:00之前，如因故无法参加活动，可申请全额退款。</p><p>2.活动开始时间前一天中午12:00之后，活动报名截止之前，如因故无法参加活动，可申请退款，但是需要承担拼车费用。</p><p>3.活动报名截止后，如因故无法参加活动者，不予退款。</p><p>4.活动报名截止后，根据当前报名人数和对应的拼团价格返还差价。</p>';
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
				$('.tips').remove();
			},1000);
		}

	