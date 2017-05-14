<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
if ( !function_exists('mylog') ) {
	function mylog() {
	}
}
if ( !function_exists('nolog') ) {
	function nolog() {
	}
}

class wxpay_web_act_url extends SystemAction {
	public function __construct(){
		$this->db=System::load_sys_class('model');
	}

	public function payinfo(){
		$msg = $this->segment(4);
		if ( $msg == "cancel" ){
			$msg = '交易取消！';
		}else if ( $msg == "fail" ){
			$msg = '交易失败！';
		}else if ( $msg == "nowechat" ){
			$msg = '请关注微信公众号在微信中登录后进行支付操作！';
		} else {
			$msg = '交易错误：'.urldecode($msg);
		}

		_messagemobile($msg);

	}

	public function init() {
		if (empty($_GET['money']) || empty($_GET['out_trade_no']) ) {
			header('Location: '.WEB_PATH.'/pay/'.__CLASS__.'/payinfo/fail');
			die;
		}

		$config=array();
		$config['money'] = $_GET['money'];
		$config['code'] = $_GET['out_trade_no'];
		$config['NotifyUrl']  = G_WEB_PATH.'/index.php/pay/'.__CLASS__.'/houtai/';

		$pay = System::load_app_class('wxpay_web','pay');
		$pay->config($config);
	}

    public function houtai(){

		include_once dirname(__FILE__)."/lib/weixin/WxPayPubHelper.php";

		$pay = $this->db->GetOne("SELECT * from `@#_pay` where `pay_class` = 'wxpay_web'");
		$config = array();
		$config['pay_type_data'] = unserialize($pay['pay_key']);

		// WxPayConf_pub::$APPID = $config['pay_type_data']['APPID']['val'];
		// WxPayConf_pub::$MCHID = $config['pay_type_data']['MCHID']['val'];
		// WxPayConf_pub::$KEY = $config['pay_type_data']['KEY']['val'];
		// WxPayConf_pub::$APPSECRET = $config['pay_type_data']['APPSECRET']['val'];

		$notify = new Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);

		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;

		if( $notify->checkSign() == false ){
			mylog('wxpay_web',"【签名错误】:\n".$xml."\n");
			die;
		}elseif ($notify->data["return_code"] == "FAIL") {
			mylog('wxpay_web',"【通信出错】:\n".$xml."\n");
			die;
		}elseif($notify->data["result_code"] == "FAIL"){
			mylog('wxpay_web',"【业务出错】:\n".$xml."\n");
			die;
		}

		nolog('wxpay_web');
		mylog('wxpay_web',$notify->data);

		$total_fee_t = $notify->data['total_fee']/100;
		$out_trade_no=$notify->data['out_trade_no'];

		$dingdaninfo = $this->db->GetOne("select * from `@#_member_activity_record` where `code` = '$out_trade_no'");

		if(!$dingdaninfo){
			mylog('wxpay_web','f1');
			echo "fail";exit;
		}
		if ( $dingdaninfo['status'] == '已支付' ) {
			mylog('wxpay_web','s1');
			echo "success";exit;
		}

		$this->db->Autocommit_start();
		$dingdaninfo = $this->db->GetOne("select * from `@#_member_activity_record` where `code` = '$out_trade_no' and `money` = '$total_fee_t' and `status` = '未支付' for update");
		if(!$dingdaninfo){
			mylog('wxpay_web','f2');
			echo "fail";exit;
		}
        $num = 1;
		$uid = $dingdaninfo['uid'];
		$time = time();
        //支付成功后开始处理数据
        $up_q1 = $this->db->Query("UPDATE `@#_member_activity_record` SET `pay_type` = '微信公众号', `status` = '已支付',`scookies` = 1 where `id` = '$dingdaninfo[id]' and `code` = '$dingdaninfo[code]'");
        if($up_q1){
            $this->db->Autocommit_commit();
        }else{
            $this->db->Autocommit_rollback();
            mylog('wxpay_web','f3');
            echo "fail";exit;
        }

        $scookies = unserialize($dingdaninfo['scookies']);
        file_put_contents("wxpay.txt",json_encode($scookies),FILE_APPEND);
        $pay=System::load_app_class('pay','pay');
        $pay_type_info = '微信支付';
        $param = $scookies['param'];
        $param['record_id'] = $dingdaninfo['id'];
        $initInfo = $pay->act_init($uid,$param,$scookies); //初始化配置信息
        file_put_contents("wxpay.txt",json_encode($initInfo),FILE_APPEND);
        if($initInfo){
            $res = $pay->act_pay_action($pay_type_info,'D'); //执行处理方法 param1:支付方式，param2:订单前缀
            file_put_contents("wxpay.txt",json_encode($res),FILE_APPEND);
            if($res){
                _setcookie('SignInfo','',''); //清除cookie
                mylog('wxpay_web','s2');
                echo "success";exit;
            }else{
                mylog('wxpay_web','f3');
                echo "fail";exit;
            }
        }else{
            _setcookie('SignInfo','',''); //清除cookie
            mylog('wxpay_web','f3');
            echo "fail";exit;
        }

        /*$actId = $scookies['actId'];
        $sId = $scookies['sId'];
        $actOrderId = $scookies['actOrderId'];
        $balance = $scookies['balance'];
        $use_integral = $scookies['integral'];

        //查询活动
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$actId");
        $userInfo = $this->db->GetOne("select * from `@#_member` WHERE `uid`=$uid");

        //支付成功后开始处理数据
		$up_q1 = $this->db->Query("UPDATE `@#_member_activity_record` SET `pay_type` = '微信公众号', `status` = '已支付',`scookies` = 1 where `id` = '$dingdaninfo[id]' and `code` = '$dingdaninfo[code]'");
        //更新活动表的报名人数
        $up_q2 = $this->db->Query("UPDATE `@#_activity` SET `act_num_signed` = `act_num_signed` + $num WHERE `act_id`= $actId");
        //更新报名表的支付状态
        $up_q3 = $this->db->Query("UPDATE `@#_act_sign` SET `s_status` = '已支付' WHERE `s_id` = $sId AND `s_uid` = $uid");
        //更新活动订单表的支付状态
        $up_q4 = $this->db->Query("UPDATE `@#_act_order` SET `o_status` = '已支付' WHERE `o_sid` = $sId AND `o_uid` = $uid AND `o_id` = $actOrderId");

        //添加积分
        $integral = intval($activity['give_integral']);
        $user_integral = $userInfo['score']+$integral-$use_integral;
        $acive_balance = $userInfo['money']-$balance;
        $query_1 = $this->db->Query("UPDATE `@#_member` SET `score`= '$user_integral',`money`=$acive_balance WHERE (`uid`='$uid')");
        //分享积分*/

        /*file_put_contents("wxpay.txt",json_encode($up_q1),FILE_APPEND);
        file_put_contents("wxpay.txt",json_encode($up_q2),FILE_APPEND);
        file_put_contents("wxpay.txt",json_encode($up_q3),FILE_APPEND);
        file_put_contents("wxpay.txt",json_encode($up_q4),FILE_APPEND);*/

		/*if($up_q1 && $up_q2 && $up_q3 && $up_q4){
			$this->db->Autocommit_commit();
            mylog('wxpay_web','s2');
            echo "success";exit;
		}else{
			$this->db->Autocommit_rollback();
			mylog('wxpay_web','f3');
			echo "fail";exit;
		}*/
		/*if(empty($dingdaninfo['scookies'])){
			mylog('wxpay_web','s2');
			echo "success";exit;
		}*/

	}


}
