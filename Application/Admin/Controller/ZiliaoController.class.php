<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class ZiliaoController extends AuthController {

	public function getZiliaoList(){
		$ziliao = M("Ziliao",'dl_');
		$count = $ziliao->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $ziliao->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function index(){
		$ziliao = M("Ziliao",'dl_');
		$res = $this->getZiliaoList();
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
	    	$info[$key]['title'] = $value['vc_title'];
	    	if($value['bl_type']==1){
	    		$info[$key]['type'] = '公共基础';
	    	}else if($value['bl_type']==2){
	    		$info[$key]['type'] = '专项部分';
	    	}
	    	$biaoqianStr = $value['vc_biaoqian'];
	    	$biaoqianArr = explode('&&', $biaoqianStr);
	    	if($biaoqianStr==''){
	    		$info[$key]['biaoqian'] = '';
	    	}else{
	    		foreach ($biaoqianArr as $k => $v) {
		    		$biaoqianName = M("Ziliaobiaoqian",'dl_')->where("int_id='$v'")->getField('vc_name');
		    		$info[$key]['biaoqian'] .= $biaoqianName.',';
		    	}
	    	}
	    	$info[$key]['movie'] = $value['vc_movie'];
	    	$info[$key]['download'] = $value['vc_download'];
	    	$info[$key]['downpass'] = $value['vc_downpass'];
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}

	public function add(){
		$toplist = M("Ziliaobiaoqian",'dl_')->where("int_parentid=0 and bl_state=1")->select();
    	foreach ($toplist as $k => $v) {
    		$top[$k]['biaoqianid'] = $v['int_id'];
    		$top[$k]['biaoqianname'] = $v['vc_name'];
    	}
    	$this->assign("top",$top);
		$this->display();
	}

	public function addAction(){
		$title = $_POST['title'];//标题
        $type = $_POST['type'];//类型
        $top = $_POST['top'];
        $parent = $_POST['parent'];
        $son = $_POST['son'];
        
        if($type==1){
        	$biaoqian = '';
        }else{
        	if($parent==NULL){
	        	$biaoqian = $top;//标签
	        }else if($son==NULL){
	        	$biaoqian = $top.'&&'.$parent;//标签
	        }else{
	        	$biaoqian = $top.'&&'.$parent.'&&'.$son;//标签
	        }
        }
        $word = $_POST['word'];
        
        $movie = $_POST['movie'];//视频链接
        $yulan = $_POST['yulan'];//预览视频
        $download = $_POST['download'];//下载链接
        $downpass = $_POST['downpass'];//下载密码
        if($title==null){
        	$this->error("请填写标题",U("Ziliao/add"));
        }
        
        
        $fileNum = sizeof($_FILES["cover"]["name"]);
        //echo $fileNum;
        if($fileNum>3){
        	$this->error("请不要上传超过3张图片");
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/ziliao/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload();
	        
	        $ziliao = M("Ziliao",'dl_');
	        $map['bl_type'] = $type;
		    $map['vc_title'] = $title;
		    $map['vc_biaoqian'] = $biaoqian;
		    $map['vc_download'] = $download;
		    $map['vc_downpass'] = $downpass;
		    $map['vc_movie'] = $movie;
		    $map['vc_yulan'] = $yulan;
		    $map['text_content'] = $word;
		    $map['dt_date'] = date('Y-m-d',time());
		    if($info){
		    	foreach ($info as $key => $value) {
		        	$map['vc_cover'] .= "Public/Upload/ziliao/".$value['savename'].'&&';
		    	}
		    }else{
		    	$map['vc_cover'] = "";
		    }
		    $result = $ziliao->add($map);
        }
        if($result){
        	$this->success("添加成功",U("Ziliao/add"));
        }
	}

	/*public function ziliaoInfo(){
		$id = $_GET['id'];
		$res = M("Ziliao",'dl_')->where("int_id='$id'")->find();
		if($res['bl_type']==1){
			$type = "公共基础";
		}else if($res['bl_type']==2){
			$type = "专项部分";
		}
		$this->assign('type',$type);
		$this->assign('info',$res);
		$this->display();
	}*/

	/*public function editAction(){
		$id = $_POST['id'];
		$title = $_POST['title'];
        $word = $_POST['word'];
        $type = $_POST['type'];
        $url = $_POST['url'];
        if($title==null || $word==null){
        	$this->error("请填写内容",U("Ziliao/add"));
        }
        $ziliao = M("Ziliao",'dl_');
        $map['bl_type'] = $type;
        $map['vc_title'] = $title;
        $map['vc_url'] = $url;
        $map['text_content'] = $word;
        $map['dt_date'] = date('Y-m-d',time());
        $result = $ziliao->where("int_id='$id'")->save($map);
        
        if($result){
        	$this->success("修改成功",U("Ziliao/index"));

        }
	}*/

	public function delAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$ziliao = M("Ziliao",'dl_');
		$ziliao->where($where)->delete();
		$this->success("删除成功",U("Ziliao/index"));
	}

	public function biaoqianIndex(){
		$biaoqian = M("Ziliaobiaoqian",'dl_');

		$count = $biaoqian->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $biaoqian->where("bl_state=1")->limit($Page->firstRow.','.$Page->listRows)->order('int_parentid')->select();

		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
	    	$info[$key]['name'] = $value['vc_name'];
	    	$parent = $value['int_parentid'];
	    	if($parent==0){
	    		$info[$key]['up'] = '最高级标签';
	    	}else{
	    		$info[$key]['up'] = $biaoqian->where("int_id='$parent'")->getField('vc_name');
	    	}
			
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}


	public function biaoqianAdd(){
		$toplist = M("Ziliaobiaoqian",'dl_')->where("int_parentid=0 and bl_state=1")->select();
    	foreach ($toplist as $k => $v) {
    		$top[$k]['biaoqianid'] = $v['int_id'];
    		$top[$k]['biaoqianname'] = $v['vc_name'];
    	}
    	$this->assign("top",$top);
		$this->display();
	}

	public function biaoqianAddAction(){

		$map['vc_name'] = $_POST['name'];

		if($_POST['top']==0){

			$map['int_parentid'] = 0;

		}else{

			$map['int_parentid'] = $_POST['parent'];

		}

		$map['bl_state'] = '1';

		$menuid = M("Ziliaobiaoqian",'dl_')->add($map);

		$this->success("添加成功",U("Ziliao/biaoqianAdd"));
		//redirect(U("Ziliao/biaoqianAdd"));

	}

	public function biaoqianDelAction(){
		$id = $_GET['id'];
		$where['int_id'] = $id;
		$mop['bl_state'] = 0;
		$biaoqian = M("Ziliaobiaoqian",'dl_');
		//先判断该标签是否有下级标签
		$res = $biaoqian->where("int_parentid='$id'")->count();
		if($res==0){
			$biaoqian->where($where)->save($mop);
			$this->success("删除成功",U("Ziliao/biaoqianIndex"));
		}else{
			$this->error("还有关联标签未删除");
		}
		
	}

	public function getParent(){

		$topid = $_POST['topid'];

		if($topid==0){

			exit($topid);

		}else{

			$parentlist = M("Ziliaobiaoqian",'dl_')->where("int_parentid='$topid' and bl_state=1")->select();

			foreach ($parentlist as $key => $value) {

				$parent[$key]['biaoqianid'] = $value['int_id'];

    			$parent[$key]['biaoqianname'] = $value['vc_name'];

			}

			echo json_encode($parent);

		}

		

	}






}