<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class MenuController extends AuthController {

	public function getMenuList(){

		$where['bl_state'] = array('EQ','1');

		$count = D("systemMenu")->where($where)->count();// 查询满足要求的总记录数

        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = D("systemMenu")->where($where)->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

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



	public function add(){

    	$adminname = session('auth')['name'];

    	$this->assign('addname',$adminname);

    	//顶级菜单

    	$toplist = D("systemMenu")->where("int_parent_id=0 and bl_state=1")->select();

    	foreach ($toplist as $k => $v) {

    		$top[$k]['menuid'] = $v['int_id'];

    		$top[$k]['menuname'] = $v['vc_name'];

    	}

    	$this->assign("top",$top);

		$this->display();

	}



	public function addAction(){

		$map['vc_name'] = $_POST['name'];

		if($_POST['top']==0){

			$map['int_parent_id'] = 0;

		}else{

			$map['int_parent_id'] = $_POST['parent'];

		}

		$map['int_num'] = $_POST['num'];

		$map['bl_state'] = '1';

		$map['vc_add_user'] = $_POST['addname'];

		$map['dt_add_date'] = date('Y-m-d H-i-s',time());

		$menuid = D("systemMenu")->add($map);

		$map1['int_menu_id'] = $menuid;

		$map1['vc_action_name'] = $_POST['name'];

		if(!($_POST['url']=='' || $_POST['url']==NULL)){

			$map1['vc_action_url'] = $_POST['url'];

		}else{

			$map1['vc_action_url'] = NULL;

		}

		D("systemPermission")->add($map1);

		redirect(U("Menu/add"));

	}



	public function del(){

		$res = $this->getMenuList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['name'] = $value['vc_name'];

			$info[$key]['num'] = $value['int_num'];

			$parentID = $value['int_parent_id'];

			$parentName = D("systemMenu")->where("int_id='$parentID'")->getField('vc_name');

			$info[$key]['parent'] = $parentName;

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

		D("systemMenu")->where($where)->save($map);

		//$permissionID = D("systemPermission")->where("int_menu_id='$id'")->delete();

		//D("systemRelation")->where("vc_type='role_permission' and int_relation_two='$permissionID'")->delete();

		redirect(U("Menu/del"));

	}







}