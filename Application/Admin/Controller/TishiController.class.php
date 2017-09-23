<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class TishiController extends AuthController {

	public function getTishiList(){
		$tishi = M("Zhuanjiatishi",'dl_');
		$count = $tishi->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $tishi->where("bl_state=1")->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$tishi = M("Zhuanjiatishi",'dl_');
		$res = $this->getTishiList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
	    	$info[$key]['title'] = $value['vc_title'];
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
        $tishi = M("Zhuanjiatishi",'dl_');
        $map['vc_title'] = $title;
        $map['text_tishi'] = $word;
        $map['dt_date'] = date('Y-m-d',time());
        $result = $tishi->add($map);
        
        if($result){
        	$this->success("添加成功",U("Tishi/add"));
        }
	}

	public function info(){
		$id = $_GET['id'];
		$res = M("Zhuanjiatishi",'dl_')->where("int_id='$id'")->find();
		$this->assign('info',$res);
		$this->display();
	}

	public function editAction(){
		$title = $_POST['title'];
        $word = $_POST['word'];
        if($title==null || $word==null){
        	$this->error("请填写内容");
        }
        $id = $_POST['id'];
        $tishi = M("Zhuanjiatishi",'dl_');
        $map['vc_title'] = $title;
        $map['text_tishi'] = $word;
        $map['dt_date'] = date('Y-m-d',time());
        $result = $tishi->where("int_id='$id'")->save($map);
        
        if($result){
        	$this->success("修改成功",U("Tishi/index"));
            //redirect(U("News/add"));
        }
	}

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$tishi = M("Zhuanjiatishi",'dl_');
		$tishi->where($where)->setField('bl_state',0);
		$this->success("删除成功",U("Tishi/index"));
		//redirect(U("News/index"));
	}











}