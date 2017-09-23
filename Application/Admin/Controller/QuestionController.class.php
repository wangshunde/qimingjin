<?php

namespace Admin\Controller;

use Admin\Common\Controller\AuthController;

class QuestionController extends AuthController {

	public function getList($where){
		$news = M("question",'dl_');
		$count = $news->where($where)->where("bl_state=1")->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $news->where($where)->where("bl_state=1")->order("dt_date desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$res['list'] = $list;
		$res['show'] = $show;
		return $res;
	}

	public function gangwei(){
		//tag 1:岗位动态分析；2：技能提升手段；3：提升机会预设
		$tag = $_GET['tag'];
		$where['vc_type'] = 2;
		switch ($tag) {
			case '1':
				$where['vc_tag'] = '岗位动态分析';
				break;
			case '2':
				$where['vc_tag'] = '技能提升手段';
				break;
			case '3':
				$where['vc_tag'] = '提升机会预设';
				break;
			default:
				break;
		}
		
		$res = $this->getList($where);
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
			$info[$key]['tag'] = $value['vc_tag'];
			$info[$key]['text'] = $value['text_question'];
			$info[$key]['date'] = $value['dt_date'];
			if($value['bl_res']==1){
				$info[$key]['res'] = "已回答";
			}else{
				$info[$key]['res'] = "未回答";
			}
			
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display('question');
	}

	public function yinan(){
		//tag 1:突发性问题；2：行业共性问题；3：单位个性问题
		$tag = $_GET['tag'];
		$where['vc_type'] = 1;
		switch ($tag) {
			case '1':
				$where['vc_tag'] = '突发性问题';
				break;
			case '2':
				$where['vc_tag'] = '行业共性问题';
				break;
			case '3':
				$where['vc_tag'] = '单位个性问题';
				break;
			default:
				break;
		}
		
		$res = $this->getList($where);
		$list = $res['list'];
		$show = $res['show'];
		foreach ($list as $key => $value) {
			$info[$key]['id'] = $value['int_id'];
			$info[$key]['text'] = $value['text_question'];
			$info[$key]['date'] = $value['dt_date'];
			if($value['bl_res']==1){
				$info[$key]['res'] = "已回答";
			}else{
				$info[$key]['res'] = "未回答";
			}
			
		}
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display('question');
	}

	public function reply(){
		$id = $_GET['id'];
		$zhuanjianame = session('auth')['realname'];
		$question = M("question",'dl_')->where("int_id='$id'")->getField('text_question');
		$reply = M("answer",'dl_')->where("int_question_id='$id' and vc_zhuanjia='$zhuanjianame'")->find();
		$this->assign('questionid',$id);
    	$this->assign('question',$question);
    	$this->assign('reply',$reply['text_content']);
		$this->display();
	}

	public function replyAction(){
		$zhuanjianame = session('auth')['realname'];
    	$zhuanjiaid = M("zhuanjia",'dl_')->where("vc_name='$zhuanjianame'")->getField('int_id');
		$content = $_POST['reply'];
		$questionid = $_POST['questionid'];
        $question = M("question",'dl_');
        $answer = M("answer",'dl_');
        $map['bl_res'] = '1';
        
        
        $map1['int_question_id'] = $questionid;
        $map1['int_zhuanjia_id'] = $zhuanjiaid;
        $map1['vc_zhuanjia'] = $zhuanjianame;
        $map1['text_content'] = $content;
        $map1['dt_date'] = date('Y-m-d',time());

        //该专家是否已回答过
        $count = $answer->where("int_question_id='$questionid' and int_zhuanjia_id='$zhuanjiaid'")->count();
        if($count==0){
        	$result = $answer->add($map1);
        }else{
        	$result = $answer->where("int_question_id='$questionid'")->save($map1);
        }
        
        $re = $question->where("int_id='$questionid'")->save($map);
        $questiontype = $question->where("int_id='$questionid'")->getFIeld('vc_type');
        if($result){
        	if($questiontype==1){
        		$this->success("回答成功",U("Question/yinan"));
        	}else if($questiontype=2){
        		$this->success("回答成功",U("Question/gangwei"));
        	}
        }
	}


}