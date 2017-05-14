<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_class('base','member','no');
System::load_app_fun('my');
System::load_app_fun('user');
System::load_sys_fun('user');

class mobile extends base {

	public function __construct() {
		parent::__construct();
		$this->db=System::load_sys_class('model');

        _setcookie('homePage',json_encode(0),''); //识别是活动首页还是装备首页

	}

	public function  sql_demo(){
			$sql_text = file_get_contents("up.sql");
			$sql_text = explode(";",$sql_text);


			$ok = $this->db->GetOne("SHOW TABLES LIKE '@#_pay'");
			if(empty($ok)){
				echo "kong";
			}else{
				echo "nonull";
			}
	}

	//首页
	public function init(){
		//if(!isset($_COOKIE['wel'])){
			//header ( "location: " . WEB_PATH . "/mobile/mobile/welcome" );
		//}
		$webname=$this->_cfg['web_name'];
		//最新商品
		$new_shop=$this->db->GetOne("select * from `@#_shoplist` where `pos` = '1' and `q_end_time` is null ORDER BY `id` DESC LIMIT 1");
		//$new_shop=$this->db->GetList("select * from `@#_shoplist` where `pos` = '1' and `q_uid` is null ORDER BY `id` DESC LIMIT 8");
		$new_shop=$this->db->GetList("select * from `@#_shoplist` where `pos` = '1' and `q_uid` is null and `period1_date`=''  ORDER BY `id` DESC LIMIT 8");
		//即将揭晓
		$shoplist=$this->db->GetList("select * from `@#_shoplist` where `q_end_time` is null ORDER BY `shenyurenshu` ASC LIMIT 8");
		//人气商品
		//$shoplistrenqi=$this->db->GetList("select * from `@#_shoplist` where `renqi`='1' and `q_end_time` is null ORDER BY id DESC LIMIT 10");
		$shoplistrenqi=$this->db->GetList("select * from `@#_shoplist` where `renqi`='1' and `q_uid` is null and `period1_date`=''  ORDER BY id DESC LIMIT 8");
		//获取广告
		$shop_ad=$this->db->GetList("select * from `@#_wap` where `where_is`=0");
		$max_renqi_qishu = 1;
		$max_renqi_qishu_id = 1;

		if(!empty($shoplistrenqi)){
			foreach ($shoplistrenqi as $renqikey =>$renqiinfo){
				if($renqiinfo['qishu'] >= $max_renqi_qishu){
					$max_renqi_qishu = $renqiinfo['qishu'];
					$max_renqi_qishu_id = $renqikey;
				}
			}
			$shoplistrenqi[$max_renqi_qishu_id]['t_max_qishu'] = 1;
		}
		$this_time = time();
		if(count($shoplistrenqi) > 1){
					if($shoplistrenqi[0]['time'] > $this_time - 86400*3)
					$shoplistrenqi[0]['t_new_goods'] = 1;
		}

		//触发限时揭晓
		$yanshi=$this->db->GetList("SELECT * from `@#_shoplist` WHERE `xsjx_time` > 0 AND `xsjx_time` < $this_time AND q_user = ''");
		if(!empty($yanshi)){
			foreach ($yanshi as $k => $v) {
				$url = WEB_PATH.'/mobile/autolottery/autolottery_ret_install/'.$v['id'];
				getCurl($url);
			}
		}

		$w_jinri_time = strtotime(date('Y-m-d'));
		$w_minri_time = strtotime(date('Y-m-d',strtotime("+1 day")));


		//最新揭晓
		$shopqishu=$this->db->GetList("select * from `@#_shoplist` where `q_end_time` !='' ORDER BY `q_end_time` DESC LIMIT 4");


		$jinri_shoplist = $this->db->GetList("select * from `@#_shoplist` where `xsjx_time` > '$w_jinri_time' and `xsjx_time` < '$w_minri_time' order by xsjx_time limit 0,3 ");

		//总云购次数
		$user_shop_number = array();
		$uid='';
		$shopid='';
		if(!empty($jinri_shoplist)){

			foreach($jinri_shoplist as $key=>$val){
			   $uid=$val['q_uid'];
			   $qishu=$val['qishu'];
			   $shopid=$val['id'];
			 if($val['xsjx_time'] < time()){

			   $user_shop_list = $this->db->GetList("select * from `@#_member_go_record` where `uid`= '$uid' and `shopid` = '$shopid' and `shopqishu` = '$qishu'");
			   $user_shop_number[$uid][$shopid]=0;
				foreach($user_shop_list as $user_shop_n){
					$user_shop_number[$uid][$shopid] += $user_shop_n['gonumber'];


				}
			 }
			}
		}
		
		//获取欢迎页传过来的参数
        $id = safe_replace(intval($this->segment(4)));
        $res = $this->db->GetOne("select * from `@#_price_interval` WHERE `id`='$id'");
        $res = json_encode($res);
        $interval = $this->db->GetList("select * from `@#_price_interval` WHERE 1",array('key'=>'id'));
		//查询公告和活动信息
        $declare = $this->db->GetOne("select * from `@#_declare_activity` WHERE `flag`='d'");
        $activity = $this->db->GetList("select * from `@#_declare_activity` WHERE `flag`='a'");
		//分享部分代码
		require_once("system/modules/mobile/jssdk.php");
		$wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");
		$jssdk = new JSSDK($wechat['appid'],$wechat['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		
		$count=count($jinri_shoplist);
         		$key="首页";
		include templates("mobile/index","index");
	}
//直购专区
		public function zhigou(){

			$zhigou=$this->db->GetList("select * from `@#_jf_shoplist` ORDER BY id DESC LIMIT 100000");		
			include templates("mobile/index","zhigou");
		
			}
	//商品列表
	public function glist(){
        $webname=$this->_cfg['web_name'];
		$title="商品列表_"._cfg("web_name");
		$key="所有商品";
		include templates("mobile/index","glist");
	}
		//shiyuan
	public function gslist(){
        $webname=$this->_cfg['web_name'];
		$title="商品列表_"._cfg("web_name");
		$key="所有商品";
		include templates("mobile/index","gslist");
	}
		//baiyuan
	public function gblist(){
        $webname=$this->_cfg['web_name'];
		$title="商品列表_"._cfg("web_name");
		$key="所有商品";
		include templates("mobile/index","gblist");
	}
		//商品列表
	public function jflist(){
        $webname=$this->_cfg['web_name'];
		$title="积分购物_"._cfg("web_name");
		$key="积分购物";
		include templates("mobile/index","jflist");
	}
	public function gnamelistajax(){
		 $webname=$this->_cfg['web_name'];
	//	$title =htmlspecialchars($this->segment(4));
	$search =$this->segment_array();

		array_shift($search);

		array_shift($search);

		array_shift($search);

		$search = implode('/',$search);

	

		if(!$search)_message("输入搜索关键字");

		$search = urldecode($search);

		$search = safe_replace($search);	

		if(!_is_utf8($search)){

			$search =  iconv("GBK", "UTF-8", $search); 

		}

		$mysql_model=System::load_sys_class('model');	

	

		$search = str_ireplace("union",'',$search);

		$search = str_ireplace("select",'',$search);

		$search = str_ireplace("delete",'',$search);

		$search = str_ireplace("update",'',$search);

		$search = str_ireplace("/**/",'',$search);

		$title=$search.' - '._cfg('web_name');


		$shoplist=$mysql_model->GetList("select title,thumb,id,sid,zongrenshu,canyurenshu,shenyurenshu,money from `@#_shoplist` WHERE q_uid is null and `title` LIKE '%".$search."%'");

		$count=count($shoplist);

		

		echo json_encode($shoplist);
	}
	//ajax获取商品列表信息
	public function glistajax(){
	    $webname=$this->_cfg['web_name'];
		$cate_band =htmlspecialchars($this->segment(4));
		$select =htmlspecialchars($this->segment(5));
		$p =htmlspecialchars($this->segment(6)) ? $this->segment(6) :1;
        //p($cate_band);exit;

		if(!$select){
			$select = '10';
		}
		if($cate_band){
            $fen1 = intval($cate_band); //不是数字的话是为0
		    //判断是否是欢迎页的参数
            if($cate_band == 'interval'){
                //查询价格区间
                $id = intval($select);
                $interval = $this->db->GetOne("select * from `@#_price_interval` WHERE `id`='$id'");
                $cate_band == 'interval';
            }else{
                $cate_band = 'list';
            }
		}
		if(empty($fen1)){
			$brand=$this->db->GetList("select * from `@#_brand` where 1 order by `order` DESC");
			$daohang = '所有分类';
		}else{
			$brand=$this->db->GetList("select * from `@#_brand` where `cateid`='$fen1' order by `order` DESC");
			$daohang=$this->db->GetOne("select * from `@#_category` where `cateid` = '$fen1' order by `order` DESC");
			$daohang = $daohang['name'];
		}

		$category=$this->db->GetList("select * from `@#_category` where `model` = '1'");

		//分页

		$end=10;
		$star=($p-1)*$end;

		$select_w = '';
		if($select == 10){
			$select_w = 'order by `surplus` ASC';
		}
		if($select == 20){
			$select_w = "and `renqi` = '1'";
		}
		if($select == 30){
			$select_w = 'order by `surplus` ASC';
		}
		if($select == 40){
			$select_w = 'order by `time` DESC';
		}
		if($select == 50){
			$select_w = 'order by `money` DESC';
		}
		if($select == 60){
			$select_w = 'order by `money` ASC';
		}

		if($fen1){
			$count=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 and `cateid`='$fen1' $select_w");
		}else{
		    if ($cate_band == 'interval'){
		        if($interval['close_interval'] == 0){
                    $count=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 AND `money` > '".$interval['open_interval']."'order by `money` asc");
                }else{
                    $count=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 AND `money` > '".$interval['open_interval']."' AND `money`<='".$interval['close_interval']."'order by `money` asc");
                }
            }else{
                $count=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 $select_w");
            }
		}
		if($fen1){
			$shoplist=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 and `cateid`='$fen1' $select_w limit $star,$end");
		}else{
		    if($cate_band == 'interval'){
		        if($interval['close_interval'] == 0){
                    $shoplist=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 AND `money` > '".$interval['open_interval']."' order by `money` asc limit $star,$end");
                }else{
                    $shoplist=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 AND `money` > '".$interval['open_interval']."' AND `money`<='".$interval['close_interval']."' order by `money` asc limit $star,$end");
                }
            }else{
                $shoplist=$this->db->GetList("select * from `@#_shoplist` where `surplus` <> 0 $select_w limit $star,$end");
            }
		}
		$max_renqi_qishu = 1;
		$max_renqi_qishu_id = 1;

		if(!empty($shoplistrenqi)){
			foreach ($shoplistrenqi as $renqikey =>$renqiinfo){
				if($renqiinfo['qishu'] >= $max_renqi_qishu){
					$max_renqi_qishu = $renqiinfo['qishu'];
					$max_renqi_qishu_id = $renqikey;
				}
			}
			$shoplistrenqi[$max_renqi_qishu_id]['t_max_qishu'] = 1;
		}


		$this_time = time();
		if(count($shoplist) > 1){
					if($shoplist[0]['time'] > $this_time - 86400*3)
					$shoplist[0]['t_new_goods'] = 1;
		}
		$pagex=ceil(count($count)/$end);
		if($p<=$pagex){ //要加载的页数和总页数相比较
			$shoplist[0]['page']=$p+1;
		}
		if($pagex>0){
			$shoplist[0]['sum']=$pagex;
		}else if($pagex==0){
			$shoplist[0]['sum']=$pagex;
		}

		echo json_encode($shoplist);
	}
	
//ajax获取商品列表信息
	public function gslistajax(){
	    $webname=$this->_cfg['web_name'];
		$cate_band =htmlspecialchars($this->segment(4));
		$select =htmlspecialchars($this->segment(5));
		$p =htmlspecialchars($this->segment(6)) ? $this->segment(6) :1;

		if(!$select){
			$select = '10';
		}
		if($cate_band){
			$fen1 = intval($cate_band);
			$cate_band = 'list';
		}
		if(empty($fen1)){
			$brand=$this->db->GetList("select * from `@#_brand` where 1 order by `order` DESC");
			$daohang = '所有分类';
		}else{
			$brand=$this->db->GetList("select * from `@#_brand` where `cateid`='$fen1' order by `order` DESC");
			$daohang=$this->db->GetOne("select * from `@#_category` where `cateid` = '$fen1' order by `order` DESC");
			$daohang = $daohang['name'];
		}

		$category=$this->db->GetList("select * from `@#_category` where `model` = '1'");

		//分页

		$end=10;
		$star=($p-1)*$end;

		$select_w = '';
		if($select == 10){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 20){
			$select_w = "and `renqi` = '1'";
		}
		if($select == 30){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 40){
			$select_w = 'order by `time` DESC';
		}
		if($select == 50){
			$select_w = 'order by `money` DESC';
		}
		if($select == 60){
			$select_w = 'order by `money` ASC';
		}

		if($fen1){
			$count=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `cateid`='$fen1' $select_w");
		}else{
			$count=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `yunjiage`='10' $select_w");
		}
		if($fen1){
			$shoplist=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `cateid`='$fen1' $select_w limit $star,$end");
		}else{
			$shoplist=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `yunjiage`='10' $select_w limit $star,$end");
		}
		$max_renqi_qishu = 1;
		$max_renqi_qishu_id = 1;

		if(!empty($shoplistrenqi)){
			foreach ($shoplistrenqi as $renqikey =>$renqiinfo){
				if($renqiinfo['qishu'] >= $max_renqi_qishu){
					$max_renqi_qishu = $renqiinfo['qishu'];
					$max_renqi_qishu_id = $renqikey;
				}
			}
			$shoplistrenqi[$max_renqi_qishu_id]['t_max_qishu'] = 1;
		}


		$this_time = time();
		if(count($shoplist) > 1){
					if($shoplist[0]['time'] > $this_time - 86400*3)
					$shoplist[0]['t_new_goods'] = 1;
		}
		$pagex=ceil(count($count)/$end);
		if($p<=$pagex){
			$shoplist[0]['page']=$p+1;
		}
		if($pagex>0){
			$shoplist[0]['sum']=$pagex;
		}else if($pagex==0){
			$shoplist[0]['sum']=$pagex;
		}

		echo json_encode($shoplist);
	}	
	
	//ajax获取商品列表信息
	public function gblistajax(){
	    $webname=$this->_cfg['web_name'];
		$cate_band =htmlspecialchars($this->segment(4));
		$select =htmlspecialchars($this->segment(5));
		$p =htmlspecialchars($this->segment(6)) ? $this->segment(6) :1;

		if(!$select){
			$select = '10';
		}
		if($cate_band){
			$fen1 = intval($cate_band);
			$cate_band = 'list';
		}
		if(empty($fen1)){
			$brand=$this->db->GetList("select * from `@#_brand` where 1 order by `order` DESC");
			$daohang = '所有分类';
		}else{
			$brand=$this->db->GetList("select * from `@#_brand` where `cateid`='$fen1' order by `order` DESC");
			$daohang=$this->db->GetOne("select * from `@#_category` where `cateid` = '$fen1' order by `order` DESC");
			$daohang = $daohang['name'];
		}

		$category=$this->db->GetList("select * from `@#_category` where `model` = '1'");

		//分页

		$end=10;
		$star=($p-1)*$end;

		$select_w = '';
		if($select == 10){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 20){
			$select_w = "and `renqi` = '1'";
		}
		if($select == 30){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 40){
			$select_w = 'order by `time` DESC';
		}
		if($select == 50){
			$select_w = 'order by `money` DESC';
		}
		if($select == 60){
			$select_w = 'order by `money` ASC';
		}

		if($fen1){
			$count=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `cateid`='$fen1' $select_w");
		}else{
			$count=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `yunjiage`='100' $select_w");
		}
		if($fen1){
			$shoplist=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `cateid`='$fen1' $select_w limit $star,$end");
		}else{
			$shoplist=$this->db->GetList("select * from `@#_shoplist` where `q_uid` is null and `yunjiage`='100' $select_w limit $star,$end");
		}
		$max_renqi_qishu = 1;
		$max_renqi_qishu_id = 1;

		if(!empty($shoplistrenqi)){
			foreach ($shoplistrenqi as $renqikey =>$renqiinfo){
				if($renqiinfo['qishu'] >= $max_renqi_qishu){
					$max_renqi_qishu = $renqiinfo['qishu'];
					$max_renqi_qishu_id = $renqikey;
				}
			}
			$shoplistrenqi[$max_renqi_qishu_id]['t_max_qishu'] = 1;
		}


		$this_time = time();
		if(count($shoplist) > 1){
					if($shoplist[0]['time'] > $this_time - 86400*3)
					$shoplist[0]['t_new_goods'] = 1;
		}
		$pagex=ceil(count($count)/$end);
		if($p<=$pagex){
			$shoplist[0]['page']=$p+1;
		}
		if($pagex>0){
			$shoplist[0]['sum']=$pagex;
		}else if($pagex==0){
			$shoplist[0]['sum']=$pagex;
		}

		echo json_encode($shoplist);
	}

//ajax获取商品列表信息
	public function jflistajax(){
	    $webname=$this->_cfg['web_name'];
		$cate_band =htmlspecialchars($this->segment(4));
		$select =htmlspecialchars($this->segment(5));
		$p =htmlspecialchars($this->segment(6)) ? $this->segment(6) :1;

		if(!$select){
			$select = '10';
		}
		if($cate_band){
			$fen1 = intval($cate_band);
			$cate_band = 'list';
		}
		if(empty($fen1)){
			$brand=$this->db->GetList("select * from `@#_jf_brand` where 1 order by `order` DESC");
			$daohang = '所有分类';
		}else{
			$brand=$this->db->GetList("select * from `@#_jf_brand` where `cateid`='$fen1' order by `order` DESC");
			$daohang=$this->db->GetOne("select * from `@#_category` where `cateid` = '$fen1' order by `order` DESC");
			$daohang = $daohang['name'];
		}

		$category=$this->db->GetList("select * from `@#_category` where `model` = '1'");

		//分页

		$end=10;
		$star=($p-1)*$end;

		$select_w = '';
		if($select == 10){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 20){
			$select_w = "and `renqi` = '1'";
		}
		if($select == 30){
			$select_w = 'order by `shenyurenshu` ASC';
		}
		if($select == 40){
			$select_w = 'order by `time` DESC';
		}
		if($select == 50){
			$select_w = 'order by `money` DESC';
		}
		if($select == 60){
			$select_w = 'order by `money` ASC';
		}

		if($fen1){
			$count=$this->db->GetList("select * from `@#_jf_shoplist` where zongrenshu!=canyurenshu and `q_uid` is null and `cateid`='$fen1' $select_w");
		}else{
			$count=$this->db->GetList("select * from `@#_jf_shoplist` where zongrenshu!=canyurenshu and `q_uid` is null $select_w");
		}
		if($fen1){
			$shoplist=$this->db->GetList("select * from `@#_jf_shoplist` where zongrenshu!=canyurenshu and `q_uid` is null and `cateid`='$fen1' $select_w limit $star,$end");
		}else{
			$shoplist=$this->db->GetList("select * from `@#_jf_shoplist` where zongrenshu!=canyurenshu and `q_uid` is null $select_w limit $star,$end");
		}
		$max_renqi_qishu = 1;
		$max_renqi_qishu_id = 1;

		if(!empty($shoplistrenqi)){
			foreach ($shoplistrenqi as $renqikey =>$renqiinfo){
				if($renqiinfo['qishu'] >= $max_renqi_qishu){
					$max_renqi_qishu = $renqiinfo['qishu'];
					$max_renqi_qishu_id = $renqikey;
				}
			}
			$shoplistrenqi[$max_renqi_qishu_id]['t_max_qishu'] = 1;
		}


		$this_time = time();
		if(count($shoplist) > 1){
					if($shoplist[0]['time'] > $this_time - 86400*3)
					$shoplist[0]['t_new_goods'] = 1;
		}
		$pagex=ceil(count($count)/$end);
		if($p<=$pagex){
			$shoplist[0]['page']=$p+1;
		}
		if($pagex>0){
			$shoplist[0]['sum']=$pagex;
		}else if($pagex==0){
			$shoplist[0]['sum']=$pagex;
		}

		echo json_encode($shoplist);
	}
	//商品详细
	public function item(){
	    $webname=$this->_cfg['web_name'];
		$key="商品详情";
		$mysql_model=System::load_sys_class('model');
		$itemid=safe_replace($this->segment(4));

		$item=$mysql_model->GetOne("select * from `@#_shoplist` where `id`='".$itemid."' LIMIT 1");
		if(!$item)_messagemobile("商品不存在！");
		if($item['q_end_time']){
			header("location: ".WEB_PATH."/mobile/mobile/dataserver/".$item['id']);
			exit;
		}
		$sid=$item['sid'];
		$sid_code=$mysql_model->GetOne("select * from `@#_shoplist` where `sid`='$sid' order by `id` DESC LIMIT 1,1");
		$sid_go_record=$mysql_model->GetOne("select * from `@#_member_go_record` where `shopid`='$sid_code[sid]' and `uid`='$sid_code[q_uid]' order by `id` DESC LIMIT 1");


		$category=$mysql_model->GetOne("select * from `@#_category` where `cateid` = '$item[cateid]' LIMIT 1");
		$brand=$mysql_model->GetOne("select * from `@#_brand` where `id`='$item[brandid]' LIMIT 1");

		$title=$item['title'];
		$syrs=$item['zongrenshu']-$item['canyurenshu'];
		$item['picarr'] = unserialize($item['picarr']) ;


		$us=$mysql_model->GetList("select * from `@#_member_go_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."'ORDER BY id DESC LIMIT 6");

		//$us2=$mysql_model->GetList("select * from `@#_member_go_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."'ORDER BY id DESC");

		$itemlist = $this->db->GetList("select * from `@#_shoplist` where `sid`='$item[sid]' and `q_end_time` is not null order by `qishu` DESC");

		//期数显示
		$loopqishu='';
		$loopqishu.='<li class="cur"><a href="javascript:;">'."第".$item['qishu']."期</a><b></b></li>";

		if(empty($itemlist)){
		foreach($itemlist as $qitem){
			$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/item/'.$qitem['id'].'" class="">第'.$qitem['qishu'].'期</a></li>';

		}}

		foreach($itemlist as $qitem){
			if($qitem['id'] == $itemid){

				$loopqishu.='<li class="cur"><a href="javascript:;">'."第".$itemlist[0]['qishu']."期</a><b></b></li>";
			}else{
				$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/dataserver/'.$qitem['id'].'" >第'.$qitem['qishu'].'期</a></li>';
			}
		}
		$gorecode=array();
		if(!empty($itemlist)){
		//查询上期的获奖者信息
			$gorecode=$this->db->GetOne("select * from `@#_member_go_record` where `shopid`='".$itemlist[0]['id']."' AND `shopqishu`='".$itemlist[0]['qishu']."' and huode!=0 ORDER BY id DESC LIMIT 1");
		}

		//echo "<pre>";
		//print_r($itemlist);
		//echo microt($itemlist[0]['q_end_time']);exit;
		 $curtime=time();
         $shopitem='itemfun';

		//晒单数
		$shopid=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid'");
		$shoplist=$this->db->GetList("select * from `@#_shoplist` where `sid`='$shopid[sid]'");
		$shop='';
		foreach($shoplist as $list){
			$shop.=$list['id'].',';
		}
		$id=trim($shop,',');
		if($id){
			$shaidan=$this->db->GetList("select * from `@#_shaidan` where `sd_shopid` IN ($id)");
			$sum=0;
			foreach($shaidan as $sd){
				$shaidan_hueifu=$this->db->GetList("select * from `@#_shaidan_hueifu` where `sdhf_id`='$sd[sd_id]'");
				$sum=$sum+count($shaidan_hueifu);
			}
		}else{
			$shaidan=0;
			$sum=0;
		}

		if($item['shenyurenshu']=='0' || $item['xsjx_time']=='0' || empty($item['q_uid'])){

			$last_item = $us[0];

			$period_info = $mysql_model->GetOne("select * from `@#_period` order by `id` DESC LIMIT 1");

			if($period_info['num'] >= 120){

				$period = date("Ymd",strtotime("+1 day")).'001';

			}else{

				$period = $period_info['period']+1;

			}



		}
		include templates("mobile/index","item");
	}
	//商品详细
	public function itemajax(){
		$mysql_model=System::load_sys_class('model');
		$itemid=safe_replace($this->segment(4));
		$item=$mysql_model->GetOne("select * from `@#_shoplist` where `id`='".$itemid."' LIMIT 1");
		$uid=$mysql_model->GetOne("select * from `@#_member` where `uid`='".$item['q_uid']."' LIMIT 1");
		$mysql_model->Query("UPDATE `@#_shoplist` SET `q_showtime`='N' where `id`= $itemid");
		$temp =array();
		$temp = $item;
		$temp['user'] = empty($uid['username']) ? substr($uid['mobile'],0,3).'****'.substr($uid['mobile'],7,4) : $uid['username'];
		$temp['pic'] = G_UPLOAD_PATH.'/'.$item['thumb'];
		echo '<div style="width:90%; margin-left:5%;"><img width="90%" src='.$temp['pic'].'></div><div class="txt"><h6>'.$temp['title'].'</h6><div class="zj"><span>中奖</span><span style="color: #F60;">'.$temp['user']."</span></div></div>";exit;

	}

	//商品详细
	public function jf_item(){
	    $webname=$this->_cfg['web_name'];
		$key="商品详情";
		$mysql_model=System::load_sys_class('model');
		$itemid=safe_replace($this->segment(4));

		$item=$mysql_model->GetOne("select * from `@#_jf_shoplist` where `id`='".$itemid."' LIMIT 1");
		if(!$item)_messagemobile("商品不存在！");
		if($item['q_end_time']){
			header("location: ".WEB_PATH."/mobile/mobile/dataserver/".$item['id']);
			exit;
		}
		$sid=$item['sid'];
		$sid_code=$mysql_model->GetOne("select * from `@#_jf_shoplist` where `sid`='$sid' order by `id` DESC LIMIT 1,1");
		$sid_go_record=$mysql_model->GetOne("select * from `@#_member_go_jf_record` where `shopid`='$sid_code[sid]' and `uid`='$sid_code[q_uid]' order by `id` DESC LIMIT 1");


		$category=$mysql_model->GetOne("select * from `@#_category` where `cateid` = '$item[cateid]' LIMIT 1");
		$brand=$mysql_model->GetOne("select * from `@#_jf_brand` where `id`='$item[brandid]' LIMIT 1");

		$title=$item['title'];
		$syrs=$item['zongrenshu']-$item['canyurenshu'];
		$item['picarr'] = unserialize($item['picarr']) ;


		$us=$mysql_model->GetList("select * from `@#_member_go_jf_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."'ORDER BY id DESC LIMIT 6");

		//$us2=$mysql_model->GetList("select * from `@#_member_go_jf_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."'ORDER BY id DESC");

		$itemlist = $this->db->GetList("select * from `@#_jf_shoplist` where `sid`='$item[sid]' and `q_end_time` is not null order by `qishu` DESC");

		
		//期数显示
		$loopqishu='';
		$loopqishu.='<li class="cur"><a href="javascript:;">'."第".$item['qishu']."期</a><b></b></li>";

		if(empty($itemlist)){
		foreach($itemlist as $qitem){
			$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/jf_item/'.$qitem['id'].'" class="">第'.$qitem['qishu'].'期</a></li>';

		}}

		foreach($itemlist as $qitem){
			if($qitem['id'] == $itemid){

				$loopqishu.='<li class="cur"><a href="javascript:;">'."第".$itemlist[0]['qishu']."期</a><b></b></li>";
			}else{
				$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/dataserver/'.$qitem['id'].'" >第'.$qitem['qishu'].'期</a></li>';
			}
		}
		$gorecode=array();
		if(!empty($itemlist)){
		//查询上期的获奖者信息
			$gorecode=$this->db->GetOne("select * from `@#_member_go_jf_record` where `shopid`='".$itemlist[0]['id']."' AND `shopqishu`='".$itemlist[0]['qishu']."' and huode!=0 ORDER BY id DESC LIMIT 1");
		}

		//echo "<pre>";
		//print_r($itemlist);
		//echo microt($itemlist[0]['q_end_time']);exit;
		 $curtime=time();
         $shopitem='itemfun';

		//晒单数
		$shopid=$this->db->GetOne("select * from `@#_jf_shoplist` where `id`='$itemid'");
		$shoplist=$this->db->GetList("select * from `@#_jf_shoplist` where `sid`='$shopid[sid]'");
		$shop='';
		foreach($shoplist as $list){
			$shop.=$list['id'].',';
		}
		$id=trim($shop,',');
		if($id){
			$shaidan=$this->db->GetList("select * from `@#_shaidan` where `sd_shopid` IN ($id)");
			$sum=0;
			foreach($shaidan as $sd){
				$shaidan_hueifu=$this->db->GetList("select * from `@#_shaidan_hueifu` where `sdhf_id`='$sd[sd_id]'");
				$sum=$sum+count($shaidan_hueifu);
			}
		}else{
			$shaidan=0;
			$sum=0;
		}

		include templates("mobile/index","jf_item");
	}

	//往期商品查看
	public function dataserver(){
	    $webname=$this->_cfg['web_name'];
		$key="揭晓结果";
		$itemid=intval($this->segment(4));
		$item=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid'  LIMIT 1");
		if(!$item){
			_messagemobile("商品不存在！");
		}
		if ( !$item['q_end_time'] ) {
			header("location: ".WEB_PATH."/mobile/mobile/item/".$item['id']);
			die;
		}


		if(empty($item['q_user_code'])){
			_messagemobile("该商品正在进行中...");
		}

		$itemlist = $this->db->GetList("select * from `@#_shoplist` where `sid`='$item[sid]' order by `qishu` DESC");
		$category=$this->db->GetOne("select * from `@#_category` where `cateid` = '$item[cateid]' LIMIT 1");
		$brand=$this->db->GetOne("select * from `@#_brand` where `id` = '$item[brandid]' LIMIT 1");

		//云购中奖码
		$q_user = unserialize($item['q_user']);
		$q_user_code_len = strlen($item['q_user_code']);
		$q_user_code_arr = array();
		for($q_i=0;$q_i < $q_user_code_len;$q_i++){
			$q_user_code_arr[$q_i] = substr($item['q_user_code'],$q_i,1);
		}

		//期数显示
		$loopqishu='';
		if(empty($itemlist[0]['q_end_time'])){
			$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/item/'.$itemlist[0]['id'].'">'."第".$itemlist[0]['qishu']."期</a><b></b></li>";
			array_shift($itemlist);
		}

		foreach($itemlist as $qitem){
			if($qitem['id'] == $itemid){

				$loopqishu.='<li class="cur"><a href="javascript:;">'."第".$qitem['qishu']."期</a><b></b></li>";
			}else{
				$loopqishu.='<li><a href="'.WEB_PATH.'/mobile/mobile/dataserver/'.$qitem['id'].'" >第'.$qitem['qishu'].'期</a></li>';
			}
		}

		//总云购次数
		$user_shop_number = 0;
		//用户云购时间
		$user_shop_time = 0;
		//得到云购码
		$user_shop_codes = '';

		$user_shop_list = $this->db->GetList("select * from `@#_member_go_record` where `uid`= '$item[q_uid]' and `shopid` = '$itemid' and `shopqishu` = '$item[qishu]'");
		foreach($user_shop_list as $user_shop_n){
			$user_shop_number += $user_shop_n['gonumber'];
			if($user_shop_n['huode']){
				$user_shop_time = $user_shop_n['time'];
				$user_shop_codes = $user_shop_n['goucode'];
			}
		}

		$h=abs(date("H",$item['q_end_time']));
		$i=date("i",$item['q_end_time']);
		$s=date("s",$item['q_end_time']);
		$w=substr($item['q_end_time'],11,3);
		$user_shop_time_add = $h.$i.$s.$w;
		$user_shop_fmod = fmod($user_shop_time_add*100,$item['canyurenshu']);

		if($item['q_content']){
			$item['q_content'] = unserialize($item['q_content']);
		}
        $item['picarr'] = unserialize($item['picarr']) ;

		//记录
		$itemzx=$this->db->GetOne("select * from `@#_shoplist` where `sid`='$item[sid]' and `qishu`>'$item[qishu]' and `q_end_time` is null order by `qishu` DESC LIMIT 1");

	    $gorecode=$this->db->GetOne("select * from `@#_member_go_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."' and `uid`= '$item[q_uid]' and huode!=0 LIMIT 1");
	    $gorecode_count=$this->db->GetOne("select sum(gonumber) as count from `@#_member_go_record` where `shopid`='".$itemid."' AND `shopqishu`='".$item['qishu']."' and `uid`= '$item[q_uid]'");
	    $gorecode_count=$gorecode_count ? $gorecode_count['count'] : 0;

		$shopitem='dataserverfun';
		$curtime=time();
		//晒单数
		$shopid=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid'");
		$shoplist=$this->db->GetList("select * from `@#_shoplist` where `sid`='$shopid[sid]'");
		$shop='';
		foreach($shoplist as $list){
			$shop.=$list['id'].',';
		}
		$id=trim($shop,',');
		if($id){
			$shaidan=$this->db->GetList("select * from `@#_shaidan` where `sd_shopid` IN ($id)");
			$sum=0;
			foreach($shaidan as $sd){
				$shaidan_hueifu=$this->db->GetList("select * from `@#_shaidan_hueifu` where `sdhf_id`='$sd[sd_id]'");
				$sum=$sum+count($shaidan_hueifu);
			}
		}else{
			$shaidan=0;
			$sum=0;
		}
		$itemxq=0;
		if(!empty($itemzx)){
		  $itemxq=1;
		}

		include templates("mobile/index","item");
	}




	//************************************************//
	//************************************************//
	//************************************************//

	public function tenpaysuccess(){
	    $webname=$this->_cfg['web_name'];
		$code= _getcookie('CODE');
		if(!isset($_GET['attach'])){
			_messagemobile("页面错误!");
			exit;
		}
		if(!$code){
			_messagemobile("页面错误!");
			exit;
		}
		$mysql_model=System::load_sys_class('model');
		$member=$this->userinfo;
		$total_fee      = $_GET['total_fee']/100+$member['money'];
		$attach         = $_GET['attach'];
		$sign           = $_GET['sign'];
		//if($pay_result<1){
			$mysql_model->Query("UPDATE `@#_member` SET money='".$total_fee."' where uid='".$member['uid']."'");
			$shop=explode("&",$attach);
			gopay($member,$shop[0],$shop[1],$shop[2]);
		//}
	}

	//最新揭晓
	public function lottery(){
	     $webname=$this->_cfg['web_name'];
		//最新揭晓
		$shopqishu=$this->db->GetList("select * from `@#_shoplist` where `q_end_time` is not null ORDER BY `q_end_time` DESC LIMIT 0,4");


		$shoplist=$this->db->GetList("select * from `@#_shoplist` where 1 ORDER BY `canyurenshu` DESC LIMIT 4");
		$member_record=$this->db->GetList("select * from `@#_member_go_record` order by id DESC limit 6");
		$key="最新揭晓";
		include templates("mobile/index","lottery");
	}


	//商品购买记录
	public function buyrecords(){
	    $webname=$this->_cfg['web_name'];
		$key="所有云购记录";
		$itemid=intval($this->segment(4));
		$cords=$this->db->GetList("select * from `@#_member_go_record` where `shopid`='$itemid'");
		$co = count($cords);
		include templates("mobile/index","buyrecords");
	}
	//图文详细
	public function goodsdesc(){
	    $webname=$this->_cfg['web_name'];
		$key="图文详情";
		$itemid=intval($this->segment(4));
		$desc=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid'");
		if(!$desc){
			_messagemobile('页面错误!');
		}
		include templates("mobile/index","goodsdesc");
	}
	//晒单评论
	public function goodspost(){
	    $webname=$this->_cfg['web_name'];
		$key="晒单评论";
		$itemid=intval($this->segment(4));
		$shoplist=$this->db->GetList("select * from `@#_shoplist` where `sid`='$itemid'");
		if(!$shoplist){
			_messagemobile('页面错误!');
		}
		$shop='';
		foreach($shoplist as $list){
			$shop.=$list['id'].',';
		}
		$id=trim($shop,',');
		if($id){
			$shaidan=$this->db->GetList("select * from `@#_shaidan` where `sd_shopid` IN ($id) order by `sd_id` DESC");
			$sum=0;
			foreach($shaidan as $sd){
				$shaidan_hueifu=$this->db->GetList("select * from `@#_shaidan_hueifu` where `sdhf_id`='$sd[sd_id]'");
				$sum=$sum+count($shaidan_hueifu);
			}
		}else{
			$shaidan=0;
			$sum=0;
		}
		include templates("mobile/index","goodspost");
	
	}
	
	
		//商品购买记录
	public function buyrecords_jf(){
	    $webname=$this->_cfg['web_name'];
		$key="所有云购记录";
		$itemid=intval($this->segment(4));
		$cords=$this->db->GetList("select * from `@#_member_go_jf_record` where `shopid`='$itemid'");
		if(!$cords){
			_messagemobile('页面错误!');
		}
		include templates("mobile/index","buyrecords");
	}
	//图文详细
	public function goodsdesc_jf(){
	    $webname=$this->_cfg['web_name'];
		$key="图文详情";
		$itemid=intval($this->segment(4));
		$desc=$this->db->GetOne("select * from `@#_jf_shoplist` where `id`='$itemid'");
		if(!$desc){
			_messagemobile('页面错误!');
		}
		include templates("mobile/index","goodsdesc");
	}
	//直购图文详细
	public function jf_goodsdesc(){
	    $webname=$this->_cfg['web_name'];
		$key="图文详情";
		$itemid=intval($this->segment(4));
		$desc=$this->db->GetOne("select * from `@#_jf_shoplist` where `id`='$itemid'");
		if(!$desc){
			_messagemobile('页面错误!');
		}
		include templates("mobile/index","jf_goodsdesc");
	}
	//晒单评论
	public function goodspost_jf(){
	    $webname=$this->_cfg['web_name'];
		$key="晒单评论";
		$itemid=intval($this->segment(4));
		$shoplist=$this->db->GetList("select * from `@#_jf_shoplist` where `sid`='$itemid'");
		if(!$shoplist){
			_messagemobile('页面错误!');
		}
		$shop='';
		foreach($shoplist as $list){
			$shop.=$list['id'].',';
		}
		$id=trim($shop,',');
		if($id){
			$shaidan=$this->db->GetList("select * from `@#_shaidan` where `sd_shopid` IN ($id) order by `sd_id` DESC");
			$sum=0;
			foreach($shaidan as $sd){
				$shaidan_hueifu=$this->db->GetList("select * from `@#_shaidan_hueifu` where `sdhf_id`='$sd[sd_id]'");
				$sum=$sum+count($shaidan_hueifu);
			}
		}else{
			$shaidan=0;
			$sum=0;
		}
		include templates("mobile/index","goodspost");
	}

	public function calResult(){
	  $itemid=intval($this->segment(4));
	  	$item=$this->db->GetOne("select * from `@#_shoplist` where `id`='$itemid' LIMIT 1");

	    $h=abs(date("H",$item['q_end_time']));
		$i=date("i",$item['q_end_time']);
		$s=date("s",$item['q_end_time']);
		$w=substr($item['q_end_time'],11,3);
		$user_shop_time_add = $h.$i.$s.$w;
		$user_shop_fmod = fmod($user_shop_time_add*100,$item['canyurenshu']);

		if($item['q_content']){
			$item['q_content'] = unserialize($item['q_content']);
			$user_shop_time_add = $item['q_counttime'];
			$user_shop_fmod = fmod($item['q_counttime'],$item['canyurenshu']);
		}

        $item['picarr'] = unserialize($item['picarr']) ;

	  include templates("mobile/index","calResult");
	}
	//新手指南
	public function about(){
	 $webname=$this->_cfg['web_name'];
	 $category=$this->db->GetOne("select * from `@#_category` where `parentid`='1' and `name`='新手指南'");

	 $article=$this->db->GetList("select * from `@#_article` where `cateid`='$category[cateid]'");

	include templates("mobile/index","about");
	}


	//用户服务协议
	public function terms(){
	  $webname=$this->_cfg['web_name'];
	 $category=$this->db->GetOne("select * from `@#_category` where `parentid`='1' and `name`='新手指南'");

	 $article=$this->db->GetOne("select * from `@#_article` where `cateid`='$category[cateid]' and `title`='服务协议' ");

	  include templates("mobile/system","terms");
	}

	//访问个人主页
	public function userindex(){
	  $webname=$this->_cfg['web_name'];
	  $uid=safe_replace($this->segment(4));
	  //$uid=intval($this->segment(4))-1000000000;
	  //获取个人资料
	  $member=$this->db->GetOne("select * from `@#_member` where `uid`='$uid'");
	  if(!empty($member['headimg'])){
	  	$member['img'] = $member['headimg'];
	  }else{
	  	$member['img'] = G_UPLOAD_PATH.'/'.$member['img'];
	  }
	  //获取云购等级  云购新手  云购小将==
	  $memberdj=$this->db->GetList("select * from `@#_member_group`");

	  $jingyan=$member['jingyan'];
	  if(!empty($memberdj)){
	     foreach($memberdj as $key=>$val){
		    if($jingyan>=$val['jingyan_start'] && $jingyan<=$val['jingyan_end']){
			   $member['yungoudj']=$val['name'];
			}
		 }
	  }
	  include templates("mobile/index","userindex");
	}

	
	//今日揭晓
	public function autolottery(){
	    $w_jinri_time = strtotime(date('Y-m-d'));
		$w_minri_time = strtotime(date('Y-m-d',strtotime("+1 day")));
		
		$jinri_shoplist = $this->db->GetList("select * from `@#_shoplist` where `xsjx_time` > '$w_jinri_time' and `xsjx_time` < '$w_minri_time' order by xsjx_time limit 0,3 ");
		include templates("mobile/index","buyrecords");
	
	}
    
	//明日揭晓
	public function nextautolottery(){		 
		$w_minri_time = strtotime(date('Y-m-d',strtotime("+1 day")));
		$w_houri_time = strtotime(date('Y-m-d',strtotime("+2 day")));
		
		$jinri_shoplist = $this->db->GetList("select * from `@#_shoplist` where `xsjx_time` > '$w_minri_time' and `xsjx_time` < '$w_houri_time' order by xsjx_time limit 0,3 ");
	}
	//欢迎页2016.9.23
    public function welcome(){
		$webname=$this->_cfg['web_name'];
		$wel_bg=$this->db->GetOne("select * from `@#_wap` where `where_is`=1 order by id desc");
		//var_dump($wel_bg);exit;
		$interval = $this->db->GetList("select * from `@#_price_interval` WHERE `showtop`=1 ORDER BY `sort` ASC ");
		$wel = "welcome my web site";
		_setcookie ( 'wel', json_encode ( $wel ), '' );
		include templates("mobile/index","welcome");
	}




	//摇一摇抽奖页 2016.12.13
	public function shakereward (){
        parent::__construct ();
        if (!$member = $this->userinfo) {
            //header ( "location: " . WEB_PATH . "/mobile/user/login/"); //改为正常登录
            header ( "location: " . WEB_PATH . "/api/wxloginlottery" ); //改为微信登录
            exit;
        }
			include templates("mobile/index","shakereward");
	}
	//抽奖按钮页
	public function shakelottery (){
        /*$arr = array(1=>'john',2=>'jane',3=>'leo');
        $rand = array_rand($arr,2);
        $keys = array_keys($arr);
        var_dump($rand);
        var_dump($keys);exit;*/
        //查询数据库
        $lottery = $this->db->GetList("select * from `@#_lottery_prize` WHERE 1",array('key'=>'p_id'));
        //p($lottery);exit;
        $lotteryTemp = array();
        foreach ($lottery as $k => $v){
            $lotteryTemp[$k] = $v;
            switch ($v['p_title']){
                case '特等奖':
                    $lotteryTemp[$k]['class_name'] = 'lottery-special';
                    $lotteryTemp[$k]['class_num'] = 1;
                    $lotteryTemp[$k]['class_type'] = 'special';
                    break;
                case '一等奖':
                    $lotteryTemp[$k]['class_name'] = 'lottery-first';
                    $lotteryTemp[$k]['class_num'] = 2;
                    $lotteryTemp[$k]['class_type'] = 'first';
                    break;
                case '二等奖':
                    $lotteryTemp[$k]['class_name'] = 'lottery-sencond';
                    $lotteryTemp[$k]['class_num'] = 3;
                    $lotteryTemp[$k]['class_type'] = 'sencond';
                    break;
                case '三等奖':
                    $lotteryTemp[$k]['class_name'] = 'lottery-third';
                    $lotteryTemp[$k]['class_num'] = 4;
                    $lotteryTemp[$k]['class_type'] = 'third';
                    break;
            }
        }
        $lottery = $lotteryTemp;
        //p($lottery);exit;
			include templates("mobile/index","shakelottery");
		}
		//开奖页面
		public function lotterybegin(){
            $arr = array(
                '122'=>array('name'=>'john','age'=>12),
                '125'=>array('name'=>'bir','age'=>22),
                '123'=>array('name'=>'jon','age'=>15),
                '124'=>array('name'=>'jhn','age'=>17),
                '128'=>array('name'=>'ohn','age'=>13),
                '152'=>array('name'=>'jon','age'=>152),
            );
            $arr_temp = array();
            $res = array_diff_key($arr,$arr_temp);
            echo '<pre>';
            var_dump($res);
            exit;
            //更改奖项状态,关闭通道
            $sql = "update `@#_lottery_prize` set `p_start_state`=0,`p_end_state`=1 WHERE `p_start_state`=1 AND `p_end_state`=0";
            $res = $this->db->Query($sql);

            include templates("mobile/index","lotterybegin");
        }

	//倒计时页
	public function lotterycountdown (){
			include templates("mobile/index","lotterycountdown");
		}

    //抽奖通道开启页
    public function gateway (){
        //查询数据库
        $gateWay = $this->db->GetList("select * from `@#_lottery_prize` WHERE 1",array('key'=>'p_id'));
        //p($gateWay);exit;
        $gateTemp = array();
        foreach ($gateWay as $k => $v){
            $gateTemp[$k] = $v;
            switch ($v['p_title']){
                case '特等奖':
                    $gateTemp[$k]['class_name'] = 'gateway-special';
                    break;
                case '一等奖':
                    $gateTemp[$k]['class_name'] = 'gateway-one';
                    break;
                case '二等奖':
                    $gateTemp[$k]['class_name'] = 'gateway-two';
                    break;
                case '三等奖':
                    $gateTemp[$k]['class_name'] = 'gateway-three';
                    break;
            }
        }
        $gateWay = $gateTemp;
        include templates("mobile/index","gateway");
    }

    /**
     * ajax开启奖池通道
     */
    public function ajaxOpenPool(){
        //获取抽奖通道id
        $item = isset($_POST['item'])?intval($_POST['item']):0;
        if(empty($item)){
            $response['state'] = 1;
            $response['msg'] = '通道参数不正确！';
            echo json_encode($response);
            exit;
        }
        //查询是否存在该通道
        $res = $this->db->GetOne("select `p_id`,`p_lottery_time` from `@#_lottery_prize` WHERE `p_id`=$item");
        if(!$res){
            $response['state'] = 1;
            $response['msg'] = '通道参数不存在！';
            echo json_encode($response);
            exit;
        }
        //开启通道时要清空奖池表
        $row_1 = $this->db->Query("truncate table `@#_lottery`");
        //更改通道状态
        $row_2 = $this->db->Query("update `@#_lottery_prize` set `p_start_state`=1 WHERE `p_id`=$item AND `p_end_state`=0");
        if($row_1 && $row_2){
            $response['state'] = 0;
            $response['time'] = $res['p_lottery_time'];
            $response['msg'] = '通道已开启！';
            echo json_encode($response);
            exit;
        }else{
            $response['state'] = 2;
            $response['msg'] = '通道开启失败！';
            echo json_encode($response);
            exit;
        }
    }

    /**
     * 摇一摇先通过ajax查询是那个通道开启
     */
    public function ajaxSearchBtn(){
        $searchInfo = $this->db->GetOne("select `p_id` from `@#_lottery_prize` WHERE (`p_start_state` =0 AND `p_end_state`=1) OR (`p_start_state`=1 AND `p_end_state`=0)");
        //p($searchInfo);exit;
        if($searchInfo){
            $response['state'] = 0;
            $response['item'] = $searchInfo['p_id'];
        }else{
            $response['state'] = 1;
            $response['item'] = 0;
        }
        echo json_encode($response);
        exit;
    }
    /**
     * ajax通过摇一摇把用户信息存入奖池表
     */
    public function ajaxShakeReward(){
        //var_dump($_POST);exit;
        //获取用户id
        $uid = isset($_POST['uid'])?intval($_POST['uid']):0;
        $item = isset($_POST['item'])?intval($_POST['item']):0;
        if(empty($uid) || empty($item)){
            $response['state'] = 1;
            $response['msg_p1'] = '活动还未开始';
            $response['msg_p2'] = '请耐心等待!';
            echo json_encode($response);
            exit;
        }
        //查询用户表，获取用户信息
        $userInfo = $this->db->GetOne("select `uid`,`username`,`img`,`headimg`,`wxid` from `@#_member` WHERE `uid`=$uid");
        if($userInfo){
            //查询活动是否存在，已经活动是否结束
            $prize = $this->db->GetOne("select * from `@#_lottery_prize` WHERE `p_id` = $item");
            if(!$prize){
                $response['state'] = 1;
                $response['msg_p1'] = '本轮抽奖已结束';
                $response['msg_p2'] = '请耐心等待下一轮';
            }else{
                if($prize['p_start_state']==0 && $prize['p_end_state'] == 0){
                    $response['state'] = 1;
                    $response['msg_p1'] = '抽奖活动还未开始';
                    $response['msg_p2'] = '请耐心等待!';
                }elseif($prize['p_start_state']==0 && $prize['p_end_state'] == 1){
                    $response['state'] = 1;
                    $response['msg_p1'] = '抽奖通道已关闭';
                    $response['msg_p2'] = '请勿重复摇!';
                }elseif($prize['p_start_state']==1 && $prize['p_end_state'] == 1){
                    $response['state'] = 1;
                    $response['msg_p1'] = '本轮抽奖已结束';
                    $response['msg_p2'] = '请耐心等待下一轮';
                }else{
                    //查询用户是否已经进入奖池
                    $in_prize = $this->db->GetOne("select `l_uid` from `@#_lottery` WHERE `l_uid`=$uid");
                    if($in_prize){
                        $response['state'] = 2;
                        $response['msg_p1'] = '您已进入奖池';
                        $response['msg_p2'] = '请勿重复摇!';

                    }else{
                        if(!empty($userInfo['headimg']) && $userInfo['img']=='photo/member.jpg'){
                            $uphoto = $userInfo['headimg'];
                        }else{
                            $uphoto = G_UPLOAD_PATH.'/'.$userInfo['img'];
                        }
                        $sql = "insert into `@#_lottery` (`l_uid`,`l_username`,`l_userheader`,`l_userwx`,`l_prize_id`) VALUES ($uid,'{$userInfo['username']}','$uphoto','{$userInfo['wxid']}',{$prize['p_id']})";
                        //var_dump($sql);
                        $res = $this->db->Query($sql);
                        if($res){
                            $response['state'] = 0;
                            $response['msg'] = '恭喜您，已进入奖池，等待开奖！';
                            $response['img'] = $uphoto;
                        }
                    }

                }
            }
            echo json_encode($response);
            exit;
        }else{
            $response['state'] = 1;
            $response['msg_p1'] = '亲，还未登录哦';
            $response['msg_p2'] = '请重新进入';
            echo json_encode($response);
            exit;
        }
    }
    /**
     * ajax判断抽奖按钮是否有效
     */
    public function ajaxGetBtn(){
        //var_dump($_GET['item']);exit;
        $item = isset($_GET['item'])?intval($_GET['item']):0;
        if(empty($item)){
            $response['state'] = 1;
            $response['msg'] = '参数不正确';
            echo json_encode($response);
            exit;
        }
        $prize = $this->db->GetOne("select * from `@#_lottery_prize` WHERE `p_id`=$item");
        if($prize['p_start_state']==0 && $prize['p_end_state'] == 0){
            $response['state'] = 2;
            $response['msg'] = '抽奖活动还未开始，请耐心等待';
        }elseif($prize['p_start_state']==1 && $prize['p_end_state'] == 0){
            $response['state'] = 3;
            $response['msg'] = '抽奖通道未关闭，不可抽奖';
        }elseif($prize['p_start_state']==1 && $prize['p_end_state'] == 1){
            $response['state'] = 4;
            $response['msg'] = '抽奖已结束，请联系后台重置';
        }else {
            //抽取中奖人
            $response = $this->ajaxLottery($item);
        }
        echo json_encode($response);
        exit;
    }

    /**
     * 进行抽奖
     */
    public function ajaxLottery($item){
        //var_dump($_POST['item']);exit;
        /*$item = isset($_POST['item'])?intval($_POST['item']):0;
        if(empty($item)){
            $response['state'] = 1;
            $response['msg'] = '参数不正确';
            echo serialize($response);
            exit;
        }*/
        //查询该抽奖活动是否已经失效
        $res = $this->db->GetOne("select * from `@#_lottery_prize` WHERE `p_id`=$item AND `p_start_state`=1 AND `p_end_state`=1");
        if($res){
            $response['state'] = 4;
            $response['msg'] = '抽奖已结束，请联系后台重置';
            return $response;
            /*echo serialize($response);
            exit;*/
        }
        //查询奖项配置信息
        $prize = $this->db->GetOne("select * from `@#_lottery_prize` WHERE `p_id`= $item AND `p_start_state`=0 AND `p_end_state`=1");
        //var_dump($prize);exit;
        if($prize){
            $hit_num = $prize['p_hit_num'];  //每次抽奖人数
            $award_num = $prize['p_award_num']; //抽奖总人数
            $count = $prize['p_count']; //需要抽几轮
            $last_num = $award_num % $hit_num; //取模得出最后一轮需要抽取的人数
            if($count == 0){
                //抽奖结束更新奖项字段
                $this->db->Query("update `@#_lottery_prize` set `p_start_state`=1,`p_end_state`=1 WHERE `p_id`=$item");
                $response['state'] = 4;
                $response['msg'] = '抽奖已结束，请联系后台重置';
                return $response;
                /*echo serialize($response);
                exit;*/
            }

            //获取奖池表的人数
            $people = $this->db->GetList("select * from `@#_lottery` WHERE `l_prize_id`=$item",array('key'=>'l_uid'));
            //shuffle($people); //将数组打乱
            //var_dump($people);
            //统计奖池表的总人数；
            $sum_num = count($people);
            //获取数组的键名
            $lottery = array_keys($people);
            $lottery_num = count($lottery);
            if($lottery_num==0){
                $response['state'] = 5;
                $response['msg'] = '奖池为空，请稍后抽奖';
                return $response;
                /*echo serialize($response);
                exit;*/
            }

            if($count == 1){//判断是否是最后一轮
                //判断$last_num 是否为零，是：说明抽奖人数是每次抽奖人数的整数倍
                if($lottery_num < $award_num){ //奖池人数与总抽奖人数
                    if($last_num == 0){
                        if($lottery_num < $hit_num){
                            $rand_keys = array_rand($people,$lottery_num); //得到中奖数的uid
                        }else{
                            $rand_keys = array_rand($people,$hit_num); //得到中奖数的uid
                        }
                    }elseif($lottery_num < $last_num){
                        $rand_keys = array_rand($people,$lottery_num); //得到中奖数的uid
                    }else{
                        $rand_keys = array_rand($people,$last_num); //得到中奖数的uid
                    }
                }else{
                    if($last_num == 0){
                        $rand_keys = array_rand($people,$hit_num); //得到中奖数的uid
                    }else{
                        $rand_keys = array_rand($people,$last_num); //得到中奖数的uid
                    }
                }
            }else{
                if($lottery_num < $award_num){ //奖池人数与总抽奖人数
                    if($lottery_num < $hit_num){
                        $rand_keys = array_rand($people,$lottery_num); //得到中奖数的uid
                    }else{
                        $rand_keys = array_rand($people,$hit_num); //得到中奖数的uid
                    }
                }else{
                    $rand_keys = array_rand($people,$hit_num); //得到中奖数的uid
                }
            }
            //查询中奖表
            $winner = $this->db->GetList("select * from `@#_lottery_winner` WHERE `l_prize_id`=$item",array('key'=>'l_uid'));
            //排除随机产生的uid是否是已中奖的，未，存入中奖表，有，unset当前元素，重新抽取一个
            if($winner){ //说明已经抽过
                $temp_arr = $people;
                if(is_array($rand_keys)){ //抽取多个
                    foreach ($rand_keys as $k => $v){
                        if(in_array($v,$winner)){
                            unset($temp_arr[$v]);
                            $rand_keys[$k] = array_rand($temp_arr);
                        }
                    }
                }else{
                    //抽取单个
                    if(in_array($rand_keys,$winner)){
                        unset($temp_arr[$rand_keys]);
                        $rand_keys[] = array_rand($temp_arr);
                    }
                }
                $hit_str = implode(',',$rand_keys);
            }else{
                if(is_array($rand_keys)){ //抽取多个
                    $hit_str = implode(',',$rand_keys);
                }else{
                    $hit_str = $rand_keys;
                }
            }
            //$hit_str = implode(',',$rand_keys);
            //把中奖人信息存入中奖表中
            $row_1 = $this->db->Query("insert into `@#_lottery_winner` (`l_uid`,`l_username`,`l_userheader`,`l_userwx`,`l_prize_id`) SELECT `l_uid`,`l_username`,`l_userheader`,`l_userwx`,`l_prize_id` FROM `@#_lottery` b WHERE b.l_uid IN ($hit_str)");
            //var_dump($row_1);
            //用来前端判断是否本次抽检结束
            $count_flag = $count;
            //更新抽奖轮数
            $count = $count - 1;
            $row_2 = $this->db->Query("update `@#_lottery_prize` set `p_count`= $count WHERE `p_id`=$item");
            if($count == 0){
                //这是最后一轮抽奖
                $this->db->Query("update `@#_lottery_prize` set `p_start_state`=1,`p_end_state`=1 WHERE `p_id`=$item");
            }
            //获取中奖人信息
            $win = $win_temp = array();
            foreach ($people as $key => $val){
                if(is_array($rand_keys)){
                    if(in_array($key,$rand_keys)){
                        $win_temp[] = $val;
                    }
                }else{
                    if($key = $rand_keys){
                        $win_temp[] = $val;
                    }
                }
            }
            foreach ($win_temp as $k => $v){
                $win[$k]['username'] = $v['l_username'];
                $win[$k]['header'] = $v['l_userheader'];
                $win[$k]['uid'] = $v['l_uid'];
            }
            if($row_1 && $row_2){
                $response['state'] = 0;
                $response['msg'] = 'ok';
                /*$response['lottery'] = $lottery_pool;*/
                //$response['count_flag'] = $count_flag;
                $response['winner'] = json_encode($win);
                return $response;
            }else{
                $response['state'] = 6;
                $response['msg'] = '没有参与者，本轮抽奖奖结束';
                return $response;
            }
        }

    }
    public function showlottery (){
        //更改奖项状态,关闭通道
        $sql = "update `@#_lottery_prize` set `p_start_state`=0,`p_end_state`=1 WHERE `p_start_state`=1 AND `p_end_state`=0";
        $res = $this->db->Query($sql);
        $lottery_pool = $this->db->GetList("select * from `@#_lottery` WHERE 1");
        $lottery_temp = array();
        foreach ($lottery_pool as $k => $v){
            $lottery_temp[$k] = $v;
            $lottery_temp[$k]['username'] = $v['l_username'];
            $lottery_temp[$k]['header'] = $v['l_userheader'];
        }
        $lottery_pool = json_encode($lottery_temp);
    		include templates("mobile/index","showlottery");
    }


}
?>