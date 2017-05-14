<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	
	const APPID = 'wx5d5ee9fec27cb97e';
	//受理商ID，身份标识
	const MCHID = '1289907801';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'dingmaomediacomcncomcndingmaoZDM';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '3a78f5224be625398794bd50d13f2d45';

	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = '/system/modules/pay/lib/wxpay_web.class.php';
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径D:\www\outmall\system\modules\pay\lib\wxpay\cacert
	const SSLCERT_PATH ='/pay/lib/weixin/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = '/pay/lib/weixin/cacert/apiclient_key.pem';
	const SSLCA_PATH = '/pay/lib/weixin/cacert/rootca.pem';
	//const SSLCERT12_PATH = '/system/modules/pay/lib/wxpay/cacert/apiclient_cert.p12';

	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = "/pay/wxpay_url/houtai/";

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>