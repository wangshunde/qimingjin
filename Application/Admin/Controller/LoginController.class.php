<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        if(IS_POST) {
            //获取“表单”传过来的值
            $user = I('post.user', null, false);
            $pass = I('post.pass', null, false);
            $code = I('post.verify', null, false);
            //检查验证码 
            $check = $this->check_verify($code);
            if(!$check){  
                $this->error("亲，验证码输错了哦！",$this->site_url,3);  
            }  
            //打开数据库表，将表单值 带入 查询语句中
            $condition['vc_name'] = $user;
            $condition['vc_password'] = md5($pass);
            $condition['bl_state'] = '1';
            $result = D('systemUser')->where($condition)->select();
            //判读是否与数据库匹配，并做出动作
            if(count($result)){
                $login = array();
                $login['id'] = $result[0]['int_id'];
                $login['name'] = $result[0]['vc_name'];
                $login['realname'] = $result[0]['vc_realname'];
                session('auth',$login);
                $this->success('登录成功！',U('Index/index'));
            } else {
                $this->error('用户名或密码不正确，请重新输入！');
            }
        } else {
            $title = '旗明晋';
            $this->assign('title',$title);
            $this->display();
        }
    }

    /** 
     * 验证码检查 
     */  
    public function check_verify($code, $id = ""){  
        $verify = new \Think\Verify();  
        return $verify->check($code, $id);  
    }  

    /** 
     *  
     * 验证码生成 
     */  
    public function verify_c(){  
        $Verify = new \Think\Verify();  
        $Verify->fontSize = 18;  
        $Verify->length   = 4;  
        $Verify->useNoise = false;  
        $Verify->codeSet = '0123456789';  
        $Verify->imageW = 130;  
        $Verify->imageH = 50;  
        $Verify->expire = 600;  
        $Verify->entry();  
    }  


    public function logout(){
        session('auth',null);
        $this->success('退出成功！',U('Login/index'));
    }
}