<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class WebsiteController extends AuthController {
	public function telephone(){
		$base = M("base",'dl_');
		$jiuyezhidaoTel = $base->where("vc_name='jiuyezhidaoTel'")->getField("vc_value");
		$xianchangyinanTel = $base->where("vc_name='xianchangyinanTel'")->getField("vc_value");
		$gangweitishengTel = $base->where("vc_name='gangweitishengTel'")->getField("vc_value");
		$this->assign("jiuyezhidaoTel",$jiuyezhidaoTel);
		$this->assign("xianchangyinanTel",$xianchangyinanTel);
		$this->assign("gangweitishengTel",$gangweitishengTel);
		$this->display();
	}

	public function addAction(){

		$jiuyezhidao = $_POST['jiuyezhidao'];
        $xianchangyinan = $_POST['xianchangyinan'];
        $gangweitisheng = $_POST['gangweitisheng'];
        

       
        $base = M("base",'dl_');
        $re = $base->where("vc_name='jiuyezhidaoTel'")->setField("vc_value",$jiuyezhidao);
		$base->where("vc_name='xianchangyinanTel'")->setField("vc_value",$xianchangyinan);
		$base->where("vc_name='gangweitishengTel'")->setField("vc_value",$gangweitisheng);
        if($re){
        	$this->success("添加成功",U("Website/telephone"));
        }
	}

	public function jiuye(){
		$base = M("base",'dl_');
		$jiuyeMovie = $base->where("vc_name='jiuyeMovie'")->getField("vc_value");
		$jiuyeCover = $base->where("vc_name='jiuyeCover'")->getField("vc_value");
		$jiuyeWord = $base->where("vc_name='jiuyeWord'")->getField("vc_value");
		$this->assign("jiuyeMovie",$jiuyeMovie);
		$this->assign("jiuyeCover",$jiuyeCover);
		$this->assign("jiuyeWord",$jiuyeWord);
		$this->display();
	}

	public function jiuyeAction(){
		$word = $_POST['word'];
		$movie = $_POST['movie'];
        $fileNum = sizeof($_FILES["cover"]["name"]);
        if($_FILES["cover"]["name"][0]==''){
        	$this->error("请上传图片");
        	exit();
        }
        //echo $fileNum;
        if($fileNum>3){
        	$this->error("请不要上传超过3张图片");
        }else{
        	$upload = new \Think\Upload();// 实例化上传类
	        $upload->savePath  = 'Upload/base/'; // 设置附件上传目录
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	        $info = $upload->upload();
	        $base = M("base",'dl_');
  			$map['vc_value'] = $movie;
		    if($info){
		    	foreach ($info as $key => $value) {
		        	$map1['vc_value'] .= "Public/Upload/base/".$value['savename'].'&&';
		    	}
		    }else{
		    	$map1['vc_value'] = "";
		    }
		    $map2['vc_value'] = $word;
		    $result2 = $base->where("vc_name='jiuyeWord'")->save($map2);
		    $result1 = $base->where("vc_name='jiuyeCover'")->save($map1);
            $result = $base->where("vc_name='jiuyeMovie'")->save($map);
        }
        $this->success("添加成功",U("Website/jiuye"));
	}

	public function guanggao(){
		$base = M("base",'dl_');
		$guanggao = $base->where("vc_name='guanggao'")->getField("vc_value");
		$this->assign("guanggao",$guanggao);
		$this->display();
	}

	public function guanggaoAction(){
        $word = $_POST['word'];
        if($word==null){
        	$this->error("请填写内容");
        }
        $base = M("base",'dl_');
        $re = $base->where("vc_name='guanggao'")->setField("vc_value",$word);
		$this->success("添加成功",U("Website/guanggao"));
	}


	public function jiuyePage(){
		$num = $_GET['num'];
		$page = 'jiuyePage'.$num;
		$base = M("base",'dl_');
		$jiuyePage = $base->where("vc_name='$page'")->getField("vc_value");
		$this->assign("num",$num);
		$this->assign("jiuyePage",$jiuyePage);
		$this->display();
	}

	public function jiuyePageAction(){
		$num = $_POST['num'];
		$page = 'jiuyePage'.$num;
        $word = $_POST['word'];
        if($word==null){
        	$this->error("请填写内容");
        }
        $base = M("base",'dl_');
        $re = $base->where("vc_name='$page'")->setField("vc_value",$word);
		$this->success("添加成功",U("Website/jiuyePage/num/".$num));
	}


	public function yinanPage(){
		$num = $_GET['num'];
		$page = 'yinanPage'.$num;
		$base = M("base",'dl_');
		$yinanPage = $base->where("vc_name='$page'")->getField("vc_value");
		$this->assign("num",$num);
		$this->assign("yinanPage",$yinanPage);
		$this->display();
	}

	public function yinanPageAction(){
		$num = $_POST['num'];
		$page = 'yinanPage'.$num;
        $word = $_POST['word'];
        if($word==null){
        	$this->error("请填写内容");
        }
        $base = M("base",'dl_');
        $re = $base->where("vc_name='$page'")->setField("vc_value",$word);
		$this->success("添加成功",U("Website/yinanPage/num/".$num));
	}



	public function gangweiPage(){
		$num = $_GET['num'];
		$page = 'tishengshouduan'.$num;
		$base = M("base",'dl_');
		$tisheng = $base->where("vc_name='$page'")->getField("vc_value");
		$this->assign("num",$num);
		$this->assign("tisheng",$tisheng);
		$this->display();
	}

	public function gangweiPageAction(){
		$num = $_POST['num'];
		$page = 'tishengshouduan'.$num;
        $word = $_POST['word'];
        if($word==null){
        	$this->error("请填写内容");
        }
        $base = M("base",'dl_');
        $re = $base->where("vc_name='$page'")->setField("vc_value",$word);
		$this->success("添加成功",U("Website/gangweiPage/num/".$num));
	}

	

	

	
}