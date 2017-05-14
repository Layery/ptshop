<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2017/3/17
 * Time: 15:03
 */
header("Content-Type:text/html;charset=UTF-8");
$refund_dir = dirname(__FILE__);  //自动退款目录
$refund_dir = str_replace('\\','/',$refund_dir);
$root_dir = dirname($refund_dir);  //系统根目录
$config = include_once $root_dir."/system/config/database.inc.php"; //引入数据库配置文件
require_once dirname(__FILE__).'/MyPDO.class.php'; //引入pdo
require_once $root_dir."/system/modules/pay/lib/refund.class.php";
$wxConfig = require_once $root_dir."/system/modules/api/lib/connect.ini.php";

$db = new MyPDO($config['default']);

//1.查询团购活动订单，根据截止时间，进行自动退款
$actOrder = $db->mypdoFetchAll("select * from `go_act_order` a LEFT JOIN `go_activity` b ON a.`o_act_id`=b.`act_id` WHERE a.`o_status`='已支付' AND a.`o_refund_status` <> 3 AND a.`o_refund_flag`=0 AND b.`act_is_group`=1");
//2.对查询出来的订单进行遍历判断是否到达退款标志
$time = time(); //当天时间

foreach ($actOrder as $k => $v){
    $order_id = $v['o_id'];
    $start_time = $v['act_start_time'];
    $end_time = $v['act_end_time'];
    $stopTime = strtotime('-1 day 17:00:00',$start_time);  //截止报名时间：活动开始前一天下午5点截止报名
    if($time >= $stopTime && $time <= $end_time){ //判断系统时间和团购截止时间
        //开始执行退款操作
        //1.根据活动的已报名人数进行，团购差价的计算
        $signed_num = $v['act_num_signed'];
        //2.查询阶梯团信息
        $stepInfo = $db->mypdoFetchAll("select * from `go_act_step` WHERE `act_id`={$v['o_act_id']}");
        //3.开始计算差价
        $charge = $v['o_refund']; //实付（包括余额和微信），扣除积分
        $refund_fee = 0.00;  //需要退款的差价
        foreach ($stepInfo as $key => $val){
            if($signed_num < $val['num']) { //小于阶梯人数
                $refund_fee = $charge - $val['money'];
            }
        }

        //判断差价金额，零可以不用退
        if($refund_fee == 0.00){
            $msg = date('Y-m-d H:i:s')." 错误信息：差价金额为零，不必退款;\r\n";
            file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
            continue; //结束当前循环，继续下一轮循环
        }

        //4.差价为零不给退，判断是退到余额，还是支付账户
        //判断支付来源，按支付来源进行退款；未使用MVC的模型操作数据库，很不方便
        if($v['o_payment'] == 0.00 && $v['o_pay_type'] == '余额支付'){
            //余额支付，退到余额
            $balance = $refund_fee;
            $status = 1;
        }elseif ($v['o_payment'] != $v['o_refund'] && $v['o_payment'] > 0.00){
            //余额和微信支付
            $balance = $v['o_refund'] - $v['o_payment']; //实际使用的余额支付
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
            $pay_record = $db->mypdoFetch("select * from `go_member_activity_record` WHERE `uid`={$v['o_uid']} AND `status`='已支付' AND `scookies`=1 AND `ordercode`='{$v['o_code']}'");
            //以下if等上线在打开，目前开发阶段，数据不全
            if($refund_fee > $pay_record['money']){
                $msg = date('Y-m-d H:i:s')." 错误信息：退款差价超过微信支付订单金额;\r\n";
                file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
                continue; //结束当前循环，继续下一轮循环
            }
            $config = array(
                'out_trade_no'  => $pay_record['code'],
                'out_refund_no' => $v['o_code'],
                'total_fee'     => (int)($pay_record['money'] * 100),
                'refund_fee'    => (int)($refund_fee * 100),
            );
            //var_dump($config);exit;
            $refundApi = new refund();
            //$result = $refundApi->orderQuery($config);
            $result = $refundApi->refund_wx($config);
            if($result['return_code'] == 'FAIL'){
                $msg = date('Y-m-d H:i:s')." 错误信息：申请退款请求失败，请查询是否存在该订单\r\n";
                file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
                continue;
            }
            if($result['result_code'] == 'SUCCESS'){
                $status = 1;
            }else{
                $msg = date('Y-m-d H:i:s')." 错误信息：".$result['err_code_des']."\r\n";
                file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
                continue;
            }
        }

        if($status == 1){
            //余额退款:按余额退款，并且调用微信模板；更新用户表、订单表、退款日志表、活动表的报名人数、报名表信息的支付状态
            $up_member = $db->mypdoExec("update `go_member` set `money` = `money`+$balance WHERE `uid`={$v['o_uid']}");
            $up_order = $db->mypdoExec("update `go_act_order` set `o_refund_status`=3,`o_refund_flag`=2 WHERE `o_id`=$order_id");
            $insert_log = $db->mypdoExec("insert into `go_act_refund_log` (`order_id`,`action_user`,`order_status`,`refund_status`,`refund_type`,`action_time`,`status_desc`) VALUE ($order_id,0,'已支付',3,2,$time,'差价退款成功')");
            //$up_activity = $db->mypdoExec("update `go_activity` set `act_num_signed`=`act_num_signed`-1 WHERE `act_id`={$v['o_act_id']}");
            $success = true;
            if($up_member && $up_order && $insert_log){
                $success = true;
                $msg = date('Y-m-d H:i:s')." 返回信息：差价退款成功\r\n";
                file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
            }else{
                $success = false;
                $msg = date('Y-m-d H:i:s')." 错误信息：差价退款失败\r\n";
                file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
                continue;
            }
        }else{
            $success = false;
            $msg = date('Y-m-d H:i:s')." 错误信息：差价退款失败\r\n";
            file_put_contents($refund_dir.'/refund_log.txt',$msg,FILE_APPEND);
            continue;
        }

        //发送微信模板消息
        $userInfo = $db->mypdoFetch("select * from `go_member` WHERE `uid`={$v['o_uid']}");
        if($userInfo['wxid'] && $success){
            $appid = $wxConfig['weixin']['id'];
            $appsecret = $wxConfig['weixin']['key'];
            //1.获取access_token
            $access_token = get_token($appid,$appsecret);
            //查询模板消息id'omPWDwanRxD7UHEfR0WYCGGFDXkI'
            $touser =  $userInfo['wxid'];
            $template_tk = $db->mypdoFetch("SELECT * FROM `go_wxch_cfg` WHERE `cfg_name` = 'template_tk'");
            $template_id = $template_tk['cfg_value'];
            $url = 'http://pt1618.cn/mobile/activity/myactivities';
            //2.组装数组
            $template = array(
                'touser'=>$touser,
                'template_id'=>$template_id,
                'url'=>$url,
                'topcolor'=>'#7B68EE',
                'data'=>array(
                    'first'=>array('value'=>urlencode('您的订单已经完成退款，￥'.$refund_fee.'已经退回您的付款账户，请留意查收。'),'color'=>'#2B2A2A'),
                    'orderProductPrice'=>array('value'=>urlencode('￥'.$refund_fee),'color'=>'#2B2A2A'),
                    'orderProductName'=>array('value'=>urlencode($v['o_act_title']),'color'=>'#2B2A2A'),
                    'orderName'=>array('value'=>urlencode($v['o_code']),'color'=>'#2B2A2A'),
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
    }
}
// 获取token
function get_token($appid,$appsecret){
    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
    $token=getCurl($url);
    $access_token=json_decode($token,true);
    return $access_token['access_token'];
}
// get方法请求
function getCurl($url){//get https的内容
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不直接输出内容
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result= curl_exec($ch);
    curl_close($ch);
    return $result;
}
//http 的curl
function http_request($url,$data=null){
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
    if(!empty($data)){
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

