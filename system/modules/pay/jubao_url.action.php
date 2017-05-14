<?php 

defined('G_IN_SYSTEM')or exit('No permission resources.');

include dirname(__FILE__).DIRECTORY_SEPARATOR."lib/jubao/jubaopay.php";

ini_set("display_errors","OFF");
class jubao_url extends SystemAction {
	public function __construct(){			
		$this->db=System::load_sys_class('model');		
	} 	
	
	public function qiantai(){	
		$this->houtai();
		sleep(2);
		$payid = $_GET['payid'];	//商户订单号
		$dingdaninfo = $this->db->GetOne("select * from `@#_member_addmoney_record` where `code` = '$payid'");
		if(!$dingdaninfo || $dingdaninfo['status'] == '未付款'){
			_message("支付失败");			
		}else{
			if(empty($dingdaninfo['scookies'])){
				if(_is_mobile()){
					_messagemobile("充值成功",WEB_PATH."/mobile/home");
				}else{
					_message("充值成功!",WEB_PATH."/member/home/userbalance");
				}
			}else{
				if($dingdaninfo['scookies'] == '1'){
					_message("支付成功!",WEB_PATH."/member/cart/paysuccess");
				}else{
					_message("商品还未购买,请重新购买商品!",WEB_PATH."/member/cart/cartlist");
				}
					
			}
		}
	}
	
	public function houtai(){
		$pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_class` = 'jubao' and `pay_start` = '1'");
		$pay_type_key = unserialize($pay_type['pay_key']);
		//$trade_MD5key =  $pay_type_key['key']['val'];		//MD5私钥		
				
		$jubaopay=new jubaopay(dirname(__FILE__).DIRECTORY_SEPARATOR."lib/jubao/jubaopay.ini");
		$message=$_GET["message"];
		$signature=$_GET["signature"];
		$jubaopay->decrypt($message);
		// 校验签名，然后进行业务处理
		$result=$jubaopay->verify($signature);
		if($result == 1) {//MD5验证成功
			$payid = $jubaopay->getEncrypt("payid");
			$orderNo = $jubaopay->getEncrypt("orderNo");
			$partnerid = $jubaopay->getEncrypt("partnerid");
			$amount = $jubaopay->getEncrypt("amount");
			$realReceive = $jubaopay->getEncrypt("realReceive");
			$state = $jubaopay->getEncrypt("state");
			$modifyTime = $jubaopay->getEncrypt("modifyTime");
			$payerName = $jubaopay->getEncrypt("payerName");
			$remark = $jubaopay->getEncrypt("remark");
			$payMethodType = $jubaopay->getEncrypt("payMethodType");
			$payMethod = $jubaopay->getEncrypt("payMethod");
		  //开始处理汇潮支付结果
			if($state == '2') {			
					$this->db->Autocommit_start();
					$dingdaninfo = $this->db->GetOne("select * from `@#_member_addmoney_record` where `code` = '$payid' and `status` = '未付款' for update");
					if(!$dingdaninfo){	return;}	//没有该订单,失败
					$c_money = intval($dingdaninfo['money']);			
					$uid = $dingdaninfo['uid'];
					$time = time();			
					$up_q1 = $this->db->Query("UPDATE `@#_member_addmoney_record` SET `pay_type` = '聚宝支付', `status` = '已付款' where `id` = '$dingdaninfo[id]' and `code` = '$dingdaninfo[code]'");
					$up_q2 = $this->db->Query("UPDATE `@#_member` SET `money` = `money` + $c_money where (`uid` = '$uid')");				
					$up_q3 = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('$uid', '1', '账户', '充值', '$c_money', '$time')");
						
					if($up_q1 && $up_q2 && $up_q3){
						$this->db->Autocommit_commit();			
					}else{
						$this->db->Autocommit_rollback();
						return;
					}			
					if(empty($dingdaninfo['scookies'])){					
							return;	//充值完成			
					}			
					$scookies = unserialize($dingdaninfo['scookies']);			
					$pay = System::load_app_class('pay','pay');			
					$pay->scookie = $scookies;	

					$ok = $pay->init($uid,$pay_type['pay_id'],'go_record');	//云购商品	
					if($ok != 'ok'){
						_setcookie('Cartlist',NULL);
						return;	//商品购买失败			
					}			
					$check = $pay->go_pay(1);
					if($check){
						$this->db->Query("UPDATE `@#_member_addmoney_record` SET `scookies` = '1' where `code` = '$payid' and `status` = '已付款'");
						_setcookie('Cartlist',NULL);
						return;			
					}else{
						return;
					}				
				}//开始处理订单结束
		 }
         else{
		     return;
		 }		 
	}//function end
	
}//

?>