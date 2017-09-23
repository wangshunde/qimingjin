<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class RoleController extends AuthController {

	public function getRoleList(){

		$where['bl_state'] = array('EQ','1');

		$count = D("systemRole")->where($where)->count();// 查询满足要求的总记录数

        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = D("systemRole")->where($where)->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

		$res['list'] = $list;

		$res['show'] = $show;

		return $res;

	}

	public function index(){

		

		$this->display();

	}



	public function getParent(){

		$topid = $_POST['topid'];

		if($topid==0){

			exit($topid);

		}else{

			$parentlist = D("systemMenu")->where("int_parent_id='$topid' and bl_state=1")->select();

			foreach ($parentlist as $key => $value) {

				$parent[$key]['menuid'] = $value['int_id'];

    			$parent[$key]['menuname'] = $value['vc_name'];

			}

			echo json_encode($parent);

		}

		

	}



	public function getPermission(){

		$Model = M("systemMenu");

    	$list = $Model->query("select  p.int_id as perid,m.int_id,m.vc_name from os_system_menu as m join os_system_permission as p on m.int_id=p.int_menu_id where m.int_parent_id!=0 and p.vc_action_url is null and m.bl_state=1 order by m.int_parent_id");

    	foreach ($list as $key => $value) {

    		$menuid = $value['int_id'];

    		$son = $Model->query("select  p.int_id as pid,m.vc_name as name from os_system_menu as m join os_system_permission as p on m.int_id=p.int_menu_id where m.int_parent_id='$menuid' and m.bl_state=1");

    		$father[$key]['parent'] = $value['vc_name'];

    		$father[$key]['parentid'] = $value['perid'];

    		foreach ($son as $k => $v) {

    			$permission[$key][$k]["permissionid"] = $v['pid'];

    			$permission[$key][$k]["permissionname"] = $v['name'];

    		}

    		

    	}

    	$info['pjson'] = json_encode($permission);

    	$info['fjson'] = json_encode($father);

    	return $info;

	}



	public function add(){

    	$adminname = session('auth')['name'];

    	$this->assign('addname',$adminname);

    	$info = $this->getPermission();

    	$pjson = $info['pjson'];

    	$fjson = $info['fjson'];

    	$this->assign("permission",$pjson);

    	$this->assign("father",$fjson);

		$this->display();

	}



	public function addAction(){

		$map['vc_rolename'] = $_POST['name'];

		$map['bl_state'] = '1';

		$map['vc_add_user'] = $_POST['addname'];

		$map['dt_add_date'] = date('Y-m-d H-i-s',time());

		$roleid = D("systemRole")->add($map);

		$permission = $_POST['permission'];

		if($permission==null){

			$mapn['int_relation_one'] = $roleid;

			$mapn['int_relation_two'] = 0;

			$mapn['vc_type'] = 'role_permission';

			$mapn['dt_date'] = date('Y-m-d H-i-s',time());

			$mapn['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($mapn);

		}

		foreach ($permission as $key => $value) {

			$map1['int_relation_one'] = $roleid;

			$map1['int_relation_two'] = $value;

			$map1['vc_type'] = 'role_permission';

			$map1['dt_date'] = date('Y-m-d H-i-s',time());

			$map1['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map1);

		}

		//勾选权限的父级权限和顶级权限也要保存

		$fatherpermission = $_POST['fatherpermission'];

		foreach ($fatherpermission as $k => $v) {

			$map2['int_relation_one'] = $roleid;

			$map2['int_relation_two'] = $v;

			$map2['vc_type'] = 'role_permission';

			$map2['dt_date'] = date('Y-m-d H-i-s',time());

			$map2['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map2);

			$menuid = D("systemPermission")->where("int_id='$v'")->getField("int_menu_id");

			$topid[$k] = D("systemMenu")->where("int_id='$menuid'")->getField("int_parent_id");



		}

		$top = array_unique($topid); 

		foreach ($top as $x => $y) {

			$map3['int_relation_one'] = $roleid;

			$map3['int_relation_two'] = $y;

			$map3['vc_type'] = 'role_permission';

			$map3['dt_date'] = date('Y-m-d H-i-s',time());

			$map3['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map3);

		}

		redirect(U("Role/add"));

	}



	public function edit(){

		$res = $this->getRoleList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['rolename'] = $value['vc_rolename'];

			$info[$key]['state'] = $value['bl_state'];

		}

		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}



	public function editPage(){

		$adminname = session('auth')['name'];

    	$this->assign('addname',$adminname);

    	//角色基本信息

    	$id = $_GET['id'];

    	$where['int_id'] = $id;

    	$role = D("systemRole")->where($where)->find();

    	$roleinfo['id'] = $id;

    	$roleinfo['rolename'] = $role['vc_rolename'];

    	if($role['bl_state']==0){

    		$roleinfo['state'] = '停用';

    	}else if($role['bl_state']==1){

    		$roleinfo['state'] = '启用';

    	}



    	//该角色拥有权限

    	$where1["int_relation_one"] = $id;

    	$where1["vc_type"] = "role_permission";

    	$permission =  D("systemRelation")->where($where1)->select();

    	foreach ($permission as $key => $value) {

    		$selected[$key] = $value['int_relation_two'];



    	}



    	$this->assign("selected",json_encode($selected));

    	//全部权限

    	$info = $this->getPermission();

    	$pjson = $info['pjson'];

    	$fjson = $info['fjson'];

    	$this->assign("permission",$pjson);

    	$this->assign("father",$fjson);



    	$this->assign('roleinfo',$roleinfo);

		$this->display();

	}



	public function editAction(){

		$id = $_POST['id'];

		$map['vc_rolename'] = $_POST['name'];

		$map['bl_state'] = '1';

		$map['vc_edit_user'] = $_POST['addname'];

		$map['dt_edit_date'] = date('Y-m-d H-i-s',time());

		$res = D("systemRole")->where("int_id='$id'")->save($map);

		$permission = $_POST['permission'];

		D("systemRelation")->where("int_relation_one='$id' and vc_type='role_permission'")->delete();

		foreach ($permission as $key => $value) {

			$map1['int_relation_one'] = $id;

			$map1['int_relation_two'] = $value;

			$map1['vc_type'] = 'role_permission';

			$map1['dt_date'] = date('Y-m-d H-i-s',time());

			$map1['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map1);

		}

		//var_dump($permission);exit;

		//勾选权限的父级权限和顶级权限也要保存

		$fatherpermission = $_POST['fatherpermission'];

		foreach ($fatherpermission as $k => $v) {

			$map2['int_relation_one'] = $id;

			$map2['int_relation_two'] = $v;

			$map2['vc_type'] = 'role_permission';

			$map2['dt_date'] = date('Y-m-d H-i-s',time());

			$map2['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map2);

			$menuid = D("systemPermission")->where("int_id='$v'")->getField("int_menu_id");

			$topid[$k] = D("systemMenu")->where("int_id='$menuid'")->getField("int_parent_id");

		}

		//var_dump($fatherpermission);

		$top = array_unique($topid); 

		foreach ($top as $x => $y) {

			$topper = D("systemPermission")->where("int_menu_id='$y'")->getField("int_id");

			$map3['int_relation_one'] = $id;

			$map3['int_relation_two'] = $topper;

			$map3['vc_type'] = 'role_permission';

			$map3['dt_date'] = date('Y-m-d H-i-s',time());

			$map3['vc_user'] = $_POST['addname'];

			D("systemRelation")->add($map3);

		}

		//var_dump($top);

		redirect(U("Role/edit"));

	}

	



	public function del(){

		$res = $this->getRoleList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['rolename'] = $value['vc_rolename'];

			$info[$key]['state'] = $value['bl_state'];

		}

		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}



	public function delAction(){

		$id = $_GET['id'];

		$where['int_id'] = $id;

		$map['bl_state'] = '2';

		D("systemRole")->where($where)->save($map);

		redirect(U("Role/del"));

	}







}