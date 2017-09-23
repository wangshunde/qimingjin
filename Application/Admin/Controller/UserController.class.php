<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class UserController extends AuthController {

	public function getUserList(){

		$where['bl_state'] = array('NEQ','2');

		$count = D("systemUser")->where($where)->count();// 查询满足要求的总记录数

        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = D("systemUser")->where($where)->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

		$res['list'] = $list;

		$res['show'] = $show;

		return $res;

	}

	public function index(){

		$res = $this->getUserList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['username'] = $value['vc_name'];

			$info[$key]['name'] = $value['vc_realname'];

			if($value['vc_sex']==1){

	    		$info[$key]['sex'] = '男性';

	    	}else if($value['vc_sex']==2){

	    		$info[$key]['sex'] = '女性';

	    	}else{

	    		$info[$key]['sex'] = '保密';

	    	}

			$info[$key]['tel'] = $value['vc_tel'];

			$info[$key]['state'] = $value['bl_state'];

			$userID = $value['int_id'];

			$roleID = D("systemRelation")->where("vc_type='role_user' and int_relation_two='$userID'")->getField("int_relation_one");

			$info[$key]['role'] = D("systemRole")->where("int_id='$roleID'")->getField("vc_rolename");

		}

		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}



	/*public function more(){

		$id = $_GET['id'];

		$info = D("systemUser")->where("int_id='$id'")->find();

		$this->assign("info",$info);

		$this->display();

	}*/



	public function add(){

    	$adminname = session('auth')['name'];

    	$this->assign('addname',$adminname);

    	$map['bl_state'] = '1';

    	$role = D("systemRole")->where($map)->select();

    	$this->assign('role',$role);

		$this->display();

	}



	public function addAction(){

		$map['vc_name'] = $_POST['username'];

		$map['vc_password'] = md5($_POST['password']);

		$map['vc_realname'] = $_POST['realname'];

		$map['vc_sex'] = $_POST['sex'];

		$map['vc_tel'] = $_POST['telephone'];

		$map['bl_tel_verify'] = '0';

		$map['vc_idcard'] = '0';

		$map['bl_idcard_verify'] = '0';

		$map['bl_state'] = '1';

		$map['vc_add_user'] = $_POST['addname'];

		$map['dt_add_date'] = date('Y-m-d H-i-s',time());

		$userid = D("systemUser")->add($map);

		$map1['int_relation_one'] = $_POST['role'];

		$map1['int_relation_two'] = $userid;

		$map1['vc_type'] = 'role_user';

		$map1['dt_date'] = date('Y-m-d H-i-s',time());

		$map1['vc_user'] = $_POST['addname'];

		D("systemRelation")->add($map1);

		redirect(U("User/add"));

	}



	public function edit(){

		$res = $this->getUserList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['username'] = $value['vc_name'];

			$info[$key]['name'] = $value['vc_realname'];

			if($value['vc_sex']==1){

	    		$info[$key]['sex'] = '男性';

	    	}else if($value['vc_sex']==2){

	    		$info[$key]['sex'] = '女性';

	    	}else{

	    		$info[$key]['sex'] = '保密';

	    	}

			$info[$key]['tel'] = $value['vc_tel'];

			$info[$key]['state'] = $value['bl_state'];

		}

		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}



	public function editPage(){

		$adminname = session('auth')['name'];

    	$this->assign('addname',$adminname);

    	$map['bl_state'] = '1';

    	$role = D("systemRole")->where($map)->select();

    	$this->assign('role',$role);

    	$id = $_GET['id'];

    	$where['int_id'] = $id;

    	$info = D("systemUser")->where($where)->find();

    	$userinfo['id'] = $id;

    	$userinfo['username'] = $info['vc_name'];

    	$userinfo['oldpass'] = $info['vc_password'];

    	$userinfo['realname'] = $info['vc_realname'];

    	if($info['vc_sex']==1){

    		$userinfo['sex'] = '男性';

    	}else if($info['vc_sex']==2){

    		$userinfo['sex'] = '女性';

    	}else{

    		$userinfo['sex'] = '保密';

    	}

    	$userinfo['tel'] = $info['vc_tel'];

    	if($info['bl_state']==0){

    		$userinfo['state'] = '停用';

    	}else if($info['bl_state']==1){

    		$userinfo['state'] = '启用';

    	}



    	$where1['int_relation_two'] = $id;

    	$where1['vc_type'] = 'role_user';

    	$roleid = D("systemRelation")->where($where1)->getField('int_relation_one');



    	$userinfo['role'] = D("systemRole")->where("int_id='$roleid'")->getField('vc_rolename');





    	$this->assign('userinfo',$userinfo);

		$this->display();

	}



	public function editAction(){

		$id = $_POST['id'];

		$map['vc_name'] = $_POST['username'];



		if($_POST['newpass']=='' || $_POST['newpass']==NULL){

			$map['vc_password'] = $_POST['oldpass'];

		}else{

			$map['vc_password'] = md5($_POST['newpass']);

		}

		

		$map['vc_realname'] = $_POST['realname'];

		$map['vc_sex'] = $_POST['sex'];

		$map['vc_tel'] = $_POST['telephone'];

		

		$map['bl_state'] = $_POST['state'];;

		$map['vc_edit_user'] = $_POST['addname'];

		$map['dt_edit_date'] = date('Y-m-d H-i-s',time());

		D("systemUser")->where("int_id='$id'")->save($map);



		$map1['int_relation_one'] = $_POST['role'];

		$map1['int_relation_two'] = $id;

		$map1['vc_type'] = 'role_user';

		$map1['dt_date'] = date('Y-m-d H-i-s',time());

		$map1['vc_user'] = $_POST['addname'];



		D("systemRelation")->where("int_relation_two='$id' and vc_type='role_user'")->delete();

		D("systemRelation")->add($map1);

		

		redirect(U("User/edit"));

	}

	



	public function del(){

		$res = $this->getUserList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['username'] = $value['vc_name'];

			$info[$key]['name'] = $value['vc_realname'];

			if($value['vc_sex']==1){

	    		$info[$key]['sex'] = '男性';

	    	}else if($value['vc_sex']==2){

	    		$info[$key]['sex'] = '女性';

	    	}else{

	    		$info[$key]['sex'] = '保密';

	    	}

			$info[$key]['tel'] = $value['vc_tel'];

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

		D("systemUser")->where($where)->save($map);

		redirect(U("User/del"));

	}







}