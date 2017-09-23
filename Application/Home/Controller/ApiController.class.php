<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller {
    public function index(){
		define("TOKEN", "weixin");
		$wechatObj = new wechatCallbackapiTest();
		if (isset($_GET['echostr'])) {
			$wechatObj->valid();
		}else{
			$wechatObj->responseMsg();
		}
    }
}

/**
* 微信接口类
*/


class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            //用户发送的消息类型判断
            switch ($RX_TYPE)
            {
                case 'event':
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                default:
                    $result = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $result;
        }else {
            echo "eooro";
            exit;
        }
    }
    
    private function receiveText($object)
    {
        $keyword = trim($object->Content);

        if($keyword == "1"){
            //回复文本消息
            $content = '【1】了解健康使者如何赚钱
1、分享个人健康使者海报（内含独立个人二维码），邀请朋友扫码加入，共同获得分销收入。
2、分享代金券（每日可免费领取3张）给好友，该好友在都健康下单体检，可获得实付金额2%佣金，可以提现。
3、发起组团体检（最少6人），团员全部下单，可获得实付金额2%佣金，可以提现。
4、健康使者在都健康平台下单体检，体检分数合格可获得20元返现。
5、加入健康使者精英群，每周一、三、五签到领红包，二、四、六发放内部渠道卡，统一在支付宝购买后即可以不高于门市价的金额转售，差价归个人所有，周日本周销售评比，有奖励的哦~
';
            $result = $this->transmitText($object,$content); 
        }elseif ($keyword == "2"){
        	$content = '【2】了解如何组团
进入首页选择组团体检，你可以发起自己的团（不是健康使者最少组织3人，健康使者组少组织6人）或加入已有的团，支付5元定金即可，人数达到最低限制，系统自动改价，支付剩余金额完成下单。
';
        	$result = $this->transmitText($object,$content);
        }elseif ($keyword == "3"){
        	$content = '【3】了解全网健康PK比赛
进入首页底部参与活动，选择健康PK，挑选适宜的体检套餐下单即可，系统会自动分组（8人/组）并显示本组成员，及检后体检分数。
';
        	$result = $this->transmitText($object,$content);
        }
        else if($keyword == "4"){
            $content[] = array("Title"=>"【4】加入健康使者精英群", 
                                "Description"=>"", 
                                "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz/fwIicQLtv7u0VTkoTMLiadoksFPOWejDJ9TPrkzGO2oPlTdjhhJyqRoIicE9xlI3lQm0dIFKZg2BZTXWFvRRibMmLw/0?wx_fmt=jpeg", 
                                "Url" =>"https://mmbiz.qlogo.cn/mmbiz/fwIicQLtv7u0VTkoTMLiadoksFPOWejDJ9TPrkzGO2oPlTdjhhJyqRoIicE9xlI3lQm0dIFKZg2BZTXWFvRRibMmLw/0?wx_fmt=jpeg");
            $result = $this->transmitNews($object, $content);
        }

        else if($keyword == "5"){
            $content = "【5】了解如何定制体检
进入首页选择定制体检，初级定制（适合不熟悉体检项目或者懒的自己选的用户）包含基础包（以自动购选）与增量包（相关体检项目以搭配好，可酌情选择）；高级定制（适合对体检项目熟悉的用户），没有任何限制，勾选你想要的体检项目即可。下单即可完成你的体检定制。
";
            $result = $this->transmitText($object, $content);
        }

        else if($keyword == "6"){
             $content = "【6】了解怎样最省钱
首先加入组团，然后加入健康使者，分享知识库里的资讯，我是不会告诉你这样就是64折了，体检分数合格还有额外的20元返现。加入健康使者精英群（回复【4】）一、三、五有现金红包可以拿；二、四、六有内部渠道卡可以买，差价全赚哦~
";
            $result = $this->transmitText($object, $content);
           
        }
        else if($keyword == "7"){
             $content = "【7】了解成为会员有什么好处
有专属的会员专区，6折、6折、6折，重要的事说三遍。
";
            $result = $this->transmitText($object, $content);
        }
        else if($keyword == "8"){
             $content = "【8】了解体检的检前须知
（1）体检前三天，要注意饮食，不要吃过多油腻、不易消化的食物，不饮酒；如需做大便潜血检查保持素食，避免吃血制品、动物内脏、菠菜等。
（2）不吃对肝、肾功能有损害的药物(降压药、降糖药除外)。
（3）体检前一天要注意休息，避免剧烈运动和情绪激动，检查前一日晚上十二点以后，请完全禁食(包括饮水)；最好能洗个澡，保持充足睡眠。
(4) 体检当日，不要化妆；为方便体检，女士最好不要穿连衣裙、长筒袜；做X线、CT磁共振检查时，宜穿棉布内衣，勿穿带有金属钮扣的内衣、文胸，以免影响放射检查。
（5）如戴眼镜，一定要戴眼镜前去受检；如曾经动过手术，要带相关病历和有关资料；对于贵重物品要妥善保管。
（6）糖尿病、高血压、心脏病、哮喘等慢性疾病患者，请将平时服用的药物携带备用，受检日建议不要停药（少量水），并告知医生。
（7）采血的化验，要求早上7：30-9：30空腹采血，最迟不宜超过10：00，以保证检验结果的准确性。
（8）抽血及肝、胆B超须空腹进行；做膀胱、前列腺、子宫、附件B超时，请勿排尿，如无尿，需在检查前一小时,饮水6-8杯(400-500毫升),使膀胱充盈,以保证检查结果的准确；
（9）做经颅多普勒检查时，需停服对脑血管有影响的药物三天以上；检查前24小时要停止服用镇静剂、兴奋剂及其它作用于神经系统的药物。
（10）女士例假期间，不宜作妇科检查及尿检；做妇科检查前应排空膀胱；乳腺红外线、钼靶检查最佳时间应选择月经来潮第7—10天内。
（11）妊娠女性及准备受孕的女性预先告知医护人员，暂缓X线检查（特殊情况除外）及子宫颈刮片检查。
（12）检查当天需抽完血、做完腹部超音波检查及上肠胃道摄影检查后，方可进食。
";
            $result = $this->transmitText($object, $content);
        }
        else if($keyword == "9"){
             $content = "【9】了解体检机构的资料
官方指定合作机构——美年大健康
美年大健康始创于2004年，是中国领先的专业健康体检和医疗服务集团，集团2015年成功在A股上市（SZ：002044），是医疗和大健康板块中市值和影响力领先的上市公司。拥有中国卓越的专业医师队伍和管理营销团队，以及完善的硬件设备，每年千万级的客户流量堪称中国海量的健康需求入口。
";
            $result = $this->transmitText($object, $content);
        }
        else if($keyword == "10"){
            $content[] = array("Title"=>"【10】了解当前最新活动", 
                                "Description"=>"", 
                                "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz/fwIicQLtv7u0VTkoTMLiadoksFPOWejDJ9w4sgj1LqR1GDGV8xaXEBU6XfcwIwgjVFP2z3DLepUE4ialQ4rCZ1s8A/0?wx_fmt=jpeg", 
                                "Url" =>"https://mmbiz.qlogo.cn/mmbiz/fwIicQLtv7u0VTkoTMLiadoksFPOWejDJ9w4sgj1LqR1GDGV8xaXEBU6XfcwIwgjVFP2z3DLepUE4ialQ4rCZ1s8A/0?wx_fmt=jpeg");
            $result = $this->transmitNews($object, $content);
        }
         else{
             //设置默认回复
         	 $huifuinfo=D("huifushezhi")->find();
             $content = $huifuinfo['morenhuifu'];
             $result = $this->transmitText($object, $content);            
         }
        
        return $result;
    }

    private function receiveImage($object)
    {
        //回复图片消息 
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);;
        return $result;
    }

    private function receiveVoice($object)
    {
        //回复语音消息 
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitVoice($object, $content);;
        return $result;
    }

    private function receiveVideo($object)
    {
        //回复视频消息 
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);;
        return $result;
    }  
    
    private function receiveEvent($object)
    {
        //$content = "";
        switch ($object->Event)
        {
            case "subscribe":   //关注事件
                /*$content = array();
                $content[] = array("Title"=>"单图文标题", 
                                "Description"=>"单图文内容", 
                                "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                                "Url" =>"http://www.163.com");
                */
                $content = D("huifushezhi")->where("id=1")->getField('guanzhuhuifu');
                //有参数
                if(strstr($object->EventKey,"qrscene_")){
                    $type = substr($object->EventKey, 8,2);
                    if($type=='zt'){
                        $openid = substr($object->EventKey, 12);
                        $name = D("user")->where("openid='$openid'")->getField("nickname");
                        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$name."邀请您参与团购，快进入微网站-组团体检参加活动吧！";
                    }else if($type=='td'){
                        $groupid = substr($object->EventKey, 12);

                    }else if($type=='pk'){
                        $groupid = substr($object->EventKey, 12);

                    }else if($type=='qy'){
                        $code = substr($object->EventKey, 12);
                        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;快来加入企业体检活动，这是邀请码'.$code;
                    }else if($type=='dj'){
                        $openid = $object->FromUserName;
                        $quanid = substr($object->EventKey, 12);
                        $shizhe = A("GTHjiankangshizhe");
                        $con = $shizhe->getDaijinquan($quanid,$openid);
                        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$con;
                    }else if($type=='sz'){
                        $upline = substr($object->EventKey, 12);
                        $openid = $object->FromUserName;
                        $shizhe = A("GTHjiankangshizhe");
                        $con = $shizhe->scantest($openid,$upline);
                        $content .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$con;
                    }
                    
                }

                break;
            case 'SCAN':
                $type = substr($object->EventKey, 0,2);
                    if($type=='zt'){
                        $group = substr($object->EventKey, 4);

                    }else if($type=='td'){
                        $group = substr($object->EventKey, 4);

                    }else if($type=='pk'){
                        $group = substr($object->EventKey, 4);

                    }else if($type=='dj'){
                        $openid = $object->FromUserName;
                        $quanid = substr($object->EventKey, 4);
                        $health = A("GTHjiankangshizhe");
                        $con = $health->getDaijinquan($quanid,$openid);
                        $content = $con;
                    }else if($type=='qy'){
                        $code = substr($object->EventKey, 4);
                        
                    }else if($type=='sz'){
                        $upline = $object->EventKey;
                        $openid = $object->FromUserName;
                        $shizhe = A("GTHjiankangshizhe");
                        $con = $shizhe->scantest($openid,$upline);
                        $content = $con;
                    }
                
                
                break;
            case 'CLICK':
                /*if($object->EventKey=='红包数量'){
                    //查询红包数量,mn_hongbaorecord表中count(*)where $object->FromUserName=userOpenid
                    $userOpenid = $object->FromUserName;
                    $num = M('hongbaorecord')->where("userOpenid='$userOpenid'")->count('id');
                    $content = "您的好友帮您获得了".$num."元红包";
                }*/
                break;
            case "unsubscribe": //取消关注事件
                $content = "";
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
    /*
     * 回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
    /*
     * 回复图片消息
     */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId></Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复语音消息
     */
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId></Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复视频消息
     */
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
                    <MediaId><![CDATA[%s]]></MediaId>
                    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复图文消息
     */
    private function transmitNews($object, $arr_item)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>
                    ";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <Content><![CDATA[]]></Content>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                    $item_str</Articles>
                    </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }
    
    /*
     * 回复音乐消息
     */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
}