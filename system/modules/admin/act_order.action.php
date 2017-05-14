<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2017/3/1
 * Time: 9:30
 */
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
System::load_app_class("tocode","pay",'no');
System::load_sys_fun("user");
System::load_app_fun("pay","pay");
//定义活动订单处理类
class act_order extends admin {
    private $db;
    private $orderInfo;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("lists", "订单列表", ROUTE_M . '/' . ROUTE_C . "/lists"),
            array("signList","报名列表",ROUTE_M.'/'.ROUTE_C."/signList"),
        );
    }
    /**
     * 订单列表
     */
    public function lists(){
        $where = 1;
        if(isset($_POST['search'])){
            //p($_POST);
            $order_sn = isset($_POST['order_sn'])?safe_replace(trim($_POST['order_sn'])):'';
            $startTime = isset($_POST['startTime'])?strtotime($_POST['startTime']):'';  //下单时间
            $endTime = isset($_POST['endTime'])?strtotime($_POST['endTime']):'';  //下单时间
            $order_status = isset($_POST['order_status'])?safe_replace($_POST['order_status']):0;
            $refund_status = isset($_POST['refund_status'])?intval($_POST['refund_status']):-1;
            //增加搜索条件
            $where_sn = !empty($order_sn) ? " and `o_code` = '$order_sn'" : '';
            $where_start = !empty($startTime) ? " and `o_time` >= $startTime" : '';
            $where_end = !empty($endTime) ? " and `o_time` <= $endTime" : '';
            $where_order = !empty($order_status) ? " and `o_status` = '$order_status'" : '';
            $where_refund = $refund_status != -1 ? " and `o_refund_status` = $refund_status" : '';
            //拼接搜索条件
            $where .= $where_sn.$where_start.$where_end.$where_order.$where_refund;
            //echo $where;exit;
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_act_order` WHERE $where");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $sql = "select * from `@#_act_order` WHERE $where";
        $orderInfo = $this->db->GetPage($sql);
        foreach ($orderInfo as $k => $v){
            switch ($v['o_refund_status']){
                case 0:
                    $orderInfo[$k]['refund_status'] = '未退款';
                    break;
                case 1:
                    $orderInfo[$k]['refund_status'] = '审核中';
                    break;
                case 2:
                    $orderInfo[$k]['refund_status'] = '退款中';
                    break;
                case 3:
                    $orderInfo[$k]['refund_status'] = '已退款';
                    break;
            }
        }
        //p($orderInfo);exit;
        //echo ROUTE_M.'<br>'.ROUTE_C.'<br>'.ROUTE_A;
        include $this->tpl(ROUTE_M,'act_order.lists');
    }
    /**
     * 查看订单
     */
    public function detail(){
        //查询订单
        $oid = intval($this->segment(4));
        $orderInfo = $this->db->GetOne("select * from `@#_act_order` WHERE `o_id`=$oid");
        switch ($orderInfo['o_refund_status']){
            case 0:
                $orderInfo['refund_status'] = '未退款';
                break;
            case 1:
                $orderInfo['refund_status'] = '审核中';
                break;
            case 2:
                $orderInfo['refund_status'] = '退款中';
                break;
            case 3:
                $orderInfo['refund_status'] = '已退款';
                break;
        }
        //查询报名信息
        $signInfo = $this->db->GetOne("select * from `@#_act_sign` WHERE `s_id`={$orderInfo['o_sid']}");
        //p($signInfo);exit;
        //查询活动信息
        $actInfo = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`={$orderInfo['o_act_id']}");
        /**
         * 查询可执行的订单状态
         * 未付款 -> 可设置为已付款/已关闭
         * 已付款&审核中 -> 可以设置同意退款（状态为退款中）开始执行退款，退款后自动更改退款状态为已退款
         * 已关闭/活动结束 -> 可删除活动及相关的信息
         * 状态可能不全，后期可更改
         */
        $action_log = $this->db->GetList("select * from `@#_act_refund_log` WHERE `order_id`={$orderInfo['o_id']} ORDER BY `action_time` DESC ");
        $time = time();
        $status = $orderInfo['o_status']; //订单状态：未支付，已支付，已关闭
        $refund = $orderInfo['o_refund_status']; //退款状态
        $refund_type = $orderInfo['o_refund_flag']; //退款类型：手动、自动
        $btn = array();
        switch ($status){
            case '未支付':
                if($actInfo['act_start_time']>=$time){
                    $btn['pay'] = '设为已支付';
                    $btn['close'] = '设为已关闭';
                }
                if($actInfo['act_end_time']<$time){
                    $btn['remove'] = '移除';
                }
                break;
            case '已支付':
                if($actInfo['act_start_time']>=$time){
                    $btn['pay_cancel'] = '设为未支付';
                }
                if($refund == 1 && $refund_type = 1){
                    $btn['confirm_refund'] = '同意退款';
                }
                if($actInfo['act_end_time']<$time){
                    $btn['remove'] = '移除';
                }
                break;
            case '已关闭':
                $btn['remove'] = '移除';
                break;
        }
        //查询管理员
        $CheckId=_encrypt(_getcookie("AID"),'DECODE');
        $info=$this->db->GetOne("SELECT * FROM `@#_admin` WHERE `uid` = '$CheckId'");
        //实际退款金额
        //查询退款日志的操作时间
        $log = $this->db->GetOne("select * from `@#_act_refund_log` WHERE `order_id`=$oid AND `refund_status`=1 AND `refund_type`=1 ORDER BY `action_time` DESC limit 1");
        $time = $log['action_time']; //订单下的最新退款申请
        $start_time = $actInfo['act_start_time'];
        $middleTime = strtotime('-1 day 12:00:00',$start_time); //中午12点时间
        $stopTime = strtotime('-1 day 17:00:00',$start_time);  //截止报名时间
        if($time < $middleTime){
            $refund_fee = $orderInfo['o_refund'];
        }elseif ($time >= $middleTime && $time < $stopTime){
            $refund_fee = $orderInfo['o_refund'] - $actInfo['act_fare'];
        }else{
            $refund_fee = 0.00;
        }
        //p($btn);exit;
        include $this->tpl(ROUTE_M,'act_order.detail');
    }

    /**
     * ajax 订单可执行操作
     */
    public function order_action(){
        $order_id = intval($_POST['order_id']);
        $action_type = safe_replace($_POST['type']);
        $res = array();
        if(empty($order_id)){
            $res['state'] = 1;
            $res['msg'] = '订单参数不能为空';
            exit(json_encode($res));
        }
        //查询订单是否存在
        $this->db->Autocommit_start();
        $this->orderInfo = $orderInfo = $this->db->GetOne("select * from `@#_act_order` WHERE `o_id`=$order_id");
        if(!$orderInfo){
            $res['state'] = 1;
            $res['msg'] = '订单不存在';
            exit(json_encode($res));
        }
        switch ($action_type){
            case 'pay':
                $order_status = '已支付';
                $res = $this->action_model($order_id,$order_status);
                break;
            case 'pay_cancel':
                $order_status = '未支付';
                $res = $this->action_model($order_id,$order_status);
                break;
            case 'close':
                $order_status = '已关闭';
                $res = $this->action_model($order_id,$order_status);
                break;
            case 'remove':
                $res = $this->action_del($order_id);
                break;
        }
        exit(json_encode($res));
    }

    /** ajax 对数据库执行不同的操作
     * @param $order_id  订单ID
     * @param $order_status 订单状态
     */
    public function action_model($order_id,$order_status){
        $orderInfo = $this->orderInfo;
        $up_order = $this->db->Query("update `@#_act_order` set `o_status` = '$order_status' WHERE `o_id`=$order_id");
        $up_sign = $this->db->Query("update `@#_act_sign` set `s_status` = '$order_status' WHERE `s_id`={$orderInfo['o_sid']}");
        //插入操作日志
        $res = array();
        $time = time();
        $status_desc = '设置为'.$order_status;
        $insert_log = $this->db->Query("insert into `@#_act_refund_log` (`order_id`,`action_user`,`order_status`,`refund_status`,`refund_type`,`action_time`,`status_desc`) VALUES ($order_id,0,'$order_status',{$orderInfo['o_refund_status']},{$orderInfo['o_refund_flag']},$time,'$status_desc')");
        if($up_order && $up_sign && $insert_log){
            $this->db->Autocommit_commit();
            $res['state'] = 0;
            $res['msg'] = '操作成功';
        }else{
            $this->db->Autocommit_rollback();
            $res['state'] = 1;
            $res['msg'] = '操作失败';
        }
        return $res;
    }

    /**ajax 订单详情中的移除操作订单详情
     * @param $order_id 订单ID
     */
    public function action_del($order_id){
        $orderInfo = $this->orderInfo;
        //删除订单：删除订单，删除报名信息，删除支付记录，更新报名人数
        $del_order = $this->db->Query("delete from `@#_act_order` WHERE `o_id`=$order_id");
        $del_sign = $this->db->Query("delete from `@#_act_sign` WHERE `s_id`={$orderInfo['o_sid']}");
        $up_activity = $this->db->Query("update `@#_activity` set `act_num_signed`=`act_num_signed`-1 WHERE `act_id`={$orderInfo['o_act_id']}");
        $del_record = true;
        if($orderInfo['o_payment'] != 0.00){
            $del_record = $this->db->Query("delete from `@#_member_activity_record` WHERE `ordercode`='{$orderInfo['o_code']}'");
        }
        if($del_order && $del_sign && $del_record && $up_activity){
            $this->db->Autocommit_commit();
            $res['state'] = 0;
            $res['url'] = WEB_PATH.'/admin/act_order/lists';
            $res['msg'] = '操作成功';
        }else{
            $this->db->Autocommit_rollback();
            $res['state'] = 1;
            $res['msg'] = '操作失败';
        }
        return $res;
    }

    /**
     * 删除订单：包括删除订单，用户报名信息，支付记录，活动报名人数
     */
    public function del(){
        $oid = intval($_GET['id']);
        if(empty($oid)){
            echo '参数不能为空';
            exit;
        }
        //查询订单是否存在
        $orderInfo = $this->db->GetOne("select * from `@#_act_order` WHERE `o_id`=$oid FOR UPDATE");
        if(!$orderInfo){
            echo '不存在订单';
            exit;
        }
        $this->db->Autocommit_start();
        //删除订单：删除订单，删除报名信息，删除支付记录，更新报名人数
        $del_order = $this->db->Query("delete from `@#_act_order` WHERE `o_id`=$oid");
        $del_sign = $this->db->Query("delete from `@#_act_sign` WHERE `s_id`={$orderInfo['o_sid']}");
        $up_activity = $this->db->Query("update `@#_activity` set `act_num_signed`=`act_num_signed`-1 WHERE `act_id`={$orderInfo['o_act_id']}");
        $del_record = true;
        if($orderInfo['o_payment'] != 0.00){
            $del_record = $this->db->Query("delete from `@#_member_activity_record` WHERE `ordercode`='{$orderInfo['o_code']}'");
        }
        if($del_order && $del_sign && $del_record && $up_activity){
            $this->db->Autocommit_commit();
            echo 'ok';
            exit;
        }else{
            $this->db->Autocommit_rollback();
            echo '删除失败';
            exit;
        }
    }

    /**
     * ajax 进行手动退款操作
     * 思路： ①对比实际退款和理论退款，根据支付来源进行退款，②更新订单状态，插入操作日志，③调用退款接口，④根据
     *      返回值进行更新订单状态，⑤删除报名信息，⑥
     */
    public function order_refund(){
        $order_id = intval($_POST['order_id']);
        $refund_fee = sprintf('%.2f',$_POST['refund']);
        //查询订单
        $orderInfo = $this->db->GetOne("select * from `@#_act_order` WHERE `o_id`=$order_id");
        //var_dump($orderInfo);exit;
        if(!$orderInfo){
            $res['state'] = 1;
            $res['msg'] = '不存在退款订单';
            exit(json_encode($res));
        }
        if($refund_fee > $orderInfo['o_refund'] && $refund_fee <= 0.00){
            $res['state'] = 1;
            $res['msg'] = '退款金额错误';
            exit(json_encode($res));
        }

        //判断支付来源，按支付来源进行退款；未使用MVC的模型操作数据库，很不方便
        if($orderInfo['o_payment'] == 0.00 && $orderInfo['o_pay_type'] == '余额支付'){
            //余额支付
            $balance = $refund_fee;
            $status = 1;
        }elseif ($orderInfo['o_payment'] != $orderInfo['o_refund'] && $orderInfo['o_payment'] > 0.00){
            //余额和微信支付
            $balance = $orderInfo['o_refund'] - $orderInfo['o_payment'];
            if($refund_fee <= $balance){
                $balance = $refund_fee;
                $status = 1;
            }else{
                $refund_fee = $refund_fee - $balance; //实际的退款金额
                $status = 2;
            }
        }else{
            //微信支付
            $balance = 0;
            $status = 3;
        }
        $time = time();

        if($status == 2 || $status == 3){
            //调用微信退款接口
            //根据订单号查询微信支付的流水号
            $pay_record = $this->db->GetOne("select * from `@#_member_activity_record` WHERE `uid`={$orderInfo['o_uid']} AND `status`='已支付' AND `scookies`=1 AND `ordercode`='{$orderInfo['o_code']}'");
            if($refund_fee > $pay_record['money']){
                $this->db->Autocommit_rollback();
                $res['state'] = 1;
                $res['msg'] = '退款金额超过实际支付金额';
                exit(json_encode($res));
            }
            $config = array(
                'out_trade_no'  => $pay_record['code'],
                'out_refund_no' => $orderInfo['o_code'],
                'total_fee'     => (int)($pay_record['money'] * 100),
                'refund_fee'    => (int)($refund_fee * 100),
            );
            $refundApi = System::load_app_class('refund','pay');
            //$result = $refundApi->orderQuery($config);
            $result = $refundApi->refund_wx($config);
            //echo '<pre>';
            //var_dump($result);exit;
            //$result = $refundApi->refund_wx($config);
            //$methods = get_class_methods('refund');
            //$refundApi = new refund();
            //var_dump($methods);
            //var_dump($result);exit;
            //var_dump($refundApi);exit;
            if($result['return_code'] == 'FAIL'){
                $res['state'] = 1;
                $res['msg'] = $result['return_msg'];
                exit(json_encode($res));
            }
            if($result['result_code'] == 'SUCCESS'){
                $status = 1;
            }else{
                $res['state'] = 1;
                $res['msg'] = $result['err_code_des'];
                exit(json_encode($res));
            }
        }

        $this->db->Autocommit_start();

        if($status == 1){
            //余额退款:按余额退款，并且调用微信模板；更新用户表、订单表、退款日志表、活动表的报名人数、报名表信息的支付状态
            $up_member = $this->db->Query("update `@#_member` set `money` = `money`+$balance WHERE `uid`={$orderInfo['o_uid']}");
            $up_order = $this->db->Query("update `@#_act_order` set `o_status`='已关闭',`o_refund_status`=3 WHERE `o_id`=$order_id");
            $insert_log = $this->db->Query("insert into `@#_act_refund_log` (`order_id`,`action_user`,`order_status`,`refund_status`,`refund_type`,`action_time`,`status_desc`) VALUE ($order_id,0,'已关闭',3,1,$time,'申请退款成功')");
            $up_activity = $this->db->Query("update `@#_activity` set `act_num_signed`=`act_num_signed`-1 WHERE `act_id`={$orderInfo['o_act_id']}");
            $up_sign = $this->db->Query("update `@#_act_sign` set `s_status`='已关闭' WHERE `s_id`={$orderInfo['o_sid']}");

            if($up_member && $up_order && $insert_log && $up_activity && $up_sign){
                $this->db->Autocommit_commit();
                $res['state'] = 0;
                $res['msg'] = '退款成功';
            }else{
                $this->db->Autocommit_rollback();
                $res['state'] = 1;
                $res['msg'] = '退款失败';
            }
        }else{
            $this->db->Autocommit_rollback();
            $res['state'] = 1;
            $res['msg'] = '退款失败';
        }

        //发送微信模板消息
        $userInfo = $this->db->GetOne("select * from `@#_member` WHERE `uid`={$orderInfo['o_uid']}");
        if($userInfo['wxid']){
            $config = System::load_app_config('connect','','api');
            $appid = $config['weixin']['id'];
            $appsecret = $config['weixin']['key'];
            //1.获取access_token
            $access_token = get_token($appid,$appsecret);
            //查询模板消息id'oHKInuGDs1zZ1yB42TC7O9bW2H0c'
            $touser = $userInfo['wxid'];
            $template_tk = $this->db->GetOne("SELECT * FROM `@#_wxch_cfg` WHERE `cfg_name` = 'template_tk'");
            $template_id = $template_tk['cfg_value'];
            $url = WEB_PATH.'/mobile/activity/myactivities';
            //2.组装数组
            $template = array(
                'touser'=>$touser,
                'template_id'=>$template_id,
                'url'=>$url,
                'topcolor'=>'#7B68EE',
                'data'=>array(
                    'first'=>array('value'=>urlencode('您的订单已经完成退款，￥'.$refund_fee.'已经退回您的付款账户，请留意查收。'),'color'=>'#2B2A2A'),
                    'orderProductPrice'=>array('value'=>urlencode('￥'.$refund_fee),'color'=>'#2B2A2A'),
                    'orderProductName'=>array('value'=>urlencode($orderInfo['o_act_title']),'color'=>'#2B2A2A'),
                    'orderName'=>array('value'=>urlencode($orderInfo['o_code']),'color'=>'#2B2A2A'),
                    'remark'=>array('value'=>urlencode('如有疑问请联系客服。'),'color'=>'#2B2A2A'),
                )
            );
            //echo '<pre>';
            //var_dump($template);exit;
            $data = urldecode(json_encode($template)); //转json数据
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
            //4.调用curl函数
            $result = http_request($url,$data);
            $result = json_decode($result,true);
        }
        exit(json_encode($res));
    }
}