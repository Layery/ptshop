<?php 
defined('G_IN_SYSTEM')or exit('no');
ignore_user_abort(TRUE);
set_time_limit(0); 
System::load_sys_fun("send");
System::load_sys_fun("user");
System::load_app_class('admin',G_ADMIN_DIR,'no');

class dingdan extends admin {

	private $db;

	public function __construct(){		

		parent::__construct();		

		$this->db=System::load_sys_class('model');		

		$this->ment=array(

						array("lists","订单列表",ROUTE_M.'/'.ROUTE_C."/lists"),

                        array("lists","未付款",ROUTE_M.'/'.ROUTE_C."/lists/nopay"),

                        array("lists","未发货",ROUTE_M.'/'.ROUTE_C."/lists/notsend"),

						array("lists","已发货",ROUTE_M.'/'.ROUTE_C."/lists/sendok"),

                        array("insert","已完成",ROUTE_M.'/'.ROUTE_C."/lists/ok"),

                        array("insert","已关闭",ROUTE_M.'/'.ROUTE_C."/lists/del"),

                        array("genzhong","<b>快递跟踪</b>",ROUTE_M.'/'.ROUTE_C."/genzhong"),

		);

	}

	

	public function genzhong(){	

		include $this->tpl(ROUTE_M,'dingdan.genzhong');	

	}

    /**
     * 导出不同状态的订单
     */
    public function export_order(){
        //引入phpecxcel类文件
        require G_PLUGIN.'PHPExcel/PHPExcel.php';
        //实例化phpexcel
        $objPHPExcel = new PHPExcel();
        //获取数据
        //var_dump($_POST);
        $startTime = isset($_POST['startTime'])&&!empty($_POST['startTime'])?strtotime(safe_replace($_POST['startTime'])):time();
        $endTime = isset($_POST['endTime'])&&!empty($_POST['endTime'])?strtotime(safe_replace($_POST['endTime'])):time();
        $order_status = isset($_POST['order_status'])?safe_replace($_POST['order_status']):'0';
        //echo $startTime.' '.$endTime;exit;
        //根据订单状态判断导出什么样的订单
        switch ($order_status){
            case '0':
                $list_where = "where `status` LIKE  '%已付款%'";
                $name = '已付款';
                break;
            case 'nopay':
                $list_where = "where `status` like '未付款,未发货,未完成'";
                $name = '未付款';
                break;
            case 'notsend':
                $list_where = "where `status` LIKE  '%未发货%' and `status` like '%已付款%'";
                $name = '未发货';
                break;
            case 'sendok':
                $list_where = "where `status` LIKE  '已付款,已发货%'";
                $name = '已发货';
                break;
            case 'ok':
                $list_where = "where `status` LIKE  '已付款,已发货,已完成%'";
                $name = '已完成';
                break;
            default:
                $list_where = "where `status` LIKE  '%已关闭%'";
                $name = '已关闭';
        }
        //p($list_where);exit;
        $sql = "select * from `@#_member_go_record` $list_where AND `time`>=$startTime AND `time`<=$endTime";
        $orderList = $this->db->GetList($sql);
        //p($orderList);exit;
        //查询地址
        $sql = "select * from `@#_member_dizhi` WHERE 1";
        $address = $this->db->GetList($sql,array('key'=>'id'));
        $objActSheet = $objPHPExcel->getActiveSheet(); //获取当前活动表
        $title = date('Y-m-d',$startTime).'-'.date('Y-m-d',$endTime).$name.'的订单表';
        //p($title);exit;
        $objActSheet -> setTitle($title); //设置excel表内容的标题
        $filename = $title.'.xlsx';

        //为excel表格添加表头
        $objActSheet -> setCellValue('A1','订单编号')
            -> setCellValue('B1','下单时间')
            -> setCellValue('C1','收货人')
            -> setCellValue('D1','收货地址')
            -> setCellValue('E1','联系电话')
            -> setCellValue('F1','订单金额')
            -> setCellValue('G1','支付方式')
            -> setCellValue('H1','订单状态')
            -> setCellValue('I1','商品编号')
            -> setCellValue('J1','购买数量')
            -> setCellValue('K1','商品信息')
            -> setCellValue('L1','快递公司')
            -> setCellValue('M1','快递单号')
            -> setCellValue('N1','运费')
            -> setCellValue('O1','订单备注');
        if($orderList){
            $i = 2;
            foreach ($orderList as $v){
                $addressInfo = $address[$v['address']]['sheng'].$address[$v['address']]['shi'].$address[$v['address']]['xian'].$address[$v['address']]['jiedao'];
                $goods = unserialize($v['pro_info']);
                $goodsInfo = '商品名称：'.$v['shopname'].' 商品规格：';
                if(empty($goods['flag'])){
                    $goodsInfo .= $goods['key_name'];
                }else{
                    $goodsInfo .= '无';
                }
                $objActSheet -> setCellValue('A'.$i,$v['code'])
                    -> setCellValue('B'.$i,date('Y-m-d H:i:s',$v['time']))
                    -> setCellValue('C'.$i,$address[$v['address']]['shouhuoren'])
                    -> setCellValue('D'.$i,$addressInfo)
                    -> setCellValue('E'.$i,$address[$v['address']]['mobile'])
                    -> setCellValue('F'.$i,$v['moneycount'])
                    -> setCellValue('G'.$i,$v['pay_type'])
                    -> setCellValue('H'.$i,$v['status'])
                    -> setCellValue('I'.$i,$v['shopid'])
                    -> setCellValue('J'.$i,$v['gonumber'])
                    -> setCellValue('K'.$i,$goodsInfo)
                    -> setCellValue('L'.$i,$v['company'])
                    -> setCellValue('M'.$i,$v['company_code'])
                    -> setCellValue('N'.$i,$v['company_money'])
                    -> setCellValue('O'.$i,$v['remark']);
                $i++;
            }
        }
        // 生成2007excel格式的xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save( 'php://output');
        exit;

        //p($orderList);exit;
    }

    /**
     * 删除订单
     */
    public function del(){
        $id = intval($_GET['id']);
        if(empty($id)){
            echo '参数不正确';
            exit;
        }
        //查询订单信息
        $res = $this->db->GetOne("select * from `@#_member_go_record` WHERE `id`=$id");
        if(!$res){
            echo '数据错误,请重试!';
            exit;
        }
        //删除订单
        $row = $this->db->Query("delete from `@#_member_go_record` WHERE `id`=$id");
        if($row){
            echo 'ok';
            exit;
        }else{
            echo '删除失败!';
            exit;
        }
    }

	public function lists(){	

		

		/*

			已付款,未发货,已完成

			未付款,已发货,已作废

			已付款,未发货,待收货

		*/

		$where = $this->segment(4);

		if(!$where){

            $haspay = 'selected'; //用来导出筛选

			$list_where = "where 1";

		}elseif($where == 'nopay'){
            //未付款
            $nopay = 'selected';
            $list_where = "where `status` like '未付款,未发货,未完成'";

        }elseif($where == 'sendok'){

			//已发货订单
            $sendok = 'selected';
			$list_where = "where `status` LIKE  '已付款,已发货%'";

		}elseif($where == 'notsend'){

			//未发货订单

            $notsend = 'selected';
			$list_where = "where `status` LIKE  '%未发货%' and `status` like '%已付款%'";

		}elseif($where == 'ok'){

			//已完成
            $ok = 'selected';
			$list_where = "where `status` LIKE  '已付款,已发货,已完成%'";

		}elseif($where == 'del'){

			//已作废		
            $del = 'selected';
			$list_where = "where `status` LIKE  '%已关闭%'";

		}

		

		if(isset($_POST['paixu_submit'])){

			$paixu = $_POST['paixu'];

			if($paixu == 'time1'){

				$list_where.=" order by `time` DESC";

			}

			if($paixu == 'time2'){

				$list_where.=" order by `time` ASC";

			}

			if($paixu == 'num1'){

				$list_where.=" order by `gonumber` DESC";

			}

			if($paixu == 'num2'){

				$list_where.=" order by `gonumber` ASC";

			}

			if($paixu == 'money1'){

				$list_where.=" order by `moneycount` DESC";

			}

			if($paixu == 'money2'){

				$list_where.=" order by `moneycount` ASC";

			}

		

		}else{

			$list_where.=" order by `time` DESC";

			$paixu = 'time1';

		}

		$num=20;
	

		$total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_member_go_record` $list_where");

		$page=System::load_sys_class('page');

		if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}	

		$page->config($total,$num,$pagenum,"0");

		$recordlist=$this->db->GetPage("SELECT * FROM `@#_member_go_record` $list_where",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));

		include $this->tpl(ROUTE_M,'dingdan.list');	

	}

	

	//订单详细

    public function get_dingdan(){
        $code=abs(intval($this->segment(4)));
        $record=$this->db->GetOne("SELECT * FROM `@#_member_go_record` where `id`='$code'");
        //p($record);exit;
        if(!$record)_message("参数不正确!");

        if(isset($_POST['submit'])){
            $record_code =explode(",",$record['status']);
            $status = $_POST['status'];
            $company = $_POST['company'];
            $company_code = $_POST['company_code'];
            $company_money = floatval($_POST['company_money']);
            $code = abs(intval($_POST['code']));
            if(!$company_money){
                $company_money = '0.01';
            }else{
                $company_money = sprintf("%.2f",$company_money);
            }

            if($status == '未完成'){
                $status = $record_code[0].','.$record_code[1].','.'未完成';
            }
            if($status == '已发货'){
                $status = '已付款,已发货,未完成';
            }
            if($status == '未发货'){
                $status = '已付款,未发货,未完成';
            }
            if($status == '已完成'){
                $status = '已付款,已发货,已完成';
            }
            if($status == '已关闭'){
                $status = $record_code[0].','.$record_code[1].','.'已关闭';
            }

            $ret = $this->db->Query("UPDATE `@#_member_go_record` SET `status`='$status',`company` = '$company',`company_code` = '$company_code',`company_money` = '$company_money' where id='$code'");
            if($ret){
                _message("更新成功");
            }else{
                _message("更新失败");
            }
        }

        System::load_sys_fun("user");
        $uid= $record['uid'];
        $user = $this->db->GetOne("select * from `@#_member` where `uid` = '$uid'");
        $go_time = $record['time'];
        if($record['address']){
            $user_dizhi = $this->db->GetOne("select * from `@#_member_dizhi` where `uid` = '$uid' and `id` = '{$record['address']}'");
        }else{
            $user_dizhi = $this->db->GetOne("select * from `@#_member_dizhi` where `uid` = '$uid' and `default` = 'Y'");
        }
        include $this->tpl(ROUTE_M,'dingdan.code');
    }
	/*public function get_dingdan(){

		$code=abs(intval($this->segment(4)));

		$record=$this->db->GetOne("SELECT * FROM `@#_member_go_record` where `id`='$code'");

		if(!$record)_message("参数不正确!");

		

		if(isset($_POST['submit'])){
			$record_code =explode(",",$record['status']);
			$status = $_POST['status'];
			$company = $_POST['company'];
			$company_code = $_POST['company_code'];
			$company_money = floatval($_POST['company_money']);
			$code = abs(intval($_POST['code']));
			if(!$company_money){
				$company_money = '0.01';
			}else{
				$company_money = sprintf("%.2f",$company_money);
			}
			if($status == '未完成'){
				$status = $record_code[0].','.$record_code[1].','.'未完成';		
			}
			if($status == '已发货'){
				$status = '已付款,已发货,待收货';
			}
			if($status == '未发货'){
				$status = '已付款,未发货,未完成';
			}
			if($status == '已完成'){
				$status = '已付款,已发货,已完成';	
			}
			if($status == '已作废'){
				$status = $record_code[0].','.$record_code[1].','.'已作废';				
			}			
			$ret = $this->db->Query("UPDATE `@#_member_go_record` SET `status`='$status',`company` = '$company',`company_code` = '$company_code',`company_money` = '$company_money' where id='$code'");
			if($ret){
				//调用发货通知
				if(_cfg("sendmobile")){
					//如果没有中奖短信就强制在发送一遍--E
					$data = $this->send_wx_ship_code($record['shopid']);
					if($data){
						$wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");// 获取token
						$access_token= get_token($wechat['appid'],$wechat['appsecret']);
						$postUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
						$this->https_request($postUrl,$data);
					}
				}
				_message("更新成功");
			}else{
				_message("更新失败");
			}
		}
		System::load_sys_fun("user");
		$uid= $record['uid'];
		$user = $this->db->GetOne("select * from `@#_member` where `uid` = '$uid'");
		$user_dizhi = $this->db->GetOne("SELECT * FROM `@#_member_dizhi` where `uid` = '$uid' ORDER BY `default`  DESC LIMIT 1");
		$go_time = $record['time'];
		include $this->tpl(ROUTE_M,'dingdan.code');	
	}*/
	//订单搜索
	public function select(){
		$record = '';
		if(isset($_POST['codesubmit'])){
			$code = htmlspecialchars($_POST['text']);		
			$record = $this->db->GetList("SELECT * FROM `@#_member_go_record` where `code` = '$code'");	
		}
		if(isset($_POST['usersubmit'])){	
			if($_POST['user'] == 'uid'){
				$uid = intval($_POST['text']);
				$record = $this->db->GetList("SELECT * FROM `@#_member_go_record` where `uid` = '$uid'");	
			}
		}
		if(isset($_POST['shopsubmit'])){
			if($_POST['shop'] == 'sid'){
				$sid = intval($_POST['text']);
				$record = $this->db->GetList("SELECT * FROM `@#_member_go_record` where `shopid` = '$sid'");
			}
			if($_POST['shop'] == 'sname'){
				$sname= htmlspecialchars($_POST['text']);
				$record = $this->db->GetList("SELECT * FROM `@#_member_go_record` where `shopname` = '$sname'");
			}
		}
		if(isset($_POST['timesubmit'])){
				$start_time = strtotime($_POST['posttime1']) ? strtotime($_POST['posttime1']) : time();				
				$end_time   = strtotime($_POST['posttime2']) ? strtotime($_POST['posttime2']) : time();
				$record = $this->db->GetList("SELECT * FROM `@#_member_go_record` where `time` > '$start_time' and `time` < '$end_time'");
		}
		include $this->tpl(ROUTE_M,'dingdan.soso');	
	}
	//私有方法保存菜单
	private function https_request($url,$data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return json_decode($output);
	}

	//发送发货通知
	private function send_wx_ship_code($gid=null){
		//查询模板消息id
		$template_id = $this->db->GetOne("SELECT * FROM `@#_wxch_cfg` WHERE `cfg_name` = 'template_fh'");
		if(empty($template_id['cfg_value'])){
			return false;
		}
		$info = $this->db->GetOne("SELECT * FROM `@#_shoplist` WHERE `id` = '$gid'");
		$member_band = $this->db->GetOne("SELECT * FROM `@#_member_band` WHERE `b_uid` = '{$info['q_uid']}' AND `b_type` = 'weixin'");
		if(empty($member_band)){
			return false;
		}
		$orders = $this->db->GetOne("SELECT * FROM `@#_member_go_record` WHERE `uid` = '{$info['q_uid']}' AND `shopid` = '{$info['id']}'");
		if(!empty($member_band['b_code'])){
			//发送数据组合
			$data = array(
				"touser" => $member_band['b_code'],
				"template_id"=>$template_id['cfg_value'],
				"url"=>WEB_PATH."/mobile/mobile/dataserver/".$info['id'], 
				"data" => array(
					'first' =>array(
						"value"=>"您好，您的中奖商品已经发货，请注意查收！",
						"color"=>"#173177",
						),
					"keyword1"=>array(
						"value"=>$orders['company'],
						"color"=>"#173177",
						),
					"keyword2"=>array(
						"value"=>$orders['company_code'],
						"color"=>"#173177",
						),
					"keyword3"=>array(
						"value"=>$info['title'],
						"color"=>"#173177",
						),
					"keyword4"=>array(
						"value"=>_cfg("web_name"),
						"color"=>"#173177",
						),
					"keyword5"=>array(
						"value"=>_cfg("cell"),
						"color"=>"#173177",
						),
					"remark"=>array(
						"value"=>"本订单由"._cfg("web_name")."提供发货及售后服务,感谢您的支持",
						"color"=>"#173177",
						),
				),
			);
		}
		return json_encode($data);
	}

}