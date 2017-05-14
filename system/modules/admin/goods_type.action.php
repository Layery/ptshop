<?php 

defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',NULL,'no');
System::load_app_fun('global');
System::load_sys_fun('user');
class goods_type extends admin {
	private $db;
	public function __construct(){		
		parent::__construct();		
		$this->db=System::load_app_model('admin_model');
        $this->ment=array(
            array("lists","商品类型",ROUTE_M.'/'.ROUTE_C."/lists"),
            array("add","新增商品类型",ROUTE_M.'/'.ROUTE_C."/add"),
        );
	}
    /**
     * 商品类型
     */
    public function lists(){
        //根据分页信息获取数据
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_goods_type` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $type = $this->db->GetPage("SELECT * FROM `@#_goods_type` WHERE 1",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //p($type);exit;
        include $this->tpl(ROUTE_M,'goods_type.list');
    }

	/**
     * 添加和编辑商品类型
     */
	public function add(){
	    if(isset($_POST['dosubmit'])){
            //获取数据
            $name = isset($_POST['name'])?htmlspecialchars(safe_replace($_POST['name'])):'';
            //验证数据的合法性
            if(empty($name)){
                _message('商品类型名称不能为空');
            }
            //验证数据的合理性：唯一性
            $res = $this->db->GetOne("select `name` from `@#_goods_type` WHERE `name`='$name'");
            if($res){
                _message('商品类型已存在，请重新添加！');
            }
            //数据入库
            $sql = "insert into `@#_goods_type` (`name`) VALUE ('$name')";
            $row = $this->db->Query($sql);
            if($row){
                _message('商品类型添加成功',WEB_PATH.'/'.ROUTE_M.'/goods_type/lists',1);
            }else{
                _message('商品类型添加失败');
            }
        }
        include $this->tpl(ROUTE_M, 'goods_type.addedit');
    }
    /**
     * 编辑商品类型
     */
    public function edit(){
        if(isset($_POST['dosubmit'])){
            //获取数据
            $id = isset($_POST['id'])?intval($_POST['id']):0;
            $name = isset($_POST['name'])?htmlspecialchars(safe_replace($_POST['name'])):'';
            //验证数据的合法性：是否为空
            if(empty($id) || empty($name)){
                _message('商品类型不能为空');
            }
            //验证数据的合理性：存在性和唯一性
            $res = $this->db->GetOne("select `name` from `@#_goods_type` WHERE `id`='$id'");
            if($res){
                if($res['name'] == $name){
                    _message('商品类型未作修改');
                }
            }else{
                _message('商品类型不存在');
            }
            //数据入库
            $sql = "update `@#_goods_type` set `name`='$name' WHERE `id`='$id'";
            $row = $this->db->Query($sql);
            if($row){
                _message('商品类型修改成功',WEB_PATH.'/'.ROUTE_M.'/goods_type/lists',1);
            }else{
                _message('商品类型修改失败');
            }
        }
        //获取数据
        $id = intval($this->segment(4));
        //验证数据的合法性：是否为空
        if(empty($id)){
            _message('商品类型不存在');
        }
        $type = $this->db->GetOne("select * from `@#_goods_type` WHERE `id`='$id'");
        if(!$type){
            _message('商品类型不存在');
        }
        include $this->tpl(ROUTE_M,'goods_type.addedit');
    }
    /**
     * 删除商品
     */
    public function del(){
        $id = intval($this->segment(4));
        if(empty($id)){
            _message('要删除的商品类型不存在');
        }
        $sql = "delete from `@#_goods_type` WHERE `id`='$id'";
        $row = $this->db->Query($sql);
        if($row){
            _message('删除成功','',1);
        }else{
            _message('删除失败');
        }
    }

}
?>