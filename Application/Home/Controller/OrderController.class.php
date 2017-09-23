<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller {
    /*
        接收提交的信息，生成订单
    */
    public function setDingdan(){
        //接受信息，插入订单表
        $openid = $_POST['openid'];
        $id = $_POST['id'];
        $num = $_POST['num'];
        //运费及售价
        $goodInfo = D("product")->where("int_id='$id'")->find();
        $cost = $goodInfo['int_cost']/100;
        $price = $goodInfo['int_price']/100;
        //订单信息
        $data['vc_order'] = "DL".date('YmdHms',time()).mt_rand(1,9).mt_rand(1,9);
        $data['vc_user'] = $openid;
        $data['int_product'] = $id;
        $data['int_count'] = $num;
        $data['int_money'] = ($price*$num+$cost)*100;
        $data['bl_state'] = 0;
        $data['bl_send'] = 0;
        $data['bl_get'] = 0;
        $re = D("order")->add($data);
        if($re===false){
            $output = array(
            'data'=>NULL,
            'info'=>'下单失败，请重试',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => $re, 
            'info'=>'sccess',
            'code' => 100,
            );
        }
        exit(json_encode($output)); 
    }

    //获得订单详情
    public function getOrderInfo(){
        $orderID = $_POST['orderID'];
        $orderInfo = D("order")->where("int_id='$orderID'")->find();
        $info['orderNum'] = $orderInfo['vc_order'];
        $info['num'] = $orderInfo['int_count'];
        $info['money'] = ($orderInfo['int_money'])/100;
        $addrID = $orderInfo['int_addr'];
        $info['addr'] = D("address")->where("int_id='$addrID'")->find();
        $info['yundanhao'] = $orderInfo['vc_yundanhao'];
        $productID = $orderInfo['int_product'];
        $info['proName'] = D("product")->where("int_id='$productID'")->getField("vc_name");
        $info['state'] = $orderInfo['bl_state'];
        $info['send'] = $orderInfo['bl_send'];
        $info['get'] = $orderInfo['bl_get'];
        if($orderInfo===false){
            $output = array(
            'data'=>NULL,
            'info'=>'获取信息失败，请重试',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => $info, 
            'info'=>'sccess',
            'code' => 100,
            );
        }
        exit(json_encode($output)); 
    }

    //订单列表
    public function getOrderList(){
        //根据类型查询不同范围
        $type = $_POST['type'];
        switch ($type) {
            case 'all'://全部订单
                $list = D("order")->select();
                break;
            case 'pay'://待付款
                $list = D("order")->where("bl_state=0")->select();
                break;
            case 'send'://待发货
                $list = D("order")->where("bl_state=1 and bl_send=0")->select();
                break;
            case 'get'://待收货
                $list = D("order")->where("bl_state=1 and bl_send=1 and bl_get=0")->select();
                break;
            default:
                break;
        }
        foreach ($list as $key => $value) {
            $info[$key]['orderID'] = $value['int_id'];
            $info[$key]['orderNum'] = $value['vc_order'];
            $info[$key]['num'] = $value['int_count'];
            $info[$key]['money'] = ($value['int_money'])/100;
            $productID = $value['int_product'];
            $info[$key]['proName'] = D("product")->where("int_id='$productID'")->getField("vc_name");
        }
        if($list===false){
            $output = array(
            'data'=>NULL,
            'info'=>'获取信息失败，请重试',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => $info, 
            'info'=>'sccess',
            'code' => 100,
            );
        }
        exit(json_encode($output)); 
    }

    //销售额
    public function getSalesAmount(){
        //通过我的openid统计总销售额
        $openid = $_POST['openid'];
        $res = D("fanxian")->where("vc_getopenid='$openid'")->sum("int_money");
        if($res===false){
            $output = array(
            'data'=>NULL,
            'info'=>'获取信息失败，请重试',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => $res/100, 
            'info'=>'sccess',
            'code' => 100,
            );
        }
        exit(json_encode($output)); 
    }

    //销售明细
    public function getMyFanxian(){
        //根据不同索引条件查询明细
        $openid = $_POST['openid'];
        $type = $_POST['type'];
        switch ($type) {
            case 'all':
                $list = D("fanxian")->where("vc_getopenid='$openid'")->order('dt_date desc')->select();
                break;
            case 'lastMonth':
                $month = strtotime(date('Y-m-1'));
                $lmonth = strtotime(date('Y-m-1',strtotime('-1 month')));
                $sql = "select * from my_fanxian where vc_getopenid='$openid' and '$lmonth'<=UNIX_TIMESTAMP(dt_date) and UNIX_TIMESTAMP(dt_date)<'$month' order by dt_date desc";
                $list = D("fanxian")->query($sql);
                break;
            case 'thisMonth':
                $month = strtotime(date('Y-m-1'));
                $sql = "select * from my_fanxian where vc_getopenid='$openid' and UNIX_TIMESTAMP(dt_date)>'$month' order by dt_date desc";
                $list = D("fanxian")->query($sql);
                break;
            default:
                break;
        }
        foreach ($list as $key => $value) {
            $info[$key]['date'] = $value['dt_date'];
            $payopenid = $value['vc_payopenid'];
            $info[$key]['name'] = D("user")->where("weixinid='$payopenid'")->getField("weixinname");
            $orderID = $value['int_orderid'];
            $info[$key]['orderNum'] = D("order")->where("int_id='$orderID'")->getField("vc_order");
            $info[$key]['money'] = ($value['int_money'])/100;
            $info[$key]['fanxian'] = ($value['int_fanxian'])/100;
        }
        if($list==false){
            $output = array(
            'data'=>NULL,
            'info'=>'列表为空',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => $info, 
            'info'=>'sccess',
            'code' => 100,
            );
        }
        exit(json_encode($output)); 
    }

    //确认收货
    public function setShouhuo(){
        $orderID = $_POST['orderID'];
        $res = D("order")->where("int_id='$orderID'")->setField("bl_get",'1');
        if($res===false){
            $output = array(
            'data'=>NULL,
            'info'=>'操作失败',
            'code'=>-200,
            ); 
        }else{
            $output = array(
            'data' => 'success', 
            'info'=>'success',
            'code' => 100,
            );
        }
        exit(json_encode($output));
    }

}