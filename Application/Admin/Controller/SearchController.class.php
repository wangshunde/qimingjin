<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class SearchController extends AuthController {

	public function user(){

		//接收表单的数据

	  	$type = $_POST["type"];

	  	$keyword = $_POST["keyword"];

	  	$method = $_POST['method'];

	  	//搜索记录

	  	if($keyword!=NUll){

	  		$count = D("systemUser")->where("{$type} like '%{$keyword}%' and bl_state<>2")->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = D("systemUser")->where("{$type} like '%{$keyword}%' and bl_state<>2")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

			

	  	}else{

	  		$count = D("systemUser")->where("bl_state<>2")->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = D("systemUser")->where("bl_state<>2")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

	  	}

	  	foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['username'] = $value['vc_name'];

			$info[$key]['name'] = $value['vc_realname'];

			$info[$key]['sex'] = $value['vc_sex'];

			$info[$key]['tel'] = $value['vc_tel'];

			$info[$key]['state'] = $value['bl_state'];

		}

	  	//$this->assign("type",$type);

	  	$this->assign("keyword",$keyword);

	  	$this->assign("info",$info);

	  	if($method == "del"){

	  		$this->display('User/del');

	  	}else if($method == "index"){

	  		$this->display('User/index');

	  	}else if($method == "edit"){

	  		$this->display('User/edit');

	  	}



	}





	public function cases(){

		//接收表单的数据

	  	$type = $_POST["type"];

	  	$keyword = $_POST["keyword"];

	  	$method = $_POST['method'];

	  	//搜索记录

	  	if($keyword!=NUll){

	  		$case = M("Case",'cms_');

	  		$user = M("User",'cms_');

	  		$pic = M("casePicture",'cms_');

	  		$count = $case->where("{$type} like '%{$keyword}%'")->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $case->where("{$type} like '%{$keyword}%'")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

			

	  	}else{

	  		$case = M("Case",'cms_');

	  		$user = M("User",'cms_');

	  		$pic = M("casePicture",'cms_');

	  		$count = $case->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $case->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

	  	}

	  	foreach ($list as $key => $value) {

				$id = $value['int_id'];

				$info[$key]['id'] = $id;

				$userid = $value['int_user_id'];

				$info[$key]['username'] = $user->where("int_id='$userid'")->getField("vc_username");

				if($value['vc_type']=='sound'){

					$info[$key]['type'] = '语音';

				}else if($value['vc_type']=='word'){

					$info[$key]['type'] = '文字';

				}

				

		    	$info[$key]['title'] = $value['vc_title'];

				$info[$key]['content'] = $value['text_describe'];

				$info[$key]['picture'] = $pic->where("int_case='$id'")->select();

				if($value['bl_off']=='1'){

					$info[$key]['state'] = '已关闭';

				}else if($value['bl_state']==0){

					$info[$key]['state'] = '未解答';

				}else if($value['bl_state']==1){

					$info[$key]['state'] = '已解答';

				}

				$info[$key]['date'] = $value['dt_date'];



		}

	  	//$this->assign("type",$type);

	  	$this->assign("keyword",$keyword);

	  	$this->assign("info",$info);

	  	if($method == "del"){

	  		$this->display('User/del');

	  	}else if($method == "index"){

	  		$this->display('Case/index');

	  	}else if($method == "edit"){

	  		$this->display('User/edit');

	  	}

	}





	public function lawyer(){

		//接收表单的数据

	  	$type = $_POST["type"];

	  	$keyword = $_POST["keyword"];

	  	$method = $_POST['method'];

	  	//搜索记录

	  	if($keyword!=NUll){

	  		$lawyer = M("Lawyer",'cms_');

	  		$count = $lawyer->where("{$type} like '%{$keyword}%'")->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $lawyer->where("{$type} like '%{$keyword}%'")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

			

	  	}else{

	  		$lawyer = M("Lawyer",'cms_');

	  		$count = $lawyer->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $lawyer->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

	  	}

	  	foreach ($list as $key => $value) {

			$info[$key]['id'] = $value['int_id'];

			$info[$key]['name'] = $value['vc_lawyer_name'];

			$info[$key]['headimg'] = $value['vc_img'];

			$info[$key]['tel'] = $value['vc_tel'];

			$info[$key]['point'] = $value['int_points'];

			$info[$key]['office'] = $value['vc_office'];

			if($value['bl_state']==0){

				$info[$key]['state'] = '未认证';

			}else if($value['bl_state']==1){

				$info[$key]['state'] = '已认证';

			}

			$info[$key]['date'] = $value['dt_date'];

		}

	  	//$this->assign("type",$type);

	  	$this->assign("keyword",$keyword);

	  	$this->assign("info",$info);

	  	if($method == "del"){

	  		$this->display('User/del');

	  	}else if($method == "index"){

	  		$this->display('Lawyer/index');

	  	}else if($method == "edit"){

	  		$this->display('User/edit');

	  	}

	}





	public function customer(){

		//接收表单的数据
	  	$type = $_POST["type"];
	  	$keyword = $_POST["keyword"];
	  	$method = $_POST['method'];
	  	//搜索记录
	  	$customer = M("User",'dl_');
	  	if($keyword!=NUll){
	  		$count = $customer->where("{$type} like '%{$keyword}%'")->count();// 查询满足要求的总记录数
	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $customer->where("{$type} like '%{$keyword}%'")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();
	  	}else{
	  		$count = $customer->count();// 查询满足要求的总记录数
	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $customer->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();
	  	}
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
	  	//$this->assign("type",$type);
	  	$this->assign("keyword",$keyword);
	  	$this->assign("info",$info);
	  	if($method == "del"){
	  		$this->display('User/del');
	  	}else if($method == "index"){
	  		$this->display('ChatUser/index');
	  	}else if($method == "edit"){
	  		$this->display('User/edit');
	  	}
	}





	public function resource(){

		//接收表单的数据

	  	$type = $_POST["type"];

	  	$keyword = $_POST["keyword"];

	  	$method = $_POST['method'];

	  	//搜索记录

	  	if($keyword!=NUll){

	  		$resource = M("resource",'cms_');

	  		$count = $resource->where("{$type} like '%{$keyword}%'")->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $resource->where("{$type} like '%{$keyword}%'")->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

			

	  	}else{

	  		$resource = M("resource",'cms_');

	  		$count = $resource->count();// 查询满足要求的总记录数

	        $Page = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)

	        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

			$list = $resource->order("int_id")->limit($Page->firstRow.','.$Page->listRows)->select();

	  	}

	  	foreach ($list as $key => $value) {

				$info[$key]['id'] = $value['int_id'];

				$info[$key]['name'] = $value['vc_name'];

				$info[$key]['type'] = $value['vc_type'];

				$info[$key]['headimg'] = $value['vc_img'];

				$info[$key]['date'] = $value['dt_date'];

		}

	  	$this->assign("keyword",$keyword);

	  	$this->assign("info",$info);

	  	if($method == "del"){

	  		$this->display('Resource/index');

	  	}else if($method == "index"){

	  		$this->display('Resource/index');

	  	}else if($method == "edit"){

	  		$this->display('Resource/index');

	  	}

	}









}