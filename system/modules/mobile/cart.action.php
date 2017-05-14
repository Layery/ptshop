  <?php
  defined ('G_IN_SYSTEM') or exit ( 'No permission resources.' );
  System::load_app_class ( 'base', 'member', 'no' );
  System::load_app_fun ( 'user', 'go' );
class cart extends base {
	private $Cartlist;
	private $Cartone;
	private $Cartlist_jf;
	public function __construct() {
		$this->Cartlist = json_decode ( stripslashes ( _getcookie('Cartlist') ), true );
		if(isset($_COOKIE['Cartone'])){
			$this->Cartone = json_decode ( stripslashes ( _getcookie('Cartone') ), true );
		}
		$this->Cartlist_jf = _getcookie('Cartlist_jf');
		$this->db = System::load_sys_class("model");
	}
	
	/**
	 * 订单支付页面
	 */
	public function orderPay(){
		$webname = $this->_cfg ['web_name'];
		parent::__construct ();
		if (! $member = $this->userinfo) {
			header ( "location: " . WEB_PATH . "/mobile/user/login" ); //改为微信登录
		}
		$uid = $this->userinfo ['uid'];
		//获取订单号
		$orderCode = safe_replace($this->segment(4));
		if(empty($orderCode)){
			_messagemobile('订单不存在，请确认！');
		}
		//根据订单号查询未支付订单
		$orderInfo = $this->db->GetList("select * from `@#_member_go_record` WHERE `code`='$orderCode' AND `status`='未付款,未发货,未完成' AND `uid`= '$uid'");
		if(empty($orderInfo)){
			_messagemobile('订单不存在，请确认！');
		}
		//p($orderInfo);exit;
		$shoplist = array();
		$MoenyCount = 0;
		foreach ($orderInfo as $val) {
            $key = $val['pro_id'];
			$shoplist[$key] = unserialize($val['pro_info']);
			$remark = $val['remark'];
			$MoenyCount += $shoplist[$key]['cart_xiaoji'];
			$shopid = $val['shopid'];
			$addrid = $val['address'];
			//查询货品表里面是否有余量
			$proinfo = $this->db->GetOne("select `inventory` from `@#_spec_goods_price` where `key`='$key' AND `goods_id`=$shopid");
			$shopinfo = $this->db->GetOne ( "SELECT `surplus` FROM `@#_shoplist` where `id`=$shopid", array ("key" => "id"));
			if($proinfo['inventory']==0 || $shopinfo['surplus'] == 0){
				_messagemobile('订单里的商品没有库存，请重新选择购买！');
			}
		}
		$shopnum = 0;
		// 总支付价格
		$MoenyCount = substr ( sprintf ( "%.3f", $MoenyCount ), 0, - 1 );
		// 会员余额
		$Money = $member ['money'];
		// 商品数量
		$shoplen = count ( $shoplist );
		$fufen_dikou = 0;
		$address = $this->db->GetOne("select * from `@#_member_dizhi` WHERE `uid`='$uid' AND `id`='$addrid'");
		$paylist = $this->db->GetList("SELECT * FROM `@#_pay` where `pay_start` = '1' AND pay_mobile = 1");
		session_start ();
		$_SESSION ['submitcode'] = $submitcode = uniqid ();
		//p($shoplist);exit;
		include templates ( "mobile/cart", "orderpay" );
	}

	/**
	 * 订单开始支付
	 */
	public function ordersubmit(){
		$webname = $this->_cfg ['web_name'];
		header ( "Cache-control: private" );
		parent::__construct ();
		if (! $this->userinfo) {
			header ( "location: " . WEB_PATH . "/mobile/user/login" );
			exit;
		}
		session_start ();
		$checkpay = $this->segment ( 4 ); // 获取支付方式 fufen money bank
		$checkpay = 'money';
		$banktype = $this->segment ( 5 ); // 获取选择的银行 CMBCHINA ICBC CCB
		$money = $this->segment ( 6 ); // 获取需支付金额
		$fufen = $this->segment ( 7 ); // 获取积分
		$submitcode1 = $this->segment ( 8 ); // 获取SESSION
		$addrId = intval($this->segment(9)); //获取地址ID
		$orderCode = safe_replace($this->segment(10));
		$remark = addslashes(safe_replace($this->segment(11))); //获取备注信息,防sql注入
		$uid = $this->userinfo ['uid'];
		//echo $checkpay,'<br>',$banktype,'<br>',$money,'<br>',$fufen,'<br>',$submitcode1,'<br>',$uid;exit;
		//var_dump($orderCode);exit;
		if (! empty ( $submitcode1 )) {
			if (isset ( $_SESSION ['submitcode'] )) {
				$submitcode2 = $_SESSION ['submitcode'];
			} else {
				$submitcode2 = null;
			}
			if ($submitcode1 == $submitcode2) {
				unset ( $_SESSION ["submitcode"] );
			} else {
				$WEB_PATH = WEB_PATH;
				_messagemobile ( "请不要重复提交...<a href='{$WEB_PATH}/mobile/cart/orderpay/'.$orderCode style='color:#C40000'>返回确认订单</a>查看" );
				exit;
			}
		}
		/*$zhifutype = $this->db->GetOne ( "select * from `@#_pay` where `pay_class` = 'alipay' " );
		if (! $zhifutype) {
			_messagemobile ( "手机支付只支持支付宝,请联系站长开通！" );
		}*/
		$pay_checkbox = false;
		$pay_type_bank = false;
		$pay_type_id = false;

        if ($checkpay == 'money') {
            $pay_checkbox = true;
        }

        if ($banktype != 'nobank'){
            $res = $this->db->GetOne ( "select `pay_id`,`pay_class` from `@#_pay` where `pay_id` = $banktype AND `pay_start`= 1" );
            if($res){
                $pay_type_id = $banktype;
                $pay_type_bank = $res['pay_class'];
            }else{
                _messagemobile ( "选择支付方式" );
            }
        }

		/**
		 * ***********
		 * start
		 * ***********
		 */
		$payinfo = $this->db->GetOne("select * from `@#_member_addmoney_record` where `ordercode` = '$orderCode' and `status` = '未付款'");
		//p($payinfo);exit;
		if(empty($payinfo)){
			_messagemobile("订单不存在!");
		}
		$pay=System::load_app_class('pay','pay');
		$scookies = unserialize($payinfo['scookies']);
		$pay->scookie = $scookies;
		$pay->fufen = $checkpay=='fufen'?$fufen:0;
		$pay->pay_type_bank = $pay_type_bank;
		$pay->shopinfo['addrid'] = $addrId;
		$pay->shopinfo['remark'] = $remark;
		$ok = $pay->init($uid,$pay_type_id,'go_record');

		if($ok != 'ok'){
			_messagemobile ( "购买失败,请重新购买",WEB_PATH);
		}
		$check = $pay->go_pay ( $pay_checkbox );
		//var_dump($check);exit;
		//exit;
		if ($check) {
			// 成功
			header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
		} else {
			// 失败
			$this->db->Query("UPDATE `@#_member_go_record` SET `status`='已付款,未发货,已作废' WHERE `code`='$orderCode' and `uid` = '$uid'");
			header ( "location: " . WEB_PATH . "/mobile/mobile" );
		}
		exit;
	}


	//购物车商品列表
	public function cartlist() {
		$webname = $this->_cfg ['web_name'];
		$Mcartlist = $this->Cartlist;
		_setcookie('Cartone','','');  //把直接购买的cookie给清空（重要）
		/*print_r($Mcartlist);
        exit;*/
		$id_temp = $item_temp = array();
        $item_price_key = '';
        if(is_array($Mcartlist)){
            foreach ($Mcartlist as $k => $v){
                $id_temp[] = $v['goods_id'];
				if(empty($v['flag'])){
					$item_temp[] = "'".$k."'";
				}
            }
            $goods_id = implode(',',array_unique($id_temp));
			if(!empty($item_temp)){
				$item_price_key = implode(',',$item_temp);
			}
        }
		$shoplist = array ();
		if ($goods_id != NULL) {
			$shoparr = $this->db->GetList ( "SELECT * FROM `@#_shoplist` where `id` in($goods_id)", array ("key" => "id" ) );
		}
		if(!empty($item_price_key)){
			$item_price_info = $this->db->GetList("SELECT * FROM `@#_spec_goods_price` where `key` in($item_price_key)",array('key'=>'key'));
			$item_image = $this->db->GetList("select * from `@#_spec_image`");
		}
		//p($shoparr);exit;
		if (!empty ( $shoparr )) {
			foreach ( $shoparr as $key => $val ) {//购物车商品的基本信息
				if(!isset($Mcartlist[$key])){
					foreach ($item_price_info as $k => $v){//购物车对应商品的具体不同规格的货品信息
						if($key == $v['goods_id']){ //同件商品，不同属性
							if ($val ['surplus'] != 0 && $v['inventory'] != 0) { //商品的基本信息的剩余量和货品的剩余量
								$shoplist [$k] = $v;
								$shoplist [$k]['title'] = $val['title'];
								$shoplist [$k]['goods_img'] = $Mcartlist[$k]['goods_img'];
								$shoplist [$k]['flag'] = intval($Mcartlist[$k]['flag']);
								$Mcartlist [$k] ['shenyu'] = $v ['inventory'];
								$Mcartlist [$k] ['money'] = $v ['price'];
								$Mcartlist [$k] ['sun'] = $v ['price']*$Mcartlist [$k] ['num'];
							}
						}
					}
				}else{
					$shoplist [$key] = $val;
					$shoplist [$key]['goods_id'] = $val['id'];
					$shoplist [$key]['goods_img'] = $Mcartlist[$key]['goods_img'];
					$shoplist [$key]['price'] = $val['money'];
					$shoplist [$key]['flag'] = intval($Mcartlist[$key]['flag']);
					$Mcartlist [$key] ['shenyu'] = $val ['surplus'];
					$Mcartlist [$key] ['money'] = $val ['money'];
					$Mcartlist [$key] ['sun'] = $val ['money']*$Mcartlist [$key] ['num'];
				}
			}
			//p($Mcartlist);exit;
			//p($shoplist);exit;
			_setcookie ( 'Cartlist', json_encode ( $Mcartlist ), '' );
		}

		$MoneyCount = 0;
		$Cartshopinfo = '{';
		if (count ( $shoplist ) >= 1) {
			foreach ( $Mcartlist as $key => $val ) {
				//$key = intval ( $key );
				if (isset ( $shoplist [$key] )) {
					$shoplist [$key] ['cart_gorenci'] = $val ['num'] ? $val ['num'] : 1;
					$MoneyCount += $shoplist [$key] ['price']* $shoplist [$key] ['cart_gorenci'];
					$shoplist [$key] ['cart_xiaoji'] = substr ( sprintf ( "%.3f", $shoplist [$key] ['price'] * $val ['num'] ), 0, - 1 );
					$shoplist [$key] ['cart_shenyu'] = $shoplist [$key] ['inventory'];
					$Cartshopinfo .= "'$key':{'shenyu':" . $shoplist [$key] ['cart_shenyu'] . ",'num':" . $val ['num'] . ",'money':" . $shoplist [$key] ['price'] . "},";
				}
			}
		}
		$shop = 0;
		if (! empty ( $shoplist )) {
		  $shop = 1;
		}
		//p($Mcartlist);exit;
		$MoneyCount = substr ( sprintf ( "%.3f", $MoneyCount ), 0, - 1 );
		$Cartshopinfo .= "'MoenyCount':$MoneyCount}";
        //p($shoplist);exit;
		include templates ( "mobile/cart", "cartlist" );
	}

	// 单人购买支付界面
	public function pay() {
		$webname = $this->_cfg ['web_name'];
		parent::__construct ();
		if (! $member = $this->userinfo) {
			header ( "location: " . WEB_PATH . "/mobile/user/login" ); //登录
		}
		if(!isset($this->Cartone)) {
			$Mcartlist = $this->Cartlist; //购物车
		}else{
			$Mcartlist = $this->Cartone; //直接购买
		}
		//echo '<pre>';
		//p($Mcartlist);exit;
		$id_temp = $item_temp = array();
		$item_price_key = '';
		if(is_array($Mcartlist)){
			foreach ($Mcartlist as $k => $v){
				$id_temp[] = $v['goods_id'];
				if(empty($v['flag'])){
					$item_temp[] = "'".$k."'";
				}
			}
			$goods_id = implode(',',array_unique($id_temp));
			if(!empty($item_temp)){
				$item_price_key = implode(',',$item_temp);
			}
		}
		$shoplist = array ();
		if ($goods_id != NULL) {
			$shoparr = $this->db->GetList ( "SELECT * FROM `@#_shoplist` where `id` in($goods_id)", array ("key" => "id" ) );
		}
		if(!empty($item_price_key)){
			$item_price_info = $this->db->GetList("SELECT * FROM `@#_spec_goods_price` where `key` in($item_price_key)",array('key'=>'key'));
			$item_image = $this->db->GetList("select * from `@#_spec_image`");
		}
		//p($shoparr);exit;
		if (!empty ( $shoparr )) {
			foreach ( $shoparr as $key => $val ) {//购物车商品的基本信息
				if(!isset($Mcartlist[$key])){
					foreach ($item_price_info as $k => $v){//购物车对应商品的具体不同规格的货品信息
						if($key == $v['goods_id']){ //同件商品，不同属性
							if ($val ['surplus'] != 0 && $v['inventory'] != 0) { //商品的基本信息的剩余量和货品的剩余量
								$shoplist [$k] = $v;
								$shoplist [$k]['title'] = $val['title'];
								$shoplist [$k]['goods_img'] = $Mcartlist[$k]['goods_img'];
								$shoplist [$k]['flag'] = intval($Mcartlist[$k]['flag']);
							}
						}
					}
				}else{
					$shoplist [$key] = $val;
					$shoplist [$key]['goods_id'] = $val['id'];
					$shoplist [$key]['goods_img'] = $Mcartlist[$key]['goods_img'];
					$shoplist [$key]['price'] = $val['money'];
					$shoplist [$key]['flag'] = intval($Mcartlist[$key]['flag']);
				}
			}
			$shopnum = 0;
		}else{
			//_setcookie ( 'Cartlist', NULL );
			// _message("购物车没有商品!",WEB_PATH);
			$shopnum = 1; // 表示没有商品
		}
		$MoneyCount = 0;
		if (count ( $shoplist ) >= 1) {
			foreach ( $Mcartlist as $key => $val ) {
				//$key = intval ( $key );
				if (isset ( $shoplist [$key] )) {
					$shoplist [$key] ['cart_gorenci'] = $val ['num'] ? $val ['num'] : 1;
					$MoneyCount += $shoplist [$key] ['price']* $shoplist [$key] ['cart_gorenci'];
					$shoplist [$key] ['cart_xiaoji'] = substr ( sprintf ( "%.3f", $shoplist [$key] ['price'] * $val ['num'] ), 0, - 1 );
					$shoplist [$key] ['cart_shenyu'] = $shoplist [$key] ['inventory'];
				}
			}
		}
		// 总支付价格
		$MoneyCount = substr ( sprintf ( "%.3f", $MoneyCount ), 0, - 1 );
		// 会员余额
		$Money = $member ['money'];
		// 商品数量
		$shoplen = count ( $shoplist );

		$fufen = System::load_app_config ( "user_fufen", '', 'member' );
		if ($fufen ['fufen_yuan']) {
			$fufen_dikou = intval ( $member ['score'] / $fufen ['fufen_yuan'] );
		} else {
			$fufen_dikou = 0;
		}
		//p($Mcartlist);exit;
		$paylist = $this->db->GetList("SELECT * FROM `@#_pay` where `pay_start` = '1' AND `pay_mobile` = 1");
		session_start ();
		$_SESSION ['submitcode'] = $submitcode = uniqid ();
		include templates ( "mobile/cart", "payment" );
	}

	// 开始支付
	public function paysubmit() {
		$webname = $this->_cfg ['web_name'];
		header ( "Cache-control: private" );
		parent::__construct ();
		if (! $this->userinfo) {
			header ( "location: " . WEB_PATH . "/mobile/user/login" );
			exit;
		}
		session_start ();

		$checkpay = $this->segment ( 4 ); // 获取支付方式 fufen money bank
		//$checkpay = 'money';
		$banktype = intval($this->segment ( 5 )); // 获取选择的银行 CMBCHINA ICBC CCB
		$money = $this->segment ( 6 ); // 获取需支付金额
		$fufen = $this->segment ( 7 ); // 获取积分
		$submitcode1 = $this->segment ( 8 ); // 获取SESSION
		$addrId = intval($this->segment(9)); //获取地址ID
		$remark = addslashes(safe_replace($this->segment(10))); //获取备注信息,防sql注入

		$uid = $this->userinfo ['uid'];
		//echo $checkpay,'<br>',$banktype,'<br>',$money,'<br>',$fufen,'<br>',$submitcode1,'<br>',$uid;exit;
		if (! empty ( $submitcode1 )) {
			if (isset ( $_SESSION ['submitcode'] )) {
			  	$submitcode2 = $_SESSION ['submitcode'];
		  	} else {
			  	$submitcode2 = null;
		  	}
		  	if ($submitcode1 == $submitcode2) {
			  	unset ( $_SESSION ["submitcode"] );
		  	} else {
				$WEB_PATH = WEB_PATH;
				if(isset($_COOKIE['Cartone'])){
					_messagemobile ( "请不要重复提交...<a href='{$WEB_PATH}/mobile/cart/pay' style='color:#C40000'>返回确认订单</a>查看" );
				}else{
					_messagemobile ( "请不要重复提交...<a href='{$WEB_PATH}/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看" );
				}
				exit ();
		  	}
		}else{
			$WEB_PATH = WEB_PATH;
			_messagemobile ( "正在返回购物车...<a href='{$WEB_PATH}/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看" );
		}

		//$zhifutype = $this->db->GetOne ( "select * from `@#_pay` where `pay_class` = 'alipay' " );
		//p($zhifutype);exit;

		$pay_checkbox = false;
		$pay_type_bank = false;
		$pay_type_id = false;
		//echo $checkpay.'<br>'.$banktype;exit;

		if ($checkpay == 'money') {
		  	$pay_checkbox = true;
		}

		if ($banktype != 'nobank'){
			$res = $this->db->GetOne ( "select `pay_id`,`pay_class` from `@#_pay` where `pay_id` = $banktype AND `pay_start`= 1" );
			if($res){
				$pay_type_id = $banktype;
				$pay_type_bank = $res['pay_class'];
			}else{
				_messagemobile ( "选择支付方式" );
			}
		}

		/**
		* ***********
		* start
		* ***********
		*/
		$pay=System::load_app_class('pay','pay');
		//修改支付每次都要使用福分问题 lq 2014-12-01
		//$pay->fufen = $fufen;
		$pay->fufen = $checkpay=='fufen'?$fufen:0;
		$pay->pay_type_bank = $pay_type_bank;
		$pay->shopinfo['addrid'] = $addrId;
		$pay->shopinfo['remark'] = $remark;

		$ok = $pay->init($uid,$pay_type_id,'go_record');	//合计商品

		if($ok != 'ok'){
			if(isset($_COOKIE['Cartone'])){
				_setcookie('Cartone',NULL);
				_messagemobile("商品已经下架，请重新选择购买",WEB_PATH);
			}else{
				_setcookie('Cartlist',NULL);
				_messagemobile("购物车没有商品请<a href='".WEB_PATH."/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看");
			}
		}
		foreach ($this->Cartone as $key => $value) {
			$group_pre = '';
			if ($value['is_group']) {
				$group_pre = 'E';
			}
		}
		$query = $pay->set_dingdan('在线支付','A',$group_pre);
		//var_dump($query);exit;
		if(!$query){
			if (isset($_COOKIE['Cartone'])){
				_setcookie ( 'Cartone', NULL );
				_messagemobile ( "订单添加失败,请重新购买",WEB_PATH."mobile/mobile");
			}else{
				_setcookie ( 'Cartlist', NULL );
				_messagemobile ( "订单添加失败,请<a href='" . WEB_PATH . "/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看" );
			}
		}else{
			session_start();
			$_SESSION['order'] = $pay->dingdancode;
			if(isset($_COOKIE['Cartone'])){
				_setcookie ( 'Cartone', NULL );
			}else{
				_setcookie ( 'Cartlist', NULL );
			}
		}

		//var_dump($pay_checkbox);
		$check = $pay->go_pay ( $pay_checkbox );
		//var_dump($check);exit;
		//exit;
		/*if (!$check) {
			if (isset($_COOKIE['Cartone'])){
				_messagemobile ( "订单添加失败,请重新购买",WEB_PATH);
			}else{
				_messagemobile ( "订单添加失败,请<a href='" . WEB_PATH . "/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看" );
			}
		}*/
		if ($check) {
		  	// 成功
		  	header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
		} else {
		  	// 失败
			if (isset($_COOKIE['Cartone'])){
				_messagemobile ( "订单添加失败,请重新购买",WEB_PATH);
			}else{
				_messagemobile ( "订单添加失败,请<a href='" . WEB_PATH . "/mobile/cart/cartlist' style='color:#C40000'>返回购物车</a>查看" );
			}
		}
		exit;
	}

	//成功页面
	public function paysuccess(){
		parent::__construct ();
		$member = $this->userinfo;
		$webname=$this->_cfg['web_name'];
		_setcookie('Cartlist',NULL);
		//获取活动支付成功后的cookie
		$act_id = json_decode(stripslashes(_getcookie('act_id')),true);
		if($act_id){
			$actInfo['act_id'] = $act_id;
			$actInfo['flag'] = 'ok';
			_setcookie('actInfo',json_encode($actInfo),'');
			$activity = $this->db->GetOne("select `act_id`,`act_title`,`act_start_time`,`act_address` from `@#_activity` WHERE `act_id`=$act_id");
			if($member['wxid']){ //发送模板消息
				$config = System::load_app_config('connect','','api');
				$appid = $config['weixin']['id'];
				$appsecret = $config['weixin']['key'];
				//1.获取access_token
				$access_token = get_token($appid,$appsecret);
				//查询模板消息id
				$template_act = $this->db->GetOne("SELECT * FROM `@#_wxch_cfg` WHERE `cfg_name` = 'template_act'");
				$template_id = $template_act['cfg_value'];
				$url = WEB_PATH.'/mobile/activity/activity/'.$act_id;
				//2.组装数组
				$template = array(
					'touser'=>$member['wxid'],
					'template_id'=>$template_id,
					'url'=>$url,
					'topcolor'=>'#7B68EE',
					'data'=>array(
						'first'=>array('value'=>urlencode('恭喜，您已报名成功。活动信息如下：'),'color'=>'#2B2A2A'),
						'keynote1'=>array('value'=>urlencode($activity['act_title']),'color'=>'#2B2A2A'),
						'keynote2'=>array('value'=>urlencode(date('Y-m-d H:i',$activity['act_start_time'])),'color'=>'#2B2A2A'),
						'keynote3'=>array('value'=>urlencode($activity['act_address']),'color'=>'#2B2A2A'),
						'remark'=>array('value'=>urlencode('感谢您的参与，点击查看活动详情'),'color'=>'#2B2A2A'),
					)
				);
				$data = urldecode(json_encode($template)); //转json数据
				$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
				//4.调用curl函数
				$res = http_request($url,$data);
				$res = json_decode($res,true);
			}
			header ( "location: " . WEB_PATH . "/mobile/activity/activity/$act_id" );
			exit;
		}
	include templates("mobile/cart","paysuccess");
	}

	//支付失败页面
	public function paycancel(){
	$webname=$this->_cfg['web_name'];
		//获取活动支付成功后的cookie
		$act_id = json_decode(stripslashes(_getcookie('act_id')),true);
		if($act_id){
			$actInfo['act_id'] = $act_id;
			$actInfo['flag'] = 'failed';
			_setcookie('actInfo',json_encode($actInfo),'');
			header ( "location: " . WEB_PATH . "/mobile/activity/activity/$act_id" );
			exit;
		}
	include templates("mobile/cart","paycancel");
	}

	//成功页面
	public function jf_paysuccess(){
	$webname=$this->_cfg['web_name'];
	$_COOKIE['Cartlist_jf'] = NULL;
	_setcookie("Cartlist_jf",null);
	include templates("mobile/cart","jf_paysuccess");
	}

	// 充值
	public function addmoney() {
	  parent::__construct ();
	  $webname = $this->_cfg ['web_name'];
	  $money = $this->segment ( 4 ); // 获取充值金额
	  $pay_id = $this->segment ( 5 ); // 获取选择的支付方式

	  if (! $this->userinfo) {
		  header ( "location: " . WEB_PATH . "/mobile/user/login" );
		  exit ();
	  }

	  $payment = $this->db->GetOne ( "select * from `@#_pay` where `pay_id` = ".$pay_id );


	  if (! $payment) {
		  _messagemobile ( "对不起，没有您所选择的支付方式！" );
	  }

	  if (! empty ( $payment )) {
		  $pay_type_bank = $payment ['pay_class'];
	  }
	  $pay_type_id = $pay_id;
	// 		$pay_type_bank=isset($_POST['pay_bank']) ? $_POST['pay_bank'] : false;
	// 		$pay_type_id=isset($_POST['account']) ? $_POST['account'] : false;
	// 		$money=intval($_POST['money']);
	  $uid = $this->userinfo ['uid'];
	  $pay = System::load_app_class ( 'pay', 'pay' );
	  $pay->pay_type_bank = $pay_type_bank;
	  $ok = $pay->init ( $uid, $pay_type_id, 'addmoney_record', $money );

	  if ($ok === 'not_pay') {
		  _messagemobile ( "未选择支付平台" );
	  }
	}

	/**
	 * 付费活动报名支付
	 */
	public function activity_pay(){
		$pay_type = intval($this->segment(4)); //支付方式：微信和支付宝，暂不支持其他的支付方式
		parent::__construct ();
		if (!$member = $this->userinfo) {
			if($pay_type){
				header ( "location: " . WEB_PATH . "/mobile/user/login"); //改为正常登录
			}else{
				header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
			}
			exit;
		}
		//$member = $this->userinfo;
		$signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true);
		//p($signInfo);exit;
        //$pay_type = intval($this->segment(4)); //支付方式：微信和支付宝，暂不支持其他的支付方式
        $uid = $member['uid'];
		$time = time();

		//判读用户是否已报名该活动，参加了就禁止报名
		$res = $this->db->GetOne("select * from `@#_act_order` WHERE `o_uid`=$uid AND `o_act_id`={$signInfo['actId']} AND `o_status`='已支付'");
		if($res){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "您已报名该活动，请不要重复报名！",WEB_PATH.'/mobile/mobile/activitylists');
		}

		//判断活动是否免费以及可报名
		$activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`={$signInfo['actId']} AND `act_active`=1 AND `act_start_time`>=$time");
		if(!$activity){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "报名已结束！",WEB_PATH.'/mobile/activity/activitylists');
		}
		if($activity['act_charge'] == '0.00'){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "活动信息有误，请重新提交！",WEB_PATH.'/mobile/activity/activitylists');
		}

		/*$signInfo['uid'] = $uid;
		_setcookie('SignInfo',json_encode($signInfo),'');
		var_dump($signInfo);
		var_dump($_COOKIE['SignInfo']);*/
		//查询积分兑换
		$param = array();
		$integral = $this->db->GetOne("select `value` from `@#_config` WHERE `name`='integral'");
		//point < maxpoint ? point*rate : maxpoint*rate);
		$price = $activity['act_charge']; //活动费用
		$my_integral = $member['score']; //用户积分
		$limit_discount = $activity['integral']; //该活动最多抵现金额
		$user_discount = bcdiv($my_integral,$integral['value'],2); //用户总抵现金额
		$integral_disc = $user_discount<$limit_discount ? $user_discount : $limit_discount; //实际抵现金额
		$balance = $signInfo['balance'] != 'none'? $member['money'] : 0; //是否选中余额支付
		$discount = $signInfo['integral'] != 'none'? substr ( sprintf ( "%.3f", $integral_disc ), 0, - 1 ) : 0; //是否选择积分抵现
		$dis_sum = $balance + $discount; //积分和余额的总和
		$refund = $price - $discount; //扣掉抵现金额
		$payment = $price - $dis_sum; //实际付款金额:大于，等于，小于
		$param['payment'] = $payment; //实际付款金额
		$param['refund'] = $refund; //退款金额
		$param['discount'] = $discount; //积分抵现
		if($discount != 0){
			$use_integral = $discount * $integral['value'];
		}else{
			$use_integral = 0;
		}
		$param['integral'] = $use_integral;
		if($payment <= 0){
			//不用进入支付接口
			/**
			 * ***********
			 * start balance and discount pay
			 * ***********
			 */
			if($balance != 0){
				$last_balance = $refund; //扣掉使用的余额
			}else{
				$last_balance = 0;
			}
			$param['balance'] = $last_balance;
			$param['payment'] = 0;  //账单为零
			$pay=System::load_app_class('pay','pay');
			$pay_type_info = '余额支付';

			$initInfo = $pay->act_init($uid,$param,$signInfo); //初始化配置信息
			if($initInfo){
				$res = $pay->act_pay_action($pay_type_info,'D'); //执行处理方法 param1:支付方式，param2:订单前缀
				if($res){
					//_setcookie('act_id',json_encode($signInfo['actId']),''); //用来判断活动支付成功后跳转页面
					_setcookie('SignInfo','',''); //清除cookie
					header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
				}else{
					header ( "location: " . WEB_PATH . "/mobile/cart/paycancel" );
				}
			}else{
				_setcookie('SignInfo','',''); //清除cookie
				_messagemobile ( "活动已失效,请重新查找活动",WEB_PATH.'/mobile/activity/activityhome');
			}
		}else{
			//进入支付接口
			/**
			 * ***********
			 * start pay
			 * ***********
			 */
			if($balance != 0){
				$last_balance = $balance; //扣掉的余额
			}else{
				$last_balance = 0;
			}
			$param['balance'] = $last_balance;
			$pay=System::load_app_class('pay','pay');
			$pay->pay_type_bank = $pay_type?'wapalipay':'wxpay_web';
			$pay_type_info = $pay_type?'支付宝支付':'微信支付';
			$initInfo = $pay->act_init($uid,$param,$signInfo); //初始化配置信息
			$data = serialize($pay->scookie);
			if($initInfo){
				_setcookie('act_id',json_encode($signInfo['actId']),''); //用来判断活动支付成功后跳转页面
				$res = $pay->act_pay($data); //执行处理方法 param1:支付方式，param2:订单前缀
				if($res){
					_setcookie('SignInfo','',''); //清除cookie
					header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
				}else{
					header ( "location: " . WEB_PATH . "/mobile/cart/paycancel" );
				}
			}else{
				_setcookie('SignInfo','',''); //清除cookie
				_messagemobile ( "活动已失效,请重新查找活动",WEB_PATH.'/mobile/activity/activityhome');
			}
		}
        //exit;
	}
	/**
     * 免费活动报名
     */
	public function activity_free(){
		$pay_type = intval($this->segment(4)); //支付方式：微信和支付宝，暂不支持其他的支付方式
		parent::__construct ();
		if (!$member = $this->userinfo) {
			if($pay_type){
				header ( "location: " . WEB_PATH . "/mobile/user/login"); //改为正常登录
			}else{
				header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
			}
			exit;
		}
		$signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true);
		$uid = $member['uid'];
		$time = time();

		//判断用户是否已报名该活动，参加了就禁止报名
		$res = $this->db->GetOne("select * from `@#_act_order` WHERE `o_uid`=$uid AND `o_act_id`={$signInfo['actId']} AND `o_status`='已支付'");
		if($res){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "您已报名该活动，请不要重复报名！",WEB_PATH.'/mobile/activity/activitylists');
		}
		//判断活动是否免费以及可报名
		$activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`={$signInfo['actId']} AND `act_active`=1 AND `act_start_time`>=$time");
		if(!$activity){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "报名已结束！",WEB_PATH.'/mobile/activity/activitylists');
		}
		if($activity['act_charge'] != '0.00'){
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "该活动不是免费，请重新提交！",WEB_PATH.'/mobile/activity/activitylists');
		}
		$param = array();
		$param['payment'] = 0;
		$param['refund'] = 0;
		$param['discount'] = 0;
		$param['balance'] = 0;
		$param['integral'] = 0;

		/**
		 * ***********
		 * start free act pay
		 * ***********
		 */
		$pay=System::load_app_class('pay','pay');
		$pay_type_info = '免费';
		$initInfo = $pay->act_init($uid,$param,$signInfo); //初始化配置信息
		if($initInfo){
			$res = $pay->act_pay_action($pay_type_info,'D'); //执行处理方法 param1:支付方式，param2:订单前缀
			if($res){
				//_setcookie('act_id',json_encode($signInfo['actId']),''); //用来判断活动支付成功后跳转页面
				_setcookie('SignInfo','',''); //清除cookie
				header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
			}else{
				header ( "location: " . WEB_PATH . "/mobile/cart/paycancel" );
			}
		}else{
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "活动已失效,请重新查找活动",WEB_PATH.'/mobile/activity/activityhome');
		}
		/*exit;
		$order = $pay->set_act_order($pay_type_info,'D',$uid,$param);  //生成订单
		if($order){
			_setcookie('act_id',json_encode($signInfo['actId']),''); //用来判断支付活动支付成功后跳转的页面
			//生成订单后更新报名表的支付状态
			$sId = $pay->scookie['sId'];
			$actOrderId = $pay->scookie['actOrderId'];
			$shareUid = $signInfo['shareUid'];
			$num = 1;
			//更新活动表的报名人数
			$response_1 = $this->db->Query("UPDATE `@#_activity` SET `act_num_signed` = `act_num_signed` + $num WHERE `act_id`= {$signInfo['actId']}");
			$response_2 = $this->db->Query("UPDATE `@#_act_sign` SET `s_status` = '已支付' WHERE `s_id` = $sId AND `s_uid` = $uid");
			//更新活动订单表的支付状态
			$response_3 = $this->db->Query("UPDATE `@#_act_order` SET `o_status` = '已支付' WHERE `o_sid` = $sId AND `o_uid` = $uid AND `o_id` = $actOrderId");
			//添加积分
			$integral = intval($activity['give_integral']);
			$user_integral = $member['score']+$integral;
			$query_1 = $this->db->Query("UPDATE `@#_member` SET `score`= $user_integral WHERE (`uid`='$uid')");
			//分享消费活动积分

			if($response_1 && $response_2 && $response_3){
				$this->db->Autocommit_commit();
				_setcookie('SignInfo','',''); //清除cookie
				header ( "location: " . WEB_PATH . "/mobile/cart/paysuccess" );
			}else{
				header ( "location: " . WEB_PATH . "/mobile/cart/paycancel" );
			}
		}else{
			_setcookie('SignInfo','',''); //清除cookie
			_messagemobile ( "活动已失效,请重新查找活动",WEB_PATH.'/mobile/activity/activitylists');
		}*/
    }


    //2017.2.22 拼团购买界面
	public function grouppay(){
		$webname = $this->_cfg ['web_name'];
		parent::__construct ();
		if (! $member = $this->userinfo) {
			//header ( "location: " . WEB_PATH . "/mobile/user/login" ); //登录
            header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
		}
		if(!isset($this->Cartone)) {
			$Mcartlist = $this->Cartlist; //购物车
		}else{
			$Mcartlist = $this->Cartone; //直接购买
		}
		//echo '<pre>';
		$id_temp = $item_temp = array();
		$item_price_key = '';

		//是否是团长
		$is_head = 1;
		if(is_array($Mcartlist)){
			foreach ($Mcartlist as $k => $v){
				$id_temp[] = $v['goods_id'];
				$groupOC_temp = $v['groupOrderCode'];
				$is_head = $v['is_head'];
				if(empty($v['flag'])){
					$item_temp[] = "'".$k."'";
				}
			}
			$goods_id = implode(',',array_unique($id_temp));
			$groupOC = $groupOC_temp;
			//$groupOC = implode(',',array_unique($groupOC_temp));//获取订单号2017.3.12
			if(!empty($item_temp)){
				$item_price_key = implode(',',$item_temp);
			}
		}


		$shoplist = array ();
		if ($goods_id != NULL) {
			$shoparr = $this->db->GetList ( "SELECT * FROM `@#_shoplist` where `id` in($goods_id)", array ("key" => "id" ) );
		}
		if(!empty($item_price_key)){
			$item_price_info = $this->db->GetList("SELECT * FROM `@#_spec_goods_price` where `key` in($item_price_key)",array('key'=>'key'));
			$item_image = $this->db->GetList("select * from `@#_spec_image`");
		}

		//var_dump($is_head);
        $timer = 0; //团购倒计时
        $group_id = 0;
        $group_time = 0;
		if (!empty ( $shoparr )) {
			foreach ( $shoparr as $key => $val ) {//购物车商品的基本信息
                $surple_num = $limit = $val['group_number']; //团购人数限制
				if($is_head == 0){
                    $timer = $val['group_time']; //团购倒计时
				}
				if(!isset($Mcartlist[$key])){
					foreach ($item_price_info as $k => $v){//购物车对应商品的具体不同规格的货品信息
						if($key == $v['goods_id']){ //同件商品，不同属性
							if ($val ['surplus'] != 0 && $v['inventory'] != 0) { //商品的基本信息的剩余量和货品的剩余量
								$shoplist [$k] = $v;
								$shoplist [$k]['title'] = $val['title'];
								$shoplist [$k]['price'] = $val['group_price'];
								$shoplist [$k]['goods_img'] = $Mcartlist[$k]['goods_img'];
								$shoplist [$k]['flag'] = intval($Mcartlist[$k]['flag']);
							}
						}
					}
				}else{
					$shoplist [$key] = $val;
					$shoplist [$key]['goods_id'] = $val['id'];
					$shoplist [$key]['goods_img'] = $Mcartlist[$key]['goods_img'];
					$shoplist [$key]['price'] = $val['group_price'];
					$shoplist [$key]['flag'] = intval($Mcartlist[$key]['flag']);
				}
			}
			$shopnum = 0;
		}else{
			//_setcookie ( 'Cartlist', NULL );
			// _message("购物车没有商品!",WEB_PATH);
			$shopnum = 1; // 表示没有商品
		}
		$MoneyCount = 0;
		if (count ( $shoplist ) >= 1) {
			foreach ( $Mcartlist as $key => $val ) {
				//$key = intval ( $key );
				if (isset ( $shoplist [$key] )) {
					$shoplist [$key] ['cart_gorenci'] = $val ['num'] ? $val ['num'] : 1;
					$MoneyCount += $shoplist [$key] ['price']* $shoplist [$key] ['cart_gorenci'];
					$shoplist [$key] ['cart_xiaoji'] = substr ( sprintf ( "%.3f", $shoplist [$key] ['price'] * $val ['num'] ), 0, - 1 );
					$shoplist [$key] ['cart_shenyu'] = $shoplist [$key] ['inventory'];
				}
			}
		}
		// 总支付价格
		$MoneyCount = substr ( sprintf ( "%.3f", $MoneyCount ), 0, - 1 );
		// 会员余额
		$Money = $member ['money'];
		// 商品数量
		$shoplen = count ( $shoplist );

		$fufen = System::load_app_config ( "user_fufen", '', 'member' );
		if ($fufen ['fufen_yuan']) {
			$fufen_dikou = intval ( $member ['score'] / $fufen ['fufen_yuan'] );
		} else {
			$fufen_dikou = 0;
		}
		//p($Mcartlist);exit;
		$paylist = $this->db->GetList("SELECT * FROM `@#_pay` where `pay_start` = '1' AND `pay_mobile` = 1");
		//$groupSingle = $this->db->GetOne("SELECT * FROM `@#_member_go_record` where ");
		//echo $Mcartlist[0]['groupOrderCode'];exit();

		//参团判断
		$status = 1;
		if(!empty($groupOC)){
            $groupSingle = $this->db->GetList("SELECT a.*,b.`img`,b.`headimg` from `@#_member_go_record` a left join `@#_member` b on a.`uid` = b.`uid` WHERE a.`group_code`='$groupOC' AND a.`status`='已付款,未发货,未完成'");
            $groupNum = $this->db->GetOne("select count(*) as num from `@#_member_go_record` WHERE `group_code` = '$groupOC' AND `status`='已付款,未发货,未完成'");
            if($groupNum['num'] >= $limit){//判断该团是否成功
            	$status = 0; //该团已团成功
			}
			$surple_num = $limit - $groupNum['num']; //剩余人数
			if($is_head == 0){
                $groupSingle[] = $member; //把自己的信息存入参团信息里面
			}
			foreach ($groupSingle as $v){ //找出团长的下单时间和订单id
				if($v['is_head'] == 1){
					$group_id = $v['id'];
					$group_time = $v['time'];
				}
			}
			$res = $this->db->GetOne("select * from `@#_member_go_record` WHERE `group_code`='$groupOC' AND `uid`={$member['uid']}");
			if($res){
				$status = 2; //表示已参团，不可重复参团
			}
		}
		//var_dump($timer);exit;
		session_start ();
		$_SESSION ['submitcode'] = $submitcode = uniqid ();
		include templates("mobile/cart","grouppay");
	}

}


  
  ?>
