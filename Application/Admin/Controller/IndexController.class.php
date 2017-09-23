<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

//use Think\Controller;

class IndexController extends AuthController {

    public function index(){

        //输出 后台 管理页面

        $adminid = session('auth')['id'];

    	$adminname = session('auth')['name'];

        $map['vc_type']  = array('eq','role_user');

        $map['int_relation_two']  = array('eq',$adminid);

    	$role = D("systemRelation")->where($map)->getField('int_relation_one');

        //如果没有对应角色，提示报错
        if($role==false || $role==NULL){
            session('auth',null);
            $this->error('您没有被分配角色，请联系管理员',U('Login/index'),3);
        }
        $map1['vc_type'] = array('eq','role_permission');
        $map1['int_relation_one']  = array('eq',$role);
    	$permission = D("systemRelation")->where($map1)->select();

        //如果没有对应菜单权限，提示报错
        if($permission==false || $permission==NULL){
            session('auth',null);
            $this->error('您没有被分配权限，请联系管理员',U('Login/index'),3);
        }

    	foreach ($permission as $key => $value) {
            $map2['int_id'] = array('eq',$value['int_relation_two']);
    		$menuid[] = D("systemPermission")->where($map2)->getField('int_menu_id');         
    	}

        $map3['int_id'] = array('in',$menuid);
        $map3['int_parent_id'] = array('eq','0');
        $map3['bl_state'] = array('eq','1');
        $topmenu = D("systemMenu")->where($map3)->order('int_num')->select();

        foreach ($topmenu as $k => $v) {
            $topid = $v['int_id'];
            $map4['int_parent_id'] = array('eq',$topid);
            $map4['bl_state'] = array('eq','1');
            $map4['int_id'] = array('in',$menuid);
            $son = D("systemMenu")->where($map4)->order('int_num')->select();

            foreach ($son as $x => $y) {
                $sonid = $y['int_id'];
                $map7['int_menu_id'] = array('eq',$sonid);
                $stext = D("systemPermission")->where($map7)->getField('vc_action_name');
                $map5['int_parent_id'] = array('eq',$sonid);
                $map5['bl_state'] = array('eq','1');
                $map5['int_id'] = array('in',$menuid);
                $last = D("systemMenu")->where($map5)->order('int_num')->select();
                foreach ($last as $m => $n) {
                    $lastid = $n['int_id'];
                    if($lastid!=NULL || $lastid!=''){
                        $map6['int_menu_id'] = array('eq',$lastid);
                        $text = D("systemPermission")->where($map6)->getField('vc_action_name');
                        $url = D("systemPermission")->where($map6)->getField('vc_action_url');
                        $bottom[$m]['id'] = $lastid;
                        $bottom[$m]['text'] = $text;
                        $bottom[$m]['href'] = U($url);
                    }
                }
                if(!empty($bottom)){
                    $middle[$x]['text'] = $stext; 
                    $middle[$x]['items'] = $bottom;
                    unset($bottom);
                }
            }
            
            $top[$k]['id'] = $topid;
            $top[$k]['homePage'] = $middle[0]['items'][0]['id'];
            $top[$k]['menu'] = $middle;
            unset($middle);
        }

    	$json = json_encode($top);

        $this->assign('topmenu',$topmenu);

        $this->assign('json',$json);

        $this->assign('admin',$adminname);

        session('admin',$adminname);

    	$this->display();

    }

}