<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class PeixunController extends AuthController {

	public function getPeixunList(){
		$peixun = M("Peixun",'dl_');
		$count = $peixun->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $peixun->where("bl_state=1")->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$peixun = M("Peixun",'dl_');
		$res = $this->getPeixunList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
	    	$info[$key]['name'] = $value['vc_name'];
			$info[$key]['photo'] = URL.$value['vc_photo'];
			$info[$key]['date'] = $value['dt_date'];
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}

	public function add(){
		$this->display();
	}

	public function addAction(){
		$title = $_POST['title'];
		$tag = $_POST['tag'];
        $word = $_POST['word'];
        if($title==null || $word==null ||$tag==null){
        	$this->error("请填写内容",U("Peixun/add"));
        }
        if($_FILES["cover"]["error"] == 4){
        	$this->error("请添加图片",U("Peixun/add"));
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->savePath  = 'Upload/Peixun/'; // 设置附件上传目录
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $info = $upload->upload(); 
        if($info){
        	$peixun = M("Peixun",'dl_');
        	$map['vc_name'] = $title;
        	$map['vc_tag'] = $tag;
        	$map['vc_photo'] = "Public/Upload/Peixun/".$info['cover']['savename'];
        	$map['text_content'] = $word;
        	$map['dt_date'] = date('Y-m-d',time());
            $result = $peixun->add($map);
        }
        if($result){
        	$this->success("添加成功",U("Peixun/add"));
            //redirect(U("Peixun/add"));
        }
	}

	public function info(){
		$id = $_GET['id'];
		$res = M("Peixun",'dl_')->where("int_id='$id'")->find();
		$this->assign('info',$res);
		$this->display();
	}

	public function editAction(){
		$title = $_POST['title'];
		$tag = $_POST['tag'];
        $word = $_POST['word'];
        if($title==null || $word==null){
        	$this->error("请填写内容");
        }
        if($_FILES["cover"]["error"] == 4){
        	$map['vc_photo'] = $_POST['cover_o'];
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/Peixun/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload(); 
	        if($info){
	        	$map['vc_photo'] = "Public/Upload/Peixun/".$info['cover']['savename'];
	        }
        }
        
        $id = $_POST['id'];
        $peixun = M("Peixun",'dl_');
        $map['vc_name'] = $title;
        $map['vc_tag'] = $tag;
        $map['text_content'] = $word;
        $map['dt_date'] = date('Y-m-d',time());
        $result = $peixun->where("int_id='$id'")->save($map);
        $this->success("修改成功",U("Peixun/index"));
            //redirect(U("Peixun/add"));
	}

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$peixun = M("Peixun",'dl_');
		$peixun->where($where)->setField('bl_state',0);
		$this->success("删除成功",U("Peixun/index"));
		//redirect(U("Peixun/index"));
	}











}