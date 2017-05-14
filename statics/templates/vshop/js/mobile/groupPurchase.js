	//时间戳转换成日期
	/*function UnixToDate(unixTime, isFull, timeZone) {
        if (typeof (timeZone) == 'number')
        {
            unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
        }
        var time = new Date(unixTime * 1000);
        var ymdhis = "";
        ymdhis += time.getUTCFullYear() + "-";
        ymdhis += (time.getUTCMonth()+1) + "-";
        ymdhis += time.getUTCDate();
        if (isFull === true)
        {
            ymdhis += " " + time.getUTCHours() + ":";
            ymdhis += time.getUTCMinutes() + ":";
            ymdhis += time.getUTCSeconds();
        }
        return ymdhis;
    }*/

    //拼团倒计时        
    function getNowTime(mytime,gCode,timestamp) {
        var unixTime = parseInt(timestamp);
        var oldtime = new Date(unixTime*1000);
        var orderDay = oldtime.getDate();
        var orderHour = oldtime.getHours();
        var orderMin = oldtime.getMinutes();
        var orderSec = oldtime.getSeconds();

        var date = new Date();
        var oneday = 24*60*60*1000;
        var e_day = (date.getTime()-oldtime)/oneday;//下单到现在相差天数
        var e_hours = e_day*24;//下单到现在相差小时
        /*var floorHours = Math.floor(e_hours);
        var e_minutes = (e_hours-floorHours)*60;
        var floorMinutes = Math.floor(e_minutes);
        var e_seconds = (e_minutes-floorMinutes)*60;
        var floorSeconds = Math.floor(e_seconds);*/

        var surHour = mytime - e_hours;
        var f_surHour = Math.floor(surHour);
        var surMins = (surHour - f_surHour)*60;
        var f_surMins = Math.floor(surMins)
        var surSec = (surMins - f_surMins)*60;
        var f_surSec = Math.floor(surSec);

        /*console.log(f_surHour);
        console.log(f_surMins);
        console.log(f_surSec);

        var nowDay = date.getDate();
        var nowHour = date.getHours();
        var nowMin = date.getMinutes();
        var nowSec = date.getSeconds();

        
		console.log("下单时间"+orderDay+"号"+orderHour+":"+orderMin+":"+orderSec);
        console.log("现在时间"+nowDay+"号"+nowHour+":"+nowMin+":"+nowSec);*/
        //console.log("截止时间"+endDay+"号"+endHour+"时");


        var minutes = 59;
        var seconds = 59;
        var surplusH = f_surHour;
        var surplusM = f_surMins;
        var surplusS = f_surSec;
        if (surHour<0) {
        	surplusH=surplusM=surplusS=0;
        }
        var countDown = parseInt(gCode);
        countDown = setInterval(function(){
        	if (surplusS == 0 && surplusM == 0 && surplusH == 0) {
        		clearInterval(countDown);
        		$('.groupPartner-body [groupcode="'+gCode+'"]').remove();
        	}

        	if (surplusS>0) {
        		surplusS--;
        		if (surplusS<10) {
		            $('.'+gCode+' .groupSec').text("0"+surplusS);
		        }else{
		            $('.'+gCode+' .groupSec').text(surplusS);
		        }
		        
        	}else{
        		if (surplusH == 0 && surplusM == 0) {
        			
        			surplusS = 0;
        			$('.'+gCode+' .groupSec').text("0"+surplusS);
        		}else{
        			surplusS = seconds;
        			$('.'+gCode+' .groupSec').text(surplusS);
        			surplusM--;
        		}
        	}
	        if (surplusM>0) {
        		if (surplusM<10) {
		            $('.'+gCode+' .groupMin').text("0"+surplusM);
		        }else{
		            $('.'+gCode+' .groupMin').text(surplusM);
		        }
        	}else{
        		if (surplusH == 0) {
        			surplusM = 0;
        			$('.'+gCode+' .groupMin').text("0"+surplusM);
        		}else{
        			
	        		surplusM = minutes;
	        		$('.'+gCode+' .groupMin').text(surplusM);
	        		surplusH--;
        		}
        		
        	}
	        if (surplusH>=0) {
	        	if (surplusH<10) {
		            $('.'+gCode+' .groupHou').text("0"+surplusH);
		        }else{
		            $('.'+gCode+' .groupHou').text(surplusH);
		        }
	        }else{
	        	surplusH = 0;
	        }/*
	        console.log(surplusH);
	        console.log(surplusM);
	        console.log(surplusS);
	        console.log("=======")*/
        },1000);
        //setTimeout(getNowTime,1000);
    }
    //getNowTime();//函数执行
