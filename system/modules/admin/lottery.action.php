<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');

/**
 * Class lottery 年会抽奖
 */
class lottery extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("prizeAdd", "奖项设置", ROUTE_M . '/' . ROUTE_C . "/prizeAdd"),
            array("prizeList", "奖项列表", ROUTE_M . '/' . ROUTE_C . "/prizeList"),
            array("lotteryList", "中奖列表", ROUTE_M . '/' . ROUTE_C . "/lotteryList"),
        );
    }
    /**
     * 奖项列表
     */
    public function prizeList(){
        //查询奖项
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_lottery_prize` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $prize = $this->db->GetPage("select * from `@#_lottery_prize` WHERE 1",array('key'=>'p_id'));
        include $this->tpl(ROUTE_M,'lottery.prizeList');
    }
    /**
     * ajax修改开奖动态
     */
    public function ajaxLotteryStatus(){
        $col_name = isset($_GET['col_name'])?safe_replace($_GET['col_name']):'';
        $prize_id = isset($_GET['prize_id'])?intval($_GET['prize_id']):0;
        $status = isset($_GET['status'])?intval($_GET['status']):2;
        //p($status);exit;
        if(empty($col_name)){
            echo json_encode('参数不能为空');
            exit;
        }
        if(empty($prize_id)){
            echo json_encode('奖项不存在');
            exit;
        }
        if($status == 2 && $status != 1 && $status !=0){
            echo json_encode('不存在的状态');
            exit;
        }

        //根据状态值查询对应的值，并取反更新
        switch ($col_name){
            case 'start':
                $field = 'p_start_state';
                break;
            case 'end':
                $field = 'p_end_state';
                break;
        }
        $sql = "update `@#_lottery_prize` set `$field`=$status WHERE `p_id`=$prize_id";
        $row = $this->db->Query($sql);
        if($row){
            echo json_encode('ok');
            exit;
        }else {
            echo json_encode('设置失败');
            exit;
        }
    }
    /**
     * 奖项设置：
     * 一等奖、二等奖、三等奖、特别奖...
     */
    public function prizeAdd(){
        if($_POST['dosubmit']){
            /*echo '<pre>';
            var_dump($_POST);exit;*/
            $title = isset($_POST['title'])?safe_replace($_POST['title']):'';
            $lottery_time = isset($_POST['countdown'])?intval($_POST['countdown']):0;
            $hit_num = isset($_POST['hit_num'])?intval($_POST['hit_num']):0;
            $award_num = isset($_POST['award_num'])?intval($_POST['award_num']):0;  //中奖总人数
            $prize_item = isset($_POST['prize_item'])?safe_replace($_POST['prize_item']):'';
            //判断数据的合法性
            if(empty($title))_message('奖项名称不能为空！');
            if(empty($lottery_time))_message('倒计时不能为空！');
            if(empty($hit_num))_message('中奖人数不能为空！');
            if(empty($award_num))_message('中奖总人数不能为空！');
            if($hit_num > $award_num)_message('每次抽奖人数不能大于抽奖总人数');
            //求抽奖轮数
            $count = ceil($award_num/$hit_num); //向上取整得出需要抽几轮
            //数据入库
            $res = $this->db->Query("insert into `@#_lottery_prize` (`p_title`,`p_lottery_time`,`p_hit_num`,`p_award_num`,`p_count`,`p_item`) VALUES ('$title',$lottery_time,$hit_num,$award_num,$count,'$prize_item')");
            if($res){
                _message('设置成功',G_ADMIN_PATH.'/lottery/prizeList',1);
            }else{
                _message('设置失败','',1);
            }
        }
        include $this->tpl(ROUTE_M,'lottery.prizeAdd');
    }
    /**
     * 修改奖项
     */
    public function prizeEdit(){
        if($_POST['dosubmit']){
            /*echo '<pre>';
            var_dump($_POST);exit;*/
            $prize_id = isset($_POST['prize_id'])?intval($_POST['prize_id']):0;
            $title = isset($_POST['title'])?safe_replace($_POST['title']):'';
            $lottery_time = isset($_POST['countdown'])?intval($_POST['countdown']):0;
            $hit_num = isset($_POST['hit_num'])?intval($_POST['hit_num']):0;
            $award_num = isset($_POST['award_num'])?intval($_POST['award_num']):0;  //中奖总人数
            $prize_item = isset($_POST['prize_item'])?safe_replace($_POST['prize_item']):'';
            //判断数据的合法性
            if(empty($prize_id))_message('不存在奖项！');
            if(empty($title))_message('奖项名称不能为空！');
            if(empty($lottery_time))_message('倒计时不能为空！');
            if(empty($hit_num))_message('中奖人数不能为空！');
            if(empty($award_num))_message('中奖总人数不能为空！');
            if($hit_num > $award_num)_message('每次抽奖人数不能大于抽奖总人数');
            //验证数据的合理性
            $result = $this->db->GetOne("select `p_id` from `@#_lottery_prize` WHERE `p_id`=$prize_id");
            if(!$result){
                _message('数据库中不存在该奖项！');
            }
            //求抽奖轮数
            $count = ceil($award_num/$hit_num); //向上取整得出需要抽几轮
            //数据入库
            $res = $this->db->Query("update `@#_lottery_prize` set `p_title`='$title',`p_lottery_time`=$lottery_time,`p_hit_num`=$hit_num,`p_award_num`=$award_num,`p_count`=$count,`p_item`='$prize_item' WHERE `p_id`=$prize_id");
            if($res){
                _message('修改成功',G_ADMIN_PATH.'/lottery/prizeList',1);
            }else{
                _message('修改失败','',1);
            }
        }
        $prize_id = intval($this->segment(4));
        //查询奖项
        $prize_one = $this->db->GetOne("select * from `@#_lottery_prize` WHERE `p_id`=$prize_id");
        //p($prize_one);exit;
        include $this->tpl(ROUTE_M,'lottery.prizeEdit');
    }
    /**
     * 删除奖项
     */
    public function prizeDel(){
        $prize_id = isset($_GET['id'])?intval($_GET['id']):0;
        //验证数据的合法性
        if(empty($prize_id)){
            exit('参数不能为空！');
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `p_id` from `@#_lottery_prize` WHERE `p_id`=$prize_id");
        if(!$res){
            exit('不存在该数据！');
        }
        //删除数据
        $row = $this->db->Query("delete from `@#_lottery_prize` WHERE `p_id`=$prize_id");
        if($row){
            exit('ok');
        }else{
            exit('奖项删除失败');
        }
    }
    /**
     * 初始化奖项信息
     */
    public function initPrizeState(){
        $prizeTemp = array();
        //获取所有的奖项信息
        $prize = $this->db->GetList("select * from `@#_lottery_prize` WHERE 1",array('key'=>'p_id'));
        foreach ($prize as $k => $v){
            $hit_num = $v['p_hit_num'];
            $award_num = $v['p_award_num'];
            $count = ceil($award_num/$hit_num); //向上取整得出需要抽几轮
            $prizeTemp[$k] = $v;
            $prizeTemp[$k]['p_count'] = $count;
            $prizeTemp[$k]['p_start_state'] = 0;
            $prizeTemp[$k]['p_end_state'] = 0;
        }
        //开始初始化
        foreach ($prizeTemp as $key => $val){
            $sql = "update `@#_lottery_prize` set `p_count`={$val['p_count']},`p_start_state`={$val['p_start_state']},`p_end_state`={$val['p_end_state']} WHERE `p_id`=$key";
            $res = $this->db->Query($sql);
        }
        if($res){
            $response['state'] = 0;
            $response['msg'] = '初始化成功';
            echo json_encode($response);
            exit;
        }else{
            $response['state'] = 1;
            $response['msg'] = '初始化失败';
            echo json_encode($response);
            exit;
        }
    }
    public function initLotteryState(){
        $sql = "truncate table `@#_lottery_winner`";
        $res = $this->db->Query($sql);
        if($res){
            $response['state'] = 0;
            $response['msg'] = '初始化成功';
            echo json_encode($response);
            exit;
        }else{
            $response['state'] = 1;
            $response['msg'] = '初始化失败';
            echo json_encode($response);
            exit;
        }
    }

    /**
     * 中奖人列表
     */
    public function lotteryList(){
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_lottery_winner` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $lottery = $this->db->GetPage("select * from `@#_lottery_winner` WHERE 1");
        //查询奖项列表
        $prize = $this->db->GetList("select * from `@#_lottery_prize` WHERE 1");
        $lottery_temp = array();
        foreach ($lottery as $k => $v){
            foreach ($prize as $key => $val){
                if($v['l_prize_id'] == $val['p_id']){
                    $lottery_temp[$k] = $v;
                    $lottery_temp[$k]['prize_name'] = $val['p_title'];
                }
            }
        }
        //p($lottery_temp);exit;
        include $this->tpl(ROUTE_M,'lottery.lotteryList');
    }
    /**
     * 删除中奖人
     */
    public function lotteryDel(){
        $lottery_id = isset($_GET['id'])?intval($_GET['id']):0;
        //验证数据的合法性
        if(empty($lottery_id)){
            exit('参数不能为空！');
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `l_id` from `@#_lottery_winner` WHERE `l_id`=$lottery_id");
        if(!$res){
            exit('不存在该数据！');
        }
        //删除数据
        $row = $this->db->Query("delete from `@#_lottery_winner` WHERE `l_id`=$lottery_id");
        if($row){
            exit('ok');
        }else{
            exit('删除失败');
        }
    }

}