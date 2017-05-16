<?php
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin',G_ADMIN_DIR,'no');
//定义活动类
class activity extends admin{
    private $db;
    public function __construct()
    {
        parent::__construct();
        System::load_app_fun('global', G_ADMIN_DIR);
        $this->db = System::load_sys_class("model");
        //头部导航
        $this->ment = array(
            array("categoryAdd", "新增分类", ROUTE_M . '/' . ROUTE_C . "/categoryAdd"),
            array("category", "活动分类", ROUTE_M . '/' . ROUTE_C . "/category"),
            array("add", "发布活动", ROUTE_M . '/' . ROUTE_C . "/add"),
            array("lists", "活动列表", ROUTE_M . '/' . ROUTE_C . "/lists"),
            array("signList","报名列表",ROUTE_M.'/'.ROUTE_C."/signList"),
        );
    }
    /********************************** 活动分类--start-- ****************************************/
    /**
     * 新增活动分类
     */
    public function categoryAdd(){
        if($_POST){
            $name = isset($_POST['name'])?safe_replace($_POST['name']):'';
            $pid = isset($_POST['parentid'])?intval($_POST['parentid']):0;
            $act_icon = isset($_POST['act_icon'])?safe_replace($_POST['act_icon']):'';
            $is_show = isset($_POST['is_show'])?intval($_POST['is_show']):1;
            $sort = isset($_POST['sort'])?intval($_POST['sort']):50;
            //echo $act_icon;exit;
            //验证数据
            if(empty($name)){
                _message('分类名称不能为空');
            }
            //生成缩略图
            $file_path = G_UPLOAD.'/'.$act_icon;
            System::load_sys_class('upload', 'sys', 'no');
            upload::thumbs(64, 64, true, $file_path); //参数说明：宽、高、是否覆盖原图（true：是）、文件路径
            //验证同类下是否已存在该分类
            $res = $this->db->GetOne("select * from `@#_act_category` WHERE `c_name`='{$name}' AND `c_pid`=$pid");
            if($res){
                _message('所属分类下已存在该分类，请重新输入');
            }
            $sql = "insert into `@#_act_category` (`parentid`,`c_name`,`c_icon`,`c_is_show`,`c_sort`) VALUES ($pid,'$name','$act_icon',$is_show,$sort)";
            $row = $this->db->Query($sql);
            if($row){
                _message("添加成功!",G_ADMIN_PATH.'/activity/category',1);
            }else{
                _message("添加失败!",'',1);
            }
        }
        //查询所有的分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $categoryshtml="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $tree->init($categories);
        //获取分类id
        $pid = intval($this->segment(4));
        $category = $this->db->GetOne("select * from `@#_act_category` WHERE `c_id`=$pid");
        if($category){
            $categoryshtml=$tree->get_tree(0,$categoryshtml,$pid);
        }else{
            $categoryshtml=$tree->get_tree(0,$categoryshtml);
        }
        include $this->tpl(ROUTE_M,'activity.categoryAdd');
    }
    /**
     * 修改活动分类
     */
    public function categoryEdit(){
        if($_POST){
            //var_dump($_POST);exit;
            $name = isset($_POST['name'])?safe_replace($_POST['name']):'';
            $pid = isset($_POST['parentid'])?intval($_POST['parentid']):0;
            $act_icon = isset($_POST['act_icon'])?safe_replace($_POST['act_icon']):'';
            $is_show = isset($_POST['is_show'])?intval($_POST['is_show']):1;
            $sort = isset($_POST['sort'])?intval($_POST['sort']):50;
            $c_id = isset($_POST['c_id'])?intval($_POST['c_id']):0;
            //验证数据
            if(empty($c_id)){
                _message('参数错误');
            }
            if(empty($name)){
                _message('分类名称不能为空');
            }
            //验证所属分类下是否已存在该分类，排除自己
            $res = $this->db->GetOne("select * from `@#_act_category` WHERE `c_name`='{$name}' AND `c_pid`=$pid AND `c_id` != $c_id");
            if($res){
                _message('所属分类下已存在该分类，请重新输入');
            }
            //获取所有的分类id
            $cate = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
            $ids = getChildid($cate,$c_id); //获取当前分类的子类
            $ids[] = $c_id; //排除自己做为父类
            if(in_array($pid,$ids)){
                _message('上级分类不能是当前分类或当前分类的子类');
            }
            //更新
            $sql = "update `@#_act_category` set `parentid`=$pid,`c_name`='$name',`c_icon`='$act_icon',`c_sort`=$sort WHERE `c_id`=$c_id";
            $row = $this->db->Query($sql);
            if($row){
                _message("修改成功!",G_ADMIN_PATH.'/activity/category',1);
            }else{
                _message("修改失败!",'',1);
            }
        }
        //查询所有的分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $categoryshtml="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $tree->init($categories);
        //获取分类id
        $cid = intval($this->segment(4));
        $category = $this->db->GetOne("select * from `@#_act_category` WHERE `c_id`=$cid");
        if($category){
            $categoryshtml=$tree->get_tree(0,$categoryshtml,$category['parentid']);
        }else{
            $categoryshtml=$tree->get_tree(0,$categoryshtml);
        }
        include $this->tpl(ROUTE_M,'activity.categoryEdit');
    }
    /**
     * 删除活动分类
     */
    public function categoryDel(){
        $c_id = intval($_GET['id']);
        if(empty($c_id)){
            echo '分类id不存在！';
            exit;
        }
        //查询分类id是否存在子类
        $subCate = $this->db->GetOne("select * from `@#_act_category` WHERE `parentid`= $c_id");
        if($subCate){
            echo '当前分类存在子类，不可删除';
            exit;
        }
        //删除分类
        $row = $this->db->Query("delete from `@#_act_category` WHERE `c_id`=$c_id");
        if($row){
            echo 'ok';
            exit;
        }else{
            echo '删除失败';
            exit;
        }
    }
    /**
     * 活动分类列表
     */
    public function category(){
        //查询所有的活动分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        foreach ($categories as $k => $v){
            if($v['c_is_show']){
                $v['state'] = G_GLOBAL_STYLE."/global/image/sure.png";
            }else{
                $v['state'] = G_GLOBAL_STYLE."/global/image/cancel1.png";
            }
            $v['c_icon'] = G_UPLOAD_PATH.'/'.$v['c_icon'];
            $v['addSub'] = G_ADMIN_PATH.'/'.ROUTE_C.'/categoryAdd/';
            $v['editCate'] = G_ADMIN_PATH.'/'.ROUTE_C.'/categoryEdit/';
            $v['delCate'] = G_ADMIN_PATH.'/'.ROUTE_C.'/categoryDel/';
            $categories[$k] = $v;
        }
        //echo '<pre>';
        //var_dump($categories);exit;
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $html=<<<HTML
            <tr>
            <td align='center'><input name='listorders[\$c_id]' type='text' size='3' value='\$c_sort' class='input-text-c'></td>
            <td align='center'>\$c_id</td>
            <td align='left'>\$spacer\$c_name</th>
            <td align='center'><img src='\$c_icon' class='icon'></td>
            <td align='center'><img src='\$state' data-col-name='show_flag' data-id='\$c_id' onclick=\"changeStatus(this)\"></td>
            <td align='center'>
                <a href='\$addSub\$c_id'>添加子分类</a><span class='span_fenge lr5'>|</span>
                <a href='\$editCate\$c_id'>修改</a><span class='span_fenge lr5'>|</span>
                <a href=\"javascript:window.parent.ajaxDel('\$delCate',{id:\$c_id},'get', '确认删除『 \$c_name 』栏目？');\">删除</a>
            </td>
            </tr>
HTML;

        //echo $html;exit;
        $tree->init($categories);
        $html=$tree->get_tree(0,$html);
        include $this->tpl(ROUTE_M,'activity.category');
    }
    /**
     * 活动分类排序
     */
    public function listorder(){
        if ($this->segment(4) == 'dosubmit') {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->db->Query("UPDATE `@#_act_category` SET `c_sort` = '$listorder' where `c_id` = '$id'");
            }
            _message("排序更新成功");
        } else {
            _message("请排序");
        }
    }
    /**
     * ajax更改活动分类列表中的是否显示状态
     */
    public function ajaxCateSateSet(){
        $col_name = isset($_GET['col_name'])?safe_replace($_GET['col_name']):'';
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        $status = isset($_GET['status'])?intval($_GET['status']):2;
        //p($status);exit;
        if(empty($col_name)){
            echo json_encode('参数不能为空');
            exit;
        }
        if(empty($id)){
            echo json_encode('分类不存在');
            exit;
        }
        if($status == 2 && $status != 1 && $status !=0){
            echo json_encode('不存在的状态');
            exit;
        }

        //根据状态值查询对应的值，并取反更新
        switch ($col_name){
            case 'show_flag':
                $field = 'c_is_show';
                break;
        }
        $sql = "update `@#_act_category` set `$field`=$status WHERE `c_id`=$id";
        $row = $this->db->Query($sql);
        if($row){
            echo json_encode('ok');
            exit;
        }else {
            echo json_encode('设置失败');
            exit;
        }
    }

    /********************************** 活动分类--end-- ****************************************/

    /********************************** 活动管理--start-- ****************************************/

    /**
     * 活动报名列表
     */
    public function signList(){
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_activity` WHERE 1");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}

        $page->config($total,$num,$pagenum,"0");
        $sql = "select * from `@#_activity` WHERE 1 ORDER BY act_time DESC";
        $act_info = $this->db->GetPage($sql,array('key'=>'act_id', 'type' => 1, 'num' => $num, 'page' => $pagenum));
        $act_key = implode(',',array_keys($act_info));
        //p($act_key);
        $chargeInfo = $this->db->GetList("select * from `@#_act_charge` WHERE `c_act_id` IN ($act_key)",array('key'=>'c_id'));
        include $this->tpl(ROUTE_M,'activity.signList');
    }
    /**
     * 活动报名详细表
     */
    public function signListDetail(){

        //获取活动id
        $act_id = intval($this->segment(4));
        if(empty($act_id)){
            _message('报名信息不存在');
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_act_sign` WHERE `s_act_id`=$act_id AND `s_status`='已支付'");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $sql = "select * from `@#_act_sign` a LEFT JOIN `@#_act_order` b ON a.`s_id` = b.`o_sid` WHERE a.`s_act_id`=$act_id AND a.`s_status`='已支付'";
        $signInfo = $this->db->GetPage($sql);
        include $this->tpl(ROUTE_M,'activity.signListDetail');
    }
    /**
     * 导出不同状态的订单
     */
    public function export_sign(){
        //引入phpecxcel类文件
        require G_PLUGIN.'PHPExcel/PHPExcel.php';
        //实例化phpexcel
        $objPHPExcel = new PHPExcel();
        //获取数据
        //var_dump($_POST);
        $act_id = intval($_POST['act_id']);
        $sql = "select * from `@#_act_sign` a LEFT JOIN `@#_act_order` b ON a.`s_id` = b.`o_sid` WHERE a.`s_act_id`=$act_id AND a.`s_status`='已支付'";
        $signList = $this->db->GetList($sql);
        //查询活动信息
        $sql = "select * from `@#_activity` WHERE `act_id`=$act_id";
        $activity = $this->db->GetOne($sql);
        $objActSheet = $objPHPExcel->getActiveSheet(); //获取当前活动表
        $title = $activity['act_title'].'活动报名表';
        $act_title = $activity['act_title'].'活动报名表 '.date('Y-m-d H:i',$activity['act_start_time']).'-'.date('Y-m-d H:i',$activity['act_end_time']);
        //p($title);exit;
        $objActSheet -> setTitle($title); //设置excel表内容的标题
        $filename = $title.'.xlsx';
        //为excel表格添加标题
        $objActSheet -> mergeCells('A1:E1');
        $objActSheet -> setCellValue('A1',$act_title);
        //为excel表格添加表头
        $objActSheet -> setCellValue('A2','ID')
            -> setCellValue('B2','活动主题')
            -> setCellValue('C2','报名人')
            -> setCellValue('D2','微信昵称')
            -> setCellValue('E2','联系电话')
            -> setCellValue('F2','身份证号');
        if($signList){
            $i = 3;
            foreach ($signList as $v){
                $objActSheet -> setCellValue('A'.$i,$v['s_id'])
                    -> setCellValue('B'.$i,$v['o_act_title'])
                    -> setCellValue('C'.$i,$v['s_username'])
                    -> setCellValue('D'.$i,$v['o_username'])
                    -> setCellValue('E'.$i,' '.$v['s_mobile'])
                    -> setCellValue('F'.$i,' '.$v['s_ID_card']);
                $i++;
            }
        }
        // 生成2007excel格式的xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save( 'php://output');
        exit;

        //p($orderList);exit;
    }
    /**
     * 删除报名信息
     */
    public function delSign(){
        $act_id = isset($_GET['id'])?intval($_GET['id']):0;
        //验证数据的合法性
        if(empty($act_id)){
            exit('参数不能为空！');
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `act_id` from `@#_activity` WHERE `act_id`=$act_id");
        if(!$res){
            exit('不存在该数据！');
        }
        $this->db->Autocommit_start();
        //删除数据
        $row_1 = $this->db->Query("delete from `@#_act_sign` WHERE `s_act_id`=$act_id");
        $row_2 = $this->db->Query("delete from `@#_act_order` WHERE `o_act_id`=$act_id");
        $up_activity = $this->db->Query("update `@#_activity` set `act_num_signed`= 0 WHERE `act_id`=$act_id");
        if($row_1 && $row_2 && $up_activity){
            $this->db->Autocommit_commit();
            exit('ok');
        }else{
            $this->db->Autocommit_rollback();
            exit('报名信息删除失败');
        }
    }

    /**
     * 活动列表
     */
    public function lists(){
        if($_POST['search']){
            //获取搜索条件
            //p($_POST);
            $startTime = isset($_POST['startTime'])?strtotime(safe_replace($_POST['startTime'])):'';
            $endTime = isset($_POST['endTime'])?strtotime(safe_replace($_POST['endTime'])):'';
            if($startTime && $endTime){
                if($startTime>$endTime)_message('搜索的活动时间有误');
                $list_where = "`act_start_time` > $startTime AND `act_end_time` < $endTime";
            }
            if($startTime && empty($endTime)){
                $list_where = "`act_start_time` > $startTime";
            }
            if($endTime && empty($startTime)){
                $list_where = "`act_end_time` < $endTime";
            }
            if(empty($startTime) && empty($endTime)){
                $list_where = 1;
            }
        }
        //查询活动数据
        if(!isset($startTime)&&!isset($endTime)){
            $list_where = 1;
            $startTime = '';
            $endTime = '';
        }
        //p($list_where);
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_activity` WHERE $list_where");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
        $sql = "select * from `@#_activity` WHERE ".$list_where." ORDER BY `act_time` DESC";
        $act_info = $this->db->GetPage($sql,array('key'=>'act_id', 'num' => $num, 'page' => $pagenum));
        $act_key = implode(',',array_keys($act_info));
        //p($act_key);
        //echo '<pre>';
        foreach ($act_info as $key => $val){
            $cateStr = $this->db->GetOne("select group_concat(`c_name`) as name from `@#_act_category` WHERE `c_id` IN ({$val['act_category']})");
            $act_info[$key]['c_name'] = $cateStr['name'];
        }

        include $this->tpl(ROUTE_M,'activity.lists');
    }
    /**
     * ajax查询活动内容
     */
    public function ajaxGetContent(){
        //判断是否是ajax请求
        if($_GET){
            $id = $_GET['id'];
            $sql = "select `act_content` from `@#_activity` WHERE `act_id`=$id";
            $res = $this->db->GetOne($sql);
            //var_dump($res);exit;
            //var_dump($doc->getLastSql());exit;
            //进行标签实体转译
            echo htmlspecialchars_decode($res['act_content']);
        }
    }
    /**
     * ajax活动报名有效期和推荐设置
     */
    public function ajaxActSignSet(){
        $col_name = isset($_GET['col_name'])?safe_replace($_GET['col_name']):'';
        $act_id = isset($_GET['act_id'])?intval($_GET['act_id']):0;
        $status = isset($_GET['status'])?intval($_GET['status']):2;
        //p($status);exit;
        if(empty($col_name)){
            echo json_encode('参数不能为空');
            exit;
        }
        if(empty($act_id)){
            echo json_encode('活动不存在');
            exit;
        }
        if($status == 2 && $status != 1 && $status !=0){
            echo json_encode('不存在的状态');
            exit;
        }

        //根据状态值查询对应的值，并取反更新
        switch ($col_name){
            case 'recommend':
                $field = 'act_recommend';
                break;
            case 'sign_flag':
                $field = 'act_sign_flag';
                break;
            case 'active':
                $field = 'act_active';
                break;
        }
        $sql = "update `@#_activity` set `$field`=$status WHERE `act_id`=$act_id";
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
     * 发布新活动
     */
    public function add(){
        if($_POST['dosubmit']){
            //echo '<pre>';
            //p($_POST);exit;
            //获取数据
            $title = isset($_POST['title'])?safe_replace($_POST['title']):'';
            $cateId = isset($_POST['cid'])?$_POST['cid']:0; //活动分类
            $active = isset($_POST['active'])?intval($_POST['active']):1; //活动是否上架
            $startTime = isset($_POST['startTime'])?strtotime(safe_replace($_POST['startTime'])):'';
            $endTime = isset($_POST['endTime'])?strtotime(safe_replace($_POST['endTime'])):'';
            $address = isset($_POST['address'])?safe_replace($_POST['address']):'';
            $latlng = isset($_POST['latlng'])?safe_replace($_POST['latlng']):'';

            $numOfPeople = isset($_POST['number'])?intval($_POST['number']):0;
            $charge = isset($_POST['charge'])?sprintf('%.2f',$_POST['charge']):0; //活动费用

            /* 多种消费
             * $numOfPeople = isset($_POST['num'])?$_POST['num']:0;
            $charge_name = isset($_POST['charge_name'])?$_POST['charge_name']:'';
            $charge = isset($_POST['charge'])?$_POST['charge']:'';*/
            //积分抵现
            $integral = isset($_POST['integral'])?sprintf("%.2f",$_POST['integral']):-1;
            //阶梯价格
            $num = isset($_POST['num'])?$_POST['num']:'';
            $price = isset($_POST['price'])?$_POST['price']:'';
            $fare = isset($_POST['fare'])?intval($_POST['fare']):0; //拼车费
            $give_integral = isset($_POST['give_integral'])?intval($_POST['give_integral']):0;

            //分享
            $share_integral = isset($_POST['share_integral'])?intval($_POST['share_integral']):0;
            $share_title = isset($_POST['share_title'])?safe_replace($_POST['share_title']):'';
            $share_desc = isset($_POST['share_desc'])?safe_replace($_POST['share_desc']):'';
            $share_icon = isset($_POST['share_icon'])?safe_replace($_POST['share_icon']):'';

            //筛选属性
            $attr = isset($_POST['attr'])?safe_replace($_POST['attr']):'';

            $home_poster = isset($_POST['home_poster'])?safe_replace($_POST['home_poster']):''; //活动首页的海报
            $poster = isset($_POST['poster'])?safe_replace($_POST['poster']):'';
            $act_desc = isset($_POST['act_desc'])?safe_replace(trim($_POST['act_desc'])):''; //活动描述
            $content = isset($_POST['content'])?htmlspecialchars($_POST['content']):'';
            $recommend = isset($_POST['recommend'])?intval($_POST['recommend']):0;
            $best = isset($_POST['best'])?intval($_POST['best']):0; //是否是精品
            $sale = isset($_POST['sale'])?intval($_POST['sale']):0; //是否是特价
            /*$sign_flag = isset($_POST['flag'])?intval($_POST['flag']):0;*/
            $consult = isset($_POST['consult'])?safe_replace($_POST['consult']):'';
            $notice = isset($_POST['notice'])?$_POST['notice']:'';
            $act_time = time();
            if(empty($title))_message('活动主题不能为空');
            if(empty($cateId))_message('活动分类不能为空');
            if(empty($startTime)||empty($endTime))_message('活动报名时间不能为空');
            if($startTime>$endTime)_message('活动时间有误');
            if(empty($address))_message('活动地点不能为空');
            if(empty($home_poster))_message('活动首页的宣传画不能为空');
            if(empty($poster))_message('活动宣传画不能为空');
            if(empty($notice))_message('活动须知不能为空');
            if(empty($content))_message('活动详情不能为空');
            if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $consult)){
                _message('请输入正确的咨询方式');
            }
            /* 多种费用项
             * $count = 0; //多种收费类型的总人数限制
            foreach ($charge_name as $k => $v){
                if(empty($v)){
                    _message('收费类型不能为空');
                }else{
                    $count += $numOfPeople[$k];
                }
            }*/
            //判断活动费用是否免费，免费不参加拼团
            if($charge == '0.00'){
                $is_group = 0;
            }elseif (!empty($sale)){
                $is_group = 0;
            }else{
                $is_group = 1;
            }
            //验证阶梯团的数据合法性
            if(!empty($charge)){
                if(empty($num) || empty($price)){
                    _message('阶梯团不能为空');
                }
                if(count($num) != count($price)){
                    _message('请输入正确的价格阶梯');
                }
                //判断拼车费和积分兑换金额不能大于活动费用
                if($fare > $charge){
                    _message('拼车费不能超过活动费用');
                }
                if($integral > $charge){
                    _message('积分兑换金额不能超过活动费用');
                }
            }
            //积分设置
            if($integral == -1){
                $integral = sprintf("%.2f",$charge); //积分兑换
            }
            if($give_integral == -1){
                $give_integral = intval($charge);
            }
            if($share_integral == -1){
                $share_integral = intval($charge);
            }
            //分享标题、描述、图标
            if(empty($share_title)){
                $share_title = $title;
            }
            if(empty($share_icon)){
                _message('分享图标不能为空');
            }
            //对过滤为空的数组元素:包含，null,0,false,''
            $notice = array_filter($notice);
            $num = array_filter($num);
            $cateId = array_filter($cateId);
            $cateId = $cid = array_unique($cateId); //移除重复的
            $cateId = implode(',',$cateId);  //拼接字符串，'1,2,3...'
            //var_dump($cateId);exit;

            //数据入库
            $sql = "insert into `@#_activity` (`act_category`,`act_title`,`act_desc`,`act_content`,`act_home_poster`,`act_poster`,`act_charge`,`act_time`,`act_start_time`,`act_end_time`,`act_address`,`act_latlng`,`act_num_limit`,`act_consult`,`act_recommend`,`act_active`,`act_best`,`integral`,`give_integral`,`share_integral`,`act_fare`,`act_is_group`) VALUES ('$cateId','$title','$act_desc','$content','$home_poster','$poster',$charge,$act_time,$startTime,$endTime,'$address','$latlng',$numOfPeople,'$consult',$recommend,$active,$best,$integral,$give_integral,$share_integral,$fare,$is_group)";
            $this->db->Autocommit_start();
            $query_1 = $this->db->Query($sql);
            $act_id = $this->db->insert_id();
            //p($act_id);
            /* 多种费用项
             * $sql = "insert into `@#_act_charge` (`c_act_id`,`c_name`,`c_money`,`c_num_limit`) VALUES ";
            $insert_value = "";
            foreach ($charge_name as $k => $v){
                $insert_value .= "($act_id,'$v','".sprintf("%.2f",$charge[$k])."',$numOfPeople[$k]),";
            }
            $insert_value = rtrim($insert_value,',');
            $sql = $sql.$insert_value;*/
            //p($sql);exit;
            //p($query_1);
            //p($query_2);exit;
            //插入价格阶梯
            $query_3 = true;
            if(!empty($num)){
                sort($num);
                rsort($price);
                $sql = "insert into `@#_act_step` (`act_id`,`num`,`money`) VALUES ";
                $insert_step = "";
                foreach ($num as $k1 => $v1){
                    //过滤超过人数限制
                    if($v1 <= $numOfPeople || $numOfPeople == 0){
                        $insert_step .= "($act_id,$v1,$price[$k1]),";
                    }
                }
                $insert_step = rtrim($insert_step,',');
                $sql = $sql.$insert_step;
                $query_3 = $this->db->Query($sql);
            }

            //插入活动须知表
            $query_4 = true;
            if(!empty($notice)){
                $sql = "insert into `@#_act_notice` (`n_act_id`,`n_notice`) VALUES ";
                $insert_value = '';
                foreach ($notice as $v){
                    $insert_value .= "($act_id,'$v'),";
                }
                $insert_value = rtrim($insert_value,',');
                $sql = $sql.$insert_value;
                //var_dump($sql);exit;
                $query_4 = $this->db->Query($sql);
            }

            //插入筛选属性
            $sql = "insert into `@#_act_filter` (`act_id`,`attr_id`,`attr_value`) VALUES ";
            $insert_filter = '';
            foreach ($attr as $key => $item) {
                $insert_filter .= "($act_id,$key,'$item'),";
            }
            $insert_filter = rtrim($insert_filter,',');
            $sql = $sql.$insert_filter;
            $query_5 = $this->db->Query($sql);

            //插入分享信息
            $link = WEB_PATH.'/mobile/activity/activity/'.$act_id;
            $query_6 = $this->db->Query("insert into `@#_item_share` (`type`,`item_id`,`title`,`description`,`link`,`icon`) VALUES (1,$act_id,'$share_title','$share_desc','$link','$share_icon')");
            //数据存入cookie进行缓存，以便下次编辑可以直接使用
            $act_cookie = array(
                'cid' => $cid,
                'title' => $title,
                'act_desc' => $act_desc,
                'content' => $content,
                'home_poster' => $home_poster,
                'poster' => $poster,
                'charge' => $charge,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'address' => $address,
                'latlng' => $latlng,
                'numOfPeople' => $numOfPeople,
                'consult' => $consult,
                'recommend' => $recommend,
                'active' => $active,
                'best' => $best,
                'sale' => $sale,
                'integral' => $integral,
                'give_integral' => $give_integral,
                'share_integral' => $share_integral,
                'fare' => $fare,
                'num' => $num,
                'price' => $price,
                'notice' => $notice,
                'share_title' => $share_title,
                'share_desc' => $share_desc,
                'share_icon'=> $share_icon,
                'attr' => $attr
            );
            //设置cookie,用来下次添加时还存在
            _setcookie('act_cookie',json_encode($act_cookie),'');
            if($query_1 && $query_3 && $query_4 && $query_5 && $query_6){
                $this->db->Autocommit_commit();
                _message("活动发布成功!",G_ADMIN_PATH.'/activity/lists',1);
            }else{
                $this->db->Autocommit_rollback();
                _message("活动发布失败!",'',1);
            }
        }

        $act_cookie = json_decode(stripslashes(_getcookie('act_cookie')),true);
        //echo '<pre>';
        //var_dump($act_cookie);

        //17.02.08 添加筛选属性
        $attribute = $this->db->GetList("select * from `@#_act_attr` WHERE `type_id`=0 AND `is_show`=1");
        //获取筛选属性值数组
        foreach ($attribute as $k => $v){
            $attr_value = str_replace('，', ',', $v['value']);
            $attr = explode(',',$attr_value);
            $attribute[$k]['value'] = $attr;
        }
        //17.02.08 添加装备推荐
        $equipment = $this->db->GetList("select * from `@#_shoplist` WHERE 1");

        //查询所有的分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $categoryshtml="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $tree->init($categories);
        if(isset($act_cookie['cid']) && is_array($act_cookie['cid'])){ //从缓存中读取之前数据
            foreach ($act_cookie['cid'] as $key => $val){
                $tree->ret = '';
                $categorys[$key] = $tree->get_tree(0,$categoryshtml,$val);
            }
        }else{
            $categoryshtml=$tree->get_tree(0,$categoryshtml);
        }
        //var_dump($categorys);exit;
        //var_dump($categorys);exit;
        include $this->tpl(ROUTE_M,'activity.add');
    }

    /**
     * 修改活动
     */
    public function edit(){
        if($_POST['dosubmit']){
            //p($_POST);exit;
            //获取数据
            $act_id = isset($_POST['act_id'])?intval($_POST['act_id']):0; //活动id
            $cateId = isset($_POST['cid'])?$_POST['cid']:0; //活动分类
            $active = isset($_POST['active'])?intval($_POST['active']):1; //活动是否上架
            $title = isset($_POST['title'])?safe_replace($_POST['title']):'';
            $startTime = isset($_POST['startTime'])?strtotime(safe_replace($_POST['startTime'])):'';
            $endTime = isset($_POST['endTime'])?strtotime(safe_replace($_POST['endTime'])):'';
            $address = isset($_POST['address'])?safe_replace($_POST['address']):'';
            $latlng = isset($_POST['latlng'])?safe_replace($_POST['latlng']):'';

            //积分抵现
            $integral = isset($_POST['integral'])?sprintf("%.2f",$_POST['integral']):-1;

            //费用项
            $numOfPeople = isset($_POST['number'])?intval($_POST['number']):0;
            $charge = isset($_POST['charge'])?sprintf("%.2f",$_POST['charge']):0;

            //阶梯价格
            $num = isset($_POST['num'])?$_POST['num']:'';
            $price = isset($_POST['price'])?$_POST['price']:'';
            $fare = isset($_POST['fare'])?intval($_POST['fare']):0; //拼车费
            $give_integral = isset($_POST['give_integral'])?intval($_POST['give_integral']):0;
            $step_id = isset($_POST['step_id'])?$_POST['step_id']:'';  //要修改的阶梯id

            //分享
            $share_integral = isset($_POST['share_integral'])?intval($_POST['share_integral']):0;
            $share_title = isset($_POST['share_title'])?safe_replace($_POST['share_title']):'';
            $share_desc = isset($_POST['share_desc'])?safe_replace($_POST['share_desc']):'';
            $share_icon = isset($_POST['share_icon'])?safe_replace($_POST['share_icon']):'';
            $share_id = isset($_POST['share_id'])?intval($_POST['share_id']):0; //要修改的分享id

            //筛选属性
            $attr = isset($_POST['attr'])?safe_replace($_POST['attr']):'';
            $attr_id = isset($_POST['attr_id'])?$_POST['attr_id']:0; //要修改的属性id

            $home_poster = isset($_POST['home_poster'])?safe_replace($_POST['home_poster']):''; //活动首页的还报
            $poster = isset($_POST['poster'])?safe_replace($_POST['poster']):'';
            $act_desc = isset($_POST['act_desc'])?safe_replace(trim($_POST['act_desc'])):''; //活动描述
            $content = isset($_POST['content'])?htmlspecialchars($_POST['content']):'';
            $recommend = isset($_POST['recommend'])?intval($_POST['recommend']):0;
            $best = isset($_POST['best'])?intval($_POST['best']):0; //是否是精品
            $sale = isset($_POST['sale'])?intval($_POST['sale']):0; //是否是特价
            /*$sign_flag = isset($_POST['flag'])?intval($_POST['flag']):0; //报名标志*/
            $consult = isset($_POST['consult'])?safe_replace($_POST['consult']):'';
            $notice = isset($_POST['notice'])?$_POST['notice']:''; //活动须知
            $n_id = isset($_POST['n_id'])?$_POST['n_id']:0; //要修改须知id
            $act_time = time();
            if(empty($act_id))_message('活动不存在');
            if(empty($cateId))_message('活动分类不能为空');
            if(empty($title))_message('活动主题不能为空');
            if(empty($startTime)||empty($endTime))_message('活动报名时间不能为空');
            if($startTime>$endTime)_message('活动时间有误');
            if(empty($address))_message('活动地点不能为空');
            if(empty($home_poster))_message('活动首页的宣传画不能为空');
            if(empty($poster))_message('活动宣传画不能为空');
            if(empty($content))_message('活动详情不能为空');
            if(empty($share_id))_message('分享参数有误');
            if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $consult)){
                _message('请输入正确的咨询方式');
            }
            //var_dump($content);exit;

            //判断活动费用是否免费，免费不参加拼团
            if($charge == '0.00'){
                $is_group = 0;
            }elseif (!empty($sale)){
                $is_group = 0;
            }else{
                $is_group = 1;
            }

            //积分设置
            if($integral == -1){
                $integral = sprintf("%.2f",$charge); //积分兑换
            }
            if($give_integral == -1){
                $give_integral = intval($charge);
            }
            if($share_integral == -1){
                $share_integral = intval($charge);
            }

            if(!empty($charge)){
                if(empty($num) || empty($price)){
                    _message('阶梯团不能为空');
                }
                if(count($num) != count($price)){
                    _message('请输入正确的价格阶梯');
                }
                //判断拼车费和积分兑换金额不能大于活动费用
                if($fare > $charge){
                    _message('拼车费不能超过活动费用');
                }
                if($integral > $charge){
                    _message('积分兑换金额不能超过活动费用');
                }
            }
            //分享标题、描述、图标
            if(empty($share_title)){
                $share_title = $title;
            }
            if(empty($share_icon)){
                _message('分享图标不能为空');
            }
            $num = array_filter($num);
            $step_id = array_filter($step_id);

            $notice = array_filter($notice); //过滤空元素
            $n_id = array_filter($n_id); //过滤空元素

            $cateId = array_filter($cateId);
            $cateId = $cid = array_unique($cateId); //移除重复的
            $cateId = implode(',',$cateId);  //拼接字符串，'1,2,3...'
            //更新数据库
            $sql = "update `@#_activity` set `act_category`='$cateId',`act_title`='$title',`act_desc`='$act_desc',`act_content`='$content',`act_home_poster`='$home_poster',`act_poster`='$poster',`act_charge`=$charge,`act_time`=$act_time,`act_start_time`=$startTime,`act_end_time`=$endTime,`act_address`='$address',`act_latlng`='$latlng',`act_num_limit`=$numOfPeople,`act_consult`='$consult',`act_recommend`=$recommend,`act_active`=$active,`act_best`=$best,`integral`=$integral,`give_integral`=$give_integral,`share_integral`=$share_integral,`act_fare`=$fare,`act_is_group`=$is_group WHERE `act_id`=$act_id";
            //p($sql);exit;
            $query = $this->db->Query($sql);
            //var_dump($query);exit;
            //更新阶梯表
            if(!empty($num)){
                $upStepId = $this->db->GetList("select `id` from `@#_act_step` WHERE `act_id` = $act_id");
                $temp_step = $temp_step1 = array();
                foreach ($upStepId as $v){
                    $temp_step[] = $v['id'];
                }
                sort($num);
                rsort($price);
                $upStepId = $temp_step;
                foreach ($num as $k1 => $v1){
                    //过滤超过人数限制
                    if($v1 <= $numOfPeople || $numOfPeople == 0) {
                        if (!empty($step_id[$k1])) {
                            //更新原有的数据
                            $row_1 = $this->db->Query("update `@#_act_step` set `num`=$v1,`money`= {$price[$k1]} WHERE `id`=$step_id[$k1]");
                            if (!$row_1) {
                                _message('修改失败', '', 1);
                            }
                            $temp_step1[] = $step_id[$k1];  //更新的id，用来排除无效的价格阶梯
                        } else {
                            //插入最新的
                            $row_2 = $this->db->Query("insert into `@#_act_step` (`act_id`,`num`,`money`) VALUES ($act_id,$v1,{$price[$k1]})");
                            if (!$row_2) {
                                _message('修改失败', '', 1);
                            }
                        }
                    }
                }
                //p($delId);
                //删除不要的活动阶梯
                $delStepId = array_diff($upStepId,$temp_step1);
                $delStepId = implode(',',$delStepId);
                if(!empty($delStepId)){
                    $row = $this->db->Query("delete from `@#_act_step` WHERE `id` IN ($delStepId)");
                }
                //p($upStepId);exit;
            }else{
                $row = $this->db->Query("delete from `@#_act_step` WHERE `act_id` = $act_id");
                $row_1 = $row_2 = true;
            }

            //更新筛选属性表
            foreach ($attr as $k2 => $v2){
                $up_attr = $this->db->Query("update `@#_act_filter` set `attr_id`=$k2,`attr_value`='$v2' WHERE `id`=$attr_id[$k2] AND `act_id`=$act_id");
            }
            //更新分享表
            $link = WEB_PATH.'/mobile/activity/activity/'.$act_id;
            $up_share = $this->db->Query("update `@#_item_share` set `title`='$share_title',`description`='$share_desc',`link`='$link',`icon`='$share_icon' WHERE `type`=1 AND `item_id`=$act_id");
            //更新活动须知表
            if(!empty($notice)){
                $delId = $this->db->GetList("select `n_id` from `@#_act_notice` WHERE `n_act_id` = $act_id");  //查询原有的活动须知
                $temp_arr = $temp_arr1 = array();
                foreach ($delId as $v){
                    $temp_arr[] = $v['n_id'];
                }
                $delId = $temp_arr;
                foreach ($notice as $k => $v){
                    if(!empty($n_id[$k])){
                        //更新原有的数据
                        $row_3 = $this->db->Query("update `@#_act_notice` set `n_notice`='{$v}' WHERE `n_id`=$n_id[$k]");
                        if(!$row_3){
                            _message('修改失败','',1);
                        }
                        $temp_arr1[] = $n_id[$k];  //更新的id，用来排除无效的活动须知
                    }else{
                        //插入最新的
                        $row_4 = $this->db->Query("insert into `@#_act_notice` (`n_act_id`,`n_notice`) VALUES ($act_id,'{$v}')");
                        if(!$row_4){
                            _message('修改失败','',1);
                        }
                    }
                }
                //p($delId);
                //删除不要的活动须知
                $delId = array_diff($delId,$temp_arr1);
                $delId = implode(',',$delId);
                if(!empty($delId)){
                    $row = $this->db->Query("delete from `@#_act_notice` WHERE `n_id` IN ($delId)");
                }
            }else{
                $row = $this->db->Query("delete from `@#_act_notice` WHERE `n_act_id` = $act_id");
                $row_3 = $row_4 = true;
            }
            if($query && $up_attr && $up_share && ($row_1 || $row_2) && ($row_3 || $row_4)){
                _message("活动修改成功!",G_ADMIN_PATH.'/activity/lists',1);
            }else{
                $this->db->Autocommit_rollback();
                _message("活动修改失败!",'',1);
            }
        }

        //获取要修改的活动id
        $act_id = intval($this->segment(4));
        if(empty($act_id))_message('不存在该活动');
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$act_id");
        //价格阶梯
        $price_step = $this->db->GetList("select * from `@#_act_step` WHERE `act_id`=$act_id");
        //var_dump($price_step);exit;
        //分享
        $shareInfo = $this->db->GetOne("select * from `@#_item_share` WHERE `item_id`=$act_id AND `type`=1");
        //筛选属性
        $attribute = $this->db->GetList("select * from `@#_act_attr` WHERE `type_id`=0 AND `is_show`=1");
        //获取筛选属性值数组
        foreach ($attribute as $k => $v){
            $attr_value = str_replace('，', ',', $v['value']);
            $attr = explode(',',$attr_value);
            $attribute[$k]['value'] = $attr;
        }
        $act_attr = $this->db->GetList("select * from `@#_act_filter` WHERE `act_id`=$act_id");

        //查询所有的分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $categoryshtml="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $tree->init($categories);
        $cid = explode(',',$activity['act_category']);
        //var_dump($cid);exit;

        $categorys = array();
        foreach ($cid as $key => $val){
            $tree->ret = '';
            $categorys[$key] = $tree->get_tree(0,$categoryshtml,$val);
        }

        //$categoryshtml=$tree->get_tree(0,$categoryshtml,$activity['act_category']);
        //echo '<pre>';
        //var_dump($categorys);exit;
        $notice = $this->db->GetList("select * from `@#_act_notice` WHERE `n_act_id`=$act_id ORDER BY `n_id` ASC");
        $count = count($notice);
        include $this->tpl(ROUTE_M,'activity.edit');
    }

    /**
     * 复制同一个活动
     */
    public function copy(){
        if($_POST['dosubmit']){
            //p($_POST);exit;
            //获取数据
            $act_id = isset($_POST['act_id'])?intval($_POST['act_id']):0; //活动id
            $cateId = isset($_POST['cid'])?$_POST['cid']:0; //活动分类
            $active = isset($_POST['active'])?intval($_POST['active']):1; //活动是否上架
            $title = isset($_POST['title'])?safe_replace($_POST['title']):'';
            $startTime = isset($_POST['startTime'])?strtotime(safe_replace($_POST['startTime'])):'';
            $endTime = isset($_POST['endTime'])?strtotime(safe_replace($_POST['endTime'])):'';
            $address = isset($_POST['address'])?safe_replace($_POST['address']):'';
            $latlng = isset($_POST['latlng'])?safe_replace($_POST['latlng']):'';

            //积分抵现
            $integral = isset($_POST['integral'])?sprintf("%.2f",$_POST['integral']):-1;

            //费用项
            $numOfPeople = isset($_POST['number'])?intval($_POST['number']):0;
            $charge = isset($_POST['charge'])?sprintf("%.2f",$_POST['charge']):0;

            //阶梯价格
            $num = isset($_POST['num'])?$_POST['num']:'';
            $price = isset($_POST['price'])?$_POST['price']:'';
            $fare = isset($_POST['fare'])?intval($_POST['fare']):0; //拼车费
            $give_integral = isset($_POST['give_integral'])?intval($_POST['give_integral']):0;

            //分享
            $share_integral = isset($_POST['share_integral'])?intval($_POST['share_integral']):0;
            $share_title = isset($_POST['share_title'])?safe_replace($_POST['share_title']):'';
            $share_desc = isset($_POST['share_desc'])?safe_replace($_POST['share_desc']):'';
            $share_icon = isset($_POST['share_icon'])?safe_replace($_POST['share_icon']):'';

            //筛选属性
            $attr = isset($_POST['attr'])?safe_replace($_POST['attr']):'';
            $attr_id = isset($_POST['attr_id'])?$_POST['attr_id']:0; //要修改的属性id

            $home_poster = isset($_POST['home_poster'])?safe_replace($_POST['home_poster']):''; //活动首页的还报
            $poster = isset($_POST['poster'])?safe_replace($_POST['poster']):'';
            $act_desc = isset($_POST['act_desc'])?safe_replace(trim($_POST['act_desc'])):''; //活动描述
            $content = isset($_POST['content'])?htmlspecialchars($_POST['content']):'';
            $recommend = isset($_POST['recommend'])?intval($_POST['recommend']):0;
            $best = isset($_POST['best'])?intval($_POST['best']):0; //是否是精品
            $sale = isset($_POST['sale'])?intval($_POST['sale']):0; //是否是特价
            /*$sign_flag = isset($_POST['flag'])?intval($_POST['flag']):0; //报名标志*/
            $consult = isset($_POST['consult'])?safe_replace($_POST['consult']):'';
            $notice = isset($_POST['notice'])?$_POST['notice']:''; //活动须知
            $act_time = time();
            if(empty($act_id))_message('活动不存在');
            if(empty($cateId))_message('活动分类不能为空');
            if(empty($title))_message('活动主题不能为空');
            if(empty($startTime)||empty($endTime))_message('活动报名时间不能为空');
            if($startTime>$endTime)_message('活动时间有误');
            if(empty($address))_message('活动地点不能为空');
            if(empty($home_poster))_message('活动首页的宣传画不能为空');
            if(empty($poster))_message('活动宣传画不能为空');
            if(empty($content))_message('活动详情不能为空');
            if(!preg_match("/^((0\d{2,3}\-)?([2-9]\d{6,7})+(\-[0-9]{1,6})?)$|^(1[34578]\d{9})$/", $consult)){
                _message('请输入正确的咨询方式');
            }
            //var_dump($content);exit;

            //判断活动费用是否免费，免费不参加拼团
            if($charge == '0.00'){
                $is_group = 0;
            }elseif (!empty($sale)){
                $is_group = 0;
            }else{
                $is_group = 1;
            }

            //积分设置
            if($integral == -1){
                $integral = sprintf("%.2f",$charge); //积分兑换
            }
            if($give_integral == -1){
                $give_integral = intval($charge);
            }
            if($share_integral == -1){
                $share_integral = intval($charge);
            }

            if(!empty($charge)){
                if(empty($num) || empty($price)){
                    _message('阶梯团不能为空');
                }
                if(count($num) != count($price)){
                    _message('请输入正确的价格阶梯');
                }
                //判断拼车费和积分兑换金额不能大于活动费用
                if($fare > $charge){
                    _message('拼车费不能超过活动费用');
                }
                if($integral > $charge){
                    _message('积分兑换金额不能超过活动费用');
                }
            }
            //分享标题、描述、图标
            if(empty($share_title)){
                $share_title = $title;
            }
            if(empty($share_icon)){
                _message('分享图标不能为空');
            }
            if(empty($num) || empty($price)){
                _message('价格阶梯不能为空');
            }
            $num = array_filter($num);

            if(empty($notice))_message('活动须知不能为空');
            $notice = array_filter($notice); //过滤空元素

            $cateId = array_filter($cateId);
            $cateId = $cid = array_unique($cateId); //移除重复的
            $cateId = implode(',',$cateId);  //拼接字符串，'1,2,3...'

            //数据入库
            $sql = "insert into `@#_activity` (`act_category`,`act_title`,`act_desc`,`act_content`,`act_home_poster`,`act_poster`,`act_charge`,`act_time`,`act_start_time`,`act_end_time`,`act_address`,`act_latlng`,`act_num_limit`,`act_consult`,`act_recommend`,`act_active`,`act_best`,`integral`,`give_integral`,`share_integral`,`act_fare`,`act_is_group`) VALUES ('$cateId','$title','$act_desc','$content','$home_poster','$poster',$charge,$act_time,$startTime,$endTime,'$address','$latlng',$numOfPeople,'$consult',$recommend,$active,$best,$integral,$give_integral,$share_integral,$fare,$is_group)";
            //p($sql);exit;
            $this->db->Autocommit_start();
            $query_1 = $this->db->Query($sql);
            $act_id = $this->db->insert_id();
            //p($act_id);
            /* 多种费用项
             * $sql = "insert into `@#_act_charge` (`c_act_id`,`c_name`,`c_money`,`c_num_limit`) VALUES ";
            $insert_value = "";
            foreach ($charge_name as $k => $v){
                $insert_value .= "($act_id,'$v','".sprintf("%.2f",$charge[$k])."',$numOfPeople[$k]),";
            }
            $insert_value = rtrim($insert_value,',');
            $sql = $sql.$insert_value;*/
            //p($sql);exit;
            //p($query_1);
            //p($query_2);exit;

            //插入价格阶梯
            $query_3 = true;
            if(!empty($num)){
                sort($num);
                rsort($price);
                $sql = "insert into `@#_act_step` (`act_id`,`num`,`money`) VALUES ";
                $insert_step = "";
                foreach ($num as $k1 => $v1){
                    //过滤超过人数限制
                    if($v1 <= $numOfPeople || $numOfPeople == 0){
                        $insert_step .= "($act_id,$v1,$price[$k1]),";
                    }
                }
                $insert_step = rtrim($insert_step,',');
                $sql = $sql.$insert_step;
                $query_3 = $this->db->Query($sql);
            }

            //插入活动须知表
            $query_4 = true;
            if(!empty($notice)){
                $sql = "insert into `@#_act_notice` (`n_act_id`,`n_notice`) VALUES ";
                $insert_value = '';
                foreach ($notice as $v){
                    $insert_value .= "($act_id,'$v'),";
                }
                $insert_value = rtrim($insert_value,',');
                $sql = $sql.$insert_value;
                //var_dump($sql);exit;
                $query_4 = $this->db->Query($sql);
            }

            //插入筛选属性
            $sql = "insert into `@#_act_filter` (`act_id`,`attr_id`,`attr_value`) VALUES ";
            $insert_filter = '';
            foreach ($attr as $key => $item) {
                $insert_filter .= "($act_id,$key,'$item'),";
            }
            $insert_filter = rtrim($insert_filter,',');
            $sql = $sql.$insert_filter;
            $query_5 = $this->db->Query($sql);

            //插入分享信息
            $link = WEB_PATH.'/mobile/activity/activity/'.$act_id;
            $query_6 = $this->db->Query("insert into `@#_item_share` (`type`,`item_id`,`title`,`description`,`link`,`icon`) VALUES (1,$act_id,'$share_title','$share_desc','$link','$share_icon')");
            if($query_1 && $query_3 && $query_4 && $query_5 && $query_6){
                $this->db->Autocommit_commit();
                _message("活动复制成功!",G_ADMIN_PATH.'/activity/lists',1);
            }else{
                $this->db->Autocommit_rollback();
                _message("活动复制失败!",'',1);
            }
        }

        //获取要复制的活动id
        $act_id = intval($this->segment(4));
        if(empty($act_id))_message('不存在该活动');
        $activity = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$act_id");
        //价格阶梯
        $price_step = $this->db->GetList("select * from `@#_act_step` WHERE `act_id`=$act_id");
        //var_dump($price_step);exit;
        //分享
        $shareInfo = $this->db->GetOne("select * from `@#_item_share` WHERE `item_id`=$act_id AND `type`=1");
        //筛选属性
        $attribute = $this->db->GetList("select * from `@#_act_attr` WHERE `type_id`=0 AND `is_show`=1");
        //获取筛选属性值数组
        foreach ($attribute as $k => $v){
            $attr_value = str_replace('，', ',', $v['value']);
            $attr = explode(',',$attr_value);
            $attribute[$k]['value'] = $attr;
        }
        $act_attr = $this->db->GetList("select * from `@#_act_filter` WHERE `act_id`=$act_id");

        //查询所有的分类
        $categories = $this->db->GetList("select * from `@#_act_category` WHERE 1 ORDER BY `c_sort` DESC ",array('key'=>'c_id'));
        $tree=System::load_sys_class('tree');
        $tree->icon = array('│ ','├─ ','└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $categoryshtml="<option value='\$c_id' \$selected>\$spacer\$c_name</option>";
        $tree->init($categories);

        $cid = explode(',',$activity['act_category']);
        //var_dump($cid);exit;

        $categorys = array();
        foreach ($cid as $key => $val){
            $tree->ret = '';
            $categorys[$key] = $tree->get_tree(0,$categoryshtml,$val);
        }
        //$categoryshtml=$tree->get_tree(0,$categoryshtml,$activity['act_category']);
        //echo '<pre>';
        //var_dump($act_attr);exit;
        $notice = $this->db->GetList("select * from `@#_act_notice` WHERE `n_act_id`=$act_id ORDER BY `n_id` ASC");
        $count = count($notice);
        include $this->tpl(ROUTE_M,'activity.copy');
    }


    /**
     * 修改筛选属性
     */
    public function ajaxChangeAttr(){
        $cid = isset($_GET['cid'])?intval($_GET['cid']):0;
        /*if(empty($cid)){
            echo json_encode('活动分类不能空');
            exit;
        }*/
        $attribute = $this->db->GetList("select * from `@#_act_attr` WHERE `type_id`=$cid");
        //var_dump($attribute);exit;
        if(!$attribute){
            //查询默认筛选属性，type_id = 0的
            $attribute = $this->db->GetList("select * from `@#_act_attr` WHERE `type_id`=0");
            if(!$attribute){
                echo json_encode('请先在筛选属性页中完善默认筛选属性的录入，即分类为全部分类');
                exit;
            }
            $cid = 0; //默认筛选属性
        }
        //获取筛选属性值数组
        foreach ($attribute as $k => $v){
            $attr_value = str_replace('，', ',', $v['value']);
            $attr = explode(',',$attr_value);
            $attribute[$k]['value'] = $attr;
        }
        $str = '';
        foreach ($attribute as $val) {
            $str .= "<select name='attr[{$val['id']}]'>";
            $str .= "<option value='0'>≡ 请选择{$val['name']} ≡";
            foreach ($val['value'] as $v){
                if(empty($v)){
                    continue;
                }else{
                    $str .= "<option value='$v'>$v</option>";
                }
            }
            $str .= "</select> ";
            $str .= "<input type='hidden' name='attr_id[]' value='{$val['id']}'>";
            $str .= "<input type='button' class='btn-face btn-add' value='添加条件' onclick='addOp(this)'> ";
        }
        $str .= "<span style='margin-left:10px'><font color='#0c0'>※ </font>有筛选属性时不能为空，若下拉选项中没有请点击“添加条件”，增加条件；反之，不必填写</span>";
        $msg['code'] = 0;
        $msg['content'] = $str;
        $msg['type_id'] = $cid;
        echo json_encode($msg);
        exit;
    }

    /**
     * 删除活动
     */
    public function del(){
        $act_id = isset($_GET['id'])?intval($_GET['id']):0;
        //验证数据的合法性
        if(empty($act_id)){
            exit('参数不能为空！');
        }
        //验证数据的合理性
        $res = $this->db->GetOne("select `act_id` from `@#_activity` WHERE `act_id`=$act_id");
        if(!$res){
            exit('不存在该数据！');
        }
        //判断活动是否存在订单
        $orderInfo = $this->db->GetList("select * from `@#_act_order` WHERE `o_act_id`=$act_id");
        if($orderInfo){
            exit('活动存在报名信息，不可删除');
        }
        //删除数据
        $row_1 = $this->db->Query("delete from `@#_activity` WHERE `act_id`=$act_id");
       /* $row_2 = $this->db->Query("delete from `@#_act_charge` WHERE `c_act_id`=$act_id");*/
        $row_3 = $this->db->Query("delete from `@#_act_step` WHERE `act_id`=$act_id");
        $row_4 = $this->db->Query("delete from `@#_act_filter` WHERE `act_id`=$act_id");
        $row_5 = $this->db->Query("delete from `@#_act_notice` WHERE `n_act_id`=$act_id");
        $row_6 = $this->db->Query("delete from `@#_item_share` WHERE `type`=1 AND `item_id`=$act_id");

        if($row_1 && $row_3 && $row_4 && $row_5 && $row_6){
            exit('ok');
        }else{
            exit('活动删除失败');
        }
    }

    /**
     * 活动排序
     */
    /*public function listorder(){
        if ($this->segment(4) == 'dosubmit') {
            foreach ($_POST['listorders'] as $id => $listorder) {
                $this->db->Query("UPDATE `@#_map_mark` SET `sort` = '$listorder' where `id` = '$id'");
            }
            _message("排序更新成功");
        } else {
            _message("请排序");
        }
    }*/

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
        //p($search);exit;
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
    /********************************** 活动管理--end-- ****************************************/

    /**
     * 报名费用明细
     */
    /*public function sign_detail(){
        if($_POST){
            $charge_name = isset($_POST['charge_name'])?$_POST['charge_name']:'';
            $charge = isset($_POST['charge'])?$_POST['charge']:'';
            $num = isset($_POST['num'])?$_POST['num']:0;
            $c_id = isset($_POST['c_id'])?$_POST['c_id']:0;
            $act_id = isset($_POST['act_id'])?intval($_POST['act_id']):0;
            $count = 0;
            //p($_POST);exit;
            if(empty($charge_name)) _message('收费类型不能为空');
            if(empty($c_id)) _message('参数错误');
            if(empty($act_id))_message('活动不存在');
            foreach ($charge_name as $k => $v){
                if(empty($v)){
                    _message('收费类型不能为空');
                }else{
                    $count += $num[$k];
                }
                if(isset($c_id[$k])){
                    //修改：判断合法性和合理性
                    if(empty($c_id[$k])) _message('参数错误');
                    $row_1 = $this->db->Query("update `@#_act_charge` set `c_name`='{$v}',`c_money`=$charge[$k],`c_num_limit`=$num[$k] WHERE `c_id`=$c_id[$k] AND `c_act_id`=$act_id");
                    if(!$row_1){
                        _message('修改失败','',1);
                    }
                }else{
                    //插入
                    $row_2 = $this->db->Query("insert into `@#_act_charge` (`c_act_id`,`c_name`,`c_money`,`c_num_limit`) VALUES ($act_id,'$v',".sprintf("%.2f",$charge[$k]).",$num[$k])");
                    if(!$row_2){
                        _message('修改失败','',1);
                    }
                }
            }
            $row_3 = $this->db->Query("update `@#_activity` set `act_num_limit`=$count WHERE `act_id`=$act_id");
            if($row_1 || $row_2 && $row_3){
                _message('修改成功','',1);
            }
        }
        $act_id = intval($this->segment(4));
        $act_info = $this->db->GetOne("select * from `@#_activity` WHERE `act_id`=$act_id");
        //根据活动id查询详细报名费用
        $sql = "select * from `@#_act_charge` WHERE `c_act_id`=$act_id";
        $chargeInfo = $this->db->GetList($sql,array('key'=>'c_id'));
        //p($chargeInfo);exit;
        include $this->tpl(ROUTE_M,'activity.sign_detail');
    }*/
    /**
     * 删除报名费用项
     */
    /*public function ajaxGetDelCharge(){
        //获取数据：进行数据合理性验证和合法性验证
        $c_id = intval($_GET['id']);
        if(empty($c_id)){
            $msg['status'] = 1;
            $msg['msg'] = '参数项不能为空';
            echo json_encode($msg);
            exit;
        }
        $res = $this->db->GetOne("select `c_id` from `@#_act_charge` WHERE `c_id`=$c_id");
        if(!$res){
            $msg['status'] = 1;
            $msg['msg'] = '参数不存在';
            echo json_encode($msg);
            exit;
        }
        //删除费用项
        $sql = "delete from `@#_act_charge` WHERE `c_id`=$c_id";
        $row = $this->db->Query($sql);
        if($row){
            $msg['status'] = 0;
            $msg['msg'] = '删除成功';
            echo json_encode($msg);
            exit;
        }else{
            $msg['status'] = 1;
            $msg['msg'] = '删除失败';
            echo json_encode($msg);
            exit;
        }
    }*/

}