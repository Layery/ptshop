<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_class('base','member','no');
System::load_app_fun('my','go');
System::load_app_fun('user','go');
System::load_sys_fun('send');
System::load_sys_fun('user');
class home extends base {
	public function __construct(){
		parent::__construct();
		if(ROUTE_A!='userphotoup' and ROUTE_A!='singphotoup'){
			//var_dump(intval(_encrypt(_getcookie("uid"),'DECODE')));
			//var_dump(_getcookie("ushell"));exit;
			if(!$this->userinfo){
				if(!isset($_GET['wxid'])){
					//header("location:".WEB_PATH."/mobile/user/login");
                    header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
					//_messagemobile("请登录",WEB_PATH."/mobile/user/login",3);
				}else{
					$wxid = $_GET['wxid'];
					if (empty($wxid)){
						//header("location:".WEB_PATH."/mobile/user/login");
                        header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
						//_messagemobile("请登录",WEB_PATH."/mobile/user/login",3);
					}
					$mem=$this->db->GetOne("select * from `@#_member_band` where `b_code`='".$wxid."'");
					if (empty($mem)){
						//header("location:".WEB_PATH."/mobile/user/login");
                        header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
						//_messagemobile("请登录",WEB_PATH."/mobile/user/login",3);
					}
					$this->userinfo=$member=$this->db->GetOne("select * from `@#_member` where `uid`='".$mem['b_uid']."'");
					_setcookie("uid",_encrypt($member['uid']),60*60*24*7);
					_setcookie("ushell",_encrypt(md5($member['uid'].$member['password'].$member['mobile'].$member['email'])),60*60*24*7);
				}
				if(isset($_GET['code'])){
					$this->conf = System::load_app_config("connect",'','api');
					//echo $_GET['code'];exit;
					$code = $_GET['code'];
					$state = $_GET['state'];
					if (empty($code)){
						//header("location:".WEB_PATH."/mobile/user/login");exit;
                        header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
					}
					$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->conf['weixin']['id'].'&secret='.$this->conf['weixin']['key'].'&code='.$code.'&grant_type=authorization_code';
					$token = json_decode(getCurl($token_url));
					$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->conf['weixin']['id'].'&grant_type=refresh_token&refresh_token='.$token->refresh_token;
					//转成对象
					$access_token = json_decode(getCurl($access_token_url));
					$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token->access_token.'&openid='.$access_token->openid.'&lang=zh_CN';
					//转成对象
					$user_info = json_decode(getCurl($user_info_url),true);
					$weixin_openid = $user_info['openid'];
					$this->db = System::load_sys_class("model");
					$go_user_info = $this->db->GetOne("select * from `@#_member_band` where `b_code` = '$weixin_openid' LIMIT 1");
					if($go_user_info){
						$this->userinfo=$member=$this->db->GetOne("select * from `@#_member` where `uid`='".$go_user_info['b_uid']."'");
						_setcookie("uid",_encrypt($member['uid']),60*60*24*7);
						_setcookie("ushell",_encrypt(md5($member['uid'].$member['password'].$member['mobile'].$member['email'])),60*60*24*7);
					}else{
						//header("location:".WEB_PATH."/mobile/user/login");exit;
                        header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
					}
				}
			}
		}
		$this->db = System::load_sys_class('model');
	}

	public function init(){
        $homePage = json_decode(stripslashes(_getcookie('homePage')),true);
	    $webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$title="我的用户中心";
		//$quanzi=$this->db->GetList("select * from `@#_quanzi_tiezi` order by id DESC LIMIT 5");
		if(!empty($member['headimg'])){
			$member['img'] = $member['headimg'];
		}else{
			$member['img'] = G_UPLOAD_PATH.'/'.$member['img'];
		}
	    //获取用户等级  用户新手  用户小将==
	    $memberdj=$this->db->GetList("select * from `@#_member_group`");
	    $jingyan=$member['jingyan'];
	    if(!empty($memberdj)){
	        foreach($memberdj as $key=>$val){
		        if($jingyan>=$val['jingyan_start'] && $jingyan<=$val['jingyan_end']){
                    $member['yungoudj']=$val['name'];
			    }
		    }
	    }
	    //查询有效期间不同订单状态的数量
        $orderCount = $this->db->GetList("SELECT `status`,COUNT(DISTINCT `code`) AS `num` FROM `@#_member_go_record` WHERE `uid`='$member[uid]' GROUP BY `status`",array('key'=>'status'));
        foreach ($orderCount as $k => $v){
            switch ($k){
                case '未付款,未发货,未完成':
                    unset($orderCount[$k]);
                    $k = 1;
                    $orderCount[$k]['num']=$v['num'];
                    break;
                case '已付款,未发货,未完成':
                    unset($orderCount[$k]);
                    $k = 2;
                    $orderCount[$k]['num']=$v['num'];
                    //$orderCount[$k]['status']=2;
                    break;
                case '已付款,已发货,未完成':
                    unset($orderCount[$k]);
                    $k = 3;
                    $orderCount[$k]['num']=$v['num'];
                    //$orderCount[$k]['status']=3;
                    break;
                default:
                    unset($orderCount[$k]);
                    $k = -1;
                    $orderCount[$k]['num']=$v['num'];
                    //$orderCount[$k]['status']=-1;
            }
        }
        //p($orderCount);exit;

        //查询活动订单
        //$orderCount = $this->db->GetList("SELECT `status`,COUNT(DISTINCT `code`) AS `num` FROM `@#_act_record` WHERE `uid`='$member[uid]' GROUP BY `status`",array('key'=>'status'));
        //select count(*) as num from `go_act_order` a LEFT JOIN `go_activity` b on a.o_act_id = b.act_id WHERE `o_status`='已支付' and `act_start_time`> 1489656311 and `o_uid`=121 GROUP BY `o_status`
        $time = time();
        //活动等待中
        $waiting = $this->db->GetOne("select count(*) as num from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE `o_status`='已支付' and `act_start_time`> $time and `o_uid`={$member['uid']}");
        $waiting['state'] = 1;
        //活动进行中
        $execute = $this->db->GetOne("select count(*) as num from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE `o_status`='已支付' and `act_start_time`<=$time and `act_end_time`>$time and `o_uid`={$member['uid']}");
        $execute['state'] = 2;
        //活动已关闭
        $close = $this->db->GetOne("select count(*) as num from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE `o_status`='已关闭' and `o_uid`={$member['uid']}");
        $close['state'] = 4;
        //活动已结束
        $over = $this->db->GetOne("select count(*) as num from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE `o_status`='已支付' and `act_end_time`<$time and `o_uid`={$member['uid']}");
        $over['state'] = 3;


	    include templates("mobile/user","index");
    }
public function invite(){

        $webname=$this->_cfg['web_name'];

        $member=$this->userinfo;

        $title="我的用户中心";

        $uid=_getcookie('uid');

        //$quanzi=$this->db->GetList("select * from `@#_quanzi_tiezi` order by id DESC LIMIT 5");

        //获取云购等级  云购新手  云购小将==

        $memberdj=$this->db->GetList("select * from `@#_member_group`");

        $wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");

        $jingyan=$member['jingyan'];

        if(!empty($memberdj)){

            foreach($memberdj as $key=>$val){

                if($jingyan>=$val['jingyan_start'] && $jingyan<=$val['jingyan_end']){

                    $member['yungoudj']=$val['name'];

                }

            }

        }

        require_once("system/modules/mobile/jssdk.php");

         $wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");

        $jssdk = new JSSDK($wechat['appid'],$wechat['appsecret']);

        $signPackage = $jssdk->GetSignPackage();
        include templates("mobile/user","invite");

    }	



	public function inviteshare(){

		$member=$this->userinfo;

		require_once("system/modules/mobile/jssdk.php");

		 $wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");

		$jssdk = new JSSDK($wechat['appid'],$wechat['appsecret']);

		$signPackage = $jssdk->GetSignPackage();

		include templates("mobile/user","inviteshare");

	}

	public function shareinc(){

		$uid = empty($_POST['f']) ? 0 : $_POST['f'];

		$share=$this->db->GetList("select * from `@#_wxch_cfg`");

		if($uid<1){

			echo 5;die;

		}

		if(!$share[11]['cfg_value']){

			echo 1;die;

		}else{

			$info = $this->db->GetOne("SELECT * FROM `@#_share` WHERE `uid` ='$uid' LIMIT 1");

			if(empty($info)){

				$mon = empty($share[12]['cfg_value']) ? 0 : $share[12]['cfg_value'];

				$time = time();

				$q1 = $this->db->Query("UPDATE `@#_member` SET  `money` =`money`+$mon WHERE `uid` = $uid");

				$q2 = $this->db->Query("INSERT INTO `@#_share` SET  `time` ='$time' , `uid` ='$uid'");

				if($q1>0 && $q2>0){

					echo 2;die;

				}else{

					echo 3;die;

				}

			}else{

				echo 4;die;

			}
		}

	}
//手机验证
	public function mobilechecking(){
		$mysql_model=System::load_sys_class('model');
		$member=$this->userinfo;
		$title="手机验证";
		
		if(!empty($member['mobile'])){
			echo "<script type='text/javascript'>alert('请勿重复验证！');</script>";

			_messagemobile("您的手机已经验证成功,请勿重复验证！");
		}	else{
		include templates("mobile/user","mobilechecking");
		}
	}
	
	//手机验证
	public function mobilesuccess(){
		
		$title="手机验证";
		$member=$this->userinfo;
		
		if(isset($_POST['submit'])){
			$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : "";
			if(!_checkmobile($mobile) || $mobile==null){
				_messagemobile("手机号错误",null,3);	
			}
			$member2=$this->db->GetOne("select mobilecode,uid,mobile from `@#_member` where `mobile`='$mobile' and `uid` != '$member[uid]'");
			if($member2 && $member2['mobilecode'] == 1){
			echo "<script type='text/javascript'>alert('手机号已被注册！');</script>";
				_messagemobile("手机号已被注册！");
			}					
			if($member['mobilecode']!=1){
				//验证码
				$ok = send_mobile_reg_code($mobile,$member['uid']);			
				if($ok[0]!=1){
					_messagemobile("发送失败,失败状态:".$ok[1]);
				}else{
					_setcookie("mobilecheck",base64_encode($mobile));
				}					
			}
			$time=120;
			include templates("mobile/user","mobilesuccess");
		}
	}
	//重发手机验证码
	public function sendmobile(){
		$member=$this->userinfo;
		$mobilecodes=rand(100000,999999).'|'.time();//验证码

		if($member['mobilecode']==1){_message("该账号验证成功",WEB_PATH."/member/home");}			
		
		$checkcode=explode("|",$member['mobilecode']);
		$times=time()-$checkcode[1];
		if($times > 120){
			//重发验证码			
				$ok = send_mobile_reg_code($member['mobile'],$member['uid']);
				if($ok[0]!=1){
					_messagemobile("发送失败,失败状态:".$ok[1]);
				}
			
			_messagemobile("正在重新发送...",WEB_PATH."/mobile/user/mobilecheck/"._encrypt($member['mobile']),2);				
		}else{
			_messagemobile("重发时间间隔不能小于2分钟!",WEB_PATH."/mobile/user/mobilecheck/"._encrypt($member['mobile']));
		}
		
	}
	public function mobilecheck(){	
		$member=$this->userinfo;
		if(isset($_POST['submit'])){
			$shoujimahao =  base64_decode(_getcookie("mobilecheck"));
			if(!_checkmobile($shoujimahao))_messagemobile("手机号码错误!");			
			$checkcodes=isset($_POST['mobile']) ? $_POST['mobile'] : _messagemobile("参数不正确!");
			if(strlen($checkcodes)!=6)_messagemobile("验证码输入不正确!");
			$usercode=explode("|",$member['mobilecode']);	

			if($checkcodes!=$usercode[0])_messagemobile("验证码输入不正确!");
			$this->db->Query("UPDATE `@#_member` SET `mobilecode`='1',`mobile` = '$shoujimahao' where `uid`='$member[uid]'");
			//福分、经验添加			
			$isset_user=$this->db->GetList("select `uid` from `@#_member_account` where `content`='手机认证完善奖励' and `type`='1' and `uid`='$member[uid]' and (`pay`='经验' or `pay`='福分')");	
			if(empty($isset_user)){
				$config = System::load_app_config("user_fufen");//福分/经验
				$time=time();
				$this->db->Query("insert into `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$member[uid]','1','福分','手机认证完善奖励','$config[f_phonecode]','$time')");
				$this->db->Query("insert into `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$member[uid]','1','经验','手机认证完善奖励','$config[z_phonecode]','$time')");			
				$this->db->Query("UPDATE `@#_member` SET `score`=`score`+'$config[f_phonecode]',`jingyan`=`jingyan`+'$config[z_phonecode]' where uid='".$member['uid']."'");
			}
			_setcookie("uid",_encrypt($member['uid']));	
			_setcookie("ushell",_encrypt(md5($member['uid'].$member['password'].$member['mobile'].$member['email'])));		
//福分、经验添加			
			$isset_user=$this->db->GetOne("select `uid` from `@#_member_account` where `pay`='手机认证完善奖励' and `type`='1' and `uid`='$member[uid]' or `pay`='经验'");	
			if(empty($isset_user)){
				$config = System::load_app_config("user_fufen");//福分/经验
				$time=time();

				$this->db->Query("insert into `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$member[uid]','1','福分','手机认证完善奖励','$config[f_overziliao]','$time')");
				$this->db->Query("insert into `@#_member_account` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$member[uid]','1','经验','手机认证完善奖励','$config[z_overziliao]','$time')");			
				$mysql_model->Query("UPDATE `@#_member` SET `score`=`score`+'$config[f_overziliao]',`jingyan`=`jingyan`+'$config[z_overziliao]' where uid='".$member['uid']."'");
				$this->db->Query("UPDATE `@#_member` SET score='100' where `uid`='$member[uid]'");	
			}			
			echo "<script type='text/javascript'>alert('验证成功,请重新登录');</script>";
			//_messagemobile("验证成功,请重新登录！",WEB_PATH."/mobile/home");
		}else{
			_messagemobile("页面错误",null,3);
		}
	}
	//end
	//订单记录
	public function userbuylist(){
	    $webname=$this->_cfg['web_name'];
		$mysql_model=System::load_sys_class('model');
		$member=$this->userinfo;
		$uid = $member['uid'];
		$title="订到管理";
        //订单状态查询
        $state = intval($this->segment(4));
        $state = empty($state)? -1 : $state;
        //var_dump($state);exit;

		//$record=$mysql_model->GetList("select * from `@#_member_go_record` where `uid`='$uid' ORDER BY `time` DESC");
		$user_dizhi = $mysql_model->GetOne("select * from `@#_member_dizhi` where `uid` = '$uid'");
		include templates("mobile/user","userbuylist");
	}

	//云购记录详细

	public function userbuydetail(){

	    $webname=$this->_cfg['web_name'];

		$mysql_model=System::load_sys_class('model');

		$member=$this->userinfo;

		$title="云购详情";

		$crodid=intval($this->segment(4));

		$record=$mysql_model->GetOne("select * from `@#_member_go_record` where `id`='$crodid' and `uid`='$member[uid]' LIMIT 1");		

		if($crodid>0){

			include templates("member","userbuydetail");

		}else{

			echo _message("页面错误",WEB_PATH."/member/home/userbuylist",3);

		}

	}

	//获得的商品

	public function orderlist(){

	    $webname=$this->_cfg['web_name'];

		$member=$this->userinfo;

		$title="获得的商品";
	
		//$record=$this->db->GetList("select * from `@#_member_go_record` where `uid`='".$member['uid']."' and `huode`>'10000000' ORDER BY id DESC");				

		include templates("mobile/user","orderlist");

	}

	//账户管理
 
	public function userbalance(){

	    $webname=$this->_cfg['web_name'];

		$member=$this->userinfo;

		$title="账户记录";

		$account=$this->db->GetList("select * from `@#_member_account` where `uid`='$member[uid]' and `pay` = '账户' ORDER BY time DESC");

         		$czsum=0;

         		$xfsum=0;

		if(!empty($account)){ 

			foreach($account as $key=>$val){

			  if($val['type']==1){

				$czsum+=$val['money'];		  

			  }else{

				$xfsum+=$val['money'];		  

			  }		

			} 		

		}

		

		include templates("mobile/user","userbalance");

	}

	

	 

	public function userrecharge(){

	    $webname=$this->_cfg['web_name'];

		$member=$this->userinfo;

		$title="账户充值";

		$paylist = $this->db->GetList("SELECT * FROM `@#_pay` where `pay_start` = '1' AND pay_mobile = 1");

		

		include templates("mobile/user","recharge");

	}



	public function userqiandao(){

		$webname=$this->_cfg['web_name'];

		$member=$this->userinfo;

		$uid = $member['uid'];

		$qiandao = $this->db->GetOne("SELECT * FROM `@#_qiandao` where  `uid` = $uid");

		include templates("mobile/user","userqiandao");

	}



	public function qiandao(){

		$member=$this->userinfo;

		$uid = $member['uid'];

		$t = time();

		$start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));

		$end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

		//查询上次签到时间信息

		$qiandao = $this->db->GetOne("SELECT * FROM `@#_qiandao` where  `uid` = $uid");

		if(empty($qiandao)){

			$this->db->Query("INSERT INTO `@#_qiandao` SET `time` = $t, `uid` = $uid,`sum` = 1, `lianxu` = 1");

			//签到送100福分，同时送1元钱

			$this->db->Query("UPDATE `@#_member` SET `score` = `score`+100, `money` =`money`+0 WHERE `uid` = $uid");

			_messagemobile("签到成功，积分还可以兑换现金哦",WEB_PATH."/mobile/home/userqiandao");

		}

		if($qiandao['time']>0){

			if($qiandao['time']>$start && $qiandao['time']<$end){

				_messagemobile("今天已经签到过了",WEB_PATH."/mobile/home/userqiandao");

			}else{

				$this->db->Query("UPDATE `@#_qiandao` SET `time` = $t, `uid` =$uid, `sum` =`sum`+1  where uid=$uid");

				$this->db->Query("UPDATE `@#_member` SET `score` = `score`+100 WHERE `uid` = $uid");

				//判断是否连续

				if($t - $qiandao['time']>2 && $t - $qiandao['time']<172798 &&  $qiandao['time']>($start-86400)){

					$this->db->Query("UPDATE `@#_qiandao` SET `lianxu`  =`lianxu` +1 where `uid` = $uid");

				}else{

					$this->db->Query("UPDATE `@#_qiandao` SET `lianxu` = 1 where `uid`= $uid");

				}
				_messagemobile("签到成功，坚持签到有积分赠送的哦！同时积分还可以兑换现金哦",WEB_PATH."/mobile/home/userqiandao");
			}
		}else{
			_messagemobile("签到错误",WEB_PATH."/mobile/home/userqiandao");
		}
	}



public function useraddress(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$t = time();
		//P($_POST);exit;
		extract($_POST);
		if(empty($shouhuoren) || empty($mobile)){
			$addrInfo['msg'] = '收货人 电话 不能为空';
			$addrInfo['code'] = 0;
			echo json_encode($addrInfo);
			exit;
		}
		if(empty($sheng) || empty($shi) || empty($xian)){
			$addrInfo['msg'] = '地市信息不能为空';
			$addrInfo['code'] = 0;
			echo json_encode($addrInfo);
			exit;
		}
		if(empty($jiedao)){
			$addrInfo['msg'] = '街道地址不能为空';
			$addrInfo['code'] = 0;
			echo json_encode($addrInfo);
			exit;
		}
		$q1 = $this->db->Query("INSERT INTO `@#_member_dizhi` SET `time` = $t, `uid` = $uid, `sheng` = '$sheng', `shi` = '$shi', `xian` = '$xian', `jiedao` = '$jiedao',`shouhuoren`= '$shouhuoren', `mobile`= '$mobile'");
		if($q1){
			$addrInfo['msg'] = '地址添加成功';
			$addrInfo['code'] = 1;
		}else{
			$addrInfo['msg'] = '地址添加失败';
			$addrInfo['code'] = 0;
		}
		echo json_encode($addrInfo);
		exit;
	}



	public function address(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$dizhi = $this->db->GetList("SELECT * FROM `@#_member_dizhi` where  `uid` = $uid");
		include templates("mobile/user","address");

	}
	public function updateddress(){
		$id=intval($this->segment(4));
		$t = time();
		if($id){
			extract($_POST);
			if(empty($shouhuoren) || empty($mobile)){
				$addrInfo['msg'] = '收货人 电话 不能为空';
				$addrInfo['code'] = 0;
				echo json_encode($addrInfo);
				exit;
			}
			if(empty($sheng) || empty($shi) || empty($xian)){
				$addrInfo['msg'] = '地市信息不能为空';
				$addrInfo['code'] = 0;
				echo json_encode($addrInfo);
				exit;
			}
			if(empty($jiedao)){
				$addrInfo['msg'] = '街道地址不能为空';
				$addrInfo['code'] = 0;
				echo json_encode($addrInfo);
				exit;
			}
			$q1 = $this->db->Query("UPDATE `@#_member_dizhi` SET `time` = $t, `sheng` = '$sheng', `shi` = '$shi', `xian` = '$xian', `jiedao` = '$jiedao', `shouhuoren`= '$shouhuoren', `mobile`= '$mobile' WHERE `id`= $id");
			if($q1){
				$addrInfo['msg'] = '地址修改成功';
				$addrInfo['code'] = 1;

			}else{
				$addrInfo['msg'] = '地址修改失败';
				$addrInfo['code'] = 0;
			}
		}else{
			$addrInfo['msg'] = "地址修改失败";
			$addrInfo['code'] = 0;
		}
		echo json_encode($addrInfo);
		exit;
	}



	public function deladdress(){

		//$id=intval($this->segment(4));
		$id = intval($_GET['id']);

		if($id){

			$q1 = $this->db->Query("DELETE FROM `@#_member_dizhi`  WHERE `id`= $id");

			if($q1){

				$address['code'] = 1;


				//_message("删除成功",WEB_PATH."/mobile/home/address");

			}else{
				$address['code'] = 0;


				//_message("删除失败",WEB_PATH."/mobile/home/address");

			}

		}else{
				$address['code'] = 0;


			//_message("删除失败",WEB_PATH."/mobile/home/address");

		}
		return json_encode($address);

	}



	//设为默认

	public function setaddress(){

		//$id=intval($this->segment(4));
		$id = intval($_GET['id']);
		//var_dump($id);exit;
		$member = $this->userinfo;
		//var_dump($id);exit;

		if($id){

			$q1 = $this->db->Query("UPDATE `@#_member_dizhi` SET `default` = 'Y' WHERE `id`= {$id} AND `uid` = {$member['uid']}");

			$q2 = $this->db->Query("UPDATE `@#_member_dizhi` SET `default` = 'N' WHERE `id` != {$id} AND `uid` = {$member['uid']}");
			//var_dump($q2);exit;

			if($q1 && $q2){
				$address['code'] = 1;
				//_message("设置成功",WEB_PATH."/mobile/home/address");

			}else{
				$address['code'] = 0;
				//_message("设置失败",WEB_PATH."/mobile/home/address");

			}

		}else{
			//_message("设置失败",WEB_PATH."/mobile/home/address");
			$address['code'] = 0;
		}
		echo json_encode($address);

	}
	public function zhuanzhang(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$t = time();
		//查询用户余额
		$info= $this->db->GetOne("SELECT `money` FROM `@#_member` where  `uid` = $uid");
		if($_POST['submit1']){
			if($_POST['txtBankName'] != $_POST['txtBankName1']){
				_message("两次的用户信息不一致，请重新输入！",WEB_PATH."/mobile/home/zhuanzhang");
			}
			if($info['money']< $_POST['money']){
				_message("账户余额超过转账金额了！",WEB_PATH."/mobile/home/zhuanzhang");
			}
			if(empty($_POST['money']) || $_POST['money']<1){
				_message("请输入转账金额，且不能小于1元",WEB_PATH."/mobile/home/zhuanzhang");
			}
			// 查询数据库中用户信息
			$to_user = $_POST['txtBankName'];
			$to_info= $this->db->GetOne("SELECT * FROM `@#_member` where  `mobile` = '{$to_user}' OR `email` = '{$to_user}'");
			$cash = $_POST['money'];
			if(empty($to_info)){
				_message("用户不存在！请核对后在操作",WEB_PATH."/mobile/home/zhuanzhang");
			}
			$this->db->Autocommit_start();
				$up_q1= $this->db->Query("UPDATE `@#_member` SET `money` = `money`- {$_POST['money']}  where  `uid` = $uid");
				$up_q2= $this->db->Query("UPDATE `@#_member` SET `money` = `money`+{$_POST['money']}  where  `uid` = {$to_info['uid']}");
				$up_q3= $this->db->Query("INSERT INTO `@#_member_account`  SET `uid`= $uid, `type` = -1, `pay`= '账户', `content`= '给用户{$to_info['mobile']}转账{$cash}元', `money` = $cash ,`time` = $t");
				$up_q4= $this->db->Query("INSERT INTO `@#_member_account`  SET `uid`= {$to_info['uid']}, `type` = -1, `pay`= '账户', `content`= '获得用户{$member['mobile']}转账{$cash}元', `money` = $cash ,`time` = $t");
			if($up_q1 && $up_q2 && $up_q3 && $up_q4){
				$this->db->Autocommit_commit();
				_message("转账成功",WEB_PATH."/mobile/home/zhuanzhang");
			}else{
				$this->db->Autocommit_rollback();
				$this->error = true;
				_message("转账失败",WEB_PATH."/mobile/home/zhuanzhang");
			}	
		}
		include templates("mobile/user","zhuanzhang");
	}
	/**
	 * 转盘抽奖
	 */
	public function choujiang(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$name = $member['username'];
		include templates("mobile/user","choujiang");
	}
	public function submit(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$row =  $this->db->GetOne("SELECT * FROM `@#_member`  WHERE  `uid` = $uid");
		$score = $row['score'];
		if(intval($score/200)<1){
			$res = array(
				'ok' => true,
				'round'=>0,
				'left' => 0,
				'desc' =>'您的抽奖次数已经使用完！',
			);
			echo json_encode($res);die;	
		}else{
			//扣除积分
			$q1= $this->db->Query("UPDATE `@#_member` SET `score` = `score`- 200  where  `uid` = $uid");
			$lefts = $score - 200;
			if($q1){
				$left = intval($score/200)-1;
				$res = array(
					'ok' => true,
					'round'=>0,
					'left' => $left,
					'desc' =>'真遗憾，您没有中奖哦！剩余福分'.$lefts,
				);
			echo json_encode($res);die;	
			}else{
				$left = intval($score/200);
				$res = array(
					'ok' => true,
					'round'=>0,
					'left' => $left,
					'desc' =>'抽奖出错！请联系管理员',
			);
			echo json_encode($res);die;
			}
		}
	}
	/**
	 * 摇一摇红包
	 */
	public function yaohongbao(){
		$webname=$this->_cfg['web_name'];
		$member=$this->userinfo;
		$uid = $member['uid'];
		$name = $member['username'];
		include templates("mobile/user","yaohongbao");
	}
	//晒单
	public function singlelist(){
		 $webname=$this->_cfg['web_name'];
		include templates("mobile/user","singlelist");
	}	
	//添加晒单
	public function postsinglebk(){
		$member=$this->userinfo;
		$uid=_getcookie('uid');
		$ushell=_getcookie('ushell');
		$title="添加晒单";		
		if(isset($_POST['submit'])){
			//if($_POST['title']==null) _messagemobile("标题不能为空");	
			if($_POST['content']==null) _messagemobile("内容不能为空");	
			if(empty($_POST['file_up'])){
				_messagemobile("图片不能为空");
			}
			$pic=$_POST['file_up'];
			$pics = explode(';',$pic);
			$src=trim($pics[0]);
			$size=getimagesize(G_UPLOAD_PATH."/".$src);
			$width=220;
			$height=$size[1]*($width/$size[0]);
			$thumbs=tubimg($src,$width,$height);				
			$uid=$this->userinfo;
			$sd_userid=$uid['uid'];
			$sd_shopid=$_POST['shopid'];
			$sd_title=$_POST['title'];
			$sd_thumbs="shaidan/".$thumbs;
			$sd_content=$_POST['content'];
			$sd_photolist=$pic;
			$sd_time=time();			
			$sd_ip = _get_ip_dizhi();						
			$qishu= $this->db->GetOne("select `qishu`, `id` from `@#_shoplist` where `id`='$sd_shopid'");
			$qs = $qishu['qishu'];
			$ids = $qishu['id'];
			$this->db->Query("INSERT INTO `@#_shaidan`(`sd_userid`,`sd_shopid`,`sd_qishu`,`sd_ip`,`sd_title`,`sd_thumbs`,`sd_content`,`sd_photolist`,`sd_time`)VALUES ('$sd_userid','$ids','$qs','$sd_ip','$sd_title','$sd_thumbs','$sd_content','$sd_photolist','$sd_time')");
			_messagemobile("晒单分享成功",WEB_PATH."/mobile/home/singlelist");
		}
		$recordid=intval($this->segment(4));
		if($recordid>0){
			$shaidan=$this->db->GetOne("select * from `@#_member_go_record` where `id`='$recordid'");	
			$shopid=$shaidan['id'];
			include templates("mobile/user","postsingle");
		}else{
			_messagemobile("页面错误");
		}
	}

	public function postsingle(){
		$member=$this->userinfo;
		$uid=$member['uid'];
		$title="添加晒单";
		$recordid=intval($this->segment(4));
		$shaidan=$this->db->GetOne("select * from `@#_member_go_record` where `shopid`='$recordid' and `uid` = '$member[uid]'");
		if(!$shaidan){
			_messagemobile("该商品您不可晒单!");
		}
		$ginfo=$this->db->GetOne("select * from `@#_shoplist` where `id`='$shaidan[shopid]' LIMIT 1");
		if(!$ginfo){
			_messagemobile("该商品已不存在!");
		}
		$shaidanyn=$this->db->GetOne("select sd_id from `@#_shaidan` where `sd_shopid`='{$ginfo['id']}' and `sd_userid` = '$member[uid]'");
		if($shaidanyn){
			_messagemobile("不可重复晒单!");
		}
		if($_POST){

			//if($_POST['title']==null)_messagemobile("标题不能为空");
			if($_POST['content']==null)_messagemobile("内容不能为空");
			if(!isset($_POST['fileurl_tmp'])){
				_messagemobile("图片不能为空");
			}
			System::load_sys_class('upload','sys','no');
			$img=explode(';', $_POST['fileurl_tmp']);
			$num=count($img);
			$pic="";
			for($i=0;$i<$num;$i++){
				$img[$i] = str_replace('http://', '', $img[$i]);
				$img[$i] = str_replace($_SERVER['HTTP_HOST'], '', $img[$i]);
				$img[$i] = str_replace('/statics/uploads/', '', $img[$i]);
				$pic.=trim($img[$i]).";";
			}

			$src=trim($img[0]);
			$size=getimagesize(G_UPLOAD_PATH."/".$src);
			$width=220;
			$height=$size[1]*($width/$size[0]);
			$thumbs=tubimg($src,$width,$height);
			$sd_userid=$uid;
			$sd_shopid=intval($ginfo['id']);
			$sd_title=safe_replace($_POST['title']);
			$sd_thumbs=$src;
			$sd_content=safe_replace($_POST['content']);
			$sd_photolist=$pic;
			$sd_time=time();
			$this->db->Query("INSERT INTO `@#_shaidan`(`sd_userid`,`sd_shopid`,`sd_title`,`sd_thumbs`,`sd_content`,`sd_photolist`,`sd_time`)VALUES
			('$sd_userid','$sd_shopid','$sd_title','$sd_thumbs','$sd_content','$sd_photolist','$sd_time')");
			header("Location:".WEB_PATH."/mobile/home/singlelist");
		}

		if($recordid>0){
			$shaidan=$this->db->GetOne("select * from `@#_member_go_record` where `id`='$recordid'");
			$shopid=$shaidan['shopid'];
			include templates("mobile/user","postsingle");
		}else{
			_messagemobile("页面错误");
		}
	}
	// 晒单上传图片方法
	public function mupload(){
		$uploadDir =$_SERVER['DOCUMENT_ROOT'].'/statics/uploads/shaidan/'.date('Ymd',time()).'/';
		if(!is_dir($uploadDir)){
		 	mkdir($uploadDir,0777);				
		}
		$rand=rand(10,99).substr(microtime(),2,6).substr(time(),4,6);
		$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); 
		if (!empty($_FILES)) {
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$filetype = strtolower($fileParts['extension']);
			$tempFile   = $_FILES['Filedata']['tmp_name'];
			$targetFilename = $rand.'.'.$filetype;
			if (in_array($filetype, $fileTypes)) {
				move_uploaded_file($tempFile, $uploadDir.$targetFilename);
				echo 'shaidan/'.date('Ymd',time()).'/'.$targetFilename;
			} else {
				echo 'Invalid file type.';
			}
		}
	}
	//检查图片存在否
	public function check_exists(){
		$fileurl = $_SERVER['DOCUMENT_ROOT'].'/statics/uploads/shaidan/'.date('Ymd',time()).'/'.$_POST['filename'];
		if (file_exists($fileurl)){
			echo 1;
		}else{
			echo 0;
		}
	}
	public function file_upload(){
		ini_set('display_errors', 0);
		// error_reporting(E_ALL);
		include dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."UploadHandler.php";
		$upload_handler = new UploadHandler();
	}
	public function singoldimg(){
		if($_POST['action']=='del'){
			$sd_id=$_POST['sd_id'];
			$oldimg=$_POST['oldimg'];
			$shaidan=$this->db->GetOne("select * from `@#_shaidan` where `sd_id`='$sd_id'");
			$sd_photolist=str_replace($oldimg.";","",$shaidan['sd_photolist']);
			$this->db->Query("UPDATE `@#_shaidan` SET sd_photolist='".$sd_photolist."' where sd_id='".$sd_id."'");
		}
	}
	public function singphotoup(){
		$mysql_model=System::load_sys_class('model');
		if(!empty($_FILES)){
			$uid=isset($_POST['uid']) ? $_POST['uid'] : NULL;
			$ushell=isset($_POST['ushell']) ? $_POST['ushell'] : NULL;
			$login=$this->checkuser($uid,$ushell);
			if(!$login){_messagemobile("上传出错");}
			System::load_sys_class('upload','sys','no');
			upload::upload_config(array('png','jpg','jpeg','gif'),1000000,'shaidan');
			upload::go_upload($_FILES['Filedata']);
			if(!upload::$ok){
				echo _messagemobile(upload::$error,null,3);
			}else{
				$img=upload::$filedir."/".upload::$filename;
				$size=getimagesize(G_UPLOAD_PATH."/shaidan/".$img);
				$max=700;$w=$size[0];$h=$size[1];
				if($w>700){
					$w2=$max;
					$h2=$h*($max/$w);
					upload::thumbs($w2,$h2,1);
				}

				echo trim("shaidan/".$img);
			}
		}
	}
	public function singdel(){
		$action=isset($_GET['action']) ? $_GET['action'] : null;
		$filename=isset($_GET['filename']) ? $_GET['filename'] : null;
		if($action=='del' && !empty($filename)){
			$filename=G_UPLOAD_PATH.'shaidan/'.$filename;
			$size=getimagesize($filename);
			$filetype=explode('/',$size['mime']);
			if($filetype[0]!='image'){
				return false;
				exit;
			}
			unlink($filename);
			exit;
		}
	}
	//晒单删除
	public function shaidandel(){
		_messagemobile("不可以删除!");
		$member=$this->userinfo;
		//$id=isset($_GET['id']) ? $_GET['id'] : "";
		$id=$this->segment(4);
		$id=intval($id);
		$shaidan=$this->db->Getone("select * from `@#_shaidan` where `sd_userid`='$member[uid]' and `sd_id`='$id'");
		if($shaidan){
			$this->db->Query("DELETE FROM `@#_shaidan` WHERE `sd_userid`='$member[uid]' and `sd_id`='$id'");
			_messagemobile("删除成功",WEB_PATH."/mobile/home/singlelist");
		}else{
			_messagemobile("删除失败",WEB_PATH."/mobile/home/singlelist");
		}
	}
	
		/**
	 * 更改头像ajax
	 */
	public function changeheadimg(){
		$member=$this->userinfo;
		$imgSrc['code'] = 0;
		if(!empty($_FILES)){
		//echo $_FILES;	
			System::load_sys_class('upload','sys','no');
			upload::upload_config(array('png','jpg','jpeg','gif'),500000,'touimg');
			upload::go_upload($_FILES['Filedata']);
			$files=$_POST['typeCode'];
			if(!upload::$ok){
				echo upload::$error;
			}else{
				$img=upload::$filedir."/".upload::$filename;				
				$size=getimagesize(G_UPLOAD."/touimg/".$img);
				$max=300;$w=$size[0];$h=$size[1];				
				if($w>300 or $h>300){
					if($w>$h){
						$w2=$max;
						$h2 = intval($h*($max/$w));
						upload::thumbs($w2,$h2,true);					
					}else{
						$h2=$max;
						$w2 = intval($w*($max/$h));
						upload::thumbs($w2,$h2,true);
					}
				}
			$tname="touimg/".$img;
			$this->db->Query("UPDATE `@#_member` SET img='$tname' where uid={$member['uid']}");
			$imgSrc['code'] = 1;
			$imgSrc['src'] = G_UPLOAD.$tname;
                header("location:".WEB_PATH."/mobile/home");
			}
							
		}

	}
	
	/**
     * 物流查询
     */
    public function logistics(){
        $ordercode = addslashes(safe_replace($this->segment(4)));
        $member=$this->userinfo;
        $uid = $member['uid'];
        $orderinfo = $this->db->GetOne("SELECT * FROM `@#_member_go_record` WHERE `uid` = '$uid' AND `code`='$ordercode' AND `status`='已付款,已发货,未完成'");
        $products = unserialize($orderinfo['pro_info']);
		if ($orderinfo){
            $contents = $this->kuaidi($orderinfo['company_code'], $orderinfo['company']);
            if ($contents['message'] == 'ok') {
                $code = 0;
                switch ($contents['state']){
                    case 0:
                        $contents['stateMsg'] = '在途中';
                        break;
                    case 1:
                        $contents['stateMsg'] = '已揽件';
                        break;
                    case 3:
                        $contents['stateMsg'] = '收件人已签收';
                        break;
                    case 4:
                        $contents['stateMsg'] = '已退签';
                    case 5:
                        $contents['stateMsg'] = '快递员正在派送';
                        break;
                    case 6:
                        $contents['stateMsg'] = '退货中';
                        break;
                    default:
                        $contents['stateMsg'] = '快递寄送过程出了问题';
                }
                $contents['company'] = $orderinfo['company'];
            }
        }else{
            $code = 1;
        }

        //p($contents);exit;
        include templates("mobile/user","logistics");
    }

    public function kuaidi($invoice_no, $shipping_name) {
        switch ($shipping_name) {
            case '中国邮政':
                $logi_type = 'ems';
                break;

            case '申通快递':
                $logi_type = 'shentong';
                break;

            case '圆通速递':
                $logi_type = 'yuantong';
                break;

            case '顺丰速运':
                $logi_type = 'shunfeng';
                break;

            case '韵达快递':
                $logi_type = 'yunda';
                break;

            case '天天快递':
                $logi_type = 'tiantian';
                break;

            case '中通速递':
                $logi_type = 'zhongtong';
                break;

            case '增益速递':
                $logi_type = 'zengyisudi';
                break;
        }
        $kurl = 'http://www.kuaidi100.com/query?type=' . $logi_type . '&postid=' . $invoice_no;
        $ret = $this->curl_get_contents($kurl);
        $k_arr = json_decode($ret, true);
        return $k_arr;
    }

    /**
     * 发送curl
     * @param $url
     * @return mixed
     */
    public function curl_get_contents($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }


    //活动商城个人中心 17.01.10
    public function activityuser(){

    	include templates("/mobile/user","activityuser");
    }



	 

}

