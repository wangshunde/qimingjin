<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class NewsController extends AuthController {

	public function getNewsList(){
		$news = M("News",'dl_');
		$count = $news->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $news->where("bl_state=1")->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$news = M("News",'dl_');
		$res = $this->getNewsList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
	    	$info[$key]['title'] = $value['vc_title'];
			$info[$key]['cover'] = URL.$value['vc_cover'];
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
        $word = $_POST['word'];
        if($title==null || $word==null){
        	$this->error("请填写内容",U("News/add"));
        }
        if($_FILES["cover"]["error"] == 4){
        	$this->error("请添加图片",U("News/add"));
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->savePath  = 'Upload/news/'; // 设置附件上传目录
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $info = $upload->upload(); 
        if($info){
        	$news = M("News",'dl_');
        	$map['vc_title'] = $title;
        	$map['vc_cover'] = "Public/Upload/news/".$info['cover']['savename'];
        	$map['text_content'] = $word;
        	$map['dt_date'] = date('Y-m-d',time());
            $result = $news->add($map);
        }
        if($result){
        	$this->success("添加成功",U("News/add"));
            //redirect(U("News/add"));
        }
	}

	public function newsInfo(){
		$id = $_GET['id'];
		$res = M("News",'dl_')->where("int_id='$id'")->find();
		$this->assign('info',$res);
		$this->display();
	}

	public function editAction(){
		$title = $_POST['title'];
        $word = $_POST['word'];
        if($title==null || $word==null){
        	$this->error("请填写内容");
        }
        if($_FILES["cover"]["error"] == 4){
        	$map['vc_cover'] = $_POST['cover_o'];
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/news/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload(); 
	        if($info){
	        	$map['vc_cover'] = "Public/Upload/news/".$info['cover']['savename'];
	        }
        }
        
        $id = $_POST['id'];
        $news = M("News",'dl_');
        $map['vc_title'] = $title;
        $map['text_content'] = $word;
        $map['dt_date'] = date('Y-m-d',time());
        $result = $news->where("int_id='$id'")->save($map);
        
        if($result){
        	$this->success("修改成功",U("News/index"));
            //redirect(U("News/add"));
        }
	}

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$news = M("News",'dl_');
		$news->where($where)->setField('bl_state',0);
		$this->success("删除成功",U("News/index"));
		//redirect(U("News/index"));
	}











}