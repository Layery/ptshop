<?php

defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_fun("pay","pay");
System::load_sys_fun("user");
System::load_app_class("tocode","pay",'no');
class pay {
	private $db;
	private $members;		//会员信息
	private $MoneyCount; 	//商品总金额
	private $shops; 		//商品信息
	private $pay_type;		//支付类型
	private $fukuan_type;	//付款类型 买商品 充值
	private $dingdan_query = true;	//订单的	mysql_qurey 结果
	public $pay_type_bank = false;
    public $shopinfo; //商品备注、地址

	public $scookie = null;
	public $fufen = 0;
	public $fufen_to_money = 0;
    private $act_order_id;


	//初始化类数据
	//$addmoney 充值金额
	public function init($uid=null,$pay_type=null,$fukuan_type='',$addmoney=''){
		$this->db=System::load_sys_class('model');
		$this->members = $this->db->GetOne("SELECT * FROM `@#_member` where `uid` = '$uid' for update");

		if($this->pay_type_bank){
			$pay_class = $this->pay_type_bank;
			$this->pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_class` = '$pay_class' and `pay_start` = '1'");
			$this->pay_type['pay_bank'] = $pay_type; //支付数据表中的字段id
		}
		if(is_numeric($pay_type)){
			$this->pay_type =$this->db->GetOne("SELECT * from `@#_pay` where `pay_id` = '$pay_type' and `pay_start` = '1'");
			$this->pay_type['pay_bank'] = 'DEFAULT';
		}
		
		if(in_array($pay_type, array('jf','zh','all'))){
			$this->pay_type = $pay_type;
		}

		$this->fukuan_type=$fukuan_type;
		if($fukuan_type=='go_record'){
			return $this->go_record();
		}
		if($fukuan_type=='addmoney_record'){
			return $this->addmoney_record($addmoney);
		}
		return false;
	}

	//买商品
	private function go_record(){

		if(is_array($this->scookie)){
			$Mcartlist = $this->scookie;
		}else{
		    if(isset($_COOKIE['Cartone'])){
                $Mcartlist=json_decode(stripslashes(_getcookie('Cartone')),true);
            }else{
                $Mcartlist=json_decode(stripslashes(_getcookie('Cartlist')),true);
            }
		}
		//file_put_contents("alipay.txt",json_encode($Cartlist),FILE_APPEND);

        $id_temp = $item_temp = array();
        $item_price_key = '';
        if(is_array($Mcartlist)){
            foreach ($Mcartlist as $k => $v){
                if(is_array($v)){ //后面支付的时候$Mcartlist有新增加不是数组的的元素
                    $id_temp[] = $v['goods_id'];
                    if(empty($v['flag'])){
                        $item_temp[] = "'".$k."'";
                    }
                }
            }
            $goods_id = implode(',',array_unique($id_temp));
            if(!empty($item_temp)){
                $item_price_key = implode(',',$item_temp);
            }
        }
        $shoplist = array ();
        if ($goods_id != NULL) {
            $shoparr = $this->db->GetList ( "SELECT * FROM `@#_shoplist` where `id` in ($goods_id)", array ("key" => "id" ) );
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
        $scookies_arr = array();
        $scookies_arr['MoneyCount'] = 0;
        $MoneyCount = 0;
        if (count ( $shoplist ) >= 1) {
            foreach ( $Mcartlist as $key => $val ) {
                //$key = intval ( $key );
                if (isset ( $shoplist [$key] )) {
                    $shoplist [$key] ['cart_gorenci'] = $val ['num'] ? $val ['num'] : 1;
                    $MoneyCount += $shoplist [$key] ['price']* $shoplist [$key] ['cart_gorenci'];
                    $shoplist [$key] ['cart_xiaoji'] = substr ( sprintf ( "%.3f", $shoplist [$key] ['price'] * $val ['num'] ), 0, - 1 );
                    $shoplist [$key] ['cart_shenyu'] = $shoplist [$key] ['inventory'];
                    //用户正常支付流程，当余额不足（该项目是二次开发，所以没有改变支付逻辑），先充值，再进行支付，所以实例化的对象会不一样
                    $shoplist [$key] ['addrid'] = $this->shopinfo['addrid']?$this->shopinfo['addrid']:$Mcartlist[$key]['addrid'];
                    $shoplist [$key] ['remark'] = $this->shopinfo['remark']?$this->shopinfo['remark']:$Mcartlist[$key]['remark'];
                    $scookies_arr[$key] = $val;
                    $scookies_arr['MoneyCount'] += $shoplist [$key] ['cart_xiaoji'];
                }
            }
        }else{
            $scookies_arr = array();
            return '购物车里商品已经卖完或已下架!';
        }
        //p($scookies_arr);exit;

		$this->MoneyCount=substr(sprintf("%.3f",$MoneyCount),0,-1);

		/**
		*	最多能抵扣多少钱
		**/
		if($this->fufen){
			if($this->fufen >= $this->members['score']){
				$this->fufen = $this->members['score'];
			}
			$fufen = System::load_app_config("user_fufen",'','member');
			if($fufen['fufen_yuan']){
				$this->fufen_to_money  = intval($this->fufen / $fufen['fufen_yuan']);
				if($this->fufen_to_money >= $this->MoneyCount){
					$this->fufen_to_money = $this->MoneyCount;
					$this->fufen = $this->fufen_to_money * $fufen['fufen_yuan'];
				}
			}else{
				$this->fufen_to_money = 0;
				$this->fufen = 0;
			}
		}else{
			$this->fufen_to_money = 0;
			$this->fufen = 0;
		}
		
		if(isset($Mcartlist['ordercode']) && !empty($Mcartlist['ordercode'])){
            $this->dingdancode = $Mcartlist['ordercode'];
            $scookies_arr['ordercode'] = $Mcartlist['ordercode'];
        }
		//file_put_contents("alipay.txt",json_encode($this->dingdancode),FILE_APPEND);
		//总支付价格
		$this->MoneyCount = $this->MoneyCount;
		$this->shoplist=$shoplist;
		$this->scookies_arr = $scookies_arr;
		return 'ok';
	}

	
	/* 充值 data 其他数据 */
	private function addmoney_record($money=null,$data=null){
		$uid=$this->members['uid'];
		$dingdancode = pay_get_dingdan_code('C');		//订单号
		$ordercode = $this->dingdancode;
		if(!is_array($this->pay_type)){
			return 'not_pay';
		}
		$pay_type = $this->pay_type['pay_name'];
		$time = time();
		if(!empty($data)){
			$scookies = $data;
		}else{
			$scookies = '0';
		}
		$score = $this->fufen;
		$query = $this->db->Query("INSERT INTO `@#_member_addmoney_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`score`,`scookies`,`ordercode`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未付款', '$time','$score','$scookies','$ordercode')");
		if($query){
			$this->db->Autocommit_commit();
		}else{
			$this->db->Autocommit_rollback();
			return false;
		}

		$pay_type = $this->pay_type;
        //p($pay_type);exit;
		$paydb = System::load_app_class($pay_type['pay_class'],'pay');
		$pay_type['pay_key'] = unserialize($pay_type['pay_key']);

		$config=array();
		$config['id'] = $pay_type['pay_key']['id']['val'];			//支付合作ID
		$config['key'] = $pay_type['pay_key']['key']['val'];		//支付KEY

		$config['shouname'] = _cfg('web_name');						//收款方
		$config['title'] = _cfg('web_name');						//付款项目
		$config['money'] = $money;									//付款金额$money
		$config['type']  = $pay_type['pay_type'];					//支付方式：	即时到帐1   中介担保2


		$config['ReturnUrl']  = G_WEB_PATH.'/index.php/pay/'.$pay_type['pay_class'].'_url/qiantai/';	//前台回调
		$config['NotifyUrl']  = G_WEB_PATH.'/index.php/pay/'.$pay_type['pay_class'].'_url/houtai/';		//后台回调


		$config['pay_bank'] = $this->pay_type['pay_bank'];

		$config['code'] = $dingdancode;
		$config['pay_type_data'] = $pay_type['pay_key'];

		$paydb->config($config);
		$paydb->send_pay();

		return true;
	}

	//生成订单
	public function set_dingdan($pay_type='',$dingdanzhui=''){
        $uid=$this->members['uid'];
        $uphoto = $this->members['img'];
        $username = get_user_name($this->members);
        $insert_html='';
        $this->dingdancode = $dingdancode= pay_get_dingdan_code($dingdanzhui);		//订单号
		$this->scookies_arr['ordercode'] = $this->dingdancode;
        //p($this->shoplist);exit;
        if(count($this->shoplist)>1){
                $dingdancode_tmp = 1;	//多个商品相同订单
        }else{
                $dingdancode_tmp = 0;	//单独商品订单
        }

        $ip = _get_ip_dizhi();
        //订单时间
        $time=sprintf("%.3f",microtime(true));
        $this->MoneyCount=0;
        //p($this->shoplist);exit;
        foreach($this->shoplist as $key=>$shop){
            if(empty($shop['flag'])){
                $sql = "select `inventory` from `@#_spec_goods_price` WHERE `key`='$key'";
                $res = $this->db->GetOne($sql);
                if(empty($res['inventory'])){
                    return false;
                }
            }else{
                $sql = "select `surplus` from `@#_shoplist` WHERE `id`='$key'";
                $res = $this->db->GetOne($sql);
                if(empty($res['surplus'])){
                    return false;
                }
            }
            $this->dingdan_query = true;
            $money=$shop['cart_xiaoji'];								//单条商品的总价格
            $this->MoneyCount += $money;										//总价格
            $status='未付款,未发货,未完成';
            $shop['goods_count'] = $shop['cart_gorenci'];
            $this->shoplist[$key] = $shop;
            $pro_id = $key;
            $pro_info = serialize($shop);
            $addrId = $shop['addrid'];
            $remark = $shop['remark'];
            if($shop['goods_count']){
                $insert_html.="('$dingdancode','$dingdancode_tmp','$uid','$username','$uphoto','$shop[goods_id]','$shop[title]','$pro_id','$pro_info','$shop[goods_count]','$money','$pay_type','$ip','$status','$time','$addrId','$remark'),";
            }
        }
        $sql="INSERT INTO `@#_member_go_record` (`code`,`code_tmp`,`uid`,`username`,`uphoto`,`shopid`,`shopname`,`pro_id`,`pro_info`,`gonumber`,`moneycount`,`pay_type`,`ip`,`status`,`time`,`address`,`remark`) VALUES ";
        $sql.=trim($insert_html,',');
        if(empty($insert_html)){
            return false;
        }
        //echo $sql;exit;
        //$this->db->Query("set global max_allowed_packet = 2*1024*1024*10");
        return $this->db->Query($sql);
	}

	
	/**
	*	开始支付
	**/
	public function go_pay($pay_checkbox){
        $this->db->Autocommit_start();
	    //var_dump($pay_checkbox);exit;
		if($this->members['money'] >= $this->MoneyCount){
		    //echo 'ok';exit;
			$uid=$this->members['uid'];
			$pay_1 = $this->pay_bag();
			return $pay_1;
		}
		if(!is_array($this->pay_type)){
			return 'not_pay';
		}
		if(is_array($this->scookies_arr)){
			$scookie = serialize($this->scookies_arr);
		}else{
			$scookie= '0';
		}
		if($pay_checkbox){
			$money = $this->MoneyCount - $this->members['money'];
			return $this->addmoney_record($money,$scookie);
		}else{
			//全额支付
			$this->MoneyCount;
			return $this->addmoney_record($this->MoneyCount,$scookie);
		}
		exit;
	}


	//账户里支付
	private function  pay_bag(){
		$time=time();
		$uid=$this->members['uid'];
		/*会员购买过账户剩余金额*/
		$Money = $this->members['money'] - $this->MoneyCount;
		$query_fufen = true;
		$pay_zhifu_name = '在线支付';

		//更新用户账户金额
		$query_2 = $this->db->Query("UPDATE `@#_member` SET `money`='$Money' WHERE (`uid`='$uid')");			//金额
		$query_3 = $info = $this->db->GetOne("SELECT * FROM  `@#_member` WHERE (`uid`='$uid') LIMIT 1");
		$query_4 = $this->db->Query("INSERT INTO `@#_member_account` (`uid`, `type`, `pay`, `content`, `money`, `time`) VALUES ('$uid', '-1', '$pay_zhifu_name', '购买了商品', '{$this->MoneyCount}', '$time')");
		$query_5 = true;
		$goods_count_num = 0;
		foreach($this->shoplist as $key=>$shop):
            //var_dump($shop);
            $shopinfo = $this->db->GetOne("select `inventory`,`buy_yet`,`surplus` from `@#_shoplist` where `id`='$shop[goods_id]'");
            if($shopinfo['buy_yet'] >= $shopinfo['inventory']){
                //判断商品基本信息中的商品数量
                $query_a = $this->db->Query("UPDATE `@#_shoplist` SET `buy_yet`=`inventory`,`surplus` = '0' where `id` = '$shop[goods_id]'");
                if(empty($shop['flag'])){
                    $query_b = $this->db->Query("UPDATE `@#_spec_goods_price` SET `inventory`=0, where `key` = '$key' AND `goods_id` = '$shop[goods_id]'");
                }
            }else{
                if($shop['inventory'] != 0){
                    $buy_yet = $shopinfo['buy_yet'] + $shop['cart_gorenci'];
                    $surplus = $shopinfo['surplus'] - $shop['cart_gorenci'];
                    $query_a = $this->db->Query("UPDATE `@#_shoplist` SET `buy_yet`='$buy_yet',`surplus` = '$surplus' where `id` = '$shop[goods_id]'");
                    if(empty($shop['flag'])){
                        $spec_inventory = $shop['inventory'] - $shop['cart_gorenci'];
                        $query_b =$this->db->Query("UPDATE `@#_spec_goods_price` SET `inventory`='$spec_inventory' where `key` = '$key' and `goods_id` = '$shop[goods_id]'");
                    }
                }else{
                    $query_a = $query_b = false;
                }
            }
            if($query_a || $query_b){
                $query_5 = true;
            }else{
                $query_5 = false;
            }
            $goods_count_num += $shop['goods_count'];
		endforeach;
        $info = array();
        $dingdancode=$this->dingdancode;
		$info['dingdancode'] = $dingdancode;
		$info['uid'] = $uid;
		file_put_contents("alipay.txt",json_encode($info),FILE_APPEND);

		$query_6 = $this->db->Query("UPDATE `@#_member_go_record` SET `status`='已付款,未发货,未完成' WHERE `code`='$dingdancode' and `uid` = '$uid'");
		$query_7 = $this->dingdan_query;
		$this->goods_count_num = $goods_count_num;
		if($query_2 && $query_3 && $query_4 && $query_6 && $query_7){
			if($info['money'] == $Money){
				$this->db->Autocommit_commit();
				return true;
			}else{
				$this->db->Autocommit_rollback();
				return false;
			}
		}else{
			$this->db->Autocommit_rollback();
			return false;
		}

	}


	public function pay_user_go_shop($uid=null,$gid=null,&$num=null){
		if(empty($uid) || empty($gid) || empty($num)){
			return false;
		}
		$uid = intval($uid);$gid = intval($gid);$num = intval($num);
		$this->db=System::load_sys_class('model');
		$this->db->Autocommit_start();
		$member = $this->db->GetOne("select * from `@#_member` where `uid` = '$uid' for update");
		$goodinfo = $this->db->GetOne("select * from `@#_shoplist` where `id` = '$gid' and `shenyurenshu` != '0' for update");
		if(!$goodinfo['shenyurenshu']){
			$this->db->Autocommit_rollback();
			return false;
		}
		if($goodinfo['shenyurenshu'] < $num){
			$num = $goodinfo['shenyurenshu'];
		}
		$if_money = $goodinfo['yunjiage'] * $num;
		$this->members = $member;
		$this->MoneyCount = $if_money;
		$goodinfo['goods_count_num'] = $num;
		$goodinfo['cart_gorenci'] = $num;

		$this->shoplist = array();
		$this->shoplist[0] = $goodinfo;

		if($member && $goodinfo && $member['money'] >= $if_money){

			$uid=$member['uid'];
			$pay_1 =  $this->pay_bag();
			if(!$pay_1){return $pay_1;}
			$dingdancode=$this->dingdancode;
			$pay_2 = pay_go_fund($this->goods_count_num);
			$pay_3 = pay_go_yongjin($uid,$dingdancode);
			return $pay_1;

		}else{
			$this->db->Autocommit_rollback();
			return false;
		}
	}

    /**
     * @param string $pay_type 支付方式：微信、支付宝
     * @param string $pre 订单号前缀
     * @param null $uid 用户账号id
     * @param array $param 金额参数
     * @return bool true订单生成成功，false订单生成失败
     */
    public function set_act_order($pay_type='',$pre='',$uid=null,$param=array()){
        $this->db=System::load_sys_class('model');
        $this->db->Autocommit_start();
        $this->members = $this->db->GetOne("SELECT * FROM `@#_member` where `uid` = '$uid' for update");
        $uid=$this->members['uid'];
        //$uphoto = $this->members['img'];
        if(!empty($this->members['headimg']) && $this->members['img']=='photo/member.jpg'){
            $uphoto = $this->members['headimg'];
        }else{
            $uphoto = G_UPLOAD_PATH.'/'.$this->members['img'];
        }
        if(empty($param)){ //判断支付金额方式
            return false;
        }
        //支付方式金额
        $payment = $param['payment'];  //实付金额
        $refund = $param['refund'];  //退款金额
        $discount = $param['discount']; //抵现金额
        $balance = $param['balance']; //扣掉的余额
        $integral = $param['integral']; //抵掉的积分

        $username = get_user_name($this->members);
        $this->dingdancode = $dingdancode= pay_get_dingdan_code($pre);		//生成活动订单号

        $this->scookie = $signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true); //获取报名信息
        $actId = $signInfo['actId'];
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$actId"); //查询活动信息
        /*$charge = $this->db->GetOne("select * from `@#_act_charge` WHERE `c_act_id`=$actId"); //查询费用信息*/
        if($activity['act_num_limit'] !=0 && $activity['act_num_signed']==$activity['act_num_limit']){ //报名人数已上限
            return false;
        }
        $user_name = $signInfo['userName'];
        $user_tel = $signInfo['userTel'];
        $user_id = $signInfo['userId'];
        //p($signInfo);exit;
        //数据插入报名表
        $sql = "insert into `@#_act_sign` (`s_uid`,`s_act_id`,`s_username`,`s_ID_card`,`s_mobile`,`s_status`) VALUES ($uid,$actId,'$user_name','$user_id','$user_tel','未支付')";
        //p($sql);exit;
        $row_1 = $this->db->Query($sql);
        if(!$row_1){
            $this->db->Autocommit_rollback();
            return false;
        }
        $s_id = $this->db->insert_id();

        $time = time(); //报名时间
        $this->MoneyCount = $payment; //支付实付金额
        $sql = "insert into `@#_act_order` (`o_code`,`o_uid`,`o_photo`,`o_sid`,`o_act_id`,`o_act_title`,`o_username`,`o_pay_type`,`o_money`,`o_payment`,`o_refund`,`o_discount`,`o_time`,`o_status`) VALUES ('$dingdancode',$uid,'$uphoto',$s_id,$actId,'{$activity['act_title']}','$username','$pay_type',{$activity['act_charge']},$payment,$refund,$discount,$time,'未支付')";
        //p($sql);exit;
        $row_2 = $this->db->Query($sql);
        $this->act_order_id = $this->db->insert_id();
        $this->scookie['sId'] = $s_id;
        $this->scookie['actOrderId'] = $this->act_order_id;
        $this->scookie['balance'] = $balance;
        $this->scookie['integral'] = $integral;
        if($row_2){
            return true;
        }else{
            //$this->db->Autocommit_rollback();
            return false;
        }
        //p($sql);exit;
    }
    /**
     * 活动支付
     */
	public function act_pay($data=null){
        //当前对象的订单号、支付金额和订单表的id
        $pay_class = $this->pay_type_bank; //支付表中的支付方式的名称：目前只支持手机支付宝和微信支付
        //查询支付表中的数据
        $pay_type = $this->pay_type = $this->db->GetOne("select * from `@#_pay` WHERE `pay_class`='$pay_class' AND `pay_start`=1");
        //p($pay_type);exit;

        $uid=$this->members['uid'];
        $dingdancode = pay_get_dingdan_code('B');		//订单号
        $ordercode = $this->dingdancode;
        $pay_type = $this->pay_type['pay_name'];
        $time = time();
        $money = $this->MoneyCount;
        if(!empty($data)){
            $scookies = $data;
        }else{
            $scookies = '0';
        }
        $query = $this->db->Query("INSERT INTO `@#_member_activity_record` (`uid`, `code`, `money`, `pay_type`, `status`,`time`,`scookies`,`ordercode`) VALUES ('$uid', '$dingdancode', '$money', '$pay_type','未支付', '$time','$scookies','$ordercode')");
        if($query){
            $this->db->Autocommit_commit();
        }else{
            $this->db->Autocommit_rollback();
            return false;
        }

        $pay_type = $this->pay_type;
        //p($pay_type);exit;
        $paydb = System::load_app_class($pay_type['pay_class'],'pay');
        $pay_type['pay_key'] = unserialize($pay_type['pay_key']);

        $config=array();
        $config['id'] = $pay_type['pay_key']['id']['val'];			//支付合作ID
        $config['key'] = $pay_type['pay_key']['key']['val'];		//支付KEY

        $config['shouname'] = _cfg('web_name');						//收款方
        $config['title'] = _cfg('web_name');						//付款项目
        $config['money'] = $money;									//付款金额$money
        $config['type']  = $pay_type['pay_type'];					//支付方式：	即时到帐1   中介担保2

        $config['ReturnUrl']  = G_WEB_PATH.'/index.php/pay/'.$pay_type['pay_class'].'_act_url/qiantai/';	//前台回调
        $config['NotifyUrl']  = G_WEB_PATH.'/index.php/pay/'.$pay_type['pay_class'].'_act_url/houtai/';		//后台回调

        $config['pay_bank'] = $this->pay_type['pay_bank'];

        $config['code'] = $dingdancode;
        $config['pay_type_data'] = $pay_type['pay_key'];
        //用来区分调用微信支付的接口是购买商品和报名支付
        $config['buy_type'] = 'activity';

        $paydb->config($config);
        $paydb->send_pay();

        return true;
    }

}
?>