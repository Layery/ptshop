<?php 



/**

*	V3.1.6	 time:2014-06-12

**/



class sendmobile {

	

	public $error = '';

	public $v = '';

	

	private $mobile;

	private $config;

	private $op;

	



	

	/**

	*	短信配置总入口

	*	config  @设置要发送的短信数组

	*	mobiles @短信总配置文件

	*	key 	@手动指定开启的短信接口,不指定调用配置文件

	**/

	public function init($config=null,$mobiles=null,$key=null){

		if(!$config){

			return false;

		}

		

		if($config['mobile']==NULL)return false;

		if($config['content']==NULL)return false;

		$this->config = $config;

		

		if(!$mobiles){

			$this->mobile = System::load_sys_config('mobile');

		}

		if(intval($key) && isset($this->mobile['cfg_mobile_'.$key]) && method_exists($this,"cfg_seting_".$key)){

			$op = $key;

			$func = "cfg_seting_".$key;		

		}else{

			$op = $this->mobile['cfg_mobile_on'];

			$func = "cfg_seting_".$this->mobile['cfg_mobile_on'];		

		}	

		$this->op = $op; 

		return $this->$func();	

	}

	

	

	

	/**

	*	总发送入口	

	**/	

	public function send(){

		$func = "cfg_send_".$this->op;

		return $this->$func();	

	}



    /**
     * 阿里大于短信API配置
     */
    public function cfg_seting_1(){
        //$this->client = System::load_sys_class('AlidayuClient');
        //$this->request = System::load_sys_class('SmsNumSend');
        return true;
    }
    /**
     * 阿里大于短信API发送请求
     */
    public function cfg_send_1(){
        // header('content-type:text/html;charset=utf-8');
        //date_default_timezone_set('PRC');
        $mobileconfig = $this->mobile['cfg_mobile_1'];
        //引入阿里大于类
        $client = System::load_sys_class('Alidayuclient');
        $request = System::load_sys_class('SmsNumSend');
        $mobile = $this->config['mobile'];
        $checkcode = $this->config['content'];
        $user = substr($mobile,0,3).'****'.substr($mobile,7,4);
        // $this->v = $checkcode;
        //$this->error = 1;
        // 短信内容参数
        $smsParams = array(
            'code'    => $checkcode,
            'product' => $user
        );

        // 设置请求参数
        $req = $request->setSmsTemplateCode($mobileconfig['templateid'])
            ->setRecNum($mobile)
            ->setSmsParam(json_encode($smsParams))
            ->setSmsFreeSignName($mobileconfig['mqianming'])
            ->setSmsType('normal')
            ->setExtend('demo');
        $response = $client->execute($req);
        foreach($response as $key => $v){
            if(is_array($v)){
                if($v['result']['err_code'] == 0){
                    $this->v = $v['result']['model'];//'发送成功'//$v['result']['model']
                    $this->error = 1;
                }else{
                    $this->v = '发送失败';
                    $this->error = -1;
                }
            }else{
                $this->v = $v['sub_msg'];
                $this->error = -1;
            }
        }
    }
    /**
     * 阿里大于短信API发送记录查询
     */
    public function cfg_getdata_1(){

    }


		

	/**********************************************************/

	/**********************************************************/

	/**********************************************************/

	

	

	/*短信宝短信配置设置*/

	private function cfg_seting_2(){

		$mobile = $this->mobile['cfg_mobile_2'];

		$this->config['content'] = $this->config['content'].$mobile['mqianming'];

		return true;

	}

	

	/*短信宝短信发送*/

	private function cfg_send_2(){
		$mobile = $this->mobile['cfg_mobile_2'];
		$config = $this->config;
		$url = "http://www.smsbao.com/sms?u=".$mobile["mid"]."&p=".md5($mobile["mpass"])."&m=".$config["mobile"]."&c=".$config['content'];
		$ch2 = curl_init($url);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch2);
		$errno = curl_errno($ch2);
		curl_close($ch2);
		if($html>0){
			$errorArray = array(30=>"密码错误",40=>"账号不存在",41=>"余额不足",42=>"帐号过期",43=>"IP地址限制",50=>"内容含有敏感词",51=>"手机号码不正确");
			$error = $errorArray[$html];
			if(empty($error)){
				$error = "发送失败";
			}
			$this->v=$error;
			$this->error=-1;
		}else{
			$this->v="发送成功";
			$this->error=1;
		}
	}

	

	/*短信宝短信其他操作*/

	public function cfg_getdata_2(){
		$this->mobile = System::load_sys_config("mobile");
		$flag = 0; 
		$mobile = $this->mobile['cfg_mobile_2'];	
		if($mobile['mid']==null || $mobile['mpass']==null){
			$this->error=-2;
			$this->v="短信账户或者密码不能为空!";
			return;
		}
		$url = "http://www.smsbao.com/query?u=".$mobile["mid"]."&p=".md5($mobile["mpass"]);
		$ch2 = curl_init($url);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch2);
		curl_close($ch2);
		$htmls = split(",",$html);	
		$this->v=$htmls[1];
		$this->error=1;
	 	return array($this->error,$this->v);
}

	

		

	/**********************************************************/

	/**********************************************************/

	/**********************************************************/


	

	/*URL转数组*/

	private function exp_url($url=''){

		if(!empty($url)){

			$ret = iconv("GB2312","UTF-8",$url);

			$ret = explode("&",$ret);

				foreach($ret as $k=>$v){

					$v = explode("=",$v);

					$ret[$v[0]] = $v[1];

				}

			return $ret;

		}else{

			return false;

		}

		

	}

	

}