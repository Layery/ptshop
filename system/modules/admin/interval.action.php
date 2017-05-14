<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类
class interval extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("lists", "价格区间", ROUTE_M . '/' . ROUTE_C . "/lists"),
        );
    }
    /**
     * 价格区间列表
     */
    public function lists(){
        //获取区间数据
        $sql = "select * from `@#_price_interval` WHERE 1 ORDER BY  `sort` ASC ";
        $interval = $this->db->GetList($sql);
        if (isset($_POST['dosubmit']) && $_POST['dosubmit'] != 'del'){
            //var_dump($_POST);exit;
            $id = isset($_POST['id'])?safe_replace(intval($_POST['id'])):0;
            $sort = isset($_POST['sort'])?safe_replace(intval($_POST['sort'])):0;
            $open_interval = isset($_POST['open_interval'])?intval(safe_replace($_POST['open_interval'])):0;
            $close_interval = isset($_POST['close_interval'])?intval(safe_replace($_POST['close_interval'])):0;
            $interval_name = isset($_POST['interval_name'])?safe_replace(trim($_POST['interval_name'])):'';
            //验证数据的合法性
            if(!is_numeric($open_interval) || !is_numeric($close_interval) || empty($interval_name)){
                exit("数据格式错误");
            }
            if(!empty($close_interval) && $open_interval > $close_interval){
                exit("左区间不能大于右区间");
            }
            //验证数据的合理性:修改和添加根据id来区分
            if($id == 0 && $_POST['dosubmit'] == 'install'){
                //添加
                //组织sql语句
                $sql = "insert into `@#_price_interval`(`open_interval`,`close_interval`,`interval_name`,`sort`) VALUE ('$open_interval','$close_interval','$interval_name','$sort')";
                $row = $this->db->Query($sql);
                $insert_id = $this->db->insert_id();
                $data['insert_id'] = $insert_id;
                $data['msg'] = 'ok';
                $data = json_encode($data);
                if($row){
                    exit($data);
                }else{
                    exit("添加失败");
                }
            }else{
                //修改
                //判断数据的合理性
                $res = $this->db->GetOne("select * from `@#_price_interval` WHERE `id`='$id'");
                if (!$res){
                    exit('不存在该区间');
                }
                //数据更新
                $sql = "update `@#_price_interval` set `open_interval`='$open_interval',`close_interval`='$close_interval',`interval_name`='$interval_name',`sort`='$sort' WHERE `id`='$id'";
                $row = $this->db->Query($sql);
                if($row){
                    $data['msg'] = 'ok';
                    $data = json_encode($data);
                    exit($data);
                }else{
                    exit("修改失败");
                }
            }

        }
        if(isset($_POST['dosubmit']) && $_POST['dosubmit'] == 'del'){
            //获取数据
            $id = isset($_POST['id'])?safe_replace(intval($_POST['id'])):0;
            $dosubmit = isset($_POST['dosubmit'])?safe_replace($_POST['dosubmit']):null;
            //判断数据的合法性
            if (empty($id) || empty($dosubmit)){
                exit("不存在该区间");
            }
            //判断数据的合理性
            $res = $this->db->GetOne("select * from `@#_price_interval` WHERE `id`='$id'");
            if(!$res){
                exit("不存在该区间");
            }
            //删除数据
            $sql = "delete from `@#_price_interval` WHERE `id`='$id'";
            $res = $this->db->Query($sql);
            if ($res){
                exit("ok");
            }else{
                exit('删除失败');
            }
        }
        include $this->tpl(ROUTE_M,'interval.lists');
    }

    /**
     * 价格区间排序
     */
    public function listorder()
    {
        if ($this->segment(4) == 'dosubmit') {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->db->Query("UPDATE `@#_price_interval` SET `sort` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        } else {
            _message("请排序");
        }
    }
    /**
     * 设置欢迎页信息
     */
    public function setWelcome(){
        $id = intval($_POST['id']);
        $flag = safe_replace($_POST['flag']);
        if(empty($id) || empty($flag)){
            exit('不存该区间');
        }
        //查询id是否存在
        $res = $this->db->GetOne("select `id` from `@#_price_interval` WHERE `id`=$id");
        if(!$res){
            exit('数据不存在');
        }
        //数据入库
        switch ($flag){
            case 'show':
                $row = $this->db->Query("update `@#_price_interval` set `showtop`=1 WHERE `id`=$id");
                break;
            case 'cancel':
                $row = $this->db->Query("update `@#_price_interval` set `showtop`=0 WHERE `id`=$id");
                break;
        }
        if($row){
            exit('ok');
        }else{
            exit('设置失败');
        }
    }
}