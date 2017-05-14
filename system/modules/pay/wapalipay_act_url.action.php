<?php

defined('G_IN_SYSTEM')or exit('No permission resources.');
ini_set("display_errors","OFF");
class wapalipay_act_url extends SystemAction {
	public function __construct(){
		$this->db=System::load_sys_class('model');
	}

	public function qiantai(){

		sleep(2);

		$out_trade_no = $_GET['out_trade_no'];	//商户订单号

		$actOrder = $this->db->GetOne("select * from `@#_member_activity_record` where `code` = '$out_trade_no'");
        //p($dingdaninfo);exit;
        if($actOrder['scookies'] == 1 && $actOrder['status'] == '已支付'){
            header("location: ".WEB_PATH."/mobile/cart/paysuccess");
        }else{
            header("location: ".WEB_PATH."/mobile/cart/paycancel");
        }
	}


	public function houtai(){

		file_put_contents("alipay.txt",json_encode($_POST),FILE_APPEND);

	    include G_SYSTEM."modules/pay/lib/wapalipay/alipay_notify.class.php";

		$pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_class` = 'wapalipay' and `pay_start` = '1'");

		$pay_type_key = unserialize($pay_type['pay_key']);

		$key =  $pay_type_key['key']['val'];		//支付KEY

		$partner =  $pay_type_key['id']['val'];		//支付商号ID

		$alipay_config_sign_type = strtoupper('MD5');		//签名方式 不需修改

		$alipay_config_input_charset = strtolower('utf-8'); //字符编码格式

		$alipay_config_cacert =  G_SYSTEM."modules/pay/lib/wapalipay/cacert.pem";	//ca证书路径地址

		$alipay_config_transport   = 'http';

		$alipay_config=array(

			"partner"      =>$partner,

			"key"          =>$key,

			"sign_type"    =>$alipay_config_sign_type,

			"input_charset"=>$alipay_config_input_charset,

			"cacert"       =>$alipay_config_cacert,

			"transport"    =>$alipay_config_transport

		);


		$alipayNotify = new AlipayNotify($alipay_config);

		$verify_result = $alipayNotify->verifyNotify();



		if(!$verify_result) {echo "fail";exit;} //验证失败



		$doc = new DOMDocument();

		$doc->loadXML($_POST['notify_data']);


		if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {

			//商户订单号

			$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;

			//支付宝交易号

			$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;

			//交易状态

			$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;


			//开始处理及时到账和担保交易订单

			if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS' || $trade_status == 'WAIT_SELLER_SEND_GOODS') {

				$dingdaninfo = $this->db->GetOne("select * from `@#_member_activity_record` where `code` = '$out_trade_no' and `status` = '未支付'");

				if(!$dingdaninfo){	echo "fail";exit;}	//没有该订单,失败

				$c_money = sprintf("%.2f",$dingdaninfo['money']);

                $num = 1;
				$uid = $dingdaninfo['uid'];
				$time = time();
                $scookies = unserialize($dingdaninfo['scookies']);
                $actId = $scookies['actId'];
                $sId = $scookies['sId'];
                $actOrderId = $scookies['actOrderId'];

				$this->db->Autocommit_start();
                //支付成功后开始处理数据
				$up_q1 = $this->db->Query("UPDATE `@#_member_activity_record` SET `status` = '已支付',`scookies` = 1 where `id` = '$dingdaninfo[id]' and `code` = '$dingdaninfo[code]'");
                //更新活动表的报名人数
                $up_q2 = $this->db->Query("UPDATE `@#_activity` SET `act_num_signed` = `act_num_signed` + $num WHERE `act_id`= $actId");
                //更新报名表的支付状态
                $up_q3 = $this->db->Query("UPDATE `@#_act_sign` SET `s_status` = '已支付' WHERE `s_id` = $sId AND `s_uid` = $uid");
                //更新活动订单表的支付状态
                $up_q4 = $this->db->Query("UPDATE `@#_act_order` SET `o_status` = '已支付' WHERE `o_sid` = $sId AND `o_uid` = $uid AND `o_id` = $actOrderId");

                file_put_contents("alipay.txt",json_encode($up_q1),FILE_APPEND);
                file_put_contents("alipay.txt",json_encode($up_q2),FILE_APPEND);
                file_put_contents("alipay.txt",json_encode($up_q3),FILE_APPEND);
                file_put_contents("alipay.txt",json_encode($up_q4),FILE_APPEND);

				if($up_q1 && $up_q2 && $up_q3 && $up_q4){
					$this->db->Autocommit_commit();
                    echo "success";exit;
				}else{
					$this->db->Autocommit_rollback();
					echo "fail";exit;
				}

			}//开始处理订单结束

		}



	}//function end



}

?>