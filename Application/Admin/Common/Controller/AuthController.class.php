<?php
namespace Admin\Common\Controller;
use Think\Controller;

class AuthController extends Controller{
	protected function _initialize(){
		$sess_auth = session('auth');
		
		if(!$sess_auth){
			$this->error('非法访问！正在跳转登录页面！',U('Login/index'));
		}
	}
}