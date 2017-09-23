<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class WorkController extends AuthController {

	public function getWorkList(){
		$work = M("Work",'dl_');
		$count = $work->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $work->where("bl_state=1")->order("vc_name desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$res = $this->getWorkList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
			$info[$key]['name'] = $value['vc_name'];
			$info[$key]['headimg'] = URL.$value['vc_img'];
			$info[$key]['job'] = $value['vc_job'];
			$info[$key]['educational'] = $value['vc_educational'];
			$info[$key]['major'] = $value['vc_major'];
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
        $educational = $_POST['educational'];
        $major = $_POST['major'];
        $describe = $_POST['describe'];
        $company = $_POST['company'];
        if($_FILES["img"]["error"] == 4){
        	$this->error("请添加封面图",U("Work/add"));
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->savePath  = 'Upload/work/'; // 设置附件上传目录
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $info = $upload->upload(); 
        if($info){
        	$work = M("Work",'dl_');
        	$map['vc_name'] = $name;
        	$map['vc_img'] = "Public/Upload/work/".$info['img']['savename'];
        	$map['vc_job'] = $job;
        	$map['vc_educational'] = $educational;
        	$map['vc_major'] = $major;
        	$map['text_describe'] = $describe;
        	$map['text_company'] = $company;
            $result = $work->add($map);
        }
        if($result){
        	$this->success("添加成功",U("Work/add"));
        }
	}

	public function workInfo(){
		$id = $_GET['id'];
		$work = M("Work",'dl_');
		$where['int_id'] = $id;
		$info = $work->where($where)->find();
		$this->assign("info",$info);
		$this->display();
	}


	public function editAction(){
		$name = $_POST['name'];
        $job = $_POST['job'];
        $educational = $_POST['educational'];
        $major = $_POST['major'];
        $describe = $_POST['describe'];
        $company = $_POST['company'];
        if($_FILES["img"]["error"] == 4){
        	$map['vc_img'] = $_POST['img_o'];
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/work/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload(); 
	        if($info){
	        	$map['vc_img'] = "Public/Upload/work/".$info['img']['savename'];
	        }
        }
        
        $id = $_POST['id'];
        $work = M("Work",'dl_');
        $map['vc_name'] = $name;
        $map['vc_job'] = $job;
        $map['vc_educational'] = $educational;
        $map['vc_major'] = $major;
        $map['text_describe'] = $describe;
        $map['text_company'] = $company;
        $result = $work->where("int_id='$id'")->save($map);
        
        if($result){
        	$this->success("修改成功",U("Work/index"));
            //redirect(U("News/add"));
        }
	}

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$work = M("Work",'dl_');
		$work->where($where)->setField('bl_state',0);
		$this->success("删除成功",U("Work/index"));
	}








}