<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class WorkzijianController extends AuthController {

	public function getZijianList(){

		$case = M("zijian",'dl_');

		$count = $case->count();// 查询满足要求的总记录数

        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = $case->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();

		$res['list'] = $list;

		$res['show'] = $show;

		return $res;

	}



	public function index(){
		$user = M("User",'dl_');
		$work = M("work",'dl_');
		$res = $this->getZijianList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$id = $value['int_id'];

			$info[$key]['id'] = $id;

			$openid = $value['vc_openid'];
			$info[$key]['openid'] = $openid;
			$info[$key]['username'] = $user->where("vc_openid='$openid'")->getField("vc_name");

			$workid = $value['int_work'];
			
			$info[$key]['workname'] = $work->where("int_id='$workid'")->getField("vc_job");
			$info[$key]['comname'] = $work->where("int_id='$workid'")->getField("vc_name");
		
			$info[$key]['date'] = $value['dt_date'];
		}
		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}


	public function jianli(){
		$openid = $_GET['id'];
		$jianli = M("jianli",'dl_');
		$info = $jianli->where("vc_openid='$openid' and bl_state=1")->find();
		$this->assign('info',$info);
		$this->display();
	}








}