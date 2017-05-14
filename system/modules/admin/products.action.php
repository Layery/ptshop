<?php 

defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',NULL,'no');
System::load_app_fun('global');
System::load_sys_fun('user');
class products extends admin {
	private $db;
	public function __construct(){		
		parent::__construct();		
		$this->db=System::load_app_model('admin_model');
        /*$this->ment=array(
            array("lists","货品列表",ROUTE_M.'/'.ROUTE_C."/lists"),
            array("add","添加货品",ROUTE_M.'/'.ROUTE_C."/add"),
        );*/
	}

    /**
     * 货品列表
     */
    public function lists(){
        $shopid = intval($this->segment(4));
        $products = $this->db->GetList("select * from `@#_products` WHERE `goods_id` = $shopid");
        $goods_attr = $this->db->GetList("select a.*,b.attr_name from `@#_goods_attr` a left join `@#_attribute` b on a.goods_attr_id=b.id where a.goods_id = $shopid AND b.attr_type = 1");
        $attr = array();
        $attr_name = array();
        foreach ($goods_attr as $v){
            $attr[$v['attr_name']][$v['id']] = $v['attr_value'];
            $attr_name[] = $v['attr_name'];
        }
        $first_attr[current($attr_name)] = current($attr);
        $this->ment=array(
            array("lists","货品列表",ROUTE_M.'/'.ROUTE_C."/lists/$shopid"),
            array("add","添加货品",ROUTE_M.'/'.ROUTE_C."/add/$shopid"),
        );
        include $this->tpl(ROUTE_M,"products.lists");
    }
	//期数列表
	public function add(){
	    if(isset($_POST['dosubmit'])){
            //p($_POST);exit;
            $attribute = isset($_POST['attr'])? $_POST['attr']:'';
            $price = isset($_POST['price'])&&!empty($_POST['price'])?$_POST['price']:0;
            $inventory = isset($_POST['inventory'])&&!empty($_POST['inventory'])?$_POST['inventory']:0;
            $shopimg = isset($_POST['shopimg'])?$_POST['shopimg']:'';
            $shopid = isset($_POST['shopid'])?$_POST['shopid']:0;
            //数据合法性验证和合理性验证
            if(empty($attribute)||empty($shopimg)){
                _message('货品属性和图片不能为空');
            }
            if($shopid == 0){
                _message('商品不存在');
            }
            //p($shopid);exit;
            //数据合理性验证
            $sql = "select * from `@#_shoplist` WHERE `id` = $shopid";
            $shopinfo = $this->db->GetOne($sql);
            if(!$shopinfo){
                _message('商品不存在');
            }
            if($price == 0){
                //货品的价格按商品基本信息的价格
                $price = $shopinfo['money'];
            }
            $surplus = $inventory;
            if($inventory == 0 && $shopinfo['surplus'] != 0){
                //货品库存为零按商品基本的数量，同时把剩余量设置为商品基本的剩余量
                $inventory = 0;
                $surplus = $shopinfo['surplus'];
            }

            $this->db->Autocommit_start();
            $insert_value = '';
            $sumnum = 0;
            for($i=0; $i<count($attribute); $i++){
                $attr_value = $this->db->GetOne("select `attr_value` from `@#_goods_attr` WHERE `id` = $attribute[$i]");
                //p($attr_value);
                if(is_array($price) && is_array($inventory)){
                    $insert_value .= "($shopid,$attribute[$i],'{$attr_value['attr_value']}',$price[$i],$inventory[$i],$surplus[$i],'$shopimg[$i]'),";
                    $sumnum += $inventory[$i];
                }elseif(!is_array($price) && is_array($inventory)){
                    $insert_value .= "($shopid,$attribute[$i],'{$attr_value['attr_value']}',$price,$inventory[$i],$surplus[$i],'$shopimg[$i]'),";
                    $sumnum += $inventory[$i];
                }elseif (is_array($price) && !is_array($inventory)){
                    $insert_value .= "($shopid,$attribute[$i],'{$attr_value['attr_value']}',$price[$i],$inventory,$surplus,'$shopimg[$i]'),";
                }else{
                    $insert_value .= "($shopid,$attribute[$i],'{$attr_value['attr_value']}',$price,$inventory,$surplus,'$shopimg[$i]'),";
                }

            }
            $insert_value = trim($insert_value,',');
            $sql = "insert into `@#_products` (`goods_id`,`goods_aid`,`attr_value`,`p_price`,`p_inventory`,`p_surplus`,`p_shopimg`) VALUE ".$insert_value;
            //echo $sql;exit;
            $query_1 = $this->db->Query($sql);
            //var_dump($query_1);exit;
            //更新商品数量
            $query_2 = true;
            if($sumnum > $shopinfo['inventory']){
                $set = "set `inventory`=$sumnum,`surplus`=$sumnum where `id`= $shopid";
                $query_2 = $this->db->Query("update `@#_shoplist`".$set);
            }
            if($query_1 && $query_2){
                $this->db->Autocommit_commit();
                _message("货品添加成功!");
            }else{
                $this->db->Autocommit_rollback();
                _message("货品添加失败!");
            }
        }

		$shopid=intval($this->segment(4));
		$info = $this->db->GetOne("select * from `@#_shoplist` where `id` = '$shopid' LIMIT 1");
        $goods_attr = $this->db->GetList("select a.*,b.attr_name from `@#_goods_attr` a left join `@#_attribute` b on a.goods_attr_id=b.id where a.goods_id = $shopid AND b.attr_type = 1");
        $attr = array();
        $attr_name = array();
        foreach ($goods_attr as $v){
            $attr[$v['attr_name']][$v['id']] = $v['attr_value'];
            $attr_name[] = $v['attr_name'];
        }
        $first_attr[current($attr_name)] = current($attr);
        //p($first_attr);exit;
        /*foreach ($first_attr as $k => $v){
            $option .= <<<HTML
                <option value="$k">$v</option>
HTML;
        }*/
        //$option .= '</select>';
        //echo $option;exit;
        //p($first_attr);
        //p($attr);
        //p($goods_attr);
        //exit;
        $this->ment=array(
            array("lists","货品列表",ROUTE_M.'/'.ROUTE_C."/lists/$shopid"),
            array("add","添加货品",ROUTE_M.'/'.ROUTE_C."/add/$shopid"),
        );
		include $this->tpl(ROUTE_M,'products.add');
	}

    /**
     * 货品排序
     */
    public function goods_listorder(){
        if($this->segment(4)=='dosubmit'){
            foreach($_POST['listorders'] as $id => $listorder){
                $id = intval($id);
                $listorder = intval($listorder);
                $this->db->Query("UPDATE `@#_products` SET `sort` = '$listorder' where `p_id` = '$id'");
            }
            _message("排序更新成功");
        }else{
            _message("请排序");
        }
    }
}
?>