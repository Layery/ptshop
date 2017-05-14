<?php
/** 退款API：支持微信
 * Created by PhpStorm.
 * User: John
 * Date: 2017/3/9
 * Time: 10:50
 */
class refund {
    /**
     * 申请退款
     */
    public function refund_wx($config = null){
        include_once dirname(__FILE__)."/weixin/WxPayPubHelper.php";
        if(!$config){
            $result['err_code_des'] = '配置参数不能为空';
            return $result;
        }
        //实例化退款类
        $refund = new Refund_pub();
        //设置退款参数
        $refund->setParameter("out_trade_no",$config['out_trade_no']);
        $refund->setParameter("out_refund_no",WxPayConf_pub::MCHID.date("YmdHis"));
        $refund->setParameter("total_fee",$config['total_fee']);
        $refund->setParameter("refund_fee",$config['refund_fee']);
        $refund->setParameter("op_user_id",WxPayConf_pub::MCHID);
        $result = $refund->getResult();
        return $result;
        //var_dump($config);
    }
    /**
     * 订单查询
     */
    public function orderQuery($config = null){
        include_once dirname(__FILE__)."/weixin/WxPayPubHelper.php";
        $query = new OrderQuery_pub();
        $query->setParameter("out_trade_no",$config['out_trade_no']);
        $result = $query->getResult();
        return $result;

    }

}