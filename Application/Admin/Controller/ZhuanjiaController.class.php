<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class ZhuanjiaController extends AuthController {

	public function getZhuanjiaList(){
		$zhuanjia = M("Zhuanjia",'dl_');
		$count = $zhuanjia->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $zhuanjia->where("bl_state=1")->order("vc_job desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$res = $this->getZhuanjiaList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
			$info[$key]['name'] = $value['vc_name'];
			$info[$key]['headimg'] = URL.$value['vc_photo'];
			$info[$key]['job'] = $value['vc_job'];
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}

	public function add(){
		$this->display();
	}

	public function addAction(){
		$name = $_POST['name'];
        $job = $_POST['job'];
        $jieshao = $_POST['jieshao'];
        $techang = $_POST['techang'];
        if($_FILES["photo"]["error"] == 4){
        	$this->error("请添加头像",U("Zhuanjia/add"));
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->savePath  = 'Upload/zhuanjia/'; // 设置附件上传目录
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $info = $upload->upload(); 
        if($info){
        	$zhuanjia = M("Zhuanjia",'dl_');
        	$map['vc_name'] = $name;
        	$map['vc_photo'] = "Public/Upload/zhuanjia/".$info['photo']['savename'];
        	$map['vc_job'] = $job;
        	$map['text_jieshao'] = $jieshao;
        	$map['text_techang'] = $techang;
            $result = $zhuanjia->add($map);
        }
        if($result){
        	$this->success("添加成功",U("Zhuanjia/add"));
        }
	}

	public function zhuanjiaInfo(){
		$id = $_GET['id'];
		$zhuanjia = M("Zhuanjia",'dl_');
		$where['int_id'] = $id;
		$info = $zhuanjia->where($where)->find();
		$this->assign("info",$info);
		$this->display();
	}


	public function editAction(){
		$name = $_POST['name'];
        $job = $_POST['job'];
        $jieshao = $_POST['jieshao'];
        $techang = $_POST['techang'];
        if($_FILES["photo"]["error"] == 4){
        	$map['vc_photo'] = $_POST['photo_o'];
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/zhuanjia/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload(); 
	        if($info){
	        	$map['vc_photo'] = "Public/Upload/zhuanjia/".$info['photo']['savename'];
	        }
        }
        
        $id = $_POST['id'];
        $zhuanjia = M("Zhuanjia",'dl_');
        $map['vc_name'] = $name;
        $map['vc_job'] = $job;
        $map['text_jieshao'] = $jieshao;
        $map['text_techang'] = $techang;
        $result = $zhuanjia->where("int_id='$id'")->save($map);
        
        if($result){
        	$this->success("修改成功",U("Zhuanjia/index"));
            //redirect(U("News/add"));
        }
	}

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$zhuanjia = M("Zhuanjia",'dl_');
		$zhuanjia->where($where)->setField('bl_state',0);
		$this->success("删除成功",U("Zhuanjia/index"));
	}








}