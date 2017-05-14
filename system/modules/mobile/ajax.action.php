<?php

defined('G_IN_SYSTEM')or exit('No permission resources.');

System::load_app_class('base','member','no');

System::load_app_fun('my','go');

System::load_app_fun('user','go');

System::load_sys_fun('send');

System::load_sys_fun('user');

class ajax extends base {

    private $Mcartlist;

    private $Mcartlist_jf;



	public function __construct(){

		parent::__construct();

/* 		if(ROUTE_A!='userphotoup' and ROUTE_A!='singphotoup'){

			if(!$this->userinfo)_message("请登录",WEB_PATH."/mobile/user/login",3);

		}	 */

		$this->db = System::load_sys_class('model');





		//查询购物车的信息

		$Mcartlist=_getcookie("Cartlist");

		$this->Mcartlist=json_decode(stripslashes($Mcartlist),true);

	

		$Mcartlist_jf=_getcookie("Cartlist_jf");

		$this->Mcartlist_jf=json_decode(stripslashes($Mcartlist_jf),true);

	}

	public function init(){

	    if(ROUTE_A!='userphotoup' and ROUTE_A!='singphotoup'){

			if(!$this->userinfo)_message("请登录",WEB_PATH."/mobile/user/login",3);

		}



		$member=$this->userinfo;

		$title="我的会员中心";



		 $user['code']=1;

		 $user['username']=get_user_name($member['uid']);

		 $user['uid']=$member['uid'];

		 if(!empty($member)){

		   $user['code']=0;

		 }



		echo json_encode($user);





	}

	//幻灯

	public function slides(){

	  $sql="select * from `@#_wap` where 1";

	  $SlideList=$this->db->GetList($sql);

	  if(empty($SlideList)){

	    $slides['state']=1;

	  }else{

	   $slides['state']=0;

	    foreach($SlideList as $key=>$val){

		   $shopid = ereg_replace('[^0-9]','',$val['link']);

		  // $shopid=explode("/",$val['link']);

		   $slides['listItems'][$key]['alt']=$val['color'];

		   $slides['listItems'][$key]['url']=WEB_PATH."/mobile/mobile/item/".$shopid;

		   $slides['listItems'][$key]['src']=G_WEB_PATH."/statics/uploads/".$val['img'];

		   $slides['listItems'][$key]['width']='614PX';

		   $slides['listItems'][$key]['height']='110PX';



		}



	  }

	   echo json_encode($slides);

	}



   // 今日揭晓商品

    public function show_jrjxshop(){

		$pagetime=safe_replace($this->segment(4));





		$w_jinri_time = strtotime(date('Y-m-d'));

		$w_minri_time = strtotime(date('Y-m-d',strtotime("+1 day")));





		$jinri_shoplist = $this->db->GetList("select * from `@#_shoplist` where `xsjx_time` > '$w_jinri_time' and `xsjx_time` < '$w_minri_time' order by xsjx_time limit 0,3 ");



		if(!empty($jinri_shoplist)){

		   $m['errorCode']=0;

		}else{

		   $m['errorCode']=1;

		}

		//echo $pagetime;

		echo json_encode($m);



	}

	//最新揭晓商品

	public function show_newjxshop(){



		//最新揭晓

		$shopqishu=$this->db->GetList("select * from `@#_shoplist` where `q_end_time` !='' ORDER BY `q_end_time` DESC LIMIT 4");



		echo json_encode($shopqishu);



	}



	//即将揭晓商品

	public function show_msjxshop(){

	      //暂时没做







		//即将揭晓商品

	    $shoplist['listItems'][0]['codeID']=14;  //商品id

	    $shoplist['listItems'][0]['period']=3;  //商品期数

	    $shoplist['listItems'][0]['goodsSName']='苹果';  //商品名称

	    $shoplist['listItems'][0]['seconds']=10;  //商品名称



		$shoplist['errorCode']=0;

		//echo json_encode($shoplist);



	}



    //购物车数量

	public function cartnum(){

	  $Mcartlist=$this->Mcartlist;

	  if(is_array($Mcartlist)){

	  	  $cartnum['code']=0;

	      $cartnum['num']=count($Mcartlist);

	  }else{

	  	  $cartnum['code']=1;

	      $cartnum['num']=0;

	  }

      //var_dump($Mcartlist);

	  echo json_encode($cartnum);

	}


    /**
     * 添加购物车：1、没有商品规格；2、有商品规格
     */

    public function addShopCart(){
        $is_group=isset($_POST['is_group'])?intval($_POST['is_group']):0;
        $ShopId=isset($_POST['goods_id'])?intval($_POST['goods_id']):0;
        $ShopNum=isset($_POST['num'])?intval($_POST['num']):0;
        $goods_price = isset($_POST['price'])?safe_replace($_POST['price']):0;
        $goods_img = isset($_POST['goods_img'])?safe_replace($_POST['goods_img']):'';
        $mark = isset($_POST['mark'])?safe_replace($_POST['mark']):'';
        $cartKey = $flag = safe_replace($_POST['flag']);
        if(empty($flag)){//有商品规格
            $cartKey = $item_id  = isset($_POST['item_id'])?safe_replace($_POST['item_id']):0;
            $item_name = isset($_POST['item_name'])?safe_replace($_POST['item_name']):'';
            $item_id_arr = isset($_POST['item_id_arr'])?safe_replace($_POST['item_id_arr']):'';
        }

        $is_head = '';
        $groupOrderCode = '';
        //17.3.17 团购参数
        if(!empty($is_group)){
            $is_head=isset($_POST['is_head'])?intval($_POST['is_head']):1;
            $groupOrderCode= isset($_POST['groupOrderCode'])?safe_replace($_POST['groupOrderCode']):'';  //团购编号，参团使用
        }
        //echo '<pre>';
        //var_dump($_POST);exit;
        //echo $ShopId,$mark,$ShopNum,'/',$goods_price,$cartKey;exit;
        $shopis=0;          //0表示不存在  1表示存在
        _setcookie('Cartone','','');
        if(isset($mark) && $mark == 'cart'){
            $Mcartlist=$this->Mcartlist;
        }else{
            _setcookie('Cartone','',''); //清除cookie
            $Mcartlist = array();
        }
        //var_dump(is_array($Mcartlist));exit;
        if(empty($ShopId) || empty($ShopNum) || empty($goods_price) || empty($mark)){
            if(empty($flag)){
                if(empty($item_id)||empty($item_id_arr)||empty($item_name)){
                    $cart['code']=1;   //表示添加失败
                }
            }
            $cart['code']=1;   //表示添加失败
        }else{
            if(is_array($Mcartlist) && !empty($Mcartlist)){
                foreach($Mcartlist as $key=>$val){
                    if($key==$cartKey){
                        $Mcartlist[$key]['groupOrderCode']=$groupOrderCode;
                        $Mcartlist[$key]['is_head']=$is_head;
                        $Mcartlist[$key]['is_group']=$is_group;
                        $Mcartlist[$key]['goods_id']=$ShopId;
                        $Mcartlist[$key]['goods_price']=$goods_price;
                        $Mcartlist[$key]['goods_img']=$goods_img;
                        $Mcartlist[$key]['num']=$val['num']+$ShopNum;
                        $Mcartlist[$key]['flag']=$flag;
                        if(empty($flag)) {
                            $Mcartlist[$key]['item_ids'] = $item_id_arr;
                            $Mcartlist[$key]['item_name'] = $item_name;
                        }
                        $shopis=1;
                    }
                }
            }else{
                $Mcartlist[$cartKey]['groupOrderCode']=$groupOrderCode;
                $Mcartlist[$cartKey]['is_head']=$is_head;
                $Mcartlist[$cartKey]['is_group']=$is_group;
                $Mcartlist[$cartKey]['goods_id']=$ShopId;
                $Mcartlist[$cartKey]['goods_price']=$goods_price;
                $Mcartlist[$cartKey]['goods_img']=$goods_img;
                $Mcartlist[$cartKey]['num']=$ShopNum;
                $Mcartlist[$cartKey]['flag']=$flag;
                if(empty($flag)) {
                    $Mcartlist[$cartKey]['item_ids']=$item_id_arr;
                    $Mcartlist[$cartKey]['item_name']=$item_name;
                }
            }
            if($shopis==0){
                $Mcartlist[$cartKey]['groupOrderCode']=$groupOrderCode;
                $Mcartlist[$cartKey]['is_head']=$is_head;
                $Mcartlist[$cartKey]['is_group']=$is_group;
                $Mcartlist[$cartKey]['goods_id']=$ShopId;
                $Mcartlist[$cartKey]['goods_price']=$goods_price;
                $Mcartlist[$cartKey]['goods_img']=$goods_img;
                $Mcartlist[$cartKey]['num']=$ShopNum;
                $Mcartlist[$cartKey]['flag']=$flag;
                if(empty($flag)) {
                    $Mcartlist[$cartKey]['item_ids']=$item_id_arr;
                    $Mcartlist[$cartKey]['item_name']=$item_name;
                }
            }
            if(isset($mark) && $mark == 'cart'){
                _setcookie('Cartlist',json_encode($Mcartlist),'');
            }elseif(isset($mark) && $mark == 'shopping'){
                _setcookie('Cartone',json_encode($Mcartlist),'');
            }

            $cart['code']=0;   //表示添加成功
        }
        //p($Mcartlist);exit;
        $cart['num']=count($Mcartlist);    //表示现在购物车有多少条记录
        echo json_encode($cart);
    }
	
	/**
     * 修改购物车
     */
    public function editShopCart(){
        //echo '<pre>';
        //var_dump($_POST);exit;
        $ShopId=isset($_POST['goods_id'])?intval($_POST['goods_id']):0;
        $ShopNum=isset($_POST['num'])?intval($_POST['num']):0;
        $goods_price = isset($_POST['price'])?safe_replace($_POST['price']):0;
        $goods_img = isset($_POST['goods_img'])?safe_replace($_POST['goods_img']):'';
        $mark = isset($_POST['mark'])?safe_replace($_POST['mark']):'';
        $cartKey = $flag = safe_replace($_POST['flag']);
        $editCartKey = safe_replace($_POST['cartkey']); //修改购物车的键名
        //echo $editCartKey;exit;
        if(empty($flag)){//有商品规格
            $cartKey = $item_id  = isset($_POST['item_id'])?safe_replace($_POST['item_id']):0;
            $item_name = isset($_POST['item_name'])?safe_replace($_POST['item_name']):'';
            $item_id_arr = isset($_POST['item_id_arr'])?safe_replace($_POST['item_id_arr']):'';
        }
        //echo $ShopId,$mark,$ShopNum,'/',$goods_price,$cartKey;exit;
        $shopis=0;          //0表示不存在  1表示存在
        if(isset($mark) && $mark == 'cart'){
            $Mcartlist=$this->Mcartlist;
        }else{
            _setcookie('Cartone','',''); //清除cookie
            $Mcartlist = array();
        }
        //var_dump(is_array($Mcartlist));exit;
        if(empty($ShopId) || empty($ShopNum) || empty($goods_price) || empty($mark)){
            if(empty($flag)){
                if(empty($item_id)||empty($item_id_arr)||empty($item_name)){
                    $cart['code']=1;   //表示添加失败
                }
            }
            $cart['code']=1;   //表示添加失败
        }else{
            if(is_array($Mcartlist) && !empty($Mcartlist)){ //购物车里面有商品
                foreach($Mcartlist as $key=>$val){
                    if($cartKey==$editCartKey) { //判断是否是购物已有的商品
                        if ($key == $cartKey) {//再判断是购物车的哪件
                            $Mcartlist[$key]['goods_id'] = $ShopId;
                            $Mcartlist[$key]['goods_price'] = $goods_price;
                            $Mcartlist[$key]['goods_img'] = $goods_img;
                            $Mcartlist[$key]['num'] = $ShopNum;
                            $Mcartlist[$key]['flag'] = $flag;
                            if (empty($flag)) {
                                $Mcartlist[$key]['item_ids'] = $item_id_arr;
                                $Mcartlist[$key]['item_name'] = $item_name;
                            }
                            $shopis = 1;
                        }
                    }else{
                        unset($Mcartlist[$editCartKey]);//不是原来的，就删除
                        if($key==$cartKey){ //判断购物车中已存在的商品，数量就增加
                            $Mcartlist[$key]['goods_id']=$ShopId;
                            $Mcartlist[$key]['goods_price']=$goods_price;
                            $Mcartlist[$key]['goods_img']=$goods_img;
                            $Mcartlist[$key]['num']=$val['num']+$ShopNum;
                            $Mcartlist[$key]['flag']=$flag;
                            if(empty($flag)) {
                                $Mcartlist[$key]['item_ids'] = $item_id_arr;
                                $Mcartlist[$key]['item_name'] = $item_name;
                            }
                            $shopis=1;
                        }
                    }
                }
            }else{ //修改购物车里面没有商品
                $Mcartlist[$cartKey]['goods_id']=$ShopId;
                $Mcartlist[$cartKey]['goods_price']=$goods_price;
                $Mcartlist[$cartKey]['goods_img']=$goods_img;
                $Mcartlist[$cartKey]['num']=$ShopNum;
                $Mcartlist[$cartKey]['flag']=$flag;
                if(empty($flag)) {
                    $Mcartlist[$cartKey]['item_ids']=$item_id_arr;
                    $Mcartlist[$cartKey]['item_name']=$item_name;
                }
            }
            if($shopis==0){//如果购物车中都没有存在，就添加到购物车
                $Mcartlist[$cartKey]['goods_id']=$ShopId;
                $Mcartlist[$cartKey]['goods_price']=$goods_price;
                $Mcartlist[$cartKey]['goods_img']=$goods_img;
                $Mcartlist[$cartKey]['num']=$ShopNum;
                $Mcartlist[$cartKey]['flag']=$flag;
                if(empty($flag)) {
                    $Mcartlist[$cartKey]['item_ids']=$item_id_arr;
                    $Mcartlist[$cartKey]['item_name']=$item_name;
                }
            }
            if(isset($mark) && $mark == 'cart'){
                _setcookie('Cartlist',json_encode($Mcartlist),'');
            }elseif(isset($mark) && $mark == 'shopping'){
                _setcookie('Cartone',json_encode($Mcartlist),'');
            }

            $cart['code']=0;   //表示添加成功
        }
        //p($Mcartlist);exit;
        $cart['num']=count($Mcartlist);    //表示现在购物车有多少条记录
        echo json_encode($cart);
    }

	public function cartnum_jf(){
	  $Mcartlist=$this->Mcartlist_jf;
	  if(is_array($Mcartlist)){
	  	  $cartnum['code']=0;
	      $cartnum['num']=count($Mcartlist);
	  }else{
	  	  $cartnum['code']=1;
	      $cartnum['num']=0;
	  }
      //var_dump($Mcartlist);
	  echo json_encode($cartnum);
	}

	//添加购物车

	public function jf_addShopCart(){

		$ShopId=safe_replace($this->segment(4));

		$ShopNum=safe_replace($this->segment(5));



		$cartbs=safe_replace($this->segment(6));//标识从哪里加的购物车



		$shopis=0;          //0表示不存在  1表示存在

		$Mcartlist=$this->Mcartlist_jf;

		if($ShopId==0 || $ShopNum==0){



		$cart['code']=1;   //表示添加失败



		}else{

		  if(is_array($Mcartlist)){

			foreach($Mcartlist as $key=>$val){

			   if($key==$ShopId){

			      if(isset($cartbs) && $cartbs=='cart'){

		            $Mcartlist[$ShopId]['num']=$ShopNum;

				  }else{

				    $Mcartlist[$ShopId]['num']=$val['num']+$ShopNum;

				  }

				  $shopis=1;

			   }else{

				  $Mcartlist[$key]['num']=$val['num'];

			   }

			}



		    }else{

				$Mcartlist =array();

				$Mcartlist[$ShopId]['num']=$ShopNum;

		    }





		    if($shopis==0){

		    	$Mcartlist[$ShopId]['num']=$ShopNum;

		    }



			_setcookie('Cartlist_jf',json_encode($Mcartlist),'');

			$cart['code']=0;   //表示添加成功

		}



		$cart['num']=count($Mcartlist);    //表示现在购物车有多少条记录



		echo json_encode($cart);



	}



	/**
     * 删除购物车
     */
	public function delCartItem(){
        //$ShopId=safe_replace($this->segment(4));
        $cartKey = safe_replace($this->segment(4));
        $cartlist=$this->Mcartlist;
        if($cartKey==0){
            $cart['code']=1;   //删除失败
        }else{
            if(is_array($cartlist)){
                if(count($cartlist)==1){
                    foreach($cartlist as $key=>$val){
                        if($key==$cartKey){
                            $cart['code']=0;
                            _setcookie('Cartlist','','');
                        }else{
                            $cart['code']=1;
                        }
                    }
                }else{
                    foreach($cartlist as $key=>$val){
                        if($key==$cartKey){
                            $cart['code']=0;
                            _setcookie("Cartlist['".$key."']",'','');
                        }else{
                            $Mcartlist[$key]=$val;
                        }
                    }
                    _setcookie('Cartlist',json_encode($Mcartlist),'');
                }
            }else{
                $cart['code']=1;   //删除失败
            }
        }
        echo json_encode($cart);
	}

public function delCartItem_jf(){

	   $ShopId=safe_replace($this->segment(4));



	   $cartlist=$this->Mcartlist_jf;



		if($ShopId==0){



		  $cart['code']=1;   //删除失败



		}else{

			   if(is_array($cartlist)){

			      if(count($cartlist)==1){

				     foreach($cartlist as $key=>$val){

					   if($key==$ShopId){

					     $cart['code']=0;

						    _setcookie('Cartlist_jf','','');

						}else{

					     $cart['code']=1;

					   }

					 }



				  }else{

					   foreach($cartlist as $key=>$val){

							if($key==$ShopId){

							  $cart['code']=0;

							}else{

							  $Mcartlist[$key]['num']=$val['num'];

							}

						}



						   _setcookie('Cartlist_jf',json_encode($Mcartlist),'');



					}



				}else{

				   $cart['code']=1;   //删除失败

				}



		}

		echo json_encode($cart);

	}

	public function getCodeState(){

	  $itemid=safe_replace($this->segment(4));

	  $item=$mysql_model->GetOne("select * from `@#_shoplist` where `id`='".$itemid."' LIMIT 1");



	  $a['Code']=1;

	  if(!$item){

	     $a['Code']=0;

	  }



	 echo json_encode($a);

	}

	public function userlogin(){
	    $username=safe_replace($this->segment(4));
	    $password=md5(base64_decode(safe_replace($this->segment(5))));
		$logintype='';
		if(strpos($username,'@')==false){
			$logintype='mobile';//手机
		}else{
			$logintype='email';//邮箱
		}

        $sql = "select * from `@#_member` where `$logintype`='$username' and `password`='$password'";
		$member=$this->db->GetOne($sql);
        

		$mem = $this->db->GetOne("select * from `@#_member` where `$logintype`='$username'");
		if(!$mem){
			//帐号不存在错误
			$user['state']=1;
			$user['num']=-2;
			echo json_encode($user);die;
		}
		if($member[$logintype.'code'] != 1){
			$user['state']=2; //未验证
			echo json_encode($user);die;
		}
		if(!$member){
			//帐号或密码错误
			$user['state']=1;
			$user['num']=-1;
		}else{
		   //登录成功
			_setcookie("uid",_encrypt($member['uid']),60*60*24*7);
			_setcookie("ushell",_encrypt(md5($member['uid'].$member['password'].$member['mobile'].$member['email'])),60*60*24*7);
			$user['state']=0;
		}
		echo json_encode($user);

	}



	//登录成功后

	public function loginok(){



	  $user['Code']=0;

	  echo json_encode($user);

	}

	/***********************************注册*********************************/



	//检测用户是否已注册

	public function checkname(){

	    $config_email = System::load_sys_config("email");

		$config_mobile = System::load_sys_config("mobile");

		$name= $this->segment(4);

		$regtype=null;

		if(_checkmobile($name)){

			$regtype='mobile';

			$cfg_mobile_type  = 'cfg_mobile_'.$config_mobile['cfg_mobile_on'];

			$config_mobile = $config_mobile[$cfg_mobile_type];

			if(empty($config_mobile['mid']) && empty($config_email['mpass'])){



				 $user['state']=2;//_message("系统短息配置不正确!");

				 echo json_encode($user);

				 exit;

			}

		}

		$member=$this->db->GetOne("SELECT * FROM `@#_member` WHERE `mobile` = '$name' LIMIT 1");

		if(is_array($member)){

			if($member['mobilecode']==1 || $member['emailcode']==1){

			  $user['state']=1;//_message("该账号已被注册");

			}else{

			  $sql="DELETE from`@#_member` WHERE `mobile` = '$name'";

			  $this->db->Query($sql);

			  $user['state']=0;

			}

		}else{

		    $user['state']=0;//表示数据库里没有该帐号

		}



	    echo json_encode($user);

	}



	//将数据注册到数据库

	public function userMobile(){

		$name= isset($_GET['username'])? $_GET['username']: $this->segment(4);

		$pass= isset($_GET['password'])? md5($_GET['password']): md5(base64_decode($this->segment(5)));

		$time=time();

		$code=abs(intval(_encrypt(_getcookie("code"),'DECODE')));

		if($code>0){

			$decode = $code;

		}else{

			$decode = 0;

		}

		//邮箱验证 -1 代表未验证， 1 验证成功 都不等代表等待验证

		$sql="INSERT INTO `@#_member`(`mobile`,password,img,emailcode,mobilecode,yaoqing,time)VALUES('$name','$pass','photo/member.jpg','-1','-1','$decode','$time')";

		if(!$name || $this->db->Query($sql)){

			//header("location:".WEB_PATH."/mobile/user/".$regtype."check"."/"._encrypt($name));

			//exit;

			$userMobile['state']=0;

		}else{

			//_message("注册失败！");

			$userMobile['state']=1;

		}

	  echo json_encode($userMobile);

	}



	//验证输入的手机验证码

	public function mobileregsn(){

	    $mobile= $this->segment(4);

	    $checkcodes= $this->segment(5);



		$member=$this->db->GetOne("SELECT * FROM `@#_member` WHERE `mobile` = '$mobile' LIMIT 1");



			if(strlen($checkcodes)!=6){

			    //_message("验证码输入不正确!");

				$mobileregsn['state']=1;

				echo json_encode($mobileregsn);

				exit;

			}

			$usercode=explode("|",$member['mobilecode']);

			if($checkcodes!=$usercode[0]){

			   //_message("验证码输入不正确!");

				$mobileregsn['state']=1;

				echo json_encode($mobileregsn);

				exit;

			}





			$this->db->Query("UPDATE `@#_member` SET mobilecode='1' where `uid`='$member[uid]'");



			_setcookie("uid",_encrypt($member['uid']),60*60*24*7);

			_setcookie("ushell",_encrypt(md5($member['uid'].$member['password'].$member['mobile'].$member['email'])),60*60*24*7);



			 $mobileregsn['state']=0;

			 $mobileregsn['str']=1;



	        echo json_encode($mobileregsn);

	}



	//重新发送验证码

	public function sendmobile(){



	  		$name=$this->segment(4);

			$member=$this->db->GetOne("SELECT * FROM `@#_member` WHERE `mobile` = '$name' LIMIT 1");

			if(!$member){

			    //_message("参数不正确!");

				$sendmobile['state']=1;

				echo json_encode($sendmobile);

				exit;

		    }

			$checkcode=explode("|",$member['mobilecode']);

			$times=time()-$checkcode[1];

			if($times > 120){



				$sendok = send_mobile_reg_code($name,$member['uid']);

				if($sendok[0]!=1){

					//_message($sendok[1]);exit;

                   	$sendmobile['state']=1;

					echo json_encode($sendmobile);

					exit;

				}

				//成功

				    $sendmobile['state']=0;

					echo json_encode($sendmobile);

					exit;

			}else{

				    $sendmobile['state']=1;

					echo json_encode($sendmobile);

					exit;

			}



	}

	//最新揭晓

	public function getLotteryList(){

	   $FIdx=$this->segment(4);

	   $EIdx=10;//$this->segment(5);

	   $isCount=$this->segment(6);



	   $shopsum=$this->db->GetOne("SELECT count(*) AS total FROM `@#_shoplist` WHERE `q_uid` is not null AND `q_showtime` = 'N'");



	   //最新揭晓

		$shoplist['listItems']=$this->db->GetList("SELECT * FROM `@#_shoplist` WHERE `q_uid` is not null AND `q_showtime` = 'N' ORDER BY `q_end_time` DESC limit $FIdx,$EIdx");



		if(empty($shoplist['listItems'])){

		  $shoplist['code']=1;

		}else{

		 foreach($shoplist['listItems'] as $key=>$val){

		 //查询出购买次数

		   $recodeinfo=$this->db->GetOne("select `gonumber` from `@#_member_go_record` where `uid` ='$val[q_uid]'  and `shopid`='$val[id]' ");

		   //echo "select `gonumber` from `@#_member_go_record` where `uid` !='$val[q_uid]'  and `shopid`='$val[id]' ";

		   $shoplist['listItems'][$key]['q_user']=get_user_name($val['q_uid']);

		   $shoplist['listItems'][$key]['userphoto']=get_user_key($val['q_uid'],'img');

		   $shoplist['listItems'][$key]['q_end_time']=microt($val['q_end_time']);

		   $shoplist['listItems'][$key]['gonumber']=$recodeinfo['gonumber'];

		 }

		  $shoplist['code']=0;
		  $shoplist['count']=$shopsum['total'];

		}



		echo json_encode($shoplist);



	}



	//访问他人购买记录

	public function getUserBuyList(){

	   $type=$this->segment(4);

	   $uid=$this->segment(5);

	   $FIdx=$this->segment(6);

	   $EIdx=10;//$this->segment(7);

	   $isCount=$this->segment(8);



		 if($type==0){

          //参与云购的商品 全部...

		  $shoplist=$this->db->GetList("select *,sum(gonumber) as gonumber from `@#_member_go_record` a left join `@#_shoplist` b on a.shopid=b.id where a.uid='$uid' GROUP BY shopid ");



		  $shop['listItems']=$this->db->GetList("select *,sum(gonumber) as gonumber from `@#_member_go_record` a left join `@#_shoplist` b on a.shopid=b.id where a.uid='$uid' GROUP BY shopid order by a.time desc limit $FIdx,$EIdx " );

		 }elseif($type==1){

		   //获得奖品

		    $shoplist=$this->db->GetList("select * from  `@#_shoplist`  where q_uid='$uid' " );



		    $shop['listItems']=$this->db->GetList("select * from  `@#_shoplist`  where q_uid='$uid' order by q_end_time desc limit $FIdx,$EIdx" );

		 }elseif($type==2){

		   //晒单记录

		    $shoplist=$this->db->GetList("select * from `@#_shaidan` a left join `@#_shoplist` b on a.sd_shopid=b.id where a.sd_userid='$uid' " );



		    $shop['listItems']=$this->db->GetList("select * from `@#_shaidan` a left join `@#_shoplist` b on a.sd_shopid=b.id where a.sd_userid='$uid' order by a.sd_time desc limit $FIdx,$EIdx" );



		 }



		 if(empty($shop['listItems'])){

		   $shop['code']=4;



		 }else{

		   foreach($shop['listItems'] as $key=>$val){

		      if($val['q_end_time']!=''){

			    $shop['listItems'][$key]['codeState']=3;

			    $shop['listItems'][$key]['q_user']=get_user_name($val['q_uid']);

                $shop['listItems'][$key]['q_end_time']=microt($val['q_end_time']);



			  }

			  if(isset($val['sd_time'])){

			   $shop['listItems'][$key]['sd_time']=date('m月d日 H:i',$val['sd_time']);

			  }

		   }

		   $shop['code']=0;

		   $shop['count']=count($shoplist);

		 }

	   echo json_encode($shop);

	}



	 //查看计算结果

	 public function getCalResult(){

	     $itemid=$this->segment(4);

		 $curtime=time();



		 $item=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid' and `q_end_time` is not null LIMIT 1");



		if($item['q_content']){

		    $item['contcode']=0;

			$item['itemlist'] = unserialize($item['q_content']);



			foreach($item['itemlist'] as $key=>$val){

			  	$item['itemlist'][$key]['time']	=microt($val['time']);

				$h=date("H",$val['time']);

			    $i=date("i",$val['time']);

			    $s=date("s",$val['time']);

			    list($timesss,$msss) = explode(".",$val['time']);



				$item['itemlist'][$key]['timecode']=$h.$i.$s.$msss;

			}



		}else{

		    $item['contcode']=1;

		}



		if(!empty($item)){

		  $item['code']=0;



		}else{

		  $item['code']=1;

		}



    //echo "<pre>";

	//print_r($item);

	//print_r($record_time);

	   echo json_encode($item);





	 }





	 //付款

	public function UserPay(){





	}



	// 马上揭晓的商品

	public function GetStartRaffleAllList(){

		$maxSeconds = intval($this->segment(4));



		$result = array();

		$result['errorCode'] = 0;

		$result['maxSeconds'] = $maxSeconds;

		$result['listItems'] = array();



		$times = (int)System::load_sys_config('system','goods_end_time');

		$time = time();

		$list = $this->db->getlist("select qishu,xsjx_time,id,thumb,title,q_uid,q_user,q_end_time,money from `@#_shoplist` where `q_showtime` = 'Y' AND id > '$maxSeconds' order by `q_end_time` DESC");

		foreach($list as $item) {

			if ( $result['maxSeconds'] == $maxSeconds ) {

				$result['maxSeconds'] = $item['id'];

			}



			if($item['xsjx_time']){

				$item['q_end_time'] += $times;

			}



			$data = array();

			$data['id'] = $item['id'];

			$data['qishu'] = $item['qishu'];

			$data['title'] = $item['title'];

			$data['money'] = $item['money'];

			$data['thumb'] = $item['thumb'];

			$data['seconds'] = intval($item['q_end_time'] - $time);

			$result['listItems'][] = $data;

		}





		die(json_encode($result));

	}

	public function BarcodernoInfo(){
		$itemid = intval($this->segment(4));
		$res = $this->db->Query("UPDATE `@#_shoplist` SET `q_showtime`='N' where `id`= $itemid");
		$list = $this->db->GetOne("SELECT * FROM `@#_shoplist` WHERE `id`= $itemid");
		$num=$this->db->GetOne("SELECT `gonumber` FROM `@#_member_go_record` WHERE `uid` ='$list[q_uid]'  AND `shopid`='$list[id]'");
		$result = array();
		if($res>0){
			$result['code'] = 0;
			$result['codeType']=0;
			$result['buyCount']=$num['gonumber'];
			$result['thumb']=$list['thumb'];
			$result['codeRNO'] = $list['q_user_code'];
			$result['codeRTime'] = microt($list['q_end_time']);
			$result['user'] =unserialize($list['q_user']);
			die(json_encode($result));
		}
	}


	public function paywx(){

		// ini_set('display_errors', 1);

		// error_reporting(E_ALL);



		$tradeno = $this->segment(4);

		if(empty($tradeno)){

			_message("订单不存在!");

		}

		$pay=System::load_app_class('pay','pay');

		$pay->go_pay_wx($tradeno);

		exit;

	}

	//选择商品参数
	public function goodsSelect(){
		$ShopId=safe_replace($this->segment(4));
		$type=safe_replace($this->segment(5));
		$cartKey=safe_replace($this->segment(6)); //编辑购物车
		//var_dump($ShopId);exit;
		$shopinfo = $this->db->GetOne("select * from `@#_shoplist` where `id` = $ShopId");
        //p($shopinfo);exit;
        //获取商品规格
        $spec_goods_price = $this->db->GetList("select * from `@#_spec_goods_price` WHERE `goods_id`=$ShopId",array('key'=>'key'));   //商品规格项的价格数据
        //订单列表
        $group_list = $this->db->GetList("select a.*,b.`img`,b.`headimg` from `@#_member_go_record` a left join `@#_member` b on a.`uid` = b.`uid` WHERE a.`is_head`=1 AND a.`group_code` <> '' and a.`shopid` = $ShopId AND a.`status`='已付款,未发货,未完成'");//a.`status`='已付款,未发货,未完成'
        $group_num = $this->db->GetList("select count(*) as num,a.`group_code` from `@#_member_go_record` a left join `@#_member` b on a.`uid` = b.`uid` WHERE a.`group_code` <> '' and a.`shopid` = $ShopId AND a.`status`='已付款,未发货,未完成' GROUP BY a.`group_code`",array('key'=>'group_code'));
        //var_dump($group_list);exit;

        $goods_price = json_encode($spec_goods_price);

        if(!empty($spec_goods_price)){
            $item_key = $item_id = $spec_id = $target = array();
            foreach ($spec_goods_price as $v){
                $item_key[$v['key']] =  explode('_', $v['key']);
            }
            foreach ($item_key as $v){
                $item_id = array_unique(array_merge($item_id,$v)); //合并数组，去重（数组中重复的id）
            }
            $item_keys = $item_key;
            //p($item_id);exit;
            sort($item_id); //排序 ，有利于数据库的查询效率
            $item_id_set = implode(',',$item_id);
            $spec_item = $this->db->GetList("SELECT * FROM `@#_spec` AS a INNER JOIN `@#_spec_item` AS b ON a.id = b.spec_id WHERE b.id IN($item_id_set) ORDER BY b.id ASC ");
            $item_image = $this->db->GetList("select * from `@#_spec_image` WHERE `goods_id`=$ShopId AND `spec_image_id` IN ($item_id_set)",array('key'=>'spec_image_id')); //规格项的图片
            $spec_name = '';
            foreach($spec_item as $key => $val)
            {
                $filter_spec[$val['name']][] = array(
                    'item_id'=> $val['id'],
                    'item'=> $val['item'],
                    'src'=>$item_image[$val['id']]['src'],
                );
                $spec_id[$val['name']] = $val['spec_id'];
            }
            $spec_name = implode(' ',array_unique(array_keys($filter_spec))); //获取的数据格式：“颜色 尺寸”
        }else{
            $spec_name = '颜色分类';
        }
        //p($shopinfo);exit;
        //p($spec_goods_price);exit;
		$str='<div class="selectBar-wrap"><div class="selectBar"><a id="selectBar-close" href="javascript:;"></a><div class="goodsInfo"><div class="goodsInfo-wrap"><div class="goodsInfo-img">';
		$str.='<script src="'.G_TEMPLATES_JS.'/mobile/goodsselect.js" type="text/javascript"></script>';
		$str.='<input type="hidden" class="shopId" value="'.$shopinfo['id'].'"/>';
		$str.='<img src="'.G_UPLOAD_PATH.'/'.$shopinfo['thumb'].'" alt="goodsImg" />';
		$str.='</div>';
		$str.='<div class="goodsInfo-detail">';
		$str.='<p class="detail-price">￥<span>'.$shopinfo['money'].'</span></p>';
		$str.='<p class="detail-last">库存：<span>'.$shopinfo['surplus'].'</span>件</p>';
		$str.='<p class="detail-selected">请选择：<span>'.$spec_name.'</span></p>';
		$str.='</div></div></div>';
        $str.='<div class="goodsClass"><div class="goodsClass-wrap">';
        if(!empty($spec_goods_price)){
            foreach ($filter_spec as $key => $val){
                $str.='<h3>'.$key.'</h3><ul class="goodsClass-list" id="goodsSpec-'.$spec_id[$key].'">';
                foreach ($val as $k => $v){
                    foreach ($spec_goods_price as $ko => $vo){
                        //p($filter_spec);exit;
                        if(in_array($v['item_id'],$item_key[$ko]) && $vo['inventory']>0){
                            $key = array_search($v['item_id'],$item_key[$ko]);
                            if($key !== false){
                                unset($item_keys[$ko][$key]);
                                $target[$v['item_id']] .= implode(',',$item_keys[$ko]).',';
                                $item_keys = $item_key;
                            }
                        }
                    }
                    //p($target);exit;
                    $target[$v['item_id']] = rtrim($target[$v['item_id']],',');
                    if($target[$v['item_id']]){
                        $data = "data-target='".$target[$v['item_id']]."'";
                    }else{
                        $data = '';
                    }
                        $str.='<li '.$data.' title="'.$v['item'].'" data-src="'.$v['src'].'" data-item-id="'.$v['item_id'].'">'.$v['item'].'</li>';
                }
                $str.='</ul>';
            }
        }else{
            //没有添加商品规格
            $str.='<h3>'.$spec_name.'</h3><ul class="goodsClass-list" id="goodsSpec-'.$spec_id[$key].'">';
            $str.='<li title="'.$shopinfo['title'].'" data-src="'.$shopinfo['thumb'].'" data-id="'.$shopinfo['id'].'">'._strcut($shopinfo['title'],25).'</li>';
            $str.='</ul>';
        }
        //p($target);exit;
		$str.='</div></div>';
		$str.='<div class="goodsNums"><div class="goodsNums-wrap"><h3>购买数量</h3><div class="numSelBar">';
		$str.='<a href="javascript:;" id="Less">-</a><span id="nums">1</span><a href="javascript:;" id="More">+</a>';
		$str.='</div></div></div>';
        
        //2017.2.22
        $str.='<div class="groupPartner">
            <div class="groupPartner-header">
                <p>以下小伙伴正在发起团购，您可以直接参与</p>
            </div>
            <div class="groupPartner-body">';
        //循环输出团购订单

        if($group_list){
            foreach ($group_list as $value) {//需要sql语句过滤已过期订单
                $surples_num = $group_num[$value['group_code']]['num']?$group_num[$value['group_code']]['num'] : 1;

                $str.='<div class="groupPartner-container '.$value['id'].'" groupcode="'.$value['id'].'">
                    <div class="container-left">
                        <div class="partnerLogo"><img src="'.G_UPLOAD_PATH.'/'.$value['img'].'"></div>
                    </div>
                    <div class="container-center">
                        <div class="partnerName">'.$value['username'].'</div>
                        <div class="number-time">
                            <p class="number">还差<span>'.$surples_num.'</span>人成团</p>

                            <p>剩余<span class="groupHou">00</span>:<span class="groupMin">00</span>:<span class="groupSec">00</span>结束</p>
                        </div>
                    </div>
                    <div class="container-right">
                        <div class="join">
                            <a href="javascript:;" codeid="'.$ShopId.'" data-head="0" data-group-code="'.$value['group_code'].'">去参团<span></span></a>
                            <!--<input class="groupOrderCode" value="'.$value['code'].'" hidden>获取团购订单编号2017.3.12-->
                        </div>
                    </div>
                    <script>getNowTime('.$shopinfo['group_time'].',"'.$value['id'].'",'.$value['time'].');//调用倒计时函数</script>
                </div>';
            }
        }
        $str.='</div>
        </div> ';

        $str .= <<<SCRIPT
        <script type='text/javascript'>
            
            function sortNumber(a,b){
                return a - b;
            }
            function get_goods_price(){
                var price = {$shopinfo['money']};
                var inventory = {$shopinfo['inventory']};
                var spec_goods_price = {$goods_price};  
                var item_arr = new Array();
                var item_name = new Array();
                $('.classSel').each(function(){
                    item_arr.push($(this).data('item-id'));
                    item_name.push($(this).attr('title'));
                });
                var spec_key = item_arr.sort(sortNumber).join('_');  //排序后组合成 key
                var choose_name = '"'+item_name.join(' ')+'"';
                //alert(spec_goods_price)
                if(spec_goods_price.hasOwnProperty(spec_key)){
                    goods_price = spec_goods_price[spec_key]['price']; // 找到对应规格的价格
                    store_count = spec_goods_price[spec_key]['inventory']; // 找到对应规格的库存
                    var goods_num = parseInt($('#nums').text());
                    if(goods_num > store_count)
                    {
                       goods_num = store_count;	   
                       $('#nums').text(goods_num);
                    }
                    $('.detail-price span').text(goods_price);
                    $('.detail-last span').text(store_count);
                    $('.detail-selected').html('已选:<span>'+choose_name+'<span>');
                }
                
            }
            function get_goods_spec(obj){
                //alert(obj.data('target'));
                if(!obj.data('target')){
                    //alert(1);
                    return;
                }
                var targetArr = obj.data('target').toString().split(',');
                //console.log(targetArr)
                var spec_ul = obj.parent().siblings('ul');
                spec_ul.each(function(){
                    var item_li = $(this).children();
                    item_li.each(function(){
                        var item_id = $(this).data('item-id');
                        $(this).removeClass('sellOut');
                        if($.inArray(item_id.toString(),targetArr)==-1){
                            $(this).addClass('sellOut');
                        }
                });
                });
                //console.log(targetArr);
            }

        </script>
SCRIPT;

		//$str = include $this->tpl(ROUTE_M,'goods_select');
		$str.='<div class="theBtns">';
        $str.='<a href="javascript:;" class="online"><span class="online-bg"></span><b>客服</b></a>
                    <a href="javascript:;" class="tel"><span class="tel-bg"></span><b>电话</b></a>';
		if(isset($cartKey) && !empty($cartKey)){ //修改购物车时，标识是否是原来的商品规格
            
            $str.='<input type="hidden" name="cartkey" class="cartkey" value="'.$cartKey.'"/>';
        }
		if($type==1){
		// $str.='<a href="javascript:;" id="addBtn" class="addto">加入购物车</a><a href="javascript:;" id="parchaseBtn" class="parchase">立即购买</a>';
            $str.='<a href="javascript:;" id="parchaseBtn" class="parchase">单独购买</a><a href="javascript:;" id="addGroup" class="addGroup" data-head="1">2人拼团</a>';
		}
		else if ($type==2) {
			$str.='<a href="javascript:;" id="addBtn" class="submitBtn">确定</a>';
		}
		else if($type==3){
			$str.='<a href="javascript:;" id="parchaseBtn" class="submitBtn">确定</a>';
		}
		else if($type==4){
            $str.='<a href="javascript:;" id="editBtn" class="submitBtn">确定</a>';
        }

		$str.='</div></div></div>';

		echo json_encode($str);
	}

/**
     * 判断用户是否登录和用户是否填写地址,填写并获取地址
     */

	   // public function getTip(){
    //     $userinfo = $this->userinfo;
    //     if(!empty($userinfo)){
    //         $where = array(
    //             'default' => "`uid` = {$userinfo['uid']} AND `default` = 'Y'",
    //             'common' => "`uid` = {$userinfo['uid']}",
    //         );
    //         foreach ($where as $k => $v){
    //             $sql = "select * from `@#_member_dizhi` WHERE ".$v;
    //             $address[$k] = $this->db->GetOne($sql);
    //         }
    //         //P($address);exit;
    //         if(isset($address['default']) && !empty($address['default'])) {
    //             $addrInfo['address'] = $address['default'];
    //             $addrInfo['flag'] = 1;
    //             $addrInfo['default'] = 'Y';
    //         }elseif(isset($address['common']) && !empty($address['common'])){
    //             $addrInfo['default'] = 'N';
    //             $addrInfo['tip'] = '请设置默认地址';
    //             $addrInfo['flag'] = 2;
    //         }
    //     }else{
    //         $addrInfo['flag'] = 0;
    //         $addrInfo['default'] = 'N';
    //     }
    //     //echo '<pre>';
    //     //var_dump($addrInfo);exit;
    //     echo json_encode($addrInfo);
    // }


    public function getTip(){
        $userinfo = $this->userinfo;
        if(!empty($userinfo)){
            $where = array(
                'default' => "`uid` = {$userinfo['uid']} AND `default` = 'Y'",
                'common' => "`uid` = {$userinfo['uid']} AND `default` = 'N' order by `time` desc",
            );
            foreach ($where as $k => $v){
                $sql = "select * from `@#_member_dizhi` WHERE ".$v;
                $address[$k] = $this->db->GetOne($sql);
            }
            //P($address);exit;
			if(!empty($address['default'])){
				$addrInfo['address'] = $address['default'];
				$addrInfo['flag'] = 1;
			}elseif(!empty($address['common'])){
				$addrInfo['address'] = $address['common'];
                $addrInfo['flag'] = 1;
			}else{
				$addrInfo['flag'] = 0;
			}
        }else{
            $addrInfo['flag'] = 0;
        }
        //echo '<pre>';
        //var_dump($addrInfo);exit;
        echo json_encode($addrInfo);
    }

    /**
     * 判断报名是付费的还是免费的
     */
    public function actIsFree(){
        //p($_POST);exit;
        _setcookie("act_id",null); //清除支付返回的活动id
        _setcookie("actInfo",null); //清除支付返回的活动信息
        $userName = isset($_POST['userName'])?safe_replace($_POST['userName']):'';
        $userTel = isset($_POST['userTel'])?safe_replace($_POST['userTel']):'';
        $userId = isset($_POST['userId'])?safe_replace($_POST['userId']):'';
        $actId = isset($_POST['actId'])?intval($_POST['actId']):0;
        $shareUid = isset($_POST['shareUid'])?intval($_POST['shareUid']):0; //分享的用户id
        if(empty($actId)){
            $response['status'] = 1;
            $response['msg'] = '参数不正确';
            exit(json_encode($response));
        }
        $member=$this->userinfo;
        if ($member) {
            $uid = $member['uid'];
            $res = $this->db->GetOne("select * from `@#_act_order` WHERE `o_uid`=$uid AND `o_act_id`=$actId AND `o_status`='已支付'");
            if($res){
                $response['status'] = 1;
                $response['msg'] = '已报名，请不要重复报名';
                echo json_encode($response);
                exit;
            }
            //分享消费返积分
            if($shareUid == $uid){
                $shareUid = 0;  //排除自己
            }
        }
        //判断活动是否免费
        $charge = $this->db->GetOne("select `act_charge`,`act_start_time` from `@#_activity` WHERE `act_id` = $actId");
        if(!$charge){
            $response['status'] = 1;
            $response['msg'] = '数据有误';
            exit(json_encode($response));
        }else{
            //判断活动截止报名时间
            $time = time();
            $stopTime = strtotime('-1 day 17:00:00',$charge['act_start_time']);
            if($time >= $stopTime && $time < $charge['act_start_time']){
                $response['status'] = 1;
                $response['msg'] = '活动已截止报名';
                exit(json_encode($response));
            }
            if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $userTel)){
                $response['status'] = 1;
                $response['msg'] = '请输入正确的手机号';
                echo json_encode($response);
                exit;
            }
            if(!preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/",$userId)){
                $response['status'] = 1;
                $response['msg'] = '请输入正确的身份证号';
                echo json_encode($response);
                exit;
            }
            if(empty($userName) || empty($actId)){
                $response['status'] = 1;
                $response['msg'] = '姓名不能为空';
                echo json_encode($response);
                exit;
            }
            $signInfo['actId'] = $actId;
            $signInfo['userName'] = $userName;
            $signInfo['userTel'] = $userTel;
            $signInfo['userId'] = $userId;
            $signInfo['shareUid'] = $shareUid;
            _setcookie('SignInfo',json_encode($signInfo),3600);
            if($charge['act_charge']=='0.00'){ //免费
                $response['status'] = 0;
                $response['msg'] = 'ok';
                echo json_encode($response);
                exit;
            }else{
                $response['status'] = 2;
                $response['msg'] = '活动需付费';
                exit(json_encode($response));
            }
        }
    }
    /**
     * 把报名信息存入cookie
     */
    public function addSignToCookie(){
        //var_dump($_POST);exit;
        $userName = isset($_POST['userName'])?safe_replace($_POST['userName']):'';
        $userTel = isset($_POST['userTel'])?safe_replace($_POST['userTel']):'';
        $userId = isset($_POST['userId'])?safe_replace($_POST['userId']):'';
        $actId = isset($_POST['actId'])?intval($_POST['actId']):0;
        $shareUid = isset($_POST['shareUid'])?intval($_POST['shareUid']):0; //分享的用户id
        $member=$this->userinfo;
        if ($member) {
            $uid = $member['uid'];
            $res = $this->db->GetOne("select * from `@#_act_order` WHERE `o_uid`=$uid AND `o_act_id`=$actId AND `o_status`='已支付'");
            if($res){
                $response['status'] = 1;
                $response['msg'] = '该活动已报名，请不要重复报名';
                echo json_encode($response);
                exit;
            }
            //分享消费返积分
            if($shareUid == $uid){
                $shareUid = 0;  //排除自己
            }
        }

        if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $userTel)){
            $response['status'] = 1;
            $response['msg'] = '请输入正确的手机号';
            echo json_encode($response);
            exit;
        }
        if(!preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/",$userId)){
            $response['status'] = 1;
            $response['msg'] = '请输入正确的身份证号';
            echo json_encode($response);
            exit;
        }
        if(empty($userName) || empty($actId)){
            $response['status'] = 1;
            $response['msg'] = '姓名不能为空';
            echo json_encode($response);
            exit;
        }
        //根据
        $signInfo['actId'] = $actId;
        $signInfo['userName'] = $userName;
        $signInfo['userTel'] = $userTel;
        $signInfo['userId'] = $userId;
        $signInfo['shareUid'] = $shareUid;
        //var_dump($signInfo);exit;
        _setcookie('SignInfo',json_encode($signInfo),3600);
        /*$cookie = json_decode(stripcslashes()_getcookie('SignInfo'),true);
        var_dump($cookie);
        var_dump($_COOKIE['SignInfo']);*/
        $response['status'] = 0;
        $response['msg'] = 'ok';
        echo json_encode($response);
        exit;
    }

    /**
     * 更新cookie
     */
    public function updateSignCookie(){
        //获取数据
        $actId = isset($_POST['act_id'])?intval($_POST['act_id']):0;
        $balance = isset($_POST['balance'])?safe_replace($_POST['balance']):'none';
        $integral = isset($_POST['integral'])?safe_replace($_POST['integral']):'none';
        //判断是否是同个活动
        $signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true);
        if($actId != $signInfo['actId']){
            $response['status'] = 1;
            $response['msg'] = '活动有误';
            echo json_encode($response);
            exit;
        }
        _setcookie('SignInfo','',''); //清除cookie
        $signInfo['balance'] = $balance;
        $signInfo['integral'] = $integral;
        _setcookie('SignInfo',json_encode($signInfo),3600);
        $response['status'] = 0;
        $response['msg'] = 'ok';
        echo json_encode($response);
        exit;
    }

}



?>