<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class ChatUserController extends AuthController {

	public function getCustomerList(){

		$customer = M("User",'dl_');

		$count = $customer->count();// 查询满足要求的总记录数

        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

		$list = $customer->limit($Page->firstRow.','.$Page->listRows)->select();

		$res['list'] = $list;

		$res['show'] = $show;

		return $res;

	}



	public function index(){

		$res = $this->getCustomerList();

		$list = $res['list'];

		$show = $res['show'];

		foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['username'] = $value['vc_name'];

			$info[$key]['openid'] = $value['vc_openid'];

			$info[$key]['headimg'] = $value['vc_photo'];

			if($value['bl_vip']==0){
				$info[$key]['vip'] = '否';
			}else if($value['bl_vip']==1){
				$info[$key]['vip'] = '是';
			}
			$info[$key]['limit'] = $value['dt_limit'];

		}

		$this->assign('page',$show);

		$this->assign('info',$info);

		$this->display();

	}

	public function recard(){
		$openid = $_GET['openid'];
		$xiaofei = M("xiaofei",'dl_');
		$where['vc_openid'] = $openid;
		$count = $xiaofei->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $xiaofei->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('dt_date desc')->select();
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
			$info[$key]['text'] = $value['vc_text'];
			$info[$key]['money'] = $value['int_money']/100;
			$info[$key]['date'] = $value['dt_date'];
			if($value['bl_pay']==0){
				$info[$key]['pay'] = '未付款';
			}else if($value['bl_pay']==1){
				$info[$key]['pay'] = '已付款';
			}
		}
		$where1['vc_openid'] = $openid;
		$where1['bl_pay'] = 1;
		$total = ($xiaofei->where($where1)->sum('int_money'))/100;
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->assign('total',$total);
		$this->display();
	}











}