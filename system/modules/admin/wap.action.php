<?php 
defined('G_IN_SYSTEM')or exit('no');
System::load_app_class('admin','admin','no');
class wap extends admin {
    private $db;
	
	public function __construct(){
		parent::__construct();
		$this->db=System::load_sys_class('model');
		$this->ment=array(
						array("navigation","轮播图管理",ROUTE_M.'/'.ROUTE_C),
						array("navigation","添加轮播图",ROUTE_M.'/'.ROUTE_C."/add"),
						/*array("navigation","背景管理",ROUTE_M.'/'.ROUTE_C."/background_list"),
						array("navigation","添加背景",ROUTE_M.'/'.ROUTE_C."/background_add"),*/
		);
	}
	public function init(){
        $typeId = isset($_GET['typeId']) ? intval($_GET['typeId']) : -1;
        if($typeId == -1){
            $where = '1';
        }else{
            $where = "`where_is`=$typeId";
        }
        $num=20;
        $total=$this->db->GetCount("SELECT COUNT(*) FROM `@#_wap` WHERE $where");
        $page=System::load_sys_class('page');
        if(isset($_GET['p'])){$pagenum=$_GET['p'];}else{$pagenum=1;}
        $page->config($total,$num,$pagenum,"0");
		$lists=$this->db->GetPage("SELECT * FROM `@#_wap` where $where");
		include $this->tpl(ROUTE_M,'wap_list');
	}
	
	public function add(){
		if(isset($_POST['submit'])){
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';
            $link = isset($_POST['link'])?htmlspecialchars(trim($_POST['link'])):'';
            $category = isset($_POST['category'])?intval($_POST['category']):0;
            $image = isset($_POST['image'])?safe_replace($_POST['image']):'';
            //验证数据的合法性
            if(empty($image)){
                _message('轮播图不能为空');
            }
            if(empty($link)){
                _message('链接不能为空');
            }
            $this->db->Query("insert into `@#_wap`(`title`,`link`,`img`,`where_is`) values('$title','$link','$image',$category)");
			if($this->db->affected_rows()){
					_message("添加成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/init");
			}else{
					_message("添加失败");
			}
		}
		include $this->tpl(ROUTE_M,'wap_add');
	}
	
	public function delete(){
		$id=intval($this->segment(4)); 
        $this->db->Query("DELETE FROM `@#_wap` WHERE `id`=$id");
        if($this->db->affected_rows()){
                _message("删除成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/init");
        }else{
                _message("删除失败");
        }
	}
	
	public function update(){
		$id=intval($this->segment(4));
		$wap=$this->db->Getone("SELECT * FROM `@#_wap` where `id`=$id");
		
		if(isset($_POST['submit'])){
            $title = isset($_POST['title'])?htmlspecialchars(trim($_POST['title'])):'';
            $link = isset($_POST['link'])?htmlspecialchars(trim($_POST['link'])):'';
            $category = isset($_POST['category'])?intval($_POST['category']):0;
            $image = isset($_POST['image'])?safe_replace($_POST['image']):'';
            //验证数据的合法性
            if(empty($image)){
                _message('轮播图不能为空');
            }
            if(empty($link)){
                _message('链接不能为空');
            }
			$this->db->Query("UPDATE `@#_wap` SET `img`='$image',`title`='$title',`link`='$link',`where_is`=$category WHERE `id`=$id");
			if($this->db->affected_rows()){
					_message("修改成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/init");
			}else{
					_message("修改失败");
			}
		}
		include $this->tpl(ROUTE_M,'wap_update');
	}

	/**
     * 背景管理
     */
	public function background_list(){
        $lists=$this->db->GetList("SELECT * FROM `@#_wap` where `where_is`= 1");
        include $this->tpl(ROUTE_M,'wap_background_list');
    }
    /**
     * 添加背景
     */
    public function background_add(){
        if(isset($_POST['submit'])){
            $title=htmlspecialchars(trim($_POST['title']));
            $link=htmlspecialchars(trim($_POST['link']));
            $title2=htmlspecialchars(trim($_POST['title2']));
            if(isset($_POST['image'])){
                $img=$_POST['image'];
            }else{
                $img='';
            }

            $this->db->Query("insert into `@#_wap`(`title`,`link`,`img`,`color`,`where_is`) values('$title','$link','$img','$title2',1) ");
            if($this->db->affected_rows()){
                _message("添加成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/background_list");
            }else{
                _message("添加失败");
            }
        }
        include $this->tpl(ROUTE_M,'wap_background_add');
    }
    /**
     * 修改图片
     */
    public function background_update(){
        $id=intval($this->segment(4));
        $wapone=$this->db->Getone("SELECT * FROM `@#_wap` where `id`='$id' AND `where_is`=1");

        if(isset($_POST['submit'])){
            $title=htmlspecialchars(trim($_POST['title']));
            $link=htmlspecialchars(trim($_POST['link']));
            $title2=htmlspecialchars(trim($_POST['title2']));
            if(isset($_POST['image'])){
                $img=$_POST['image'];
            }else{
                $img=$wapone['img'];
            }
            $this->db->Query("UPDATE `@#_wap` SET `img`='$img',`title`='$title',`link`='$link',`color`='$title2' WHERE `id`=$id AND `where_is`=1");
            if($this->db->affected_rows()){
                _message("修改成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/background_list");
            }else{
                _message("修改失败");
            }
        }
        include $this->tpl(ROUTE_M,'wap_background_update');
    }
    /**
     * 删除背景图片
     */
    public function background_delete(){
        $id=intval($this->segment(4));
        $this->db->Query("DELETE FROM `go_wap` WHERE (`id`='$id') AND `where_is`=1");
        if($this->db->affected_rows()){
            _message("删除成功",WEB_PATH.'/'.ROUTE_M.'/'.ROUTE_C."/background_list");
        }else{
            _message("删除失败");
        }
    }
	
	
}



?>