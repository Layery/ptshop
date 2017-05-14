<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_class('base','member','no');
System::load_app_fun('my');
System::load_app_fun('user');
System::load_sys_fun('user');

class activity extends base {

	public function __construct() {
		parent::__construct();
		$this->db=System::load_sys_class('model');
        $this->conf = System::load_app_config("connect",'','api');
        _setcookie('homePage',json_encode(1),'');
	}
    //活动首页17.01.10
    public function activityhome (){
        //查询轮播图
        $banner = $this->db->GetList("select * from `@#_wap` WHERE `where_is`=0 ORDER BY `id` ASC ");
        //查询活动分类
        $act_category = $this->db->GetList("select * from `@#_act_category` WHERE `parentid`=0 AND `c_is_show`=1 ORDER BY `c_sort` DESC ");
        $act_category = array_chunk($act_category,8);
        $time = time();
        //查询活动
        $activities = $this->db->GetList("select * from `@#_activity` WHERE `act_active`=1 AND `act_start_time` > $time ORDER BY (`act_start_time`-$time) ASC ",array('key'=>'act_id'));
        //p($chargeInfo);exit;
        include templates("mobile/act","activityhome");
    }

    //活动列表页 2016.12.6
    public function activitylists(){
        $cid = intval($this->segment(4));
        $time = time();
        $where = '';
        if(empty($cid)){
            $where = "`act_active`=1 AND `act_start_time` >= $time ORDER BY (`act_start_time`-$time) ASC";
        }else{
            $where = "`act_active`=1 AND `act_start_time` >= $time AND find_in_set($cid,`act_category`) ORDER BY (`act_start_time`-$time) ASC";
        }
        //查询分类信息
        $act_category = $this->db->GetList("select * from `@#_act_category` WHERE `parentid`=0 AND `c_is_show`=1 ORDER BY `c_sort` DESC ");
        //p($where);exit;
        $activities = $this->db->GetList("select * from `@#_activity` WHERE".$where,array('key'=>'act_id'));
        //p($activities);exit;
        $filter = $this->db->GetList("select * from `@#_act_attr` WHERE 1 ORDER BY `sort` DESC limit 2");
        $location = $filter[0];
        $strength = $filter[1];
        $location['value'] = explode(',',$location['value']);
        $strength['value'] = explode(',',$strength['value']);
        include templates("mobile/act","activitylists");
    }
    /**活动首页的获取活动的ajax
     * ajax 没有筛选条件获取活动内容
     */
    public function ajaxGetActList(){
        //p($_POST);exit;
        $pageNum = isset($_POST['pageNum']) && !empty($_POST['pageNum'])?intval($_POST['pageNum']):1; //需要加载第几页
        $pageRow = isset($_POST['pageRow']) && !empty($_POST['pageRow'])?intval($_POST['pageRow']):10; //需要加载几行记录
        $time = time();
        $where = "`act_active`=1 AND `act_start_time` > $time ORDER BY (`act_start_time`-$time) ASC,`act_charge` ASC";
        //查询总记录数
        $count = $this->db->GetOne("select COUNT(*) as count from `@#_activity` WHERE $where");
        $offset = ($pageNum - 1) * $pageRow; //偏移量（mysql重0开始）
        //根据偏移量和每页记录数查询活动信息
        $activityInfo = $this->db->GetList("select * from @#_activity WHERE $where limit $offset,$pageRow");
        //p($count);
        foreach ($activityInfo as $k => $v){
            $title_tag = '';
            switch(date('w',$v['act_start_time'])){
                case 0:
                    $title_tag = '【周日】';
                    break;
                case 1:
                    $title_tag = '【周一】';
                    break;
                case 2:
                    $title_tag = '【周二】';
                    break;
                case 3:
                    $title_tag = '【周三】';
                    break;
                case 4:
                    $title_tag = '【周四】';
                    break;
                case 5:
                    $title_tag = '【周五】';
                    break;
                case 6:
                    $title_tag = '【周六】';
                    break;
            }
            $activityInfo[$k]['act_title'] = $title_tag.$v['act_title'];
            $activityInfo[$k]['start_time'] = date('m',$v['act_start_time']).'月'.date('d',$v['act_start_time']).'日';
            if($v['act_charge']=='0.00'){
                $charge = '免费';
            }else{
                $charge = $v['act_charge'];
            }
            $activityInfo[$k]['charge'] = $charge;
        }
        $pages = ceil($count['count']/$pageRow);
        if($pageNum <= $pages){ //要加载的页数和总页数相比较
            $activityInfo[0]['pageNum'] = $pageNum + 1;
        }
        $activityInfo[0]['pageSum'] = $pages;
        echo json_encode($activityInfo);
        exit;
        //p($activityInfo);exit;
    }
    /**活动列表的ajax
     * ajax 根据筛选条件获取对应的活动内容
     */
    public function ajaxGetAct(){
        //p($_POST);exit;
        $pageNum = isset($_POST['pageNum']) && !empty($_POST['pageNum'])?intval($_POST['pageNum']):1; //需要加载第几页
        $pageRow = isset($_POST['pageRow']) && !empty($_POST['pageRow'])?intval($_POST['pageRow']):10; //需要加载几行记录
        $cid = isset($_POST['cid'])?intval($_POST['cid']):0; //活动分类
        $type = isset($_POST['type']) && !empty($_POST['type'])?safe_replace($_POST['type']):'all'; //筛选类别
        $filter = isset($_POST['filter']) && !empty($_POST['filter'])?safe_replace($_POST['filter']):''; //筛选条件
        $filter1 = isset($_POST['filter1']) && !empty($_POST['filter1'])?safe_replace($_POST['filter1']):''; //可选筛选条件
        //根据筛选条件组装mysql判断语句
        $where = '';
        $cat_filter = '';
        $time = time();
        if($cid == 0){
            $cat_filter = 1;
        }else{
            $cat_filter = "find_in_set($cid,`act_category`)";
        }
        if($type == 'location' || $type == 'strength'){
            $type = 'both';
        }
        switch ($type){
            case 'price':
                if($filter == 0){ //递减
                    $where = "$cat_filter AND `act_active`=1 AND `act_start_time`>=$time ORDER BY `act_charge` DESC";
                }else{
                    $where = "$cat_filter AND `act_active`=1 AND `act_start_time`>=$time ORDER BY `act_charge` ASC";
                }
                break;
            case 'time':
                $filter = strtotime($filter);
                $where = "$cat_filter AND `act_active`=1 AND `act_start_time`>=$filter AND `act_end_time`> $time ORDER BY `act_charge` ASC";
                break;
            case 'both':
                //获取活动id
                $filter1 = intval($filter1);
                $act_ids = $this->db->GetList("select `act_id` from `@#_act_filter` WHERE `attr_id`=$filter1 AND `attr_value`='$filter'");
                foreach ($act_ids as $val){
                    $actId[] = $val['act_id'];
                }
                $actId = implode(',',$actId);
                if(empty($actId)){
                    $actId = 0;
                }
                //查询所有的id
                $where = "$cat_filter AND `act_active`=1 AND `act_start_time`>=$time AND `act_id` IN ($actId) ORDER BY `act_charge` ASC";
                break;
            default:
                $where = "$cat_filter AND `act_active`=1 AND `act_start_time`>=$time";
        }
        //查询活动总数
        $count = $this->db->GetOne("select COUNT(*) as count from `@#_activity` WHERE $where");
        $offset = ($pageNum - 1) * $pageRow; //偏移量（mysql重0开始）
        //查询活动
        $activityInfo = $this->db->GetList("select * from `@#_activity` WHERE $where limit $offset,$pageRow");
        if(!$activityInfo){
            $activityInfo[0]['pageSum'] = 0;
        }
        foreach ($activityInfo as $k => $v){
            $title_tag = '';
            switch(date('w',$v['act_start_time'])){
                case 0:
                    $title_tag = '【周日】';
                    break;
                case 1:
                    $title_tag = '【周一】';
                    break;
                case 2:
                    $title_tag = '【周二】';
                    break;
                case 3:
                    $title_tag = '【周三】';
                    break;
                case 4:
                    $title_tag = '【周四】';
                    break;
                case 5:
                    $title_tag = '【周五】';
                    break;
                case 6:
                    $title_tag = '【周六】';
                    break;
            }
            $activityInfo[$k]['act_title'] = $title_tag.$v['act_title'];
            $activityInfo[$k]['start_time'] = date('m',$v['act_start_time']).'月'.date('d',$v['act_start_time']).'日';
            if($v['act_charge']=='0.00'){
                $charge = '免费';
            }else{
                $charge = $v['act_charge'];
            }
            $activityInfo[$k]['charge'] = $charge;
        }
        $pages = ceil($count['count']/$pageRow);
        if($pageNum <= $pages){ //要加载的页数和总页数相比较
            $activityInfo[0]['pageNum'] = $pageNum + 1;
        }
        $activityInfo[0]['pageSum'] = $pages;
        //p($activityInfo);exit;
        echo json_encode($activityInfo);
        exit;
    }
    //活动详情页 2016.11.30
    public function activity(){
        //$str['age'] = 12;
        //$str['name'] = '辰枫';
        //$str = json_encode($str);
        //echo $str;
        //前一天12点strtotime('-1 day 12:00:00', $now);
        //获取活动id
        $act_id = intval($this->segment(4));
        $shareUid = intval($this->segment(5));  //分享的用户id
        $time = time();
        parent::__construct();
        //p($this->userinfo);
        $uid = 0;
        if($this->userinfo){
            //用来判断用户是否已登录且该活动是否已报名
            $uid = $this->userinfo['uid'];
            $res = $this->db->GetOne("select * from `@#_act_order` WHERE `o_act_id`=$act_id AND `o_uid`=$uid AND `o_status`='已支付'");
            //var_dump($res);
            if($res){
                $flag = 1;
            }else{
                $flag = 0;
            }
            //分享消费返积分
            if($shareUid == $uid){
                $shareUid = 0;  //排除自己
            }
        }else{
            $flag = 0;
        }
        $webname=$this->_cfg['web_name'];
        //获取应用和key
        $appIdArr = $this->db->GetList("select `id` from `@#_map_config` WHERE `on_off`=1",array('key'=>'id'));
        $appId= array_rand($appIdArr,1);
        $appInfo = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$appId AND `on_off`=1");
        //查询活动
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$act_id");
        $signNum = $this->db->GetList("select `o_photo` from `@#_act_order` WHERE `o_act_id`=$act_id AND `o_status`='已支付'");
        //查询活动须知
        $notice = $this->db->GetList("select * from `@#_act_notice` WHERE `n_act_id`=$act_id ORDER BY `n_id` ASC ");
        //查询阶梯团
        if($activity['act_is_group']){
            $price_step = $this->db->GetList("select * from `@#_act_step` WHERE `act_id`=$act_id ORDER BY `id` ASC ");
            $ele = array(
                'num'=>0,
                'money'=>$activity['act_charge']
            );
            array_unshift($price_step,$ele); //从开头插入一个数组
        }
        //查询分享信息
        $shareInfo = $this->db->GetOne("select * from `@#_item_share` WHERE `type`=1 AND `item_id`=$act_id");
        $content = htmlspecialchars_decode($activity['act_content']);

        //判断活动截止报名时间
        $stopTime = strtotime('-1 day 17:00:00',$activity['act_start_time']);
        if($time >= $stopTime && $time < $activity['act_start_time']){
            $flag = 2;  //截止报名
        }
        $signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true); //报名信息
        $actInfo = json_decode(stripslashes(_getcookie('actInfo')),true); //支付返回的信息
        if(!$actInfo){
            $actInfo = 0;
        }
        _setcookie("act_id",null); //清除支付返回的活动id
        _setcookie("actInfo",null); //清除支付返回的活动信息

        //分享部分代码
        require_once("system/modules/mobile/jssdk.php");
        $wechat= $this->db->GetOne("select * from `@#_wechat_config` where id = 1");
        $jssdk = new JSSDK($wechat['appid'],$wechat['appsecret']);
        $signPackage = $jssdk->GetSignPackage();

        include templates("mobile/act","activity");
    }

    /**
     * 我的活动 17.02.28
     */
    public function myactivities(){
        parent::__construct ();
        if (!$member = $this->userinfo) {
            //header ( "location: " . WEB_PATH . "/mobile/user/login"); //改为正常登录
            header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
            exit;
        }
        $state = intval($this->segment(4))? intval($this->segment(4)):-1;
        include templates("mobile/act","myactivities");
    }
    /**
     * ajax获取我的订单
     */
    public function ajaxGetOrder(){
        parent::__construct ();
        $member = $this->userinfo;
        $uid = $member['uid'];
        $state = isset($_POST['state']) && !empty($_POST['state'])? intval($_POST['state']):-1;
        $pageNum = isset($_POST['pageNum']) && !empty($_POST['pageNum'])?intval($_POST['pageNum']):1; //需要加载第几页
        $pageRow = isset($_POST['pageRow']) && !empty($_POST['pageRow'])?intval($_POST['pageRow']):10; //需要加载几行记录
        $time = time();
        //$where = "`act_active`=1 AND `act_start_time` > $time ORDER BY (`act_start_time`-$time) ASC,`act_charge` ASC";
        $where = '';
        switch ($state){
            case 1:
                $where = "`o_status`='已支付' and `act_start_time`>$time and `o_uid`=$uid";
                $tag = '等待中';
                $flag = 1;
                break;
            case 2:
                $where = "`o_status`='已支付' and `act_start_time`<=$time and `act_end_time`>$time and `o_uid`=$uid";
                $tag = '进行中';
                $flag = 2;
                break;
            case 3:
                $where = "`o_status`='已支付' and `act_end_time`<$time and `o_uid`=$uid";
                $tag = '已结束';
                $flag = 2;
                break;
            case 4:
                $where = "`o_status`='已关闭' and `o_uid`=$uid";
                $tag = '已关闭';
                $flag = 3;
                break;
            default:
                $where = "`o_uid`=$uid";
        }
        //查询总记录数
        $count = $this->db->GetOne("select COUNT(*) as count from `@#_act_order` a LEFT JOIN `@#_activity` b on a.o_act_id = b.act_id WHERE $where");
        //var_dump($count);exit;
        $offset = ($pageNum - 1) * $pageRow; //偏移量（mysql重0开始）
        //根据偏移量和每页记录数查询活动信息
        $orderInfo = $this->db->GetList("select * from `@#_act_order` a LEFT JOIN `@#_activity` b on a.o_act_id = b.act_id WHERE $where limit $offset,$pageRow");
        foreach ($orderInfo as $k => $v){
            //截止报名不予退款按钮
            $stopTime = strtotime('-1 day 17:00:00',$v['act_start_time']);
            if($time >= $stopTime){
                $invalid = 0; //不可申请退款
            }else{
                if($v['o_status'] == '已支付' && $v['o_money'] != 0.00){
                    //判断是否已经申请退款，
                    $refundInfo = $this->db->GetOne("select `o_id` from `@#_act_order` WHERE `o_id`={$v['o_id']} AND `o_uid`={$member['uid']} AND `o_refund_status`=1 AND `o_refund_flag`=1");
                    //var_dump($refundInfo);
                    if(!$refundInfo){
                        $invalid = 1; //可申请退款
                    }else{
                        $invalid = 2; //已申请退款，查看进度
                    }
                }else{
                    $invalid = 0; //不可申请退款
                }
            }
            //判断标志
            if($state == -1){
                if((int)$v['act_start_time'] > $time && $v['o_status'] != '已关闭'){
                    $tag = '等待中';
                    $flag = 1;
                }elseif ($v['act_start_time']<=$time && $v['act_end_time']>$time){
                    $tag = '进行中';
                    $flag = 2;
                }elseif ($v['act_end_time']<$time){
                    $tag = '已结束';
                    $flag = 2;
                }else{
                    $tag = '已关闭';
                    $flag = 3;
                }
            }
            $orderInfo[$k]['tag'] = $tag;
            $orderInfo[$k]['flag'] = $flag;
            $orderInfo[$k]['invalid'] = $invalid;
            $orderInfo[$k]['start_time'] = date('Y-m-d H:i',$v['act_start_time']).'开始';
            if($v['act_charge']=='0.00'){
                $charge = '免费';
            }else{
                $charge = $v['act_charge'];
            }
            $orderInfo[$k]['charge'] = $charge;
        }
        $pages = ceil($count['count']/$pageRow);
        if($pageNum <= $pages){ //要加载的页数和总页数相比较
            $orderInfo[0]['pageNum'] = $pageNum + 1;
        }
        $orderInfo[0]['pageSum'] = $pages;
        echo json_encode($orderInfo);
        exit;
    }


   /* //查看并导出报名信息页 2016.12.2
    public function checkinfos(){
        $webname=$this->_cfg['web_name'];

        include templates("mobile/act","checkinfos");
    }

    //报名信息列表
    public function infolists(){
        $state = safe_replace($this->segment(4));
        $time = time();
        $where = '';
        switch ($state){
            case 'all':
                $where = "`act_end_time`>$time";
                break;
            case 'time':
                $where = "`act_end_time`>$time order by `act_time` desc";
                break;
            case 'price':
                $charge = $this->db->GetList("select * from `@#_act_charge` WHERE 1",array('key'=>'c_id'));
                $charge_temp = array();
                foreach ($charge as $k => $v){
                    $charge_temp[$k] = $v['c_money'];
                }
                asort($charge_temp);
                $charge_keys = implode(',',array_keys($charge));
                $where = "`act_id` in ($charge_keys) and `act_end_time`>$time";
                break;
            default:
                $where = "`act_end_time`>$time";
        }
        $chargeInfo = $this->db->GetList("select * from `@#_act_charge` WHERE 1",array('key'=>'c_act_id'));
        $activities = $this->db->GetList("select * from `@#_activity` WHERE $where",array('key'=>'act_id'));
        include templates("mobile/act","infolists");
    }*/

    //活动支付失败页 2016.12.9

    public function payfailed (){

        include templates("mobile/index","payfailed");
    }

    //查看退款进度
    public function refund(){
        parent::__construct ();
        if (!$member = $this->userinfo) {
            session_start();
            $_SESSION['prevUrl'] = $_SERVER['REQUEST_URI']; //保存登录前的页面
            header ( "location: " . WEB_PATH . "/mobile/user/login"); //改为正常登录
            //header ( "location: " . WEB_PATH . "/api/wxloginsy" ); //改为微信登录
            exit;
        }
        //获取订单id
        $order_id = intval($this->segment(4));
        if(!empty($order_id)){
            //查询订单，可能会查询微信退款进度
            $member = $this->userinfo;
            $orderInfo = $this->db->GetOne("select * from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE a.`o_id`=$order_id AND a.`o_uid`={$member['uid']}");
            $sys_mobile = $this->db->GetOne("select `name`,`value` from `@#_config` WHERE `name`='cell'");
        }
        include templates("mobile/act","refund");
    }
    //撤销退款 17.03.08
    public function ajaxCancelRefund(){
        $member = $this->userinfo;
        $order_id = intval($_GET['order_id']);
        $time = time();
        if(empty($order_id)){
            $res['state'] = 1;
            $res['msg'] = '参数错误';
            exit(json_encode($res));
        }
        //查询退款订单
        $refundInfo = $this->db->GetOne("select * from `@#_act_order` WHERE `o_id`=$order_id AND `o_uid`={$member['uid']} AND `o_refund_status`=1 AND `o_refund_flag`=1");
        if(!$refundInfo){
            $res['state'] = 1;
            $res['msg'] = '退款订单无效';
            exit(json_encode($res));
        }
        //开始撤销退款
        $this->db->Autocommit_start();
        $up_order = $this->db->Query("update `@#_act_order` set `o_refund_status`=0,`o_refund_flag`=0 WHERE `o_status`='已支付' AND `o_id`=$order_id");
        $status_desc = '撤销申请退款';
        $insert_log = $this->db->Query("insert into `@#_act_refund_log` (`order_id`,`action_user`,`order_status`,`refund_status`,`refund_type`,`action_time`,`status_desc`) VALUES ($order_id,{$member['uid']},'{$refundInfo['o_status']}',0,0,$time,'$status_desc')");
        if($up_order && $insert_log){
            $this->db->Autocommit_commit();
            $res['state'] = 0;
            $res['msg'] = '撤销成功';
            exit(json_encode($res));
        }else{
            $this->db->Autocommit_rollback();
            $res['state'] = 1;
            $res['msg'] = '撤销失败，请重新撤销';
            exit(json_encode($res));
        }

    }
    
    //活动申请退款 17.03.06
    public function refundrequest(){
        $member = $this->userinfo;
        //获取订单id
        $order_id = intval($this->segment(4));
        //查询订单和活动信息
        $orderInfo = $this->db->GetOne("select * from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE a.`o_id`=$order_id AND a.`o_uid`={$member['uid']}");
        //var_dump($orderInfo);exit;
        //计算退款金额
        $time = time();
        $start_time = $orderInfo['act_start_time'];
        $middleTime = strtotime('-1 day 12:00:00',$start_time); //中午12点时间
        $stopTime = strtotime('-1 day 17:00:00',$start_time);  //截止报名时间
        if($time < $middleTime){
            $refund_fee = $orderInfo['o_refund'];
        }elseif ($time >= $middleTime && $time < $stopTime){
            $refund_fee = $orderInfo['o_refund'] - $orderInfo['act_fare'];
        }else{
            $refund_fee = 0.00;
        }
        include templates("mobile/act","refundrequest");
    }
    //活动退款进行ajax处理判断
    public function ajaxRefund(){
        $member = $this->userinfo;
        $order_id = intval($_POST['refund_id']);
        $fee = $_POST['refund_fee']; // 退款金额
        if(empty($order_id) || empty($fee)){
            $res['state'] = 1;
            $res['msg'] = '退款订单参数错误';
            exit(json_encode($res));
        }
        //查询活动订单以及订单信息
        $infos = $this->db->GetOne("select * from `@#_act_order` a LEFT JOIN `@#_activity` b ON a.`o_act_id`=b.`act_id` WHERE a.`o_id`=$order_id AND a.`o_uid`={$member['uid']}");
        if(!$infos){
            $res['state'] = 1;
            $res['msg'] = '退款订单无效';
            exit(json_encode($res));
        }
        //查询是否已申请退款
        $refundInfo = $this->db->GetOne("select `o_id` from `@#_act_order` WHERE `o_id`=$order_id AND `o_uid`={$member['uid']} AND `o_refund_status`=1 AND `o_refund_flag`=1");
        if($refundInfo){
            $res['state'] = 1;
            $res['msg'] = '已申请退款';
            exit(json_encode($res));
        }
        $time = time();
        $start_time = $infos['act_start_time'];
        $middleTime = strtotime('-1 day 12:00:00',$start_time); //中午12点时间
        $stopTime = strtotime('-1 day 17:00:00',$start_time);  //截止报名时间
        if($time < $middleTime){
            $refund_fee = $infos['o_refund'];
        }elseif ($time >= $middleTime && $time < $stopTime){
            $refund_fee = $infos['o_refund'] - $infos['act_fare'];
        }else{
            $refund_fee = 0.00;
        }
        if($refund_fee != $fee || $refund_fee == 0.00){
            $res['state'] = 1;
            $res['msg'] = '退款订单无效';
            exit(json_encode($res));
        }
        //更新订单、日志、发送消息模板通知商户进行处理$this->db->Autocommit_commit();$this->db->Autocommit_rollback();
        $this->db->Autocommit_start();
        $up_order = $this->db->Query("update `@#_act_order` set `o_refund_status`=1,`o_refund_flag`=1 WHERE `o_status`='已支付' AND `o_id`=$order_id");
        $status_desc = '申请退款';
        $insert_log = $this->db->Query("insert into `@#_act_refund_log` (`order_id`,`action_user`,`order_status`,`refund_status`,`refund_type`,`action_time`,`status_desc`) VALUES ($order_id,{$member['uid']},'{$infos['o_status']}',1,1,$time,'$status_desc')");
        if($up_order && $insert_log){
            $this->db->Autocommit_commit();
            //发送消息模板
            $config = System::load_app_config('connect','','api');
            $appid = $config['weixin']['id'];
            $appsecret = $config['weixin']['key'];
            //1.获取access_token
            $access_token = get_token($appid,$appsecret);
            //查询模板消息id
            $openid = $this->db->GetOne("SELECT * FROM `@#_wxch_cfg` WHERE `cfg_name` = 'openid'");
            $template_re = $this->db->GetOne("SELECT * FROM `@#_wxch_cfg` WHERE `cfg_name` = 'template_re'");
            $template_id = $template_re['cfg_value'];
            $touser = $openid['cfg_value'];
            $url = '';
            //2.组装数组
            $template = array(
                'touser'=>$touser,
                'template_id'=>$template_id,
                'url'=>$url,
                'topcolor'=>'#7B68EE',
                'data'=>array(
                    'first'=>array('value'=>urlencode('您有一笔退款申请，请及时处理'),'color'=>'#2B2A2A'),
                    'orderProductPrice'=>array('value'=>urlencode('¥'.$refund_fee),'color'=>'#2B2A2A'),
                    'orderProductName'=>array('value'=>urlencode($infos['act_title']),'color'=>'#2B2A2A'),
                    'orderName'=>array('value'=>urlencode($infos['o_code']),'color'=>'#2B2A2A'),
                    'remark'=>array('value'=>urlencode('如有疑问，请登录 后台->活动管理->订单列表->详细 进行操作'),'color'=>'#2B2A2A'),
                )
            );
            //echo '<pre>';
            //var_dump($template);exit;
            $data = urldecode(json_encode($template)); //转json数据
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
            //4.调用curl函数
            $res = http_request($url,$data);
            $res = json_decode($res,true);
            $res['state'] = 0;
            $res['msg'] = '申请成功';
            exit(json_encode($res));
        }else{
            $this->db->Autocommit_rollback();
            $res['state'] = 1;
            $res['msg'] = '申请失败，请重新申请';
            exit(json_encode($res));
        }

    }

    //活动支付页 2017.2.23
    public function pay(){
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
        $signInfo = json_decode(stripslashes(_getcookie('SignInfo')),true); //获取报名信息
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`={$signInfo['actId']}");
        //查询积分兑换
        $integral = $this->db->GetOne("select `value` from `@#_config` WHERE `name`='integral'");
        //point < maxpoint ? point*rate : maxpoint*rate);
        $user_integral = $member['score']; //积分
        $limit_discount = $activity['integral']; //金额
        $user_discount = bcdiv($user_integral,$integral['value'],2);
        $discount = $user_discount<$limit_discount ? $user_discount : $limit_discount; //用户积分兑换的金额与该活动的限制抵现金额相比

        //p($member);exit;
        include templates("mobile/act","payment");
    }

}
?>