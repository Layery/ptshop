<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类
class goods_spec extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("lists", "商品规格", ROUTE_M . '/' . ROUTE_C . "/lists"),
            array("addEdit", "添加商品规格", ROUTE_M . '/' . ROUTE_C . "/addEdit"),
        );
    }
    /**
     * 商品规格
     */
    public function lists(){
        $typeId = isset($_GET['typeId']) ? intval($_GET['typeId']) : 0;
        if(empty($typeId)){
            $where = "1";
        }else{
            $where = "`type_id` = $typeId";
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_spec` WHERE $where");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $goods_spec = $this->db->GetPage("select a.*,b.name type_name,c.item FROM `@#_spec` a LEFT JOIN `@#_goods_type` b ON a.type_id=b.id LEFT JOIN (select `spec_id`,group_concat(`item` ORDER BY `id`) as item from `@#_spec_item` WHERE `spec_input_type`=1 GROUP BY `spec_id`) c on a.id = c.spec_id WHERE $where",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //p($goods_spec);exit;
        $goods_type = $this->db->GetList("select * from `@#_goods_type` WHERE 1");
        include $this->tpl(ROUTE_M,'spec.lists');
    }
    /**
     * 添加和修改商品规格
     */
    public function addEdit(){
        if($_POST['dosubmit']){
            //echo '<pre>';
            //var_dump($_POST);exit;
            $id = intval($_POST['id']);
            $type = $id > 0 ? 2 : 1; //1：表示插入；2：表示更新
            $spec_name = isset($_POST['spec_name']) ? safe_replace($_POST['spec_name']) : '';
            $goods_type = isset($_POST['typeId']) ? intval($_POST['typeId']) : 0;
            $spec_value = isset($_POST['spec_value']) ? trim(safe_replace($_POST['spec_value'])) : '';
            $spec_input_type = isset($_POST['spec_input_type']) ? intval($_POST['spec_input_type']) : 2;
            $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 50;
            //验证数据的合法性：是否为空
            if(empty($spec_name)){
                _message('商品规格名称不能为空！');
            }
            if(empty($goods_type)){
                _message('商品类型不存在！');
            }
            if ($spec_input_type == 2){
                _message('商品规格输入类型不存在！');
            }
            if($spec_input_type == 1 && empty($spec_value)){
                _message('规格选项不能为空！');
            }
            //验证数据的合理性：数据库中是否存在该类型
            $res = $this->db->GetOne("select `id` from `@#_goods_type` WHERE `id`=$goods_type");
            if(!$res){
                _message('商品类型不存在！');
            }
            $this->db->Autocommit_start();
            if($type == 1){
                //插入
                $sql = "insert into `@#_spec` (`type_id`,`spec_input_type`,`name`,`sort`) VALUES ('$goods_type','$spec_input_type','$spec_name','$sort')";
                $row = $this->db->Query($sql);
                if(!$row){
                    $this->db->Autocommit_rollback();
                }
                //返回规格表的最后插入的id
                $spec_id = $this->db->insert_id();
                $spec_arr = array();
                $spec_arr['id'] = $spec_id;
                $spec_arr['item'] = $spec_value;
                $spec_arr['spec_input_type'] = $spec_input_type;
                $this->afterOpreate($spec_arr);
            }else{
                //更新
                //先判断是否存在规格id
                $res = $this->db->GetOne("select * from `@#_spec` WHERE `id`=$id");
                if(!$res){
                    _message('不存在商品规格！');
                }
                $sql = "update `@#_spec` set `type_id`='$goods_type',`spec_input_type`='$spec_input_type',`name`='$spec_name',`sort`='$sort' WHERE `id` = $id";
                $row = $this->db->Query($sql);
                if(!$row){
                    $this->db->Autocommit_rollback();
                }
                $spec_arr = array();
                $spec_arr['id'] = $id; //要修改的规格表的id
                $spec_arr['item'] = $spec_value;
                $spec_arr['spec_input_type'] = $spec_input_type;
                $this->afterOpreate($spec_arr);
            }

        }
        //区别编辑和添加：点击编辑过来会带id
        $id = $this->segment(4) ? intval($this->segment(4)) : 0;
        //$res = $this->db->GetList("select `spec_id`,group_concat(`item` ORDER BY `id`) as item from `@#_spec_item` WHERE `spec_input_type`=1 GROUP BY `spec_id`");
        //p($res);exit;
        //查询规格表以及相应的规格项表
        $goods_spec = $this->db->GetOne("select a.*,b.item FROM `@#_spec` a LEFT JOIN (select `spec_id`,group_concat(`item` ORDER BY `id`) as item from `@#_spec_item` WHERE `spec_input_type`=1 GROUP BY `spec_id`) b on a.id = b.spec_id WHERE a.id=$id");
        //p($goods_spec);exit;
        //查询商品类型
        $goods_type = $this->db->GetList("select * from `@#_goods_type` WHERE 1");
        include $this->tpl(ROUTE_M,'spec.addEdit');
    }

    /**
     * @param array $arr 规格项和商品规格项和价格
     */
    private function afterOpreate($arr = array()){
        $this->db->Autocommit_start();
        $id = $arr['id'];
        //判断手工录入和列表选择
        if($arr['spec_input_type']){
            if(!empty($arr['item'])){
                $item = str_replace('，',',',$arr['item']);
                $item = explode(',',$item);
                foreach ($item as $k => $v){
                    if(empty($v)){
                        unset($item[$k]);
                    }
                }
            }
            //列表选择:判断是插入还是更新(在规格项表中是否有值)
            $db_item = $this->db->GetList("select * from `@#_spec_item` WHERE `spec_id`=$id",array('key'=>'id'));
            $value = '';
            if(!$db_item){
                //插入
                $sql = "insert into `@#_spec_item` (`spec_id`,`item`) VALUES ";
                foreach ($item as $v){
                    $value .= "('$id','$v'),";
                }
                $value = rtrim($value,',');
                $row = $this->db->Query($sql.$value);
                if($row){
                    $this->db->Autocommit_commit();
                    _message('添加成功',G_ADMIN_PATH.'/goods_spec/lists',1);

                }else{
                    $this->db->Autocommit_rollback();
                    _message('添加失败');
                }
            }else{
                //修改
                //判断是原来的规格是否有修改
                $row_1 = $row_2 = $row_3 = true;
                foreach ($db_item as $k=>$v){
                    $db_item[$k] = $v['item'];
                }
                foreach ($item as $k => $v){
                    if(!in_array($v,$db_item)){
                        $sql = "insert into `@#_spec_item` (`spec_id`,`item`) VALUES ('$id','$v')";
                        $row_1 = $this->db->Query($sql.$value);
                    }
                }
                foreach ($db_item as $k=>$v){
                    if(!in_array($v,$item)){
                        $sql = "DELETE FROM `@#_spec_goods_price` WHERE `key` REGEXP '^{$k}_' OR `key` REGEXP '_{$k}_' OR `key` REGEXP '_{$k}$' OR `key` = '{$k}'";
                        $row_2 = $this->db->Query($sql);
                        $row_3 = $this->db->Query("delete from `@#_spec_item` WHERE `id`= $k");
                    }
                }
                if($row_1 && $row_2 && $row_3){
                    $this->db->Autocommit_commit();
                    _message('修改成功',G_ADMIN_PATH.'/goods_spec/lists',1);
                }else{
                    $this->db->Autocommit_rollback();
                    _message('修改失败');
                }
            }
        }else{
            //var_dump(G_ADMIN_PATH.'/goods_lists');exit;
            $res = $this->db->GetList("select * from `@#_spec_item` WHERE `spec_id`=$id");
            if($res){
                $this->db->Autocommit_commit();
                _message('修改成功',G_ADMIN_PATH.'/goods_spec/lists',1);

            }else{
                $this->db->Autocommit_commit();
                _message('添加成功',G_ADMIN_PATH.'/goods_spec/lists',1);
            }
        }
    }

    /**
     * 删除商品规格
     */
    public function del(){
        $spec_id = isset($_GET['id'])?intval($_GET['id']):0;
        //判断规格项的是否有商品存在，存在则不得删除
        if(empty($spec_id)){
            exit('商品规格不存在！');
        }
        $item_id = $this->db->GetList("select `id` from `@#_spec_item` WHERE `spec_id`=$spec_id");
        foreach ($item_id as $v){
            $item_ids[] = $v['id']; //二维数组降为一维数组
        }
        foreach ($item_ids as $v){
            $res = $this->db->GetOne("select * from `@#_spec_goods_price` WHERE `key` REGEXP '^{$v}_' OR `key` REGEXP '_{$v}_' OR `key` REGEXP '_{$v}$' OR `key` = '{$v}'");
            if($res){
                exit('该规格存在商品，请先删除商品再继续删除商品规格');
            }
        }
        $this->db->Autocommit_start();
        $row_1 = $this->db->Query("delete from `@#_spec_item` WHERE `spec_id`=$spec_id");
        $row_2 = $this->db->Query("delete from `@#_spec`WHERE `id`=$spec_id");
        if($row_1 && $row_2){
            $this->db->Autocommit_commit();
            exit('ok');
        }else{
            $this->db->Autocommit_rollback();
            exit('删除失败！');
        }
    }

    /**
     * 商品规格排序
     */
    public function listorder()
    {
        if ($this->segment(4) == 'dosubmit') {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->db->Query("UPDATE `@#_spec` SET `sort` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        } else {
            _message("请排序");
        }
    }

    /**
     * 添加编辑手工录入的规格项
     */
    public function ajaxAddEditSpec(){
        //获取规格数据
        $item = isset($_GET)?trim(safe_replace($_GET['item'])):'';
        $spec_id = intval($_GET['spec_id']);
        $spec_input_type = intval($_GET['spec_input_type']);
        $item_id = isset($_GET['item_id'])?intval($_GET['item_id']):0;
        //判断数据的合法性
        if(empty($item)){
            echo json_encode('规格项不能为空！');
            exit;
        }
        if(empty($spec_id)){
            echo json_encode('不存在该规格，请重新添加规格');
            exit;
        }
        if(!empty($spec_input_type)){
            echo json_encode('规格项的录入方式必须为零');
            exit;
        }
        //判断数据的合理性
        if(!empty($item_id)){
            $res = $this->db->GetOne("select `id` from `@#_spec_item` WHERE `id`=$item_id");
            if(!$res){
                echo json_encode('不存在该规格，请重新添加商品');
                exit;
            }
            $sql = "update `@#_spec_item` set `item`='$item' WHERE `id`=$item_id AND `spec_id`=$spec_id";
        }else{
            //规格项入库
            $sql = "insert into `@#_spec_item` (`spec_id`,`item`,`spec_input_type`) VALUES ('$spec_id','$item','$spec_input_type')";

        }
        $row = $this->db->Query($sql);
        if(!$row){
            echo json_encode('规格项录入失败');
            exit;
        }
        $insert_id = $this->db->insert_id();
        $data['msg'] = 'ok';
        $data['item_id'] = $insert_id;
        //p($data);exit;
        echo json_encode($data);
        exit;
        //p($_GET);exit;
    }
    public function ajaxDelSpec(){
        $spec_id = intval($_GET['spec_id']);
        $item_id = intval($_GET['item_id']);
        //验证数据的合法性:是否为空
        if(empty($item_id) || empty($item_id)){
            echo json_encode('商品规格不能为空');
            exit;
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `id` from `@#_spec_item` WHERE `id`=$item_id AND `spec_id`=$spec_id");
        if(!$res){
            echo json_encode('不存在的商品规格');
            exit;
        }
        //删除数据
        $sql = "delete from `@#_spec_item` WHERE `id`=$item_id AND `spec_id`=$spec_id";
        $row = $this->db->Query($sql);
        if(!$row){
            echo json_encode('删除数据失败');
            exit;
        }
        echo json_encode('ok');
        exit;

    }


    /**
     * 添加商品时动态显示商品规格
     */
    public function ajaxGetSpecSelect(){
        //获取商品类型id：根据id查询规格，以及根据规格表`go_spec`的id查询规格项表`go_spec_item`的值,根据录入方式显示不同的HTML标签
        $typeId = intval($_GET['typeId']);
        $goods_id = isset($_GET['goods_id'])?intval($_GET['goods_id']):0;
        //查询规格表和规格项表
        $spec = $this->db->GetList("select * from `@#_spec` WHERE `type_id`=$typeId");
        foreach ($spec as $k => $v) {
            $spec[$k]['spec_item'] = $this->db->GetList("select `id`,`item` from `@#_spec_item` WHERE `spec_id` = $v[id] ORDER BY `id`");
        }
        $items_id = $this->db->GetOne("select group_concat(`key` separator '_') as items_id from `@#_spec_goods_price` WHERE `goods_id` = $goods_id");
        $items_ids = explode('_', $items_id['items_id']);
        $items_ids = array_unique($items_ids); //移除数组中相同的值
        sort($items_ids);
        if($goods_id)
        {
            //获取规格图片
            $specImageList = $this->db->GetList("select `spec_image_id`,`src` from `@#_spec_image` WHERE `goods_id`=$goods_id",array('key'=>'spec_image_id'));
        }
        //p($specImageList);exit;
        $table['data'] = include $this->tpl(ROUTE_M,'ajax_spec_select');
        $table['msg'] = 'ok';
        echo json_encode($table);
        exit;
    }

    /**
     * 根据选择的商品规格，动态获取商品对应的规格数据比如价格、库存；这是动态获取商品规格表格的入口
     */
    public function ajaxGetSpecInput(){
        /* post提交过来的数据
         * $spec_arr = array(
            20 => array('7','8','9'),
            10=>array('1','2'),
            1 => array('3','4'),
        );
        <input name="item[2_4_7][price]" value="100" /><input name="item[2_4_7][name]" value="蓝色_S_长袖" />*/
        //p($_POST);exit;
        $spec_arr = $_POST['spec_arr'];
        $goods_type = intval($_POST['goods_type']);
        $goods_id = intval($_POST['goods_id']); //修改时要用的商品id，来查询数据
        // 对数组进行排序，保留键名，同时以小到大
        foreach ($spec_arr as $k => $v)
        {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);
        foreach ($spec_arr_sort as $key =>$val)
        {
            $spec_arr2[$key] = $spec_arr[$key];
        }
        $clo_name = array_keys($spec_arr2);
        $spec_ids = implode(',',$clo_name); //利用spec_id 查询规格项
        $spec_arr2 = combineDika($spec_arr2); //进行笛卡尔积的联合，参考线性代数
        //获取规格表和规格项表
        $spec = $this->db->GetList("select * from `@#_spec` WHERE `type_id`=$goods_type",array('key'=>'id'));
        $specItem = $this->db->GetList("select * from `@#_spec_item` WHERE `spec_id` in ($spec_ids)",array('key'=>'id'));
        $specGoodsPrice = $this->db->GetList("select * from `@#_spec_goods_price` WHERE `goods_id`=$goods_id",array('key'=>'key'));
        //p($specGoodsPrice);exit;

        $str = "<table class='table table-bordered' id='spec_input_tab' width='100%'>";
        $str .="<tr>";
        // 显示第一行的数据
        foreach ($clo_name as $k => $v)
        {
            $str .=" <td><b>{$spec[$v]['name']}</b></td>";
        }
        $str .= "<td><b>本店价格</b></td>
                <td><b>市场价格</b></td>
                <td><b>库存</b></td>
                </tr>";
        // 显示第二行开始
        foreach ($spec_arr2 as $k => $v)
        {
            $str .="<tr>";
            $item_key_name = array();
            foreach($v as $k2 => $v2)
            {
                $str .="<td>{$specItem[$v2]['item']}</td>";
                $item_key_name[$v2] = $spec[$specItem[$v2]['spec_id']]['name'].':'.$specItem[$v2]['item'];
            }
            ksort($item_key_name);
            $item_key = implode('_', array_keys($item_key_name));
            $item_name = implode(' ', $item_key_name);

            $specGoodsPrice[$item_key][price] ? false : $specGoodsPrice[$item_key][price] = 0; // 价格默认为0
            $specGoodsPrice[$item_key][market_price] ? false : $specGoodsPrice[$item_key][market_price] = 0; // 市场价格默认为0
            $specGoodsPrice[$item_key][inventory] ? false : $specGoodsPrice[$item_key][inventory] = 0; //库存默认为0
            $str .="<td><input class='input-text' name='item[$item_key][price]' value='{$specGoodsPrice[$item_key][price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td><input class='input-text' name='item[$item_key][market_price]' value='{$specGoodsPrice[$item_key][market_price]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
            $str .="<td><input class='input-text' name='item[$item_key][store_count]' value='{$specGoodsPrice[$item_key][inventory]}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/><input type='hidden' name='item[$item_key][key_name]' value='$item_name' /></td>";
            $str .="</tr>";
        }
        $str .= "</table>";
        //p($spec);
        //p($item_key);exit;
        echo $str;


    }
}