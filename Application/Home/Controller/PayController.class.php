<?php 
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {  
    /*订单支付微信方式*/
    public function orderPay(){
        $orderID = $_GET['orderID'];
        $openid = $_GET['openid'];
        $money = $_GET['money'];//订单金额（单位分）
        $num = $_GET['num'];
        $success_url = URL."index.php/Pay/orderOK?openid=$openid&money=$money&orderID=$orderID&num=$num";
        $this->assign('openid',$openid);
        $this->assign('body','订单支付');
        $this->assign('money',$money/100);
        $this->assign('title','订单支付');
        $this->assign('success_url',$success_url);
        $this->display();
    }

    /*微信支付成功，给上级返现*/
    public function orderOK(){
        $openid = $_GET['openid'];
        $money = $_GET['money'];
        $orderID = $_GET['orderID'];
        $num = $_GET['num'];

        $where['int_id'] = $orderID;
        $mop['bl_pay'] = 1;
        $re = D("xiaofei")->where($where)->save($mop);//补全订单信息

        if($re===false || $re===0){
            $this->assign('info','支付失败，请重试！');
            $this->display();
        }else{
            //给上线返现
            $res = $this->fanxian($orderID,$openid,$money,0.1);
            //改变vip状态
            //session('vip','1');
            //延长vip日期
            $mod = M("user");
            $limit = D("user")->where("vc_openid='$openid'")->getField("dt_limit");
            if($limit==''||$limit==NULL){
                $day = date('Y-m-d',time());
                D("user")->where("vc_openid='$openid'")->setField("dt_now",$day);
                $sql = "update dl_user set bl_vip=1, dt_limit=DATE_ADD(dt_now,INTERVAL $num MONTH) where vc_openid='$openid'";
            }else{
                $sql = "update dl_user set bl_vip=1, dt_limit=DATE_ADD(dt_limit,INTERVAL $num MONTH) where vc_openid='$openid'";
            }
            
            $mod->execute($sql);
            $this->assign('info','支付成功！');
            $this->display();
        } 
    }

    //返现
    public function fanxian($orderID,$openid,$money,$num){
        $fanxian = $money*$num;
        $upline = D("user")->where("vc_openid='$openid'")->getField("vc_upline");
        if($upline==null || $upline==''){
            return false;
        }else{
            $main = A("Main");
            $kefu = $main->sentTongzhi($upline,$openid,$fanxian);
            D("user")->where("vc_openid='$upline'")->setInc("int_fanxian",$fanxian);
            $add['int_orderid'] = $orderID;
            $add['vc_payopenid'] = $openid;
            $add['vc_getopenid'] = $upline;
            $add['int_fanxian'] = $fanxian;
            $re = D("fanxian")->add($add);
            return $re;
        }
        
    }


}
 ?>