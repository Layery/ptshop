<?php 

include dirname(__FILE__).DIRECTORY_SEPARATOR."jubao".DIRECTORY_SEPARATOR."jubaopay.php";

class jubao {
	//配置
	private $config;
	public function config($config=null){	
		$this->config=$config;	
		$out_trade_no = $config['code'];
		$partner = $config['id'];     
		$total_fee = $config['money'];
		$desc = $config['title'];
		//$notify_url = $config['NotifyUrl'];					//显示支付结果页面,*替换成payReturnUrl.php所在路径	houtai
		$return_url = $config['ReturnUrl'];				//支付完成后的回调处理页面,*替换成payNotifyUrl.php所在,qiantai
				//var_dump($return_url);
				//exit;
		//////////////////////////////////////////////////////////////////////////////////////////////////
		 //商户利用支付订单（payid）和商户号（mobile）进行对账查询
		$jubaopay=new jubaopay(dirname(__FILE__).DIRECTORY_SEPARATOR."jubao".DIRECTORY_SEPARATOR."jubaopay.ini");
		/*$jubaopay->setEncrypt("payid", "abcdefghijklmnopqrstuvwxyzABC");
		$jubaopay->setEncrypt("partnerid", "14061642390911131749");
		$jubaopay->setEncrypt("amount", "0.02");
		$jubaopay->setEncrypt("payerName", "zs001");
		$jubaopay->setEncrypt("goodsName", "商品名称");
		$jubaopay->setEncrypt("payMethod", "ALL");
		$jubaopay->setEncrypt("remark", "商品备注");
		$jubaopay->setEncrypt("returnURL", $return_url);
		$jubaopay->setEncrypt("callBackURL", $notify_url);*/
		$jubaopay->setEncrypt("payid", $out_trade_no);
		$jubaopay->setEncrypt("partnerid", $partner);
		$jubaopay->setEncrypt("amount", $total_fee);
		$jubaopay->setEncrypt("payerName", "");
		$jubaopay->setEncrypt("goodsName", $desc);
		$jubaopay->setEncrypt("payMethod", "ALL");
		$jubaopay->setEncrypt("remark", "");
		$jubaopay->setEncrypt("returnURL", $return_url);
		$jubaopay->setEncrypt("callBackURL", $notify_url);
				
		//对交易进行加密=$message并签名=$signature
		$jubaopay->interpret();
		$message=$jubaopay->message;
		$signature=$jubaopay->signature;
		//将message和signature一起aPOST到聚宝支付
				
		$parameter=array(
			"message"      =>$message,
			"signature"          =>$signature,
			"payMethod"    =>"ALL"
		);	
			
		$this->url = $jubaopay->buildRequestForm($parameter,'POST','submit');	
		/*var_dump($jubaopay);
		exit;*/	
	}

	//支付页面
	public function send_pay(){
		 echo  $this->url;
		 exit;
	}

}

?>
