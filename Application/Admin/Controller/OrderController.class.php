<?php
namespace Admin\Controller;
use Admin\Common\Controller\AuthController;
class OrderController extends AuthController {
	public function getOrderList(){
		$order = M("Order",'cms_');
		$count = $order->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $order->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$user = M("User",'cms_');
		$lawyer = M("Lawyer",'cms_');
		$res = $this->getOrderList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];//订单id
			$info[$key]['caseID'] = $value['int_case_id'];//案件/产品id
			$lawyerID = $value['int_lawyer_id'];
			$lawyerName = $lawyer->where("int_id='$lawyerID'")->getField("vc_lawyer_name");
			$info[$key]['lawyerName'] = $lawyerName;//律师姓名
			$userID = $value['int_user_id'];
			$userName = $user->where("int_id='$userID'")->getField("vc_realname");
			$info[$key]['userName'] = $userName;//用户姓名
			$info[$key]['content'] = $value['vc_content'];//服务项目
			$info[$key]['method'] = $value['vc_pay_method'];//支付方式
			$info[$key]['count'] = $value['int_pay_amount']/100;//金额
			if($value['bl_state']==0){
				$info[$key]['state'] = '未支付';
			}else if($value['bl_state']==1){
				$info[$key]['state'] = '已支付';
			}//状态
			$info[$key]['date'] = $value['dt_date'];//日期
		}
		//var_dump($list);
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}






}