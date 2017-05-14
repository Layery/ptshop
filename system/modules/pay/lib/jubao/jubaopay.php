<?php

require_once("RSA.php");

class jubaopay
{
	private $_rsa;
	
	private $message;
	private $signature;
	private $digest;
	private $encrypts;
	
	public function __construct($conf)
	{	
		$this->_rsa = new RSA($conf);	
		
		$this->message="";
		$this->signature="";
		$this->digest="";
		$this->encrypts=array();		
	}
	
	public function __destruct()
	{
		
	}
	
	public function __set($name,$value)
	{
		$this->$name = $value ;
	}
	
	public function __get($name)
	{
		return $this->$name;
	}
	
	public function generateRandomString( $length = 16 ) {
		
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
		$password = "";
		for ( $i = 0; $i < $length; $i++ )
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	
		return $password;
	}
	
	public function setEncrypt($key,$value){		
		$this->encrypts[$key]=$value;
	}
	
	public function getEncrypt($key){
		return $this->encrypts[$key];
	}	
	
	public function interpret()
	{	
		$this->digest="";
		$plainString="";
		
		$count=0;
		foreach( $this->encrypts as $key => $value ) {
			$count=$count+1;
			$this->digest.=$value;
			$plainString.=urlencode($key)."&".urlencode($value);
			if ($count < count($this->encrypts))
				$plainString.="&";
		}
		
		$key = $this->generateRandomString();
		$iv = $this->generateRandomString();
		
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainString, MCRYPT_MODE_CBC, $iv));
		
		$this->message=$this->_rsa->pubEncrypt($key).$this->_rsa->pubEncrypt($iv).$encrypted;
		$this->signature=$this->_rsa->sign($this->digest);

	}
	
	public function decrypt($message)
	{
		$this->message=$message;
		$key=$this->_rsa->privDecrypt(substr($message,0,172));
		$iv=$this->_rsa->privDecrypt(substr($message,172,172));	
		$decrypted = base64_decode(substr($message,172+172));		
		$plainString=rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted, MCRYPT_MODE_CBC, $iv),"\0");
		
		$this->digest="";		
		$this->encrypts=array();
		$items=explode('&', $plainString);
		
		for ($i=0; $i<count($items)/2; $i++){
			$field = urldecode($items[2*$i]);
			$value = urldecode($items[2*$i+1]);
			$this->encrypts[$field] = $value;
			$this->digest.=$value;			
		}		
	}
	
	public function verify($signature)
	{		
		$this->signature=$signature;
		return $this->_rsa->verify($this->digest,$this->signature);				
	}
	
	public function encryptComfirm()
	{		
		$this->digest="";
		$plainString="";
		
		$count=0;
		foreach( $this->encrypts as $key => $value ) {
			$count=$count+1;
			$this->digest.=$value;
			$plainString.=urlencode($key)."&".urlencode($value);
			if ($count < count($this->encrypts))
				$plainString.="&";
		}
		
		$key = $this->generateRandomString();
		$iv = $this->generateRandomString();
		
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainString, MCRYPT_MODE_CBC, $iv));
		
		$this->message=$this->_rsa->pubEncrypt($key).$this->_rsa->pubEncrypt($iv).$encrypted;
			
	}
	
	public function signComfirm()
	{
		$this->signature=$this->_rsa->sign($this->digest);
		return $this->signature;
	}
	
	/**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
	function buildRequestForm($para_temp, $method, $button_name) {
		//待请求参数数组
		$sHtml = "<h3>正在跳转到聚宝支付....</h3>";
		$flag=$this->is_mobile_request();
		if($flag)
			$sHtml .= "<form name='E_FORM' action='http://www.jubaopay.com/apiwapsyt.htm' method='".$method."'>";	
		else
			$sHtml .= "<form name='E_FORM' action='http://www.jubaopay.com/apipay.htm' method='".$method."'>";	
		while (list ($key, $val) = each ($para_temp)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
		//submit按钮控件请不要含有name属性
       // $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";		
		$sHtml = $sHtml."</form><script>document.forms['E_FORM'].submit();</script>";		
		return $sHtml;
	}
	
	function is_mobile_request()  {   
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';   
		$mobile_browser = '0';   
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))    
			$mobile_browser++;   
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))    
			$mobile_browser++;   
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']))    
			$mobile_browser++;   
		if(isset($_SERVER['HTTP_PROFILE']))    
			$mobile_browser++;   
			$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));   
			$mobile_agents = array(      
				'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',      
				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',      
				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',      
				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',     
				'newt','noki','oper','palm','pana','pant','phil','play','port','prox',      
				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',      
				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',      
				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',      
				'wapr','webc','winw','winw','xda','xda-'    
				);   
		if(in_array($mobile_ua, $mobile_agents))    
			$mobile_browser++;   
		if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)    
			$mobile_browser++;   // Pre-final check to reset everything if the user is on Windows   
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)   
			$mobile_browser=0;   // But WP7 is also Windows, with a slightly different characteristic   
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)    
			$mobile_browser++;   
		if($mobile_browser>0)    
			return true;   
		else  
			return false;
	}
	
}
