<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义属性类
class attribute extends admin{
    private $category;
    private $categories;
    private $tree;
    private $cid;
    private $db;
    public function __construct() {
        parent::__construct();
		System::load_app_fun('global',G_ADMIN_DIR);
		$this->db=$this->DB('category_model',ROUTE_M);
		//头部导航
		$this->ment=array(
		    array("lists","属性管理",ROUTE_M.'/'.ROUTE_C."/lists"),
		    array("insert","添加属性",ROUTE_M.'/'.ROUTE_C."/insert"),
		);
		//查询分类并进行无限极分类
		$this->cid = isset($_GET['cateid']) ? $_GET['cateid'] : 0;
		//var_dump($cid);
		$categorys=$this->db->GetList("SELECT * FROM `@#_category` WHERE `model` = '1' order by `parentid` ASC,`cateid` ASC",array('key'=>'cateid'));
		$this->tree=System::load_sys_class('tree');
		$this->tree->icon = array('│ ','├─ ','└─ ');
		$this->tree->nbsp = '&nbsp;';
		$this->category="<option value='\$cateid'\$selected>\$spacer\$name</option>";
		$this->tree->init($categorys);
		$categoryshtml=$this->tree->get_tree(0,$this->category,$this->cid);
		$this->categories = '<option value="0">≡ 请选择分类≡</option>'.$categoryshtml;
    }
    
    
    /**
     * 属性列表
     */
    public function lists(){
        $cid = $_GET['cateid'];
        if($cid == 0){
            $where = "1";
        }else {
            $where = "type_id={$cid}";
        }
        $num=20;  
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_attribute` WHERE $where");        
        $page=System::load_sys_class('page');       
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}       
        $page->config($total,$num,$pagenum,"0");     
        $attributes=$this->db->GetPage("SELECT a.*,b.name as cate_name FROM `@#_attribute` a left join `@#_category` b on a.type_id = b.cateid WHERE $where and b.model = '1'",array("num"=>$num,"page"=>$pagenum,"type"=>1,"cache"=>0));
        //echo '<pre>';
        //var_dump($attributes);exit;
        //查询全部属性
        $categoryshtml = $this->categories;
        include $this->tpl(ROUTE_M,'attribute.list');
    }
    
    /**
     * 添加属性
     */
    public function insert(){
        if($_POST['dosubmit']){
            //接收数据
            $cateId = isset($_POST['cateid']) ? $_POST['cateid']: 0;
            $attrName = isset($_POST['attr_name']) ? trim($_POST['attr_name']) : '';
            $attrType = isset($_POST['attr_type']) ? (int)$_POST['attr_type'] : 0;
            $attrInputType = isset($_POST['attr_input_type']) ? (int)$_POST['attr_input_type'] : 0;
            $attrValue = isset($_POST['attr_value']) ? trim($_POST['attr_value']) : '';
            //判断数据的合法性
            if(empty($cateId) || empty($attrName)){
                _message('商品所属分类或属性名称不能为空！');
            }
            if(!is_numeric($cateId) && $cateId == 0){
                _message('请选择商品所属分类！');
            }
			if($attrInputType==1){
				if(empty($attrValue)){
					_message('选择列表选择，请输入属性值');
				}
			}
            //判断数据的合理性
            $cid = $this->db->GetOne("select `cateid` from `@#_category` where `model` = '1' and `cateid` = $cateId");
            if(!$cid){
                _message('请选择正确的商品分类！');
            }
            //数据入库
            $sql = "insert into `@#_attribute`(`type_id`,`attr_name`,`attr_type`,`attr_input_type`,`attr_value`) values($cateId,'$attrName',$attrType,$attrInputType,'$attrValue')";
            //echo $sql ;exit;
            $row = $this->db->Query($sql);
            if($row){
                _message('商品属性添加成功！',WEB_PATH.'/'.ROUTE_M.'/attribute/lists',1);
            }
        }
        $categoryshtml = $this->categories;	
        include $this->tpl(ROUTE_M,'attribute.add');
    }
    
    /**
     * 修改属性值
     */
     public function edit(){
         if($_POST['dosubmit']){
             //接收数据
             $attr_id = $_POST['attr_id'];
             $cateId = isset($_POST['cateid']) ? $_POST['cateid']: 0;
             $attrName = isset($_POST['attr_name']) ? trim($_POST['attr_name']) : '';
             $attrType = isset($_POST['attr_type']) ? (int)$_POST['attr_type'] : 0;
             $attrInputType = isset($_POST['attr_input_type']) ? (int)$_POST['attr_input_type'] : 0;
             $attrValue = isset($_POST['attr_value']) ? trim($_POST['attr_value']) : '';
             //判断数据的合法性
             if(empty($cateId) || empty($attrName)){
                 _message('商品所属分类、属性名称不能为空！');
             }
             if(!is_numeric($cateId) && $cateId == 0){
                 _message('请选择商品所属分类！');
             }
             //判断数据的合法性
             $cid = $this->db->GetOne("select `cateid` from `@#_category` where `model` = '1' and `cateid` = $cateId");
             if(!$cid){
                 _message('请选择正确的商品分类！');
             }
             //数据入库
             $sql = "UPDATE `@#_attribute` SET `type_id`=$cateId,`attr_name`='$attrName',`attr_type`=$attrType,`attr_input_type`=$attrInputType,`attr_value`='$attrValue' WHERE `id`=$attr_id";
             //echo $sql ;exit;
             $row = $this->db->Query($sql);
             if($row){
                 _message('商品属性修改成功！',WEB_PATH.'/'.ROUTE_M.'/attribute/lists',1);
             }
         }
        $attr_id = $this->segment(4) ? (int)$this->segment(4) : 0;
        //根据attr_id查询对应的属性信息
        $sql = "select * from `@#_attribute` where `id`={$attr_id}";
        $attribute = $this->db->GetOne($sql);
        $this->cid = $attribute['type_id'];
        $this->tree->ret = '';
        $categoryshtml = $this->tree->get_tree(0,$this->category,$this->cid);
        //var_dump($categoryshtml);
        $this->categories = '<option value="0">≡ 请选择分类≡</option>'.$categoryshtml;
        $categoryshtml = $this->categories;
        include $this->tpl(ROUTE_M,'attribute.edit');   
    }
    
    /**
     * 删除属性
     */
    public function del(){
        $attr_id = $this->segment(4) ? (int)$this->segment(4) : 0;
        //判断属性id是否存在
        if($attr_id == 0){
            _message('属性不存在，请重试！');
        }
        //删除属性
        $sql = "delete from `@#_attribute` where `id` = $attr_id";
        $row = $this->db->Query($sql);
        if($row){
            _message('属性删除成功！',WEB_PATH.'/'.ROUTE_M.'/attribute/lists',1);
        }else{
            _message('属性删除失败！');
        }
    }
    /**
     * 属性排序
     */
    public function listorder(){
        if($this->segment(4)=='dosubmit'){
            foreach($_POST['listorders'] as $id => $listorder){
                $this->db->Query("UPDATE `@#_attribute` SET `order` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        }else{
            _message("请排序");
        }
    }
    /**
     * 后台ajax请求返回属性数据
     */
    public function showAttr(){
        $cid = intval($this->segment(4));
        $shopid = intval($this->segment(5));
        //p($shopid);exit;
        $sql = "select * from `@#_attribute` where `type_id`=$cid";
        $attrs = $this->db->GetList($sql);
        //p($attrs);
        //$sql = "select * from `@#_goods_attr` where `goods_id`=$shopid";
        //$goods_attr = $this->db->GetList($sql);
       // p($goods_attr);exit;
        $str = '<tr>
			<td align="right" style="width:120px"><font color="red">*</font><b>提示：</b></td>
			<td style="width: 993px;">
				<div class="header-data lr10">
					请先选择商品分类，再进一步完善商品属性添加，只能选择一个属性类型为单选的商品属性进行<font style="color: red;">多属性值</font>的选择（[+]：表示单选属性）
				</div>
			</td>';
        //$sub = '';
        foreach ($attrs as $v){
             if($v['attr_type'] == 0){
                //属性类型唯一
                if($v['attr_input_type'] == 0){
                    //手工输入
                    $str .= '<tr><td align="right" style="width:120px">'.$v['attr_name'].'：</td>';
                    $str .= "<td style=\"width:933px;\"><input type='text' name='attr[".$v['id']."]'/></td></tr>";
                }else{
                    //列表选择
                    $str .= '<tr><td align="right" style="width:120px">'.$v['attr_name'].'：</td>';
                    $str .= "<td style=\"width:933px;\"><select name='attr[".$v['id']."]'><option value=''>请选择</option>";
                    $attr = str_replace('，', ',', $v['attr_value']);
                    $arr_attr = explode(',', $attr);
                        foreach($arr_attr as $v1){
                            $str .= '<option value='.$v1.'>'.$v1.'</option>';
                        }
                        $str .= '</select></td></tr>';
                }
            }else{
                //属性类型单选
                if($v['attr_input_type'] == 0){
                        //手工输入
                        $str .= '<tr><td align="right" style="width:120px"><a href="javascript:;" onclick="copythis(this)">[+]</a>'.$v['attr_name'].'：</td>';
                        $str .= "<td style=\"width:933px;\"><input type='text' name='attr[".$v['id']."][]' /></td></tr>";
                }else{
                    //列表选择
                    $str .= '<tr><td align="right" style="width:120px"><a href="javascript:;" onclick="copythis(this)">[+]</a>'.$v['attr_name'].'：</td>';
                    $str .= "<td style=\"width:933px;\"><select name='attr[".$v['id']."][]'><option value=''>请选择</option>";
                    $attr = str_replace('，', ',', $v['attr_value']);
                    $arr_attr = explode(',', $attr);
                    foreach($arr_attr as $v1){
                        $str .= '<option value='.$v1.'>'.$v1.'</option>';
                    }
                    $str .= '</select></td></tr>';
                }
            } 
            //$attr = str_replace('，', ',', $v['attr_value']);
            //$color = explode(',', $attr);
            //p($color);
            //p($v);exit;
        }
        
        echo json_encode($str);
    }
    /**
     * 修改属性的ajax请求
     */
    public function editAttr(){
        $cid = intval($this->segment(4));
        $shopid = intval($this->segment(5));
        //p($shopid);exit;
        $sql = "select * from `@#_attribute` where `type_id`=$cid";
        $attrs = $this->db->GetList($sql);
        //p($attrs);
        $sql = "select * from `@#_goods_attr` where `goods_id`=$shopid";
        $goods_attr = $this->db->GetList($sql);
        $arr_temp = array();
        $var_temp = '';
        $var_temp1 = '';
        //p($goods_attr);exit;
        $str = '<tr>
			<td align="right" style="width:120px"><font color="red">*</font><b>提示：</b></td>
			<td style="width: 993px;">
				<div class="header-data lr10">
					请先选择商品分类，再进一步完善商品属性添加，只能选择一个属性类型为单选的商品属性进行<font style="color: red;">多属性值</font>的选择（[+]：表示单选属性）
				</div>
			</td>';
        //$sub = '';
        foreach ($goods_attr as $item) {
            $var_temp = $item['goods_attr_id'];
            foreach ($attrs as $k => $v){
                if($item['goods_attr_id'] == $v['id']){
                    //echo 'ok';
                    if($v['attr_type'] == 0){
                        //属性类型唯一
                        if($v['attr_input_type'] == 0){
                            //手工输入
                            $str .= '<tr><td align="right" style="width:120px">'.$v['attr_name'].'：</td>';
                            $str .= "<td style=\"width:933px;\"><input type='text' name='attr[".$v['id']."]' value='".$item['attr_value']."'/></td></tr>";
                        }else{
                            //列表选择
                            $str .= '<tr><td align="right" style="width:120px">'.$v['attr_name'].'：</td>';
                            $str .= "<td style=\"width:933px;\"><select name='attr[".$v['id']."]'><option value=''>请选择</option>";
                            $attr = str_replace('，', ',', $v['attr_value']);
                            $arr_attr = explode(',', $attr);
                            foreach($arr_attr as $v1){
                                if(trim($item['attr_value']) == $v1){
                                    $str .= '<option value='.$v1.'selected="selected">'.$v1.'</option>';
                                }else{
                                    $str .= '<option value='.$v1.'>'.$v1.'</option>';
                                }
                            }
                            $str .= '</select></td></tr>';
                        }
                    }else{
                        //属性类型单选
                        foreach ($goods_attr as $item) {
                            $arr_temp[$item['goods_attr_id']][] = $item['attr_value'];
                        }
                        //p($arr_temp);exit;
                        if($v['attr_input_type'] == 0){
                            //手工输入
                            $str .= '<tr><td align="right" style="width:120px"><a href="javascript:;" onclick="copythis(this)">[+]</a>'.$v['attr_name'].'：</td>';
                            $str .= "<td style=\"width:933px;\"><input type='text' name='attr[".$v['id']."][]' /></td></tr>";
                            if(count($arr_temp[$v['id']])>1){
                                $str .= '<tr><td align="right" style="width:120px"><a href="javascript:;" onclick="copythis(this)">[-]</a>'.$v['attr_name'].'：</td>';
                                $str .= "<td style=\"width:933px;\"><input type='text' name='attr[".$v['id']."][]' value='".$item['attr_value']."'/></td></tr>";
                            }
                        }else{
                            //列表选择
                            $str .= '<tr><td align="right" style="width:120px"><a href="javascript:;" onclick="copythis(this)">[+]</a>'.$v['attr_name'].'：</td>';
                            $str .= "<td style=\"width:933px;\"><select name='attr[".$v['id']."][]'><option value=''>请选择</option>";
                            $attr = str_replace('，', ',', $v['attr_value']);
                            $arr_attr = explode(',', $attr);
                            foreach($arr_attr as $v1){
                                $str .= '<option value='.$v1.'>'.$v1.'</option>';
                            }
                            $str .= '</select></td></tr>';
                        }
                    }
                }
                //$attr = str_replace('，', ',', $v['attr_value']);
                //$color = explode(',', $attr);
                //p($color);
                //p($v);exit;
            }
        }

        echo json_encode($str);
    }
    
}