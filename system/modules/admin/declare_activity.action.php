<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类
class declare_activity extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("lists", "公告活动", ROUTE_M . '/' . ROUTE_C . "/lists"),
            array("add", "添加内容", ROUTE_M . '/' . ROUTE_C . "/add"),
        );
    }
    /**
     * 公告活动列表
     */
    public function lists(){
        $flag = '0';
        if(isset($_GET['declare_act'])){
            $flag = safe_replace($_GET['declare_act']);
            //var_dump($flag);
        }

        //根据前台传过来的参数查询所有的数据
        switch ($flag){
            case '0':
                $where = 'where 1';
                break;
            case 'a':
                $where = "where `flag`='$flag'";
                break;
            case 'd':
                $where = "where `flag`='$flag'";
                break;
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_declare_activity`".$where);
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $res=$this->db->GetPage("select * from `@#_declare_activity`".$where,array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //$res = $this->db->GetList("select * from `@#_declare_activity`".$where);
        include $this->tpl(ROUTE_M,'declare_activity.lists');
    }
    /**
     * 添加公告活动
     */
    public function add(){
        if(isset($_POST['dosubmit'])){
            //获取数据
            $title = isset($_POST['title'])?addslashes(safe_replace($_POST['title'])):'';
            $content = isset($_POST['content'])?addslashes(safe_replace($_POST['content'])):'';
            $updatetime = isset($_POST['posttime'])?strtotime(safe_replace($_POST['posttime'])):time();
            $flag = isset($_POST['declare_act'])?safe_replace($_POST['declare_act']):'a';
            //var_dump($updatetime);exit;
            //判断数的合法性
            if(empty($content)){
                if($flag == 'a'){
                    _message('活动内容不能为空');
                }
                _message('公告内容不能为空');
            }
            //数据入库
            //组织sql
            $sql = "insert into `@#_declare_activity` (`title`,`content`,`updatetime`,`flag`)VALUE ('$title','$content','$updatetime','$flag')";
            //var_dump($sql);exit;
            $row = $this->db->Query($sql);
            if($row){
                _message('添加成功',G_ADMIN_PATH.'/declare_activity/lists');
            }else{
                _message('添加失败');
            }
        }
        include $this->tpl(ROUTE_M,'declare_activity.add');
    }
    /**
     * 修改内容
     */
    public function edit(){
        if(isset($_POST['dosubmit'])){
            //获取数据
            $id = isset($_POST['id'])?addslashes(intval($_POST['id'])):0;
            $title = isset($_POST['title'])?addslashes(safe_replace($_POST['title'])):'';
            $content = isset($_POST['content'])?addslashes(safe_replace($_POST['content'])):'';
            $updatetime = isset($_POST['posttime'])?strtotime(safe_replace($_POST['posttime'])):time();
            $flag = isset($_POST['declare_act'])?safe_replace($_POST['declare_act']):'a';
            //var_dump($updatetime);exit;
            //判断数据的合法性
            if(empty($id)){
                _message('请选择要修改的公告/活动内容');
            }
            if(empty($content)){
                if($flag == 'a'){
                    _message('活动内容不能为空');
                }
                _message('公告内容不能为空');
            }
            //判断数据的合理性
            $res = $this->db->GetOne("select * from `@#_declare_activity` WHERE `id`='$id'");
            if(!$res){
                _message('不存在该条内容');
            }
            //数据入库
            //组织sql
            $sql = "update `@#_declare_activity` set `title`='$title',`content`='$content',`updatetime`='$updatetime',`flag`='$flag' WHERE `id`='$id'";
            $row = $this->db->Query($sql);
            if($row){
                _message('修改成功',G_ADMIN_PATH.'/declare_activity/lists');
            }else{
                _message('修改失败');
            }
        }
        $id = intval($this->segment(4));
        //判断数据的合法性
        if(empty($id)){
            _message('请选择要修改的公告/活动内容');
        }
        //判断数据的合法性
        $res = $this->db->GetOne("select * from `@#_declare_activity` WHERE `id`='$id'");
        //var_dump($res);exit;
        if(!$res){
            _message('不存在该条内容');
        }
        include $this->tpl(ROUTE_M,'declare_activity.edit');
    }
    /**
     * 删除内容
     */
    public function del(){
        $id = intval($this->segment(4));
        if(empty($id)){
            _message('请选择要删除的公告/活动内容');
        }
        //判断数据的合法性
        $res = $this->db->GetOne("select `id` from `@#_declare_activity` WHERE `id`='$id'");
        if(!$res){
            _message('不存在该条内容');
        }
        $sql = "delete from `@#_declare_activity` WHERE `id`='$id'";
        $row = $this->db->Query($sql);
        if($row){
            _message('删除成功',G_ADMIN_PATH.'/declare_activity/lists');
            exit;
        }else{
            _message('删除失败');
        }
    }

    /**
     * 内容排序
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
}