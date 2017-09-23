<?php
namespace Home\Controller;
use Think\Controller;
class MainController extends Controller {
    protected $appid = "wx5cf1906edb367f98";
    protected $appsecret = "72e6b94d1bd25051f0fb81108d2db3c4";
    
    public function getTicket($openid){
        //$openid = $_POST['openid'];
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $access_token = $this->gettoken($appid,$appsecret);
        //$ticket_arr = $this->ticket($openid,$access_token);
        //$ticket = json_encode($ticket_arr['ticket']);
        $ticket = $this->ticket($openid,$access_token);
        return $ticket;
    }

    protected function gettoken($appid,$appsecret){
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $token_json = $this->https_request($url);
        $token_arr=json_decode($token_json,true);
        $access_token=$token_arr['access_token'];
        return $access_token;
    }

    protected function ticket($openid,$access_token){
        //带参数二维码
        $qrcode = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene": {"scene_str":"'.$openid.'"}}}';
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $result = $this->https_request($url,$qrcode);
        $jsoninfo = json_decode($result,true);
        $ticket = $jsoninfo['ticket'];
        return $ticket;
    }

    protected function https_request($url,$data = null){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($data)){
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
    }

    //扫码事件
    public function scantest($openid,$upline){
        $user = M("user");
        $record = $user->where("vc_openid='$openid'")->find();
        if($record=='' || $record==NULL){
            $time = date('Y-m-d H-i-s',time());
            $sql ="insert into dl_user(vc_openid,vc_upline) values('$openid','$upline')";
            $re = $user->execute($sql);
            if($re){
                $this->sentTianjia($openid,$upline);
                return '添加推荐人成功';
            }else{
                return '添加推荐人失败，请重试！';
            }
        }else{
            $up = $user->where("vc_openid='$openid'")->getField("vc_upline");
            if($up==NULL|| $up==''){
                $sql ="update dl_user set vc_upline='$upline' where vc_openid='$openid'";
                $re = $user->execute($sql);
                if($re){
                    $this->sentTianjia($openid,$upline);
                    return '添加推荐人成功';
                }else{
                    return '添加推荐人失败，请重试！';
                }
            }else{
                return '您已经有推荐人！';
            }
            
        }
    }


    /*public function senthongbao($openid,$money,$action){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        //商户ID
        $MCHID = "1388122402";
        
        //API密钥
        $PARTNERKEY = "HUAKEMEIYANsds2016101huakemeiyan";
        
        import("Weixin.pay");

        //现金红包调用方法
        $sender = "华科美研";
        $obj2 = array();
        $obj2['wxappid'] = $appid;
        $obj2['mch_id'] = $MCHID;
        $obj2['mch_billno'] = $MCHID.date('YmdHis').rand(1000,9999);
        $obj2['client_ip'] = $_SERVER['REMOTE_ADDR'];
        $obj2['re_openid'] = $openid;
        $m = $money*100;
        $obj2['total_amount'] = $m;
        $obj2['min_value'] = $m;
        $obj2['max_value'] = $m;
        $obj2['total_num'] = 1;
        $obj2['nick_name'] = $sender;
        $obj2['send_name'] = $sender;
        $obj2['wishing'] = "恭喜发财";
        //活动名称
        $obj2['act_name'] = $action;
        $obj2['remark'] = $action;

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $wxHongbaoHelper = new \Weixin\pay($appid,$appsecret);
        $result = $wxHongbaoHelper->pay($url,$obj2,$PARTNERKEY);
        $responseObj = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
        
    }*/

    //购买成功后给上线发送通知
    public function sentTongzhi($upline,$openid,$fanxian){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $access_token = $this->gettoken($appid,$appsecret);
       
        $kefu = $this->kefu($upline,$openid,$fanxian,$access_token);
        return true;
    }

    protected function kefu($upline,$openid,$fanxian,$access_token){
        //客服消息
        $fromuser = D("user")->where("vc_openid='$openid'")->getField("vc_name");
        $word = $fromuser."消费，给您返现".($fanxian/100).'元';
        $qrcode = '{"touser":"'.$upline.'","msgtype":"text","text":{"content":"'.$word.'"}}';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $result = $this->https_request($url,$qrcode);
        $jsoninfo = json_decode($result,true);
        return true;
    }


    //通过扫码添加下线后给上线发送提示信息
    public function sentTianjia($openid,$upline){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $access_token = $this->gettoken($appid,$appsecret);
       
        $kefutj = $this->kefutj($openid,$upline,$access_token);
        
    }

    protected function kefutj($openid,$upline,$access_token){
        //客服消息
        $fromuser = D("user")->where("vc_openid='$openid'")->getField("vc_name");
        if($fromuser==NULL||$fromuser==''){
            $fromuser = "用户".$openid;
        }
        $word = $fromuser."通过扫描您的二维码成功加入旗明晋!";
        $qrcode = '{"touser":"'.$upline.'","msgtype":"text","text":{"content":"'.$word.'"}}';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $result = $this->https_request($url,$qrcode);
        $jsoninfo = json_decode($result,true);
        return $jsoninfo;
    }
}
 ?>