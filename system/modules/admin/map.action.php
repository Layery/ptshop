<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类
class map extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("configList", "应用管理", ROUTE_M . '/' . ROUTE_C . "/configList"),
            array("configAddEdit", "创建应用（key）", ROUTE_M . '/' . ROUTE_C . "/configAddEdit"),
            array("markerLists", "标记管理", ROUTE_M . '/' . ROUTE_C . "/markerLists"),
            array("addEditMarker", "添加标记", ROUTE_M . '/' . ROUTE_C . "/addEditMarker"),
        );
    }
    /**
     * 地图配置列表
     */
    public function configList(){
        //查询配置信息
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_map_config` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $configInfo = $this->db->GetPage("select * from `@#_map_config` WHERE 1",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //p($mark);exit;
        include $this->tpl(ROUTE_M,'map.configList');
    }
    /**
     * ajax更改应用开启状态
     */
    public function ajaxSetStatus(){
        $id = isset($_GET['config_id'])?intval($_GET['config_id']):0;
        $status = intval($_GET['status']);
        if(empty($id)){
            echo json_encode('请选择要修改的应用');
            exit;
        }
        $res = $this->db->GetOne("select `id` from `@#_map_config` WHERE `id`=$id");
        if(!$res){
            echo json_encode('参数不正确');
            exit;
        }
        $row = $this->db->Query("update `@#_map_config` set `on_off`='$status' WHERE `id` = $id");
        if($row){
            echo json_encode('ok');
            exit;
        }else{
            echo json_encode('设置失败');
            exit;
        }

    }
    /**
     * 地图配置
     */
    public function configAddEdit(){
        if($_POST){
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            $type = isset($_POST['type'])?intval($_POST['type']):0;
            $app_name = isset($_POST['appName'])?safe_replace($_POST['appName']):'';
            $app_type = isset($_POST['appType'])?safe_replace($_POST['appType']):'';
            $key = isset($_POST['key'])?safe_replace($_POST['key']):'';
            $flag = $id>0? 2 : 1;
            //p($_POST);exit;
            if(empty($app_name) || empty($key)){
                _message('应用名称或应用密钥不能为空！');
            }
            //密钥正则验证
            if(!preg_match('/^(([0-9a-zA-Z]{5})\-){5}([0-9a-zA-Z]{5})$/',$key)){
                _message('应用密钥输入格式有误！');
            }
            $key = strtoupper($key);
            if($flag == 1){
                //插入
                $sql = "insert into `@#_map_config` (`app_name`,`app_type`,`key`,`on_off`) VALUES ('$app_name','$app_type','$key','$type')";
                $row = $this->db->Query($sql);
                if($row){
                    _message('添加成功',G_ADMIN_PATH.'/map/configList',1);
                }else{
                    _message('添加失败');
                }
            }else{
                //更新
                $sql = "update `@#_map_config` set `app_name`='$app_name',`app_type`='$app_type',`key`='$key',`on_off`='$type' WHERE `id` = $id";
                $row = $this->db->Query($sql);
                if($row){
                    _message('修改成功',G_ADMIN_PATH.'/map/configList',1);
                }else{
                    _message('修改失败');
                }
            }
        }
        //区别编辑和添加：点击编辑过来会带id
        $id = $this->segment(4) ? intval($this->segment(4)) : 0;
        $config = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$id");

        include $this->tpl(ROUTE_M,'map.configAddEdit');
    }

    /**
     * 删除标记
     */
    public function delConfig(){
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        //判断规格项的是否有商品存在，存在则不得删除
        if(empty($id)){
            exit('要删除的应用不存在！');
        }
        //判断数据库是否存在该标记
        $res = $this->db->GetOne("select `id` from `@#_map_config` WHERE `id`=$id");
        if(!$res){
            exit('参数不正确');
        }
        $row = $this->db->Query("delete from `@#_map_config`WHERE `id`=$id");
        if($row){
            exit('ok');
        }else{
            exit('删除失败！');
        }
    }




    /**
     * 添加标记
     */
    public function addEditMarker(){
        if($_POST){
            //p($_POST);exit;
            $id = intval($_POST['id']);
            $type = $id > 0 ? 2 : 1; //1：表示插入；2：表示更新
            $mark_name = isset($_POST['mark_name'])?safe_replace($_POST['mark_name']):'';
            $mobile = isset($_POST['tel_num'])?safe_replace($_POST['tel_num']):'';
            $address = isset($_POST['address'])?safe_replace($_POST['address']):'';
            $latlng = isset($_POST['latlng'])?safe_replace($_POST['latlng']):'';
            //验证数据的合法性
            if(empty($mark_name) || empty($mobile) || empty($latlng)){
                _message('标记名称 联系电话或标记坐标不能为空！');
            }
            if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $mobile)){
                _message('请输入正确的联系电话');
            }
            if($type == 1){
                //表示插入新标记
                $sql = "insert into `@#_map_mark` (`mark_name`,`mobile`,`latlng`,`address`) VALUES ('$mark_name','$mobile','$latlng','$address')";
                $row = $this->db->Query($sql);
                if($row){
                    _message('添加成功',G_ADMIN_PATH.'/map/markerLists',1);
                }else{
                    _message('添加失败');
                }
            }else {
                //判断是否存在标记id
                $res = $this->db->GetOne("select `id` from `@#_map_mark` WHERE `id`=$id");
                var_dump($res);exit;
                if (!$res) {
                    _message('不存在标记！');
                }
                $sql = "update `@#_map_mark` set `mark_name`='$mark_name',`mobile`='$mobile',`latlng`='$latlng',`address`='$address' WHERE `id` = $id";
                $row = $this->db->Query($sql);
                if ($row) {
                    _message('修改成功', G_ADMIN_PATH . '/map/markerLists', 1);
                } else {
                    _message('修改失败');
                }
            }
        }
        //区别编辑和添加：点击编辑过来会带id
        $id = $this->segment(4) ? intval($this->segment(4)) : 0;
        $mark = $this->db->GetOne("select * from `@#_map_mark` WHERE `id`=$id");

        include $this->tpl(ROUTE_M,'map.addEditMarker');
    }
    /**
     * 标记管理
     */
    public function markerLists(){
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_map_mark` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $mark = $this->db->GetPage("select * from `@#_map_mark` WHERE 1",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //p($mark);exit;

        include $this->tpl(ROUTE_M,'map.markerLists');
    }

    /**
     * 删除标记
     */
    public function delMarker(){
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        //判断规格项的是否有商品存在，存在则不得删除
        if(empty($id)){
            exit('标记不存在！');
        }
        //判断数据库是否存在该标记
        $res = $this->db->GetOne("select `id` from `@#_map_mark` WHERE `id`=$id");
        if(!$res){
            exit('数据库中不存在该标注');
        }
        $row = $this->db->Query("delete from `@#_map_mark`WHERE `id`=$id");
        if($row){
            exit('ok');
        }else{
            exit('删除失败！');
        }
    }

    /**
     * 标记排序
     */
    public function listorder()
    {
        if ($this->segment(4) == 'dosubmit') {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->db->Query("UPDATE `@#_map_mark` SET `sort` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        } else {
            _message("请排序");
        }
    }

    /**
     * ajax进行地图的地址解析
     */
    public function ajaxGetAddress(){
        $keyword = isset($_GET['keyword'])?safe_replace($_GET['keyword']):'';
        if(empty($keyword)){
            $res['status'] = 1;
            $res['msg'] = '地址不能为空';
            echo json_encode($res);
            exit;
        }
        //获取应用和key
        $appIdArr = $this->db->GetList("select `id` from `@#_map_config` WHERE `on_off`=1",array('key'=>'id'));
        $appId= array_rand($appIdArr,1);
        $appInfo = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$appId AND `on_off`=1");
        $url = "http://apis.map.qq.com/ws/place/v1/suggestion/?keyword={$keyword}&key={$appInfo['key']}";
        $search = getCurl($url);
        p($search);exit;
        if($search){
            echo $search;
            exit;
        }else{
            $res['status'] = 1;
            $res['msg'] = '查询失败';
            echo json_encode($res);
            exit;
        }
    }

}