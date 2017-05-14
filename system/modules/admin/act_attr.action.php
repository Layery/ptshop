<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类：用以活动筛选
class act_attr extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("lists", "筛选属性", ROUTE_M . '/' . ROUTE_C . "/lists"),
            array("add", "新增属性", ROUTE_M . '/' . ROUTE_C . "/add"),
        );
        //查询分类并进行无限极分类
        $this->cid = isset($_GET['cateid']) ? $_GET['cateid'] : 0;
        //var_dump($cid);
        $categorys=$this->db->GetList("SELECT * FROM `@#_act_category` WHERE 1 order by `parentid` ASC,`c_id` ASC",array('key'=>'c_id'));
        //echo '<pre>';
        //var_dump($categorys);
        $this->tree=System::load_sys_class('tree');
        $this->tree->icon = array('│ ','├─ ','└─ ');
        $this->tree->nbsp = '&nbsp;';
        $this->category="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $this->tree->init($categorys);
        $categoryshtml=$this->tree->get_tree(0,$this->category,$this->cid);
        //var_dump($categoryshtml);exit;
        $this->categories = '<option value="0">≡ 全部分类≡</option>'.$categoryshtml;
    }

    /**
     * 活动属性列表
     */
    public function lists(){
        $cid = $_GET['cateid']; //由于删除分类的筛选属性，把筛选属性定死，所有的分类都有相同的筛选属性
        //var_dump($cid);exit;
        if($cid == 0){
            $where = "1";
        }else {
            $where = "type_id={$cid}";
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_act_attr` WHERE $where");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $attributes=$this->db->GetPage("SELECT a.*,b.c_name as cate_name FROM `@#_act_attr` a left join `@#_act_category` b on a.type_id = b.c_id WHERE $where ORDER BY `sort` DESC ",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //var_dump($attributes);exit;
        $categoryshtml = $this->categories;
        include $this->tpl(ROUTE_M,'act_attr.lists');
    }
    /**
     * 分类添加
     */
    public function add(){
        if($_POST['dosubmit']){
            //echo '<pre>';
            //var_dump($_POST);exit;
            //获取数据
            //$cid = isset($_POST['cid'])?intval($_POST['cid']):0; //0表示作为全部分类
            $name = isset($_POST['name'])?safe_replace($_POST['name']):''; //属性名称
            $is_show = isset($_POST['is_show'])?intval($_POST['is_show']):0;
            $attr_value = isset($_POST['attr_value'])?trim($_POST['attr_value']):'';
            $sort = isset($_POST['sort'])?intval($_POST['sort']):50;
            $attr_value = trim($attr_value,',');
            $attr_value = trim($attr_value,'，');
            //验证数据
            if(empty($name)){
                _message('筛选属性名称不能为空');
            }
            //17.02.09 先考虑只能添加两个筛选属性，后期前端有修改样式，可以适当增加
            $res = $this->db->GetList("select `id` from `@#_act_attr` WHERE `is_show`=1");
            if(count($res)>=2){
                _message('筛选属性不能超过两个');
            }
            //验证数据库中是否存在
            $row = $this->db->GetOne("select * from `@#_act_attr` WHERE `name`='$name'");
            if($row){
                _message('已存在该筛选属性，请勿重复添加');
            }
            $sql = "insert into `@#_act_attr` (`name`,`value`,`is_show`,`sort`) VALUES ('$name','$attr_value',$is_show,$sort)";
            $query = $this->db->Query($sql);
            if($query){
                _message('添加成功');
            }else{
                _message('添加失败');
            }
        }
        //排除分类下没有筛选属性且，已发布活动就不能添加新的筛选属性，除非修改该分类下的全部活动
        //echo '<pre>';
        //var_dump($categorys);
        /*$actId = $this->db->GetOne("select GROUP_CONCAT(DISTINCT(`act_id`)) as actId from `@#_act_filter` WHERE `attr_id` IN (1,2)");
        $outcid = $this->db->GetList("select DISTINCT(`act_category`) as cateId FROM `@#_activity` WHERE `act_id` IN ({$actId['actId']})");
        //var_dump($outcid);
        $condition = '';
        foreach ($outcid as $k => $v){
            $condition .= $v['cateId'].',';
        }
        $condition = rtrim($condition,',');
        $categorys=$this->db->GetList("SELECT * FROM `@#_act_category` WHERE `c_id` NOT IN ($condition) order by `parentid` ASC,`c_id` ASC",array('key'=>'c_id'));
        //var_dump($categorys);exit;
        $this->tree->init($categorys);
        $categoryshtml=$this->tree->get_tree(0,$this->category,$this->cid);
        //var_dump($categoryshtml);exit;
        $this->categories = '<option value="0">≡ 全部分类≡</option>'.$categoryshtml;
        $categoryshtml = $this->categories;*/
        include  $this->tpl(ROUTE_M,'act_attr.add');
    }
    /**
     * 分类修改
     */
    public function edit(){
        if($_POST['dosubmit']){
            //echo '<pre>';
            //var_dump($_POST);exit;
            //获取数据
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            //$cid = isset($_POST['cid'])?intval($_POST['cid']):0; //0表示作为全部分类
            $name = isset($_POST['name'])?safe_replace($_POST['name']):''; //属性名称
            $is_show = isset($_POST['is_show'])?intval($_POST['is_show']):0;
            $attr_value = isset($_POST['attr_value'])?safe_replace($_POST['attr_value']):'';
            $sort = isset($_POST['sort'])?intval($_POST['sort']):50;
            $attr_value = trim($attr_value,',');
            $attr_value = trim($attr_value,'，');
            //验证数据
            if(empty($id)){
                _message('不存在该属性');
            }
            if(empty($name)){
                _message('筛选属性名称不能为空');
            }
            //验证数据库中是否存在
            $row = $this->db->GetOne("select * from `@#_act_attr` WHERE `id`=$id");
            if(!$row){
                _message('不存在该属性');
            }
            //更新
            $sql = "update `@#_act_attr` set `name`='$name',`value`='$attr_value',`is_show`=$is_show,`sort`=$sort WHERE `id`=$id";
            $query = $this->db->Query($sql);
            if($query){
                _message('修改成功');
            }else{
                _message('修改失败');
            }
        }
        $id = intval($this->segment(4));
        //查询筛选属性信息
        $attribute = $this->db->GetOne("select * from `@#_act_attr` WHERE `id`=$id");
        $this->cid = $attribute['type_id'];
        //var_dump($this->category);
        $this->tree->ret = '';
        //排除分类下没有筛选属性且，已发布活动就不能添加新的筛选属性，除非修改该分类下的全部活动
        //echo '<pre>';
        //var_dump($categorys);
        /*$actId = $this->db->GetOne("select GROUP_CONCAT(DISTINCT(`act_id`)) as actId from `@#_act_filter` WHERE `attr_id` IN (1,2)");
        $outcid = $this->db->GetList("select DISTINCT(`act_category`) as cateId FROM `@#_activity` WHERE `act_id` IN ({$actId['actId']})");
        //var_dump($outcid);
        $condition = '';
        foreach ($outcid as $k => $v){
            $condition .= $v['cateId'].',';
        }
        $condition = rtrim($condition,',');
        $categorys=$this->db->GetList("SELECT * FROM `@#_act_category` WHERE `c_id` NOT IN ($condition) order by `parentid` ASC,`c_id` ASC",array('key'=>'c_id'));
        //var_dump($categorys);exit;
        $this->tree->init($categorys);
        $categories = $this->tree->get_tree(0,$this->category,$this->cid);
        //var_dump($attribute);exit;
        $categoryshtml = '<option value="0">≡ 全部分类≡</option>'.$categories;*/
        include  $this->tpl(ROUTE_M,'act_attr.edit');
    }
    /**
     * 删除属性
     */
    public function del(){
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        //验证数据的合法性
        if(empty($id)){
            exit('参数不能为空！');
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `id` from `@#_act_attr` WHERE `id`=$id");
        if(!$res){
            exit('不存在该数据！');
        }
        //删除数据
        $row_1 = $this->db->Query("delete from `@#_act_attr` WHERE `id`=$id");
        if($row_1){
            exit('ok');
        }else{
            exit('活动删除失败');
        }
    }

    /**
     * 属性排序
     */
    public function listorder(){
        if($this->segment(4)=='dosubmit'){
            foreach($_POST['listorders'] as $id => $listorder){
                $this->db->Query("UPDATE `@#_act_attr` SET `sort` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        }else{
            _message("请排序");
        }
    }

    /**
     * ajax修改筛选属性的状态
     */
    public function ajaxAttrSet(){
        $col_name = isset($_GET['col_name'])?safe_replace($_GET['col_name']):'';
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        $status = isset($_GET['status'])?intval($_GET['status']):2;
        //p($status);exit;
        if(empty($col_name)){
            echo json_encode('参数不能为空');
            exit;
        }
        if(empty($id)){
            echo json_encode('属性不存在');
            exit;
        }
        if($status == 2 && $status != 1 && $status !=0){
            echo json_encode('不存在的状态');
            exit;
        }

        //根据状态值查询对应的值，并取反更新
        switch ($col_name){
            case 'is_show':
                $field = 'is_show';
                break;
        }
        $sql = "update `@#_act_attr` set `$field`=$status WHERE `id`=$id";
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
     * ajax增加活动分类信息
     */
    public function ajaxAddAttr(){
        //echo '<pre>';
        //var_dump($_GET);exit;
        //获取数据
        $typeId = isset($_GET['typeId'])?intval($_GET['typeId']):0;
        $condition = isset($_GET['condition'])?trim(safe_replace($_GET['condition'])):'';
        $id = isset($_GET['attr_id'])?intval($_GET['attr_id']):0;
        //验证数据的合法性
        if(empty($condition)){
            echo json_encode('筛选条件不能为空');
            exit;
        }
        if(empty($id)){
            echo json_encode('不存在该筛选条件');
            exit;
        }
        //判断是否已经存在该筛选条件
        $attribute = $this->db->GetOne("select * from `@#_act_attr` WHERE `type_id`=$typeId AND `id`=$id");
        if(!$attribute){
            echo json_encode('不存在该筛选条件，请确定已为该分类添加筛选条件');
            exit;
        }
        $attr = array();
        if(!empty($attribute['value'])){
            $attr_value = str_replace('，', ',', $attribute['value']);
            $attr = explode(',',$attr_value);
            foreach ($attr as $v){
                //var_dump($v);
                if($v == $condition){
                    echo json_encode('已存在该筛选条件，无须添加');
                    exit;
                }
            }
        }
        //增加
        $attr[] = $condition;
        $attr_value = implode(',',$attr);
        $res = $this->db->Query("update `@#_act_attr` set `value`='$attr_value' WHERE `type_id`=$typeId AND `id`=$id");
        if($res){
            echo json_encode('ok');
            exit;
        }else{
            echo json_encode('添加失败，请重新添加');
            exit;
        }
    }
}