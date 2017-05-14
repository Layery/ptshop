<?php
defined('G_IN_SYSTEM')or exit('No permission resources.');
System::load_app_class('base','member','no');
System::load_app_fun('my');
System::load_app_fun('user');
System::load_sys_fun('user');

class map extends base {

	public function __construct() {
		parent::__construct();
		$this->db=System::load_sys_class('model');

	}
    /**
     * 显示地图列表
     */
    public function showMark(){
        //获取应用和key
        $appIdArr = $this->db->GetList("select `id` from `@#_map_config` WHERE `on_off`=1",array('key'=>'id'));
        $appId= array_rand($appIdArr,1);
        $appInfo = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$appId AND `on_off`=1");
        //p($appInfo);exit;
        $sql = "select `id`,`mark_name` as name,`latlng`,`mobile` as tel,`address` from `@#_map_mark` WHERE 1";
        $markInfo = $this->db->GetList($sql);
        $markInfo = json_encode($markInfo);

        //p($markInfo);exit;
        include templates("mobile/map","showMark");
    }

    /**
     * 显示单个标注详情
     */
    public function location(){
        //获取地图标注id
        $id = intval($this->segment(4));
        $markInfo = $this->db->GetList("select `id`,`mark_name` as name,`latlng`,`mobile` as tel,`address` from `@#_map_mark` WHERE `id`=$id");
        // p($markInfo);exit;
        //获取应用和key
        $appIdArr = $this->db->GetList("select `id` from `@#_map_config` WHERE `on_off`=1",array('key'=>'id'));
        $appId= array_rand($appIdArr,1);
        $appInfo = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$appId AND `on_off`=1");
        $markInfo = json_encode($markInfo);

        include templates("mobile/map","location"); 
        
    }
    /**
     * ajax请求地图标注距离单起点到多起点
     */
    public function ajaxMeasureDistance(){
        //var_dump($_GET);exit;
        $lat = safe_replace($_GET['lat']);
        $lng = safe_replace($_GET['lng']);//获取经纬度
        $latlng = $lat.','.$lng;
        $appIdArr = $this->db->GetList("select `id` from `@#_map_config` WHERE `on_off`=1",array('key'=>'id'));
        $appId= array_rand($appIdArr,1);
        $appInfo = $this->db->GetOne("select * from `@#_map_config` WHERE `id`=$appId AND `on_off`=1");
        //查询所有的地图标注
        $marker = $this->db->GetList("select `id`,`mark_name` as name,`latlng`,`mobile` as tel,`address` from `@#_map_mark` WHERE 1",array('key'=>'id'));
        //$to = '';
        $distance = $markerInfo = array();
        foreach ($marker as $k => $v){
            $to = $v['latlng'];
            $url = "http://apis.map.qq.com/ws/distance/v1/?mode=driving&from={$latlng}&to={$to}&key={$appInfo['key']}";
            $distanceInfo = getCurl($url);
            $distanceInfo = json_decode($distanceInfo,true);
            //p($distanceInfo);
            if($distanceInfo['status'] == 0){
                foreach ($distanceInfo['result']['elements'] as $element) {
                    $marker[$k]['distance'] = $element['distance'];
                    $marker[$k]['duration'] = $element['duration'];
                    $distance[$k] = $element['distance'];
                }
            }
        }
        asort($distance);
        foreach ($distance as $k => $v){
            if(isset($marker[$k])){
                $markerInfo[] = $marker[$k];
                unset($marker[$k]);
            }
        }
        foreach ($marker as $item){
            $markerInfo[] = $item;
        }
        //p($markerInfo);exit;
        echo json_encode($markerInfo);
        exit;
        //p($marker);exit;
    }

}
?>