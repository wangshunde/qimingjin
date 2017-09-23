<?php
namespace Home\Controller;
use Think\Controller;
class PersonController extends Controller {
    //个人中心
    public function index(){
        //头像 姓名 是否会员 会员到期日期
        $openid = session("openid");
        $where['vc_openid'] = $openid;
        $userinfo = D("user")->where($where)->find();
        $userUp = $userinfo['vc_upline'];
        $upName = D("user")->where("vc_openid='$userUp'")->getField('vc_name');
        if($upName==''||$upName==NULL){
            $upline = '无';
        }else{
            $upline = $upName;
        }
        $fanxianFen = $userinfo['int_fanxian'];
        if($fanxianFen=='' ||$fanxianFen==NULL){
            $fanxian = 0;
        }else{
            $fanxian = ($fanxianFen/100);
        }
        $vip = session('vip');
        $this->assign("vip",$vip);
        $this->assign("upline",$upline);
        $this->assign("fanxian",$fanxian);
        $this->assign('userinfo',$userinfo);
        $this->assign('tittle','我');
        $this->display();
    }

    //我的留言
    public function myQuestion(){
        $openid = session('openid');
        $wentiWhere['vc_openid'] = $openid; 
        $wentiRes = D("Question")->where($wentiWhere)->field('int_id,vc_tag,text_question')->select();
        $this->assign('wentiRes',$wentiRes);
        
        $this->assign('tittle','我的提问');
        $this->assign('ticket',$ticket);
        $this->display();
    }

    //我的二维码
    public function commodity(){
        $openid = session('openid');
        $main = A("Main");
        $ticket = $main->getTicket($openid);
        
        $this->assign('tittle','推广');
        $this->assign('ticket',$ticket);
        $this->display();
    }

    public function joinvip(){
        $this->assign('tittle','VIP会员');
        $this->display();
    }

    public function setDingdan(){
        //接受信息，插入订单表
        $openid = session("openid");//微信id
        $money = $_GET['pay']*100;//价格（单位分）
        $num = $_GET['num'];//时长
        //订单信息
        $data['vc_order'] = "DL".date('YmdHms',time()).mt_rand(1,9).mt_rand(1,9);
        $data['vc_openid'] = $openid;
        $data['vc_text'] = 'VIP会员充值'.$num.'个月';
        $data['int_money'] = $money;
        $data['bl_pay'] = 0;
        $data['dt_date'] = date('Y-m-d',time());
        $re = D("xiaofei")->add($data);
        if($re===false){
            $this->error('下单失败');
        }else{
            $url = URL."index.php/Pay/orderPay?openid=".$openid."&orderID=".$re."&money=".$money."&num=".$num;
            redirect($url);
        }
        
    }

























    //是否加入分销
    public function joinIndex(){
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->getField('shizhe');
        if($result===false||$result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else{
            $output = array(
            'data'=>$result,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //根据opneid获取上线信息
    public function getUpline(){
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $uplineID = D("user")->where($where)->getField('upline');
        $upline = D("user")->where("weixinid='$uplineID'")->getField('weixinname');
        if($upline===false||$upline===NULL){
            $output = array(
            'data'=>'',
            'info'=>'总部',
            'code'=>-200,
            );
        }else{
            $output = array(
            'data'=>$upline,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //是否编辑姓名
    public function nameIndex(){
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->getField('username');
        if($result===false||$result===NULL||$result===''){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else{
            $output = array(
            'data'=>$result,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //默认地址
    public function getIndexAddr(){
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $addrID = D("user")->where($where)->getField('addr');
        $result = D("address")->where("int_id='$addrID'")->find();
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$result,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //所有地址
    public function getMyAddr(){
        $openid = $_POST['openid'];
        $where['vc_openid'] = $openid;
        $result = D("address")->where($where)->select();
        foreach ($result as $key => $value) {
            $info[$key]['id'] = $value['int_id'];
            $info[$key]['name'] = $value['vc_name'];
            $info[$key]['tele'] = $value['vc_tel'];
            $info[$key]['city'] = $value['vc_city'];
            $info[$key]['addr'] = $value['text_addr'];
            $info[$key]['postcode'] = $value['vc_postcode'];
        }
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //获得地址信息
    public function getOneAddr(){
        $addrID = $_POST['addrID'];
        $where['int_id'] = $addrID;
        $result = D("address")->where($where)->find();
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'查询失败',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$result,
            'info'=>'查询成功',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //删除地址
    public function delAddr(){
        $addrID = $_POST['addrID'];
        $where['int_id'] = $addrID;
        $result = D("address")->where($where)->delete();
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'删除失败',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>'',
            'info'=>'删除成功',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //设为默认地址
    public function indexAddr(){
        $addrID = $_POST['addrID'];
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->setField("addr",$addrID);
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'设置失败',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>'',
            'info'=>'设置成功',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //提交地址
    public function setAddr(){
        $name = $_POST['name'];
        $tele = $_POST['tele'];
        $city = $_POST['city'];
        $addr = $_POST['addr'];
        $postcode = $_POST['postcode'];

        $type = $_POST['type'];
        if($type=='edit'){
            $addrID = $_POST['addrID'];
            $where['int_id'] = $addrID;
            $mop['vc_name'] = $name;
            $mop['vc_tel'] = $tele;
            $mop['vc_city'] = $city;
            $mop['text_addr'] = $addr;
            $mop['vc_postcode'] = $postcode;
            $res = D("address")->where($where)->save($mop);
        }else if($type=='add'){
            $mop['vc_openid'] = $_POST['openid'];
            $mop['vc_name'] = $name;
            $mop['vc_tel'] = $tele;
            $mop['vc_city'] = $city;
            $mop['text_addr'] = $addr;
            $mop['vc_postcode'] = $postcode;
            $res = D("address")->add($mop);
        }
        
        if($res===false){
            $output = array(
            'data'=>'',
            'info'=>'操作失败',
            'code'=>-200,
            );
        }else if($res===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>'',
            'info'=>'操作成功',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }


    //商品信息
    public function getGoodInfo(){
        $id = $_POST['id'];
        $where['int_id'] = $id;
        $result = D("product")->where($where)->find();
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$result,
            'info'=>'success',
            'code'=>100,
            );
        }
        
        exit(json_encode($output));
    }

    //用户信息
    public function getMyInfo(){
        $openid = $_POST['openid'];
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->find();
        $info['userName'] = $result['weixinname'];//昵称
        $info['userImg'] = $result['weixinimage'];//头像
        $info['gold'] = $result['gold']/100;//余额
        $info['realName'] = $result['username'];//姓名
        $info['tel'] = $result['tel'];//手机
        $info['sex'] = $result['sex'];//性别
        $info['city'] = $result['city'];//城市
        $upOpenid = $result['upline'];//上线的微信id
        $upUser= D("user")->where("weixinid='$upOpenid'")->getField('weixinname');
        if($upUser==NULL){
            $info['upline'] = '无';
        }else{
            $info['upline'] = $upUser;
        }
        

        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //修改用户信息
    public function setUserInfo(){
        $openid = $_POST['openid'];
        $realName = $_POST['name'];
        $tel = $_POST['tel'];
        $city = $_POST['city'];
        $sex = $_POST['sex'];
        $mop['username'] = $realName;
        $mop['sex'] = $sex;
        $mop['tel'] = $tel;
        $mop['city'] = $city;
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->save($mop);
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'修改失败',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>array('res'=>'0'),
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>array('res'=>'1'),
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //推广页面信息
    public function getTuiguang(){
        $openid = $_POST['openid'];

        //用户信息
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->find();
        $info['userImg'] = $result['weixinimage'];//头像
        $info['realName'] = $result['username'];//姓名
        $saleAmount = D("fanxian")->where("vc_getopenid='$openid'")->sum("int_money");
        if($saleAmount==null || $saleAmount==''){
            $info['saleAmount'] = 0;//销售额
        }else{
            $info['saleAmount'] = $saleAmount/100;//销售额
        }
        
        $where1['upline'] = $openid;
        $info['groupNum'] = D("user")->where($where1)->count();//团队人数

        $upOpenid = $result['upline'];//上线的微信id
        $upline = D("user")->where("weixinid='$upOpenid'")->getField('weixinname');//上线
        if($upline==null){
            $info['upUser']= '无';
        }else{
            $info['upUser']= $upline;
        }
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //我的团队列表
    public function getMyGroupList(){
        $openid = $_POST['openid'];
        $where['upline'] = $openid;
        $result = D("user")->where($where)->getField("weixinid",true);
        foreach ($result as $key => $value) {
            $user = D("user")->where("weixinid='$value'")->find();
            $info[$key]['userName'] = $user['weixinname'];//昵称
            $info[$key]['userImg'] = $user['weixinimage'];//头像
            if($user['tel']==null){
                $info[$key]['tel'] = '无';//手机
            }else{
                $info[$key]['tel'] = $user['tel'];//手机
            }
            
        }
        if($result===false){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else if($result===NULL){
            $output = array(
            'data'=>'暂无团队成员',
            'info'=>'暂无团队成员',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //充值
    public function chongzhi(){
        $openid = $_GET['openid'];
        $chongzhi = $_GET['chongzhi'];
        $success_url = URL."index.php/Person/chongzhiOK?openid=$openid&chongzhi=$chongzhi";
        $tem_body = array('body'=>'充值','num'=>$chongzhi*100,'success_url'=>$success_url);
        $_SESSION["test"] = $tem_body;
        redirect('http://wangxiaohuawsd.cn/hkmy/Wxpay/jsapi.php');
        //redirect($success_url);
    }

    /*充值成功*/
    public function joinOK(){
        $openid = $_GET['openid'];
        $chongzhi = $_GET['chongzhi'];
        
        $where['weixinid'] = $openid;
        $re = D("user")->where($where)->setInc("gold",$chongzhi*100);

        if($re===false || $re===0){
            $url = URL."Public/html/error.html";
            redirect($url);
        }else{
            alert("充值成功");
            $url = URL."Public/html/index.html";
            redirect($url);
        }  
    }

    //提现
    public function tixian(){
        $openid = $_POST['openid'];
        $money = $_POST['money'];
        $bfanxian = $_POST['bfanxian'];
        $afanxian = $bfanxian*100-$money*100;

        $sent = A("Main");
        $res = $sent->senthongbao($openid,$money,"提现");
        if($res->result_code=='SUCCESS'){
            $ma['vc_openid'] = $openid;
            $ma['int_money'] = $money*100;
            $ma['int_bfanxian'] = $bfanxian*100;
            $ma['int_afanxian'] = $afanxian;
            D("tixian")->add($ma); 
            $data = $money;  
            $info = "提现成功";
            $code = 100;
        }else{
            $data = $res->err_code_des;
            $info = '提现失败';
            $code = -401;
        }
        $output = array(
            'data' => $data, 
            'info' => $info,
            'code' => $code
        );
        exit(json_encode($output));
    }

    //提现列表
    public function getMyTixianList(){
        $openid = $_POST['openid'];
        $list = D("tixian")->where("vc_openid='$openid'")->order("dt_date desc")->select();
        foreach ($list as $key => $value) {
            $info[$key]['money'] = $value['int_money']/100;
            $info[$key]['date'] = $value['da_date'];
        }
        if($list===false || $list===NULL){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //获得二维码
    public function getMyErweima(){
        $openid = $_POST['openid'];
        $main = A("Main");
        $ticket = $main->getTicket($openid);
        $info['ticket'] = $ticket;
        $where['weixinid'] = $openid;
        $result = D("user")->where($where)->find();
        $info['userImg'] = $result['weixinimage'];//头像
        $info['realName'] = $result['username'];//姓名
        $info['userName'] = $result['weixinname'];//用户名
        if($result===false || $result===NULL){
            $output = array(
            'data'=>'',
            'info'=>'无法获取您的信息，请退出重试！',
            'code'=>-200,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

}