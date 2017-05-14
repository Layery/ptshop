$(function(){
	var total = $('#hidShopMoney').val();
	var balance = $('#hidBalance').val();
	/*var point = $('#hidUserPoints').val();
	var maxpoint = $('#hidMaxPoints').val();*/
	var discount = $('#hidPoints').val();
	//var rate = 0.01;
	var selNum = [];

	$('.act-pay-type-item li').click(function(){
		var self = $(this);
		var id = self.attr('id');
		if(id == 'balance'){
		 var val = balance;
		}else if(id == 'integral'){
		 var val = discount;
		}

		//console.log(val);

		if(self.hasClass('pay-selected')){
			self.removeClass('pay-selected');
			removeByValue(selNum,val);
			if(id == 'balance'){
				self.find('.type-text').text('我的余额');
			}
			priceCalc();

		}else{
			self.addClass('pay-selected');
			selNum.push(val);
			if(id == 'balance'){
				self.find('.type-text').text('使用余额');
			}
			priceCalc();
		}
		//console.log(selNum);
	});
	//支付
	$('.straight_btn').click(function () {
		var act_id = $('.act_id').val();
		var my_balance = $('#balance').hasClass('pay-selected')? balance : 'none';
		var integral = $('#integral').hasClass('pay-selected')? discount : 'none';
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
		$.ajax({
			type:"POST",
			url:"/mobile/ajax/updateSignCookie/",
			data:{act_id:act_id,balance:my_balance,integral:integral},
			dataType:"json",
			success:function (data) {
				if(data.status==0){
					location.href = '/mobile/cart/activity_pay/'+pay_type;
					//location.href = '/mobile/activity/pay/'+pay_type;
				}else{
					showtips(data.msg);
				}return false;
			}
		})
	});


	function priceCalc(){
		var sumSel = eval(selNum.join('+')) || 0;
		var lastPrice = (total - sumSel).toFixed(2);
		//var lastPrice = total - sumSel;
		//console.log(sumSel);
		//console.log(lastPrice);
		if(lastPrice > 0){
			$('#total-price').text(lastPrice);
			$('.payBar').find('span').show();
			$('.payBar').find('#btnPay').animate({
				width: "50%"},200);
		} else{
			$('.payBar').find('span').hide();
			$('.payBar').find('#btnPay').animate({
				width: "100%"},200);
		}
	}

});
function removeByValue(arr, val) {
  for(var i=0; i<arr.length; i++) {
    if(arr[i] == val) {
      arr.splice(i, 1);
      break;
    }
  }
}
function showtips(val){
	var cont = '<div class="tips" style="position: fixed;width: 50%;left: 25%;top: 40%;height: 44px;line-height: 44px;color: #fff;text-align: center;z-index: 100;background-color: rgba(0,0,0,0.8);border-radius: 5px;font-size: 15px; ">'+val+'</div>';
	$('body').append(cont);
	setTimeout(function(){
		$('.tips').hide();
	},1000);
}